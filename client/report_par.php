<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_loan_arrears_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'PORTIFOLIO AT RISK';
?>
<?php

require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'LoanReport', 'ReportModelMethod' => 'getPARReport', 'is_loan_arrears' => 0];
$request_data['is_loan_arrears'] = 1;
$request_data = array_merge($request_data, $_REQUEST);
$report_reponse = $ReportService->generateReport($request_data);
$loans = @$report_reponse['data'];
// var_dump($response);
// exit;
$response = new Response();
$branches = $response->getBankBranches(@$user[0]['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search(@$_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}

$staff_names = '';
if (@$_REQUEST['loan_officer_id']) {
    $key = array_search($_REQUEST['loan_officer_id'], array_column($staff, 'id'));
    $staff_names = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
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

                        <form class="ajax_results_form" method="get">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if (@$_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= @$_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
                                                <option value="0"> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branchId'] ? "selected" : "";

                                                if (@$user[0]['branchId']) { ?>
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



                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="text-label form-label">Credit Officer *</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="loan_officer_id">
                                            <option value="0"> All </option>
                                            <?php
                                            if (@$staff !== '') {
                                                foreach ($staff as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['loan_officer_id'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php }
                                            } else { ?>
                                                <option readonly>No Staff Added yet</option>
                                            <?php } ?>
                                        </select>
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
                            Portifolio At Risk Report
                        </h4>

                        <?php if (count($loans)) :
                            $request_string = 'branchName=' . @$branch_name . 'staffName=' . @$staff_name;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>
                                <!-- <a href="#" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </a> -->
                                <a href="export_report.php?exportFile=report_loan_par&<?= $request_string ?>&orientation=landscape" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> Portifolio At Risk Report: </strong> </td>
                            </tr>
                        </table>


                        <table>

                            <?php if (@$_REQUEST['branchId']) : ?>
                                <tr>
                                    <td width="18%"> Branch:</td>
                                    <td> <strong> <?= @$branch_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['loan_officer_id']) : ?>
                                <tr>
                                    <td width="18%"> Credit Officer:</td>
                                    <td> <strong> <?= @$staff_names; ?> </strong> </td>
                                </tr>
                            <?php endif ?>


                        </table>

                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Loan Product</th>
                                    <th rowspan="2">Loan Amount</th>
                                    <th rowspan="2">Principal Outstanding</th>
                                    <!-- <th rowspan="2">(PAR)</th> -->
                                    <th colspan="3" class="text-center">Amount in Arrears</th>
                                    <th rowspan="2">PAR(%)</th>
                                    <!-- <th rowspan="2">Risk(%)</th> -->
                                </tr>

                                <tr>
                                    <th> Principal </th>
                                    <th>Interest</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_loans_amount = 0;
                                $total_principal_arrears = 0;
                                $total_interest_arrears = 0;
                                $total_arrears_absolute = 0;
                                $total_princ = 0;
                                foreach ($loans as $loan) {
                                    $total_arrears = (@$loan['principal_arrears'] + @$loan['interest_arrears']) ?? 0;
                                ?>
                                    <tr>
                                        <td> <?= @$loan['type_id'] ?> </td>
                                        <td> <?= @$loan['type_name'] . ' ( ' . @$loan['num_loans'] . ' )'; ?> </td>
                                        <td> <?= number_format(@$loan['loan_amount'] ?? 0) ?> </td>
                                        <td> <?= number_format(@$loan['principal_balance'] ?? 0) ?> </td>

                                        <td> <?= number_format(@$loan['principal_arrears'] ?? 0) ?> </td>
                                        <td> <?= number_format(@$loan['interest_arrears'] ?? 0) ?> </td>
                                        <td>
                                            <?= number_format(@$total_arrears) ?>
                                        </td>

                                        <td class="text-center"> <?= number_format((float)((($loan['principal_arrears'] ?? 0) / ($loan['principal_balance'] ?? 1)) * 100), 2, '.', '') ?>% </td>
                                        <!-- <td class="text-center"><?= number_format((float)((($loan['principal_arrears'] ?? 0) / ($loan['loan_amount'] ?? 1)) * 100), 2, '.', '') ?>% </td> -->
                                    </tr>
                                <?php
                                    $total_loans_amount += (int)(@$loan['loan_amount']);
                                    $total_principal_arrears += (int)(@$loan['principal_arrears']);
                                    $total_interest_arrears += (int)(@$loan['interest_arrears']);
                                    $total_princ += (int)(@$loan['principal_balance']);
                                    $total_arrears_absolute += (int)@$total_arrears;
                                } ?>

                                <tr>
                                    <th colspan="2">Grand Totals </th>
                                    <th> <?= number_format($total_loans_amount) ?> </th>
                                    <th> <?= number_format($total_princ) ?> </th>
                                    <!-- <th> <?= number_format($total_princ) ?> </th> -->
                                    <th> <?= number_format($total_principal_arrears) ?> </th>
                                    <th> <?= number_format($total_interest_arrears) ?> </th>

                                    <th> <?= number_format($total_arrears_absolute) ?> </th>
                                    <th class="text-danger"> <?= @$total_princ > 0 ? number_format((float)((($total_principal_arrears ?? 0) / ($total_princ ?? 1)) * 100), 2, '.', '') : number_format((float)((($total_principal_arrears ?? 0) / (1)) * 100), 2, '.', '') ?>% </th>
                                 
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        if (!count($loans)) {
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