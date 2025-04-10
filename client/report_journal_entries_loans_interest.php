<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_journal_report')) {
    return $permissions->isNotPermitted(true);
}

$title = 'LOANS INTEREST INCOME LEDGER REPORT';


require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'TransactionReport', 'ReportModelMethod' => 'getLoansLedgerReportInterest', 'is_expense_report' => false, 'branchId' => @$_REQUEST['branch'], 'loan_product_id' => @$_REQUEST['acid'] ?? 0, 'transaction_end_date' => @$_REQUEST['transaction_end_date'] ?? date('Y-m-d'), 'bank_id' => @$user[0]['bankId'], 'lpid' => $_REQUEST['id']];

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



$report_type = "Loans Ledger Report";


$journal_account = ' ';
if (@$_REQUEST['acid']) {
    $key = array_search($_REQUEST['acid'], array_column($sub_accs, 'id'));
    $journal_account ='Income from '. $sub_accs[$key]['name'];
}

if (@$_REQUEST['id'] && !@$_REQUEST['acid']) {
    $key = array_search($_REQUEST['id'], array_column($sub_accs, 'lpid'));
    $journal_account = 'Income from ' . $sub_accs[$key]['name'];
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
                            <input type="hidden" name="acid" value="<?= @$_REQUEST['acid'] ?>">
                            <input type="hidden" name="id" value="<?= @$_REQUEST['id'] ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">


                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branch" style="display: none;">
                                                <option value="0"> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branch'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branch'] == $row['id'] ? "selected" : "";
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
                                        <label class="text-label form-label">Credit Officer *</label>
                                        <select id="payment_methods" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="authorized_by_id">
                                            <option value="0"> All </option>
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
                                        <label class="text-label form-label">Loan Products*</label>
                                        <select id="clientsselect" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="acid">
                                            <option value="0"> All </option>
                                            <?php
                                            if ($sub_accs !== '') {
                                                foreach ($sub_accs as $row) {
                                                    if ($row['lpid'] > 0 && strpos($row['name'], 'Interest') === false && strpos($row['name'], 'Penalty') === false && strpos($row['name'], 'Income') === false) {
                                            ?>
                                                        <option value="<?= $row['id'] ?>" <?= @$_REQUEST['acid'] == $row['id'] ? 'selected' : '' ?>>
                                                            <?= $row['name'] ?>
                                                        </option>
                                                <?php }
                                                }
                                            } else { ?>
                                                <option readonly>No Loan Product Accounts yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> Start Date *</label>
                                        <input type="date" class="form-control" name="transaction_start_date" value="<?= @$_REQUEST['transaction_start_date'] ?? date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date" max="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> End Date *</label>
                                        <input type="date" class="form-control" name="transaction_end_date" value="<?= @$_REQUEST['transaction_end_date'] ?? date('Y-m-d'); ?>" placeholder="End Date" max="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>

                            </div><br />
                            <div class=" row">




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
                                <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                                    <i class="fas fa-file-pdf"></i>&nbsp;PDF
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
                                    <td width="18%"> Loan Product:</td>
                                    <td> <strong> <?= $journal_account; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['authorized_by_id']) : ?>
                                <tr>
                                    <td width="18%"> Credit Officer:</td>
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
                                <th>Notes</th>
                                <th>Trxn Date</th>
                                <th>Ref. No.</th>
                                <th>DR</th>
                                <th>CR</th>
                                <th>Balance</th>



                            </thead>
                            <tbody>

                                <?php
                                $total_dr = 0;
                                $total_cr = 0;

                                $dess = '';
                                $count = 1;
                                $ccount = 0;
                                $dcount = 0;
                                $val = 0;
                                $ctotal = 0;
                                $dtotal = 0;

                                $debit = 0;
                                $credit = 0;
                                $details = $response->getJournalAccBfs($_GET['id'], $_GET['branch'], $_GET['acid'], $_GET['transaction_start_date'], $_GET['transaction_end_date'], @$user[0]['bankId']);

                                $val = $details[0]['bf'] ?? 0;
                                echo '
                                                    <tr style="color: blue !important">
                                                    <td>' . $count++ . '</td>
                                                    <td>(Opening Balance-for manual entries)</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=' . @$_REQUEST['acid'] . '&transaction_start_date=1900-01-01&transaction_end_date=' . date('Y-m-d') . '"> ' . number_format($val) . '</a></td>
                                                    <td></td>

                                                    <td>' . number_format($val) . '</td>

                                                    </tr>
                                                    ';

                                foreach ($records as $record) {
                                    $debit = number_format($record['loan_interest']);
                                    $credit = "-";
                                    $val = $val +  $record['loan_interest'];
                                    $ctotal = $ctotal + $record['loan_interest'];
                                    $dcount++;
                                    $dess = '';
                                    $meth = $record['pay_method'] ?? 'cheque';

                                    $ref = strtolower($record['t_type'] . '-ref-' . $meth . '-' . $record['tid'] . '-' . $record['_authorizedby']);

                                ?>
                                    <tr>

                                        <td><?= $count++ ?></td>
                                        <td><?= $dess . @$record['description'] ?></td>
                                        <td><?= normal_date_short($record['dc']) ?></td>
                                        <td class="no_print clickable_ref_no" ref-no="<?= $ref ?>" tid="<?= $deposit['tid'] ?>"><?= $ref ?></td>

                                        <td><?= $debit ?></td>
                                        <td><?= $credit ?></td>
                                        <td> <?= number_format($val) ?></td>




                                    </tr>
                                <?php

                                } ?>

                                <tr>
                                    <th colspan="4">Total </th>
                                    <th> <?= number_format($ctotal) ?> </th>
                                    <th> <?= number_format($dtotal) ?> </th>
                                    <th> <?= number_format($val) ?> </th>


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

        <script type="text/javascript">
            var table = $('#staff').dataTable({
                destroy: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
            })
        </script>
</body>

</html>