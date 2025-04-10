<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_credit_officers_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'CREDIT OFFICER REPORT';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'LoanReport', 'ReportModelMethod' => 'getCreditOfficersReport'];
$request_data['is_credit_officers_report'] = true;
$request_data = array_merge($request_data, $_REQUEST);
$_REQUEST['disbursement_start_date'] = @$_REQUEST['disbursement_start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$_REQUEST['disbursement_end_date'] = @$_REQUEST['disbursement_end_date'] ??  date('Y-m-d');

$_REQUEST['payment_from'] = @$_REQUEST['payment_from'] ?? date('Y-m-d', strtotime('-30 days'));
$_REQUEST['payment_to'] = @$_REQUEST['payment_to'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport($request_data);
$loans = @$report_reponse['data'] ?? [];
// var_dump($response);
// exit;
$response = new Response();
// $lps = $response->getAllBankLoanProducts($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);
$lps = $response->getAllBankLoanProducts(@$user[0]['bankId'], @$user[0]['branchId']);
$regions = $response->getClientRegions(@$user[0]['bankId'], @$user[0]['branchId']);
$districts = $response->getClientDistricts(@$user[0]['bankId'], @$user[0]['branchId']);
$parishes = $response->getClientParishes(@$user[0]['bankId'], @$user[0]['branchId']);
$villages = $response->getClientVillages(@$user[0]['bankId'], @$user[0]['branchId']);
$staff = $response->getBankStaff2($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);

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

<!-- DataTables core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- Buttons extension CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

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

                                        <label class="text-label form-label">Loan Status *</label>

                                        <select name="loan_status" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="0">All</option>
                                            <option value="active" <?= @$_REQUEST['loan_status'] == 'active' || @$_REQUEST['loan_status'] == '' ? "selected" : "" ?>>Active</option>
                                            <option value="2" <?= @$_REQUEST['loan_status'] == 2 ? "selected" : "" ?>>Active - On Time</option>
                                            <option value="3" <?= @$_REQUEST['loan_status'] == 3 ? "selected" : "" ?>>Active - Due</option>
                                            <option value="4" <?= @$_REQUEST['loan_status'] == 4 ? "selected" : "" ?>>Active - Overdue</option>
                                            <option value="5" <?= @$_REQUEST['loan_status'] == 5 ? "selected" : "" ?>>Cleared</option>
                                            <option value="6" <?= @$_REQUEST['loan_status'] == 6 ? "selected" : "" ?>>Written Off</option>


                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select class="me-sm-2 default-select form-control wide" name="loan_product_id" id="osector">
                                            <option selected="0" value="">All</option>
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

                                        <label class="text-label form-label">Credit Officer*</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="loan_officer_id">
                                            <option value="0"> All </option>
                                            <?php
                                            if ($staff !== '') {
                                                foreach ($staff as $row) { ?>
                                                    <option value="<?= $row['id'] ?>">
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
                                        <label class="text-label form-label" for="exampleInputEmail3">Disbursement Start
                                            Date *</label>
                                        <input type="date" name="disbursement_start_date" class="form-control" name="disbursement_start_date" value="<?= @$_REQUEST['disbursement_start_date'] ?>" placeholder="Start Date">
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

                                        <label class="text-label form-label">District *</label>

                                        <select name="district" class="me-sm-2 default-select form-control wide" id="district">
                                            <?php
                                            if ($districts != '') {
                                                foreach ($districts as $row) {
                                                    $is_selected = @$_REQUEST['district'] == $row['name'] ? "selected" : "";
                                            ?>
                                                    <option value="<?= @$row['name'] ?>" <?= $is_selected ?>>
                                                        <?= strtoupper($row['name']) ?>
                                                    </option>
                                            <?php }
                                            } ?>


                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Parish *</label>

                                        <select name="parish" class="me-sm-2 default-select form-control wide" id="sprod">
                                            <?php
                                            if ($parishes !== '') {
                                                foreach ($parishes as $row) {
                                                    $is_selected = @$_REQUEST['parish'] == $row['name'] ? "selected" : "";
                                            ?>
                                                    <option value="<?= @$row['name'] ?>" <?= $is_selected ?>>
                                                        <?= strtoupper($row['name']) ?>
                                                    </option>
                                            <?php }
                                            } ?>


                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Sub-County *</label>

                                        <select name="region" class=" form-control wide" id="credit_account">
                                            <option value="" selected>All </option>

                                            <?php
                                            if ($regions !== '') {
                                                foreach ($regions as $row) {
                                                    $is_selected = @$_REQUEST['region'] == $row['name'] ? "selected" : "";
                                            ?>
                                                    <option value="<?= @$row['name'] ?>" <?= $is_selected ?>>
                                                        <?= strtoupper($row['name']) ?>
                                                    </option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Village *</label>

                                        <select name="village" class="me-sm-2 default-select form-control wide" id="village">
                                            <?php
                                            if ($villages !== '') {
                                                foreach ($villages as $row) {
                                                    $is_selected = @$_REQUEST['village'] == $row['name'] ? "selected" : "";
                                            ?>
                                                    <option value="<?= @$row['name'] ?>" <?= $is_selected ?>>
                                                        <?= strtoupper($row['name']) ?>
                                                    </option>
                                            <?php }
                                            } ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="">Amount Paid from *</label>
                                        <input type="date" class="form-control" name="payment_from" value="<?= @$_REQUEST['payment_from'] ?? date('Y-m-d', strtotime('-30 days')) ?>" placeholder="Start Date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="">Amount Paid to *</label>
                                        <input type="date" class="form-control" name="payment_to" value="<?= @$_REQUEST['payment_to'] ?? date('Y-m-d') ?>" placeholder="End Date">
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
                            Credit Officers Report
                        </h4>

                        <?php if (count($loans)) :
                            $request_string = 'staffName=' . @$staff_name . '&loanProduct=' . @$loan_product . '&loanStatus=' . @$loan_status;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>

                                <a href="export_report.php?exportFile=report_credit_officers&<?= $request_string ?>&orientation=landscape" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>

                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> Credit Officers Report: </strong> </td>
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
                            <?php if (@$_REQUEST['region']) : ?>
                                <tr>
                                    <td width="18%"> Client Region:</td>
                                    <td> <strong> <?= strtoupper(@$_REQUEST['district'] . ', ' . @$_REQUEST['region'] . ', ' . @$_REQUEST['parish'] . ', ' . @$_REQUEST['village']); ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['disbursement_start_date'] && @$_REQUEST['disbursement_end_date']) : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            Loans Disbursed From: <strong> <?= normal_date($_REQUEST['disbursement_start_date']) ?> </strong>
                                            To: <strong> <?= normal_date($_REQUEST['disbursement_end_date']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            <?php if (@$_REQUEST['payment_from'] && @$_REQUEST['payment_to']) : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            Amount Paid From: <strong> <?= normal_date($_REQUEST['payment_from']) ?> </strong>
                                            To: <strong> <?= normal_date($_REQUEST['payment_to']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                            <tr>
                                <td colspan="2">
                                    <div>
                                        Report Generated On: <strong> <?= normal_date(date('Y-m-d')) ?> </strong>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div>
                                        Report Generated by: <strong> <?= strtoupper($user[0]['firstName'] . ' ' . $user[0]['lastName']) ?> </strong>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                    <div class="card-body">
                        <div class="table-responsive">

                            <table id="dep" class="table table-striped" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Loan No.</th>
                                        <th rowspan="2">A/C No.</th>
                                        <th rowspan="2">Names</th>
                                        <th rowspan="2">Physical Address</th>
                                        <th rowspan="2">Guarantors</th>
                                        <th rowspan="2">Has Collateral</th>
                                        <th rowspan="2">Disbursement Date</th>
                                        <th rowspan="2">Duration</th>
                                        <th rowspan="2">Int. Rate</th>
                                        <th rowspan="2">Loan Amount</th>

                                        <th colspan="3">Amount Paid</th>
                                        <th colspan="3">Amount Paid (Filtered)</th>
                                        <th colspan="4">Amount Due</th>
                                        <th colspan="3">Out. Balance</th>
                                        <th colspan="3">Amount in Arrears</th>
                                        <th rowspan="2">Days in Arrears</th>
                                    </tr>
                                    <tr>
                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>

                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>

                                        <th> Due Date </th>
                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>

                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>

                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 0;
                                    $total_loans_amount = 0;
                                    $total_principal_due = 0;
                                    $total_interest_due = 0;
                                    $total_penalty_due = 0;

                                    $total_amount_paid = 0;
                                    $total_princ_paid = 0;
                                    $total_int_paid = 0;
                                    $total_princ_paid_month = 0;
                                    $total_int_paid_month = 0;
                                    $total_princ_bal = 0;
                                    $total_int_bal = 0;

                                    $total_outstanding_amount = 0;
                                    $total_principal_arrears = 0;
                                    $total_interest_arrears = 0;
                                    foreach ($loans as $loan) {
                                        $count = $count + 1;
                                        $outstanding_amount = @$loan['principal_balance'] + @$loan['interest_balance'] + @$loan['penalty_balance'];
                                        $ftype = '';

                                        if ($loan['repay_cycle_id'] == 1) {
                                            $ftype = 'DAYS';
                                        } else if ($loan['repay_cycle_id'] == 2) {
                                            $ftype = 'WEEKS';
                                        } else if ($loan['repay_cycle_id'] == 3) {
                                            $ftype = 'MONTHS';
                                        } else if ($loan['repay_cycle_id'] == 4) {
                                            $ftype = 'DAYS';
                                        } else if ($loan['repay_cycle_id'] == 5) {
                                            $ftype = 'YEARS';
                                        }
                                    ?>
                                        <tr data-lno="<?= @$loan['loan_no'] ?>" id="row-<?= @$loan['loan_no'] ?>">
                                            <td> <?= @$loan['loan_no'] ?> </td>
                                            <td> <?= in_array(@$loan['membership_no'], [0, null]) ? '-' : @$loan['membership_no']; ?> </td>
                                            <td> <?= @$loan['client_initials'] ?> </td>
                                            <td> <?= @$loan['client_address'] ?> </td>
                                            <td>
                                                <?php foreach (@$loan['guarantors'] as $guarantor) : ?>
                                                    <?= $guarantor['guarantor_initials'] ? $guarantor['guarantor_initials'] . ' , <br>' : '' ?>
                                                <?php endforeach ?>
                                            </td>
                                            <td id="has_collateral">-</td>
                                            <td> <?= normal_date_short(@$loan['date_disbursed']) ?> </td>
                                            <td> <?= @$loan['approved_loan_duration'] . ' ' . $ftype ?> </td>
                                            <td> <?= @$loan['monthly_interest_rate']  ?>% </td>
                                            <td><a href="loan_details_page.php?id=<?= @$loan['loan_no'] ?>"> <?= number_format(@$loan['principal'] ?? 0) ?></a> </td>


                                            <td> <?= number_format((@$loan['principal_paid'] ?? 0)) ?> </td>
                                            <td> <?= number_format((@$loan['interest_paid'] ?? 0)) ?> </td>
                                            <td> <?= number_format((@$loan['principal_paid'] ?? 0) + (@$loan['interest_paid'] ?? 0)) ?> </td>

                                            <td id="princ_month"> -</td>
                                            <td id="int_month"> -</td>
                                            <td id="month_tot">- </td>

                                            <td><?= normal_date_short(@$loan['next_due_date']) ?></td>
                                            <td> <?= number_format((@$loan['principal_due'] ?? 0)) ?> </td>
                                            <td> <?= number_format((@$loan['interest_due'] ?? 0)) ?> </td>
                                            <td> <?= number_format((@$loan['principal_due'] ?? 0) + (@$loan['interest_due'] ?? 0) + (@$loan['penalty_balance'] ?? 0)) ?> </td>

                                            <td> <?= number_format(@$loan['principal_balance'] ?? 0) ?> </td>
                                            <td> <?= number_format(@$loan['interest_balance'] ?? 0) ?> </td>
                                            <td> <?= number_format((@$loan['principal_balance'] ?? 0) + (@$loan['interest_balance'] ?? 0) + (@$loan['penalty_balance'] ?? 0)) ?> </td>


                                            <td> <?= number_format((@$loan['principal_arrears'] ?? 0)) ?> </td>
                                            <td> <?= number_format((@$loan['interest_arrears'] ?? 0)) ?> </td>
                                            <td> <?= number_format((@$loan['principal_arrears'] ?? 0) + (@$loan['interest_arrears'] ?? 0)) ?> </td>
                                            <td class="text-center">
                                                <?= days_between_dates(@$loan['arrearsbegindate'], date('Y-m-d')) ?>
                                            </td>
                                        </tr>
                                    <?php
                                        $total_loans_amount += (int)(@$loan['principal']);
                                        $total_principal_due += (int)(@$loan['principal_due']);
                                        $total_interest_due += (int)(@$loan['interest_due']);
                                        $total_penalty_due += (int)(@$loan['penalty_balance']);

                                        $total_amount_paid += (int)(@$loan['amount_paid']);

                                        $total_princ_paid += (int)(@$loan['principal_paid']);
                                        $total_int_paid += (int)(@$loan['interest_paid']);

                                        $total_princ_bal += (int)(@$loan['principal_balance']);
                                        $total_int_bal += (int)(@$loan['interest_balance']);

                                        $total_outstanding_amount += (int) @$outstanding_amount;
                                        $total_principal_arrears += (int)(@$loan['principal_arrears']);
                                        $total_interest_arrears += (int)(@$loan['interest_arrears']);
                                    }
                                    if ($total_outstanding_amount) {
                                        $rec_rate = $total_amount_paid / ($total_outstanding_amount);
                                    } else {
                                        $rec_rate = $total_amount_paid;
                                    }


                                    $rec_rate = ($rec_rate * 100);

                                    ?>

                                    <tr class="datatable-totals" style="background: #cccccc !important">

                                        <th colspan="9">Totals ( for <?= number_format($count) ?> Loans) </th>
                                        <th> <?= number_format($total_loans_amount) ?> </th>
                                        <th> <?= number_format($total_princ_paid) ?> </th>
                                        <th> <?= number_format($total_int_paid) ?> </th>
                                        <th> <?= number_format($total_princ_paid + $total_int_paid) ?> </th>
                                        <th id="princ_month_tot">- </th>
                                        <th id="int_month_tot">- </th>
                                        <th id="tot_month_tot">- </th>
                                        <th></th>
                                        <th> <?= number_format($total_principal_due) ?> </th>
                                        <th> <?= number_format($total_interest_due) ?> </th>
                                        <th> <?= number_format($total_principal_due + $total_interest_due + $total_penalty_due) ?> </th>

                                        <th> <?= number_format($total_princ_bal) ?> </th>
                                        <th> <?= number_format($total_int_bal) ?> </th>
                                        <th> <?= number_format($total_princ_bal + $total_int_bal) ?> </th>

                                        <th> <?= number_format($total_principal_arrears) ?> </th>
                                        <th> <?= number_format($total_interest_arrears) ?> </th>
                                        <th> <?= number_format($total_principal_arrears + $total_interest_arrears) ?> </th>
                                        <th>-</th>
                                    </tr>

                                    <tr class="datatable-totals" style="background: #f5be7f !important">

                                        <th colspan="26">Recovery Rate </th>

                                        <th> <?= ($total_principal_due + $total_interest_due + $total_penalty_due) > 0 ? number_format(@$rec_rate ?? 0) : 0 ?> %</th>

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
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <!-- DataTables core JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <!-- Buttons extension JS -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <!-- JSZip for Excel export -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <!-- HTML5 buttons -->
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script>
            let total_princ_paid = 0;
            let total_int_paid = 0;
            let total_amount_paid = 0;

            $('tr[data-lno]').each(function() {
                var lno = $(this).data('lno');
                // console.log(`Loan No:${lno}`);
                $.ajax({
                    url: 'https://app.ucscucbs.net/backend/api/Bank/fetch_amount_paid_month.php',
                    method: 'GET',
                    data: {
                        id: lno,
                        from_date: '<?= @$_REQUEST['payment_from'] ?? date('Y-m-d', strtotime('-30 days')) ?>',
                        to_date: '<?= @$_REQUEST['payment_to'] ?? date('Y-m-d') ?>'
                    },
                    success: function(response) {
                        // console.log(`Response for Loan ${lno}:`, response);
                        if (response.princ_month) {
                            $('#row-' + lno + ' #princ_month').text(response.princ_month);
                            $('#row-' + lno + ' #int_month').text(response.int_month);
                            $('#row-' + lno + ' #month_tot').text(response.tot_month);
                            $('#row-' + lno + ' #has_collateral').text(response.has_collateral);

                            // Parse the values and remove commas
                            const princ_month = parseFloat(response.princ_month.replace(/,/g, '') || 0);
                            const int_month = parseFloat(response.int_month.replace(/,/g, '') || 0);
                            const tot_month = parseFloat(response.tot_month.replace(/,/g, '') || 0);

                            // Update the totals
                            total_princ_paid += princ_month;
                            total_int_paid += int_month;
                            total_amount_paid += tot_month;

                            $('#princ_month_tot').text(total_princ_paid.toLocaleString());
                            $('#int_month_tot').text(total_int_paid.toLocaleString());
                            $('#tot_month_tot').text(total_amount_paid.toLocaleString());


                        } else {
                            console.warn(`No valid data for Loan ${lno}`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(`AJAX Error for Loan ${lno}:`, error, xhr.responseText);
                    }
                });
            });
            $(document).ready(function() {
                if ($('#dep tbody tr').length > 0) {
                    $('#dep').DataTable({
                        destroy: true,
                        dom: 'Bfrtip', // Ensures buttons are visible
                        buttons: [
                            'excelHtml5' // Adds the export-to-Excel button
                        ],
                        pageLength: 10,
                        paging: true,
                        searching: true,
                        info: true,
                        lengthChange: true,
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, "All"]
                        ],
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                            }
                        }
                    });

                } else {
                    console.warn("No rows to initialize DataTable.");
                }
            });
        </script>
</body>

</html>