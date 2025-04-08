<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_journal_report')) {
    return $permissions->isNotPermitted(true);
}

$title = 'SAVINGS LEDGER REPORT';


require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'TransactionReport', 'ReportModelMethod' => 'getSavingsLedgerReport', 'is_expense_report' => false, 'branchId' => $_GET['branch'], 'loan_product_id' => $_GET['said']];
$request_data = array_merge($request_data, $_REQUEST);

$report_reponse = $ReportService->generateReport($request_data);
$records = @$report_reponse['data'] ?? [];


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



$report_type = "Savings Report";


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

                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section">
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
                                <th>A/C NO</th>
                                <th>Names</th>
                                <th>Contacts</th>
                                <th>Client Type</th>
                                <th>Saving Product</th>
                                <th>A/C Balance</th>
                                <th>Freezed Amount</th>
                                <th>Total Balance</th>


                            </thead>
                            <tbody>

                                <?php
                                $i = 0;
                                $total_ac = 0;
                                $total_freezed = 0;
                                foreach ($records as $record) {
                                    ++$i;
                                ?>
                                    <tr>

                                        <td class=""><a href="member_statement_range.php?id=<?= @$record['id'] ?>"><?= @$record['id'] ?></a></td>
                                        <td> <?= $record['membership_no'] ?> </td>
                                        <td> <?= @$record['shared_name'] ? @$record['shared_name'] : @$record['firstName'] . ' ' . @$record['lastName'] ?> </td>
                                        <td> <?= @$record['primaryCellPhone'] ?> </td>
                                        <td> <?= strtoupper(@$record['client_type']) ?> </td>
                                        <td> <?= @$record['product_name'] ?> </td>
                                        <td> <?= number_format(@$record['acc_balance'] ?? 0) ?> </td>
                                        <td> <?= number_format(@$record['freezed_amount'] ?? 0) ?> </td>
                                        <td> <?= number_format((@$record['acc_balance'] ?? 0) + (@$record['freezed_amount'] ?? 0)) ?> </td>



                                    </tr>
                                <?php
                                    $total_ac += (int) $record['acc_balance'];
                                    $total_freezed += (int) $record['freezed_amount'];
                                } ?>

                                <tr>
                                    <th colspan="6">Totals (<?= number_format($i) ?>) </th>
                                    <th> <?= number_format($total_ac) ?> </th>
                                    <th> <?= number_format($total_freezed) ?> </th>
                                    <th> <?= number_format($total_freezed + $total_ac) ?> </th>


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