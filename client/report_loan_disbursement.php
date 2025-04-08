<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_loan_status_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'LOAN DISBURSEMENT REPORT';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'TransactionReport', 'ReportModelMethod' => 'getDisbursementReport', 'is_disburse_report' => true, 'is_credit_officers_report' => true];
$request_data = array_merge($request_data, $_REQUEST);
$_REQUEST['transaction_start_date'] = @$_REQUEST['transaction_start_date'] ?? date('Y-m-d');
$_REQUEST['transaction_end_date'] = @$_REQUEST['transaction_end_date'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport($request_data);
$records = @$report_reponse['data'] ?? [];
// var_dump($report_reponse);
// exit;
$response = new Response();
$lps = $response->getAllBankLoanProducts($user[0]['bankId'], $user[0]['branchId']);
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
if (@$_REQUEST['loan_product_id']) {
    $key = array_search($_REQUEST['loan_product_id'], array_column($lps, 'id'));
    $account_type_name = $actypes[$key]['name'];
}

$staff_name = '';
if (@$_REQUEST['authorized_by_id']) {
    $key = array_search($_REQUEST['authorized_by_id'], array_column($staff, 'id'));
    $staff_name = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}

$report_type = "Loan Disbursement Report";

?>
<?php require_once('includes/head_tag.php');
require_once('includes/reports_css.php');
?>
<style>
    table {
        width: 100%;
        table-layout: fixed;
    }

    th,
    td {
        word-wrap: break-word;
        overflow: hidden;
    }
</style>

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
                                            <select id="bankacc" class=" form-control " name="branchId">
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

                                        <label class="text-label form-label">Credit Officer *</label>

                                        <select id="cash_acc" class=" form-control " name="authorized_by_id">
                                            <option value="">All </option>
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

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select class=" form-control" name="loan_product_id" id="osector">
                                            <option selected="" value="">All</option>
                                            <?php
                                            foreach ($lps as $row) { ?>
                                                <option value="<?= $row['id'] ?>" id="<?= $row['id'] ?>" <?= @$_REQUEST['loan_product_id'] == $row['id'] ? "selected" : "" ?>>
                                                    <?= $row['name'] . ' - ' . $row['rate'] . ' - ' . $row['method'] ?>
                                                </option>
                                                ';
                                            <?php }
                                            ?>
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

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch Entries</button>
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

                            <?php
                            if (@$_REQUEST['transaction_start_date'] && @$_REQUEST['transaction_end_date']) :
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
                            endif
                            ?>
                        </table>

                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Disbursement Date</th>
                                    <th>Loan Amount</th>
                                    <th>Interest Rate(/Annum)</th>
                                    <th>A/C No.</th>
                                    <th>Client Names</th>
                                    <th>Loan Product</th>
                                    <th>Mode of Disbursement</th>
                                    <th>Authorized by</th>
                                    <th>Branch</th>
                                </tr>


                            </thead>
                            <tbody>
                                <?php
                                $total_dibs = 0;
                                $count = 0;
                                foreach ($records as $record) {
                                ?>
                                    <tr>
                                        <td> <?= ++$count ?> </td>
                                        <td> <?= normal_date_short(@$record['trxn_date']) ?> </td>
                                        <td><a href="loan_details_page.php?id=<?= @$record['loan_no']?>"> <?= number_format(@$record['principal']) ?></a> </td>
                                        <td> <?= @$record['monthly_interest_rate'] ?>% </td>

                                        <td> <?= @$record['membership_no'] == 0 ? $record['client_id'] : @$record['membership_no']; ?> </td>
                                        <td> <?= @$record['client_names'] ?> </td>
                                        <td> <?= @$record['type_name'] ?> </td>
                                        <td> <?= @$record['pay_method'] ?> </td>
                                        <td> <?= @$record['authorized_by_names'] ?> </td>
                                        <td> <?= @$record['branch_name'] ?> </td>

                                    </tr>
                                <?php

                                    $total_dibs += (int) $record['principal'];
                                } ?>

                                <tr>
                                    <th colspan="9">Total Amount Disbursed </th>
                                    <th> <?= number_format($total_dibs) ?> </th>

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