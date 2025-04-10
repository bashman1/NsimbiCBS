<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_journal_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'Paybales Report';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'ClientReport', 'ReportModelMethod' => 'getReceivablesReport'];

if (@$_REQUEST['filtered']) {
    $request_data['branchId'] = @$_REQUEST['branchId'];
    $request_data['start_date'] = @$_REQUEST['start_date'];
    $request_data['end_date'] = @$_REQUEST['end_date'];
}
$_REQUEST['start_date'] = @$_REQUEST['start_date'] ?? date('Y-m-d');
$_REQUEST['end_date'] = @$_REQUEST['end_date'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport(@$request_data);
$members = @$report_reponse['data'];
// var_dump($report_reponse);
// exit;
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);

// var_dump($members);
// exit;

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search(@$_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}


?>
<?php
$title = 'PAYABLES REPORT';
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




                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>


                        <form class="ajax_results_form" method="GET">
                            <input type="hidden" name="filtered" value="1">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if (@$_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= @$_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branchId'] ? "selected" : "";

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


                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">From *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= @$_REQUEST['start_date'] ?? date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">To *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?? date('Y-m-d'); ?>" id="exampleInputEmail4" placeholder="End Date">
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
                            Payables Report
                        </h4>

                        <?php if (count($members)) :
                            $request_string = 'branchName=' . @$branch_name;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>

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
                                <td> <strong> Payables Report: </strong> </td>
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
                                            From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                                            To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>

                        <table class="report_table">
                            <thead>
                                <th>#</th>
                                <th>Creditor</th>
                                <th>Chart Account</th>
                                <th>Branch</th>
                                <th>Date Created</th>
                                <th>Maturity Date</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Amount Paid</th>
                                <th class="text-center">Balance</th>

                            </thead>
                            <tbody>

                                <?php
                                $total_amount = 0;
                                $total_paid = 0;
                                $total_bal = 0;
                                $tot_count = 0;
                                $status = '';
                                foreach ($members as $member) {
                                    ++$tot_count;

                                    if (@$member['pay_status'] == 0) {
                                        $status = '<span class="text-danger">Pending</span>';
                                    }
                                    if (@$member['pay_status'] == 1) {
                                        $status = '<span class="text-success">Cleared</span>';
                                    }
                                    if (@$member['pay_status'] == 2) {
                                        $status = '<span class="text-warning">Partially</span>';
                                    }

                                ?>
                                    <tr>
                                        <td> <?= $tot_count ?> </td>
                                        <td> <?= @$member['deb_name'] ?> </td>
                                        <td> <?= @$member['cname'] ?> </td>
                                        <td> <?= @$member['bname'] ?> </td>
                                        <td> <?= normal_date_short(@$member['pay_trxn_date']) ?> </td>
                                        <td> <?= normal_date_short(@$member['maturity_date']) ?> </td>
                                        <td> <?= @$member['p_descri'] ?> </td>
                                        <td> <?= @$status ?> </td>

                                        <td> <?= number_format(@$member['p_amount']) ?> </td>
                                        <td> <?= number_format(@$member['p_amount_paid']) ?> </td>
                                        <td> <?= number_format(@$member['p_amount'] - @$member['p_amount_paid']) ?> </td>


                                    </tr>
                                <?php

                                    $total_amount += @$member['p_amount'];
                                    $total_paid += @$member['p_amount_paid'];
                                    $total_bal += (@$member['p_amount'] - @$member['p_amount_paid']);
                                } ?>
                                <tr>
                                    <th colspan="10">Total Payable Amount</th>
                                    <th class="text-center"> <?= number_format($total_amount) ?> </th>
                                </tr>
                                <tr>
                                    <th colspan="10">Total Amount Paid</th>
                                    <th class="text-center"> <?= number_format($total_paid) ?> </th>
                                </tr>
                                <tr>
                                    <th colspan="10">Total Balance</th>
                                    <th class="text-center"> <?= number_format($total_bal) ?> </th>
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