<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_savings_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'LOAN REPAYMENTS REPORT';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'TransactionReport', 'ReportModelMethod' => 'getTransactionReport', 'is_loan_report2' => true, 'is_loan_report' => true];
$request_data = array_merge($request_data, $_REQUEST);
$_REQUEST['transaction_start_date'] = $_REQUEST['transaction_start_date'] ?? date('Y-m-d');
$_REQUEST['transaction_end_date'] = $_REQUEST['transaction_end_date'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport($request_data);
$records = @$report_reponse['data'] ?? [];
// var_dump($report_reponse);
// exit;
$response = new Response();
$actypes = $response->getAllBankLoanProducts($user[0]['bankId'], $user[0]['branchId']);
// var_dump($actypes);
// exit;

$branches = $response->getBankBranches($_SESSION['session_user']['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search($_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}

$account_type_name = '';
if (@$_REQUEST['actype']) {
    $key = array_search($_REQUEST['actype'], array_column($actypes, 'id'));
    $account_type_name = $actypes[$key]['name'];
}

$staff_name = '';
$credit_name = '';
if (@$_REQUEST['authorized_by_id']) {
    $key = array_search($_REQUEST['authorized_by_id'], array_column($staff, 'id'));
    $staff_name = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}
if (@$_REQUEST['credit_officer']) {
    $key = array_search($_REQUEST['credit_officer'], array_column($staff, 'id'));
    $staff_name = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}

$report_type = "Loan Repayment Report";

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

                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Clients</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Bank Clients</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>


                        <form class="ajax_results_form" method="GET">
                            <input type="hidden" name="filtered" value="1">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
                                                <option value="0"> All</option>
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



                                <div class="col-md-3">
                                    <div class="form-group">

                                        <label class="text-label form-label">Authorized By *</label>

                                        <select id="clientsselect" class=" form-control " name="authorized_by_id">
                                            <option value="0">All </option>
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


                                <div class="col-md-3">
                                    <div class="form-group">

                                        <label class="text-label form-label">Credit Officer *</label>

                                        <select id="journalacc" class="form-control" name="credit_officer">
                                            <option value="0">All </option>
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
                                <div class="col-md-3">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select id="osector" class="me-sm-2 default-select form-control wide" name="actype" style="display: none;">
                                            <option value="0"> All</option>
                                            <?php

                                            foreach ($actypes as $row) {
                                                $selected = @$_REQUEST['actype'] == $row['id'] ? "selected" : "";
                                            ?>
                                                <option value="<?= $row['id']; ?>" <?= $selected; ?>>
                                                    <?= $row['name'] . '  - ' . $row['rate'] . ' - ' . $row['method'] ?>
                                                </option>

                                            <?php }
                                            ?>

                                        </select>

                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
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
                            $request_string = 'branchName=' . @$branch_name . '&accountName=' . @$account_type_name . '&staffName=' . @$staff_name;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>
                                <!-- <a href="#" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </a> -->
                                <!-- <a class="btn btn-primary light btn-xs" onclick="PrintContent('exreportn')">
                                    <i class="fas fa-file-pdf"></i>Print
                                </a> -->
                                <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                                    <i class="fas fa-file-pdf"></i>&nbsp;PDF
                                </a>
                                <a href="export_report.php?exportFile=report_lps&<?= $request_string ?>&orientation=landscape" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> <?= @$report_type ?> </strong> </td>
                            </tr>
                        </table>

                        <table>
                            <?php if (@$_REQUEST['branchId']) : ?>
                                <tr>
                                    <td width="18%"> Branch:</td>
                                    <td> <strong> <?= @$branch_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['actype']) : ?>
                                <tr>
                                    <td width="18%"> Loan Product:</td>
                                    <td> <strong> <?= @$account_type_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>



                            <?php if (@$_REQUEST['authorized_by_id']) : ?>
                                <tr>
                                    <td width="18%"> Authorized by:</td>
                                    <td> <strong> <?= @$staff_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>
                            <?php if (@$_REQUEST['credit_officer']) : ?>
                                <tr>
                                    <td width="18%"> Credit Officer:</td>
                                    <td> <strong> <?= @$credit_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php
                            //  if (@$_REQUEST['transaction_start_date'] && @$_REQUEST['transaction_end_date']) :
                            ?>
                            <tr>
                                <td colspan="2">
                                    <div>
                                        From: <strong> <?= normal_date($_REQUEST['transaction_start_date'] ?? date('Y-m-d')) ?> </strong>
                                        To: <strong> <?= normal_date($_REQUEST['transaction_end_date'] ?? date('Y-m-d')) ?> </strong>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            //  endif 
                            ?>
                        </table>

                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th rowspan="2">REF NO.</th>
                                    <th rowspan="2">A/C N0</th>
                                    <th rowspan="2">Client Names</th>
                                    <th colspan="3" style="text-align: center;">Amount</th>
                                    <th rowspan="2">Description</th>
                                    <th rowspan="2">Authorized By</th>
                                    <th rowspan="2">Branch</th>
                                    <th rowspan="2">Trxn date</th>
                                    <th rowspan="2">Mode of Payment</th>
                                </tr>

                                <tr>
                                    <th>Interest</th>
                                    <th>Principal</th>
                                    <th>Penalty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_deposits = 0;
                                $total_withdrawals = 0;
                                $total_penalty = 0;
                                foreach ($records as $record) {
                                ?>
                                    <tr>
                                        <td> <?= @$record['transaction_id'] ?> </td>
                                        <td> <?= @$record['membership_no'] == 0 ? $record['client_id'] : @$record['membership_no']; ?> </td>
                                        <td> <?= @$record['client_names'] ?> </td>
                                        <td>
                                            <?= number_format(@$record['loan_interest']) ?>
                                        </td>
                                        <td>
                                            <?= number_format(@$record['amount']) ?>
                                        </td>
                                        <td>
                                            <?= number_format($record['loan_penalty'] ?? 0) ?>
                                        </td>

                                        <td> <?= @$record['transaction_description'] ?> </td>
                                        <td> <?= @$record['authorized_by_names'] ?> </td>
                                        <td> <?= @$record['branch_name'] ?> </td>
                                        <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>
                                        <td> <?= @$record['pay_method'] ?> </td>
                                    </tr>
                                <?php

                                    $total_deposits += (int) $record['loan_interest'];

                                    $total_withdrawals += (int) $record['amount'];
                                    $total_penalty += (int) $record['loan_penalty'] ?? 0;
                                } ?>

                                <tr>
                                    <th colspan="3">Total </th>
                                    <th> <?= number_format($total_deposits) ?> </th>
                                    <th> <?= number_format($total_withdrawals) ?> </th>
                                    <th> <?= number_format($total_penalty) ?> </th>
                                    <th></th>
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
        <?php
        include('includes/bottom_scripts.php');
        ?>

</body>

</html>