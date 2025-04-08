<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_client_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'Fixed Deposits Report';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'ClientReport', 'ReportModelMethod' => 'FixedDepositScheduleReport', 'is_client_type' => 2];

if (@$_REQUEST['filtered']) {
    $request_data['branch'] = $_REQUEST['branch'];
    $request_data['start_date'] = $_REQUEST['start_date'];
    $request_data['end_date'] = $_REQUEST['end_date'];
}
$_REQUEST['start_date'] = $_REQUEST['start_date'] ?? null;
$_REQUEST['end_date'] = $_REQUEST['end_date'] ?? null;
// $_REQUEST['client_type'] = 2;
$report_reponse = $ReportService->generateReport($request_data);
$members = @$report_reponse['data'];
// var_dump($response);
// exit;
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);

// var_dump($members);
// exit;

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search($_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}



?>
<?php
$title = 'FIXED DEPOSIT REPORT';
require_once('includes/head_tag.php');
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Maturity Date (From) *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= @$_REQUEST['start_date'] ?? null; ?>" id="exampleInputEmail3" placeholder="Deposit Date">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Maturity Date (To) *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?? null; ?>" id="exampleInputEmail4" placeholder="End Date">
                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
                                <div class="col-md-6">
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
                            Due Fixed Deposits Report
                        </h4>

                        <?php if (count($members)) :
                            $request_string = 'branchName=' . @$branch_name;
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
                                <td> <strong> Due Fixed Deposits Report: </strong> </td>
                            </tr>
                        </table>


                        <table>
                            <?php if (@$_REQUEST['branchId']) : ?>
                                <tr>
                                    <td width="18%"> Branch:</td>
                                    <td> <strong> <?= @$branch_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>


                            <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            Maturity Date From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                                            To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>

                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>A/C N0 & Name</th>
                                    <th>Amount</th>
                                    <th>Interest Rate(%)</th>
                                    <th>WHT(%)</th>
                                    <th>Period</th>
                                    <th>Compounding Frequency</th>
                                    <th>Deposit Date</th>
                                    <th>Status</th>
                                    <th>Maturity Date</th>
                                    <th class="text-center" colspan="4">Amount Paid</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Principal</th>
                                    <th>Interest</th>
                                    <th>WHT</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $total_amount = 0;
                                $total_princ = 0;
                                $total_wht = 0;
                                $total_int = 0;
                                $count = 0;
                                foreach ($members as $member) {

                                    $mno = $member['membership_no'] == 0 ? '' :  $member['membership_no'];
                                    $dtype = '';
                                    $dtype1 = '';
                                    if ($member['duration_type'] == 'y') {
                                        $dtype = 'Years';
                                    } else if ($member['duration_type'] == 'm') {
                                        $dtype = 'Months';
                                    } else if ($member['duration_type'] == 'd') {
                                        $dtype = 'Days';
                                    }


                                    if ($member['compound_freq'] == 'y') {
                                        $dtype1 = 'Annually';
                                    } else if ($member['compound_freq'] == 'm') {
                                        $dtype1 = 'Monthly';
                                    } else if ($member['compound_freq'] == 'q') {
                                        $dtype1 = 'Quarterly';
                                    } else if ($member['compound_freq'] == 'h') {
                                        $dtype1 = 'Half Yearly';
                                    }

                                    $stt = '';
                                    if ($member['fd_status'] == 0) {
                                        $currentDate = strtotime(date('Y-m-d'));
                                        $startDate = strtotime(date('Y-m-d', strtotime($member['fd_maturity_date'])));


                                        if ($startDate <= $currentDate) {
                                            $stt = '<span class="text-danger">Due</span>';
                                        } else {
                                            $stt = '<span class="text-primary">Running</span>';
                                        }
                                    } else {
                                        $stt = '<span class="text-success">Closed</span>';
                                    }
                                    $princ_paid = 0;
                                    if ($member['fd_int_paid'] > 0) {
                                        $princ_paid = $member['fd_amount'];
                                    }


                                ?>
                                    <tr>
                                        <td> <?= ++$count ?> </td>
                                        <td><?= @$mno . ' : ' . @$member['client_names']; ?> </td>
                                        <td> <a href="fixed_deposit_details.php?id=<?= $member['fd_id'] ?>" class="text-primary"><?= number_format($member['fd_amount'] ?? 0); ?> </a></td>
                                        <td> <?= number_format($member['int_rate'] ?? 0); ?> </td>
                                        <td> <?= number_format($member['wht'] ?? 0); ?> </td>
                                        <td> <?= number_format($member['fd_duration'] ?? 0) . ' ' . $dtype ?> </td>
                                        <td> <?= $dtype1 ?> </td>
                                        <td> <?= normal_date_short(@$member['fd_date']) ?> </td>
                                        <td> <?= $stt ?> </td>
                                        <td> <?= normal_date_short(@$member['fd_maturity_date']) ?> </td>
                                        <td class="text-center"> <?= number_format(@$princ_paid) ?> </td>
                                        <td class="text-center"> <?= number_format(@$member['fd_int_paid'] ?? 0) ?> </td>
                                        <td class="text-center"> <?= number_format(@$member['wht_paid'] ?? 0) ?> </td>
                                        <td class="text-center"> <?= number_format((@$member['fd_int_paid'] ?? 0) + (@$member['wht_paid'] ?? 0) + $princ_paid) ?> </td>
                                    </tr>
                                <?php
                                    $total_amount += (int) @$member['fd_amount'];
                                    $total_princ += (int) @$princ_paid;
                                    $total_int += (int) (@$member['fd_int_paid'] ?? 0);
                                    $total_wht += (int) (@$member['wht_paid'] ?? 0);
                                } ?>
                                <tr>
                                    <th colspan="10">Total Amount Fixed</th>
                                    <th class="text-center" colspan="4"> <?= number_format($total_amount) ?> </th>
                                </tr>
                                <tr>
                                    <th colspan="10">Total Principal Paid</th>
                                    <th class="text-center" colspan="4"> <?= number_format($total_princ) ?> </th>
                                </tr>
                                <tr>
                                    <th colspan="10">Total Interest Paid</th>
                                    <th class="text-center" colspan="4"> <?= number_format($total_int) ?> </th>
                                </tr>
                                <tr>
                                    <th colspan="10">Total WHT Collected</th>
                                    <th class="text-center" colspan="4"> <?= number_format($total_wht) ?> </th>
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        if (!count($members)) {
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