<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_loan_arrears_report')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'LoanReport', 'ReportModelMethod' => 'getArrearsReport', 'is_loan_arrears' => 1];
$request_data['is_loan_arrears'] = 1;
$request_data = array_merge($request_data, $_REQUEST);
$report_reponse = $ReportService->generateReport($request_data);
$loans = @$report_reponse['data'];
// var_dump($response);
// exit;
$response = new Response();
$lps = $response->getAllBankLoanProducts($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);

$branches = $response->getBankBranches($user[0]['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);

// var_dump($members);
// exit;

$loan_product = '';
if (@$_REQUEST['loan_product_id']) {
    $key = array_search($_REQUEST['loan_product_id'], array_column($lps, 'id'));
    $loan_product = $lps[$key]['name'] . '  - ' . $lps[$key]['rate'];
}

$staff_names = '';
if (@$_REQUEST['loan_officer_id']) {
    $key = array_search($_REQUEST['loan_officer_id'], array_column($staff, 'id'));
    $staff_names = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}

$loan_status = '';

if (@$_REQUEST['loan_status'] == 2) {
    $loan_status = 'Active - On Time';
} else if (@$_REQUEST['loan_status'] == 3) {
    $loan_status = 'Active - Late';
} else if (@$_REQUEST['loan_status'] == 4) {
    $loan_status = 'Active - Overdue';
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" id="branchselect" name="branchId">
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

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Status *</label>

                                        <select name="loan_status" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All</option>
                                            <option value="2" <?= @$_REQUEST['loan_status'] == 2 ? "selected" : "" ?>>Active - On Time</option>
                                            <option value="3" <?= @$_REQUEST['loan_status'] == 3 ? "selected" : "" ?>>Active - Late</option>
                                            <option value="4" <?= @$_REQUEST['loan_status'] == 4 ? "selected" : "" ?>>Active - Overdue</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select class="me-sm-2 default-select form-control wide" name="loan_product_id" style="display: none;">
                                            <option selected="" value="">All</option>
                                            <?php
                                            foreach ($lps as $row) { ?>
                                                <option value="<?= $row['id'] ?>" id="<?= $row['frequency'] ?>" <?= @$_REQUEST['loan_product_id'] == $row['id'] ? "selected" : "" ?>>
                                                    <?= $row['name'] . '  - ' . $row['rate'] ?>
                                                </option>
                                                ';
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Credit Officer *</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="loan_officer_id">
                                            <option value=""> All </option>
                                            <?php
                                            if ($staffs !== '') {
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

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Days in Arrears *</label>

                                        <select id="days_arr" class=" form-control" name="days_arrears">
                                            <option value="1"> All </option>
                                            <option value="30"> 30 & above </option>
                                            <option value="90"> 90 & above </option>
                                            <option value="180"> 180 & above </option>


                                        </select>
                                    </div>
                                </div>

                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Disbursement Start
                                            Date *</label>
                                        <input type="date" name="disbursement_start_date" class="form-control" name="from_date" value="<?= @$_REQUEST['disbursement_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Disbursement End
                                            Date *</label>
                                        <input type="date" class="form-control" name="disbursement_end_date" value="<?= @$_REQUEST['disbursement_end_date'] ?>" placeholder="End Date">
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
                            Loan Arrears Report
                        </h4>

                        <?php if (count($loans)) :
                            $request_string = 'staffName=' . @$staff_name . '&loanProduct=' . @$loan_product . '&loanStatus=' . @$loan_status;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>
                                <!-- <a href="#" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </a> -->
                                <a href="export_report.php?exportFile=report_loan_arrears&<?= $request_string ?>&orientation=landscape" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> Loan Arrears Report: </strong> </td>
                            </tr>
                        </table>


                        <table>
                            <?php if (@$_REQUEST['loan_product_id']) : ?>
                                <tr>
                                    <td width="18%"> Loan Product:</td>
                                    <td> <strong> <?= $loan_product; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['loan_officer_id']) : ?>
                                <tr>
                                    <td width="18%"> Credit Officer:</td>
                                    <td> <strong> <?= $staff_names; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['loan_status']) : ?>
                                <tr>
                                    <td width="18%"> Loan Status:</td>
                                    <td> <strong> <?= $loan_status; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['days_arrears']) : ?>
                                <tr>
                                    <td width="18%"> Days in Arrears:</td>
                                    <td> <strong> <?= @$_REQUEST['days_arrears']; ?> & Above </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['disbursement_start_date'] && @$_REQUEST['disbursement_end_date']) : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            From: <strong> <?= normal_date(@$_REQUEST['disbursement_start_date']) ?> </strong>
                                            To: <strong> <?= normal_date(@$_REQUEST['disbursement_end_date']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>

                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th rowspan="2">ID</th>
                                    <th rowspan="2">A/C N0</th>
                                    <th rowspan="2">Client Names</th>
                                    <th rowspan="2">Savings Balance</th>
                                    <th rowspan="2">Disbursement Date</th>
                                    <th rowspan="2">Loan Amount</th>
                                    <th colspan="3" class="text-center">Total Arrears</th>
                                    <th rowspan="2">Days in Arrears</th>
                                    <th rowspan="2">Penalty</th>
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
                                $total_penalty = 0;
                                foreach ($loans as $loan) {
                                    $total_arrears = @$loan['principal_arrears'] + @$loan['interest_arrears'];
                                ?>
                                    <tr>
                                        <td> <?= @$loan['loan_no'] ?> </td>
                                        <td> <?= in_array(@$loan['membership_no'], [0, null]) ? '-' : @$loan['membership_no']; ?> </td>
                                        <td> <?= @$loan['client_initials'] ?> </td>
                                        <td> <?= number_format(@$loan['acc_balance']) ?> </td>
                                        <td> <?= normal_date_short(@$loan['date_disbursed']) ?> </td>
                                        <td> <?= number_format(@$loan['principal']) ?> </td>
                                        <td> <?= number_format(@$loan['principal_arrears']) ?> </td>
                                        <td> <?= number_format(@$loan['interest_arrears']) ?> </td>
                                        <td>
                                            <?= number_format(@$total_arrears) ?>
                                        </td>
                                        <td class="text-center">
                                            <?= days_in_arrears(@$loan) ?>
                                        </td>
                                        <td> <?= number_format(@$loan['penalty_balance']) ?> </td>
                                    </tr>
                                <?php
                                    $total_loans_amount += (int)(@$loan['principal']);
                                    $total_principal_arrears += (int)(@$loan['principal_arrears']);
                                    $total_interest_arrears += (int)(@$loan['interest_arrears']);
                                    $total_penalty += (int)(@$loan['penalty_balance']);
                                    $total_arrears_absolute += (int)@$total_arrears;
                                } ?>

                                <tr>
                                    <th colspan="5">Totals </th>
                                    <th> <?= number_format($total_loans_amount) ?> </th>
                                    <th> <?= number_format($total_principal_arrears) ?> </th>
                                    <th> <?= number_format($total_interest_arrears) ?> </th>
                                    <th> <?= number_format($total_arrears_absolute) ?> </th>
                                    <th></th>
                                    <th> <?= number_format($total_penalty) ?> </th>
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