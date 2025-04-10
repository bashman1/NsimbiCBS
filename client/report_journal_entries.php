<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_journal_report')) {
    return $permissions->isNotPermitted(true);
}

if (@$_REQUEST['transaction_type'] == 'I') {
    $title = 'INCOMES LEDGER REPORT';
    $is_exp = false;
} else if (@$_REQUEST['transaction_type'] == 'E') {
    $title = 'EXPENSE LEDGER REPORT';
    $is_exp = false;
} else if (@$_REQUEST['transaction_type'] == 'LIA') {
    $title = 'LIABILITIES LEDGER REPORT';
    $is_exp = false;
} else if (@$_REQUEST['transaction_type'] == 'ASS') {
    $title = 'ASSETS LEDGER REPORT';
    $is_exp = false;
} else {
    $title = 'LEDGER REPORT';
    $is_exp = true;
}

require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'TransactionReport', 'ReportModelMethod' => 'getTransactionReport2', 'is_expense_report' => $is_exp];
$request_data = array_merge($request_data, $_REQUEST);
$_REQUEST['transaction_start_date'] = $_REQUEST['transaction_start_date'] ?? date('Y-m-d');
$_REQUEST['transaction_end_date'] = $_REQUEST['transaction_end_date'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport($request_data);
$records = @$report_reponse['data'] ?? [];
// var_dump($report_reponse);
// exit;

$response = new Response();
$branches = $response->getBankBranches($_SESSION['session_user']['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);
$sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
// var_dump($members);
// exit;

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search($_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}

$staff_name = '';
if (@$_REQUEST['authorized_by_id']) {
    $key = array_search($_REQUEST['authorized_by_id'], array_column($staff, 'id'));
    $staff_name = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}


if (@$_REQUEST['transaction_type'] == 'I') {
    $report_type = "Income Report";
} else if (@$_REQUEST['transaction_type'] == 'E') {
    $report_type = "Expense Report";
} else if (@$_REQUEST['transaction_type'] == 'LIA') {
    $report_type = "Liabilities Report";
} else if (@$_REQUEST['transaction_type'] == 'ASS') {
    $report_type = "Assets Report";
} else {
    $report_type = "Ledger Report";
}

$journal_account = '';
if (@$_REQUEST['acid']) {
    $key = array_search($_REQUEST['acid'], array_column($sub_accs, 'id'));
    $journal_account = $sub_accs[$key]['name'];
}



?>
<?php require_once('includes/head_tag.php');
require_once('includes/reports_css.php');
?>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php include('includes/preloader.php'); ?>

    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        include('includes/nav_bar.php');
        include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>


                        <form class="ajax_results_form" method="GET">
                            <input type="hidden" name="filtered" value="1">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branchId'] == $row['id'] ? "selected" : "";
                                                ?>
                                                        <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                            <?= $row['name'] ?>
                                                        </option>
                                                <?php }
                                                } ?>

                                            </select>
                                        <?php } ?>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Journal Entry Type*</label>
                                        <select id="journalacc" data-select2-id="single-select" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="transaction_type">
                                            <option value="" selected> All </option>
                                            <option value="I" <?= @$_REQUEST['transaction_type'] == "I" ? "selected" : "" ?>> Incomes</option>
                                            <option value="E" <?= @$_REQUEST['transaction_type'] == "E" ? "selected" : "" ?>>Expenses</option>
                                            <option value="ASS" <?= @$_REQUEST['transaction_type'] == "ASS" ? "selected" : "" ?>>Assets</option>
                                            <option value="LIA" <?= @$_REQUEST['transaction_type'] == "LIA" ? "selected" : "" ?>>Liabilities</option>
                                            <option value="CAP" <?= @$_REQUEST['transaction_type'] == "CAP" ? "selected" : "" ?>>Capital</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Authorized By *</label>
                                        <select id="payment_methods" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="authorized_by_id">
                                            <option value=""> All </option>
                                            <?php
                                            if ($staff !== '') {
                                                foreach ($staff as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['authorized_by_id'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php }
                                            } else { ?>
                                                <option readonly>No Staff Added yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Journal Account*</label>
                                        <select id="clientsselect" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="acid">
                                            <option value=""> All </option>
                                            <?php
                                            if ($sub_accs !== '') {
                                                foreach ($sub_accs as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['acid'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php }
                                            } else { ?>
                                                <option readonly>No Journal Accounts yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> Start Date *</label>
                                        <input type="date" class="form-control" name="transaction_start_date" value="<?= @$_REQUEST['transaction_start_date'] ?? date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> End Date *</label>
                                        <input type="date" class="form-control" name="transaction_end_date" value="<?= @$_REQUEST['transaction_end_date'] ?? date('Y-m-d'); ?>" placeholder="End Date">
                                    </div>
                                </div>

                            </div><br />
                            <div class="row">




                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch Entries</button>
                                    </div>
                                </div>

                            </div>

                        </form>


                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn"><i class="fa fa-arrow-left"></i> Back</a>
                            <?= $report_type ?>
                        </h4>

                        <?php if (count($records)) :
                            $request_string = 'branchName=' . @$branch_name . '&staffName=' . @$staff_name . '&journalAccount=' . @$journal_account;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>
                                <!-- <a href="#" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </a> -->
                                <a href="export_report.php?exportFile=report_journals&<?= $request_string ?>&orientation=landscape" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>
                                <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                                    <i class="fas fa-file-pdf"></i>&nbsp;Print
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> <?= $report_type ?> </strong> </td>
                            </tr>
                        </table>

                        <table>
                            <?php if (@$_REQUEST['branchId']) : ?>
                                <tr>
                                    <td width="18%"> Branch:</td>
                                    <td> <strong> <?= $branch_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$journal_account) : ?>
                                <tr>
                                    <td width="18%"> Journal Account:</td>
                                    <td> <strong> <?= $journal_account; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['authorized_by_id']) : ?>
                                <tr>
                                    <td width="18%"> Authorized by:</td>
                                    <td> <strong> <?= $staff_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['transaction_start_date'] && @$_REQUEST['transaction_end_date']) : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            From: <strong> <?= normal_date($_REQUEST['transaction_start_date']) ?> </strong>
                                            To: <strong> <?= normal_date($_REQUEST['transaction_end_date']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>

                        <table class="report_table">
                            <thead>
                                <th>#</th>
                                <th>Trxn date</th>
                                <th>Entry Type</th>
                                <th>DR</th>
                                <th>CR</th>
                                <th>Balance</th>
                                <th>Narration</th>
                                <th>Mode of Payment</th>
                                <th>Authorized By</th>
                                <th>Branch</th>
                                <!-- <th>Journal Account</th> -->

                            </thead>
                            <tbody>

                                <?php
                                $total_amount = 0;
                                $total_amount_dr = 0;
                                $total_amount_bal = 0;
                                $balance = 0;
                                foreach ($records as $record) {
                                    $cr_amount = 0;
                                    $dr_amount = 0;

                                    $ref =    $record['t_type'] . '-ref-' . $record['pay_method'] . '-' . $record['transaction_id'] . '-' . $record['_authorizedby'];

                                    $title1 = '';
                                    if ($record['t_type'] == 'E') {
                                        $title1 =   'Expense Entry';

                                        $cr_amount = 0;
                                        $dr_amount = $record['amount'] ?? 0;

                                        // $balance = $balance + $dr_amount;
                                    }
                                    if ($record['t_type'] == 'LIA') {
                                        $title1 =   'Liability Entry';

                                        if ($record['dr_acid'] == @$_REQUEST['acid']) {


                                            $dr_amount = 0;
                                            $cr_amount = $record['amount'] ?? 0;
                                        } else {
                                            $cr_amount = 0;
                                            $dr_amount = $record['amount'] ?? 0;
                                        }

                                        // $balance = $balance + ($cr_amount  - $dr_amount);
                                    }
                                    if ($record['t_type'] == 'CAP') {
                                        $title1 =   'Capital Entry';
                                        if ($record['dr_acid'] == @$_REQUEST['acid']) {
                                            $dr_amount = 0;
                                            $cr_amount = $record['amount'] ?? 0;
                                        } else {
                                            $cr_amount = 0;
                                            $dr_amount = $record['amount'] ?? 0;
                                        }


                                        // $balance = $balance + $cr_amount;
                                    }
                                    if ($record['t_type'] == 'AJE') {
                                        $title1 =   'Advanced Journal Entry';

                                        if ($record['cr_acid'] == @$_REQUEST['acid']) {
                                            $cr_amount = 0;
                                            $dr_amount = $record['amount'] ?? 0;
                                        } else {
                                            $dr_amount = 0;
                                            $cr_amount = $record['amount'] ?? 0;
                                        }

                                        // $balance = $balance + ($dr_amount - $cr_amount);
                                    }
                                    if ($record['t_type'] == 'ASS') {
                                        $title1 =   'Asset Entry';

                                        if ($record['cr_acid'] == @$_REQUEST['acid'] || $record['acid'] == @$_REQUEST['acid']) {

                                            $cr_amount = 0;
                                            $dr_amount = $record['amount'] ?? 0;
                                        } else {
                                            $dr_amount = 0;
                                            $cr_amount = $record['amount'] ?? 0;
                                        }



                                        // $balance = $balance + $dr_amount;
                                    }

                                    if ($record['t_type'] == 'D') {
                                        $title1 =   'Deposit Entry';

                                        $cr_amount = 0;
                                        $dr_amount = $record['amount'] ?? 0;

                                        // $balance = $balance + $dr_amount;
                                    }
                                    if ($record['t_type'] == 'W') {
                                        $title1 =   'Withdraw Entry';

                                        $dr_amount = 0;
                                        $cr_amount = $record['amount'] ?? 0;

                                        // $balance = $balance + $dr_amount;
                                    }

                                    if ($record['t_type'] == 'BF') {
                                        $title1 =   'Imported Entry';

                                        $cr_amount = 0;
                                        $dr_amount = $record['amount'] ?? 0;

                                        // $balance = $balance + $dr_amount;
                                    }
                                    if ($record['t_type'] == 'I' || $record['t_type'] == 'R' || $record['t_type'] == 'SMS' || $record['t_type'] == 'C') {
                                        $title1 =   'Income Entry';

                                        $cr_amount = 0;
                                        $dr_amount = $record['amount'] ?? 0;

                                        // $balance = $balance + $cr_amount;
                                    }

                                    $balance = $balance + ($dr_amount - $cr_amount);
                                ?>
                                    <tr>

                                        <td class="no_print clickable_ref_no" ref-no="<?= @$ref ?> " tid="<?= @$record['transaction_id'] ?>"><?= @$ref ?></td>
                                        <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>
                                        <td> <?= $title1 ?> </td>
                                        <td> <?= number_format(@$dr_amount) ?> </td>
                                        <td> <?= number_format(@$cr_amount) ?> </td>
                                        <td> <?= number_format(@$balance) ?> </td>
                                        <td> <?= @$record['transaction_description'] ?> </td>
                                        <td> <?= @$record['pay_method'] ?> </td>
                                        <td> <?= @$record['authorized_by_names'] ?> </td>
                                        <td> <?= @$record['branch_name'] ?> </td>


                                    </tr>
                                <?php
                                    $total_amount += (int) $cr_amount;
                                    $total_amount_dr += (int) $dr_amount;
                                    $total_amount_bal = $balance;
                                } ?>

                                <tr>
                                    <th colspan="3">Total </th>
                                    <th> <?= number_format($total_amount_dr) ?> </th>
                                    <th> <?= number_format($total_amount) ?> </th>
                                    <th> <?= number_format($total_amount_bal) ?> </th>

                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        if (!count($records)) {
                            require_once('./not_records_found.php');
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <div class="modal fade" id="pageGeneralModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>

                </div>
            </div>
        </div>
        <?php
        include('includes/bottom_scripts.php');
        ?>

</body>

</html>