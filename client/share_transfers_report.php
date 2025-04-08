<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_client_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'Share Transfers';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'ClientReport', 'ReportModelMethod' => 'getShareTransfersReport'];

if (@$_REQUEST['filtered']) {
    $request_data['branchId'] = @$_REQUEST['branchId'];
    $request_data['gender'] = @$_REQUEST['gender'];
    $request_data['region'] = @$_REQUEST['region'];
    $request_data['district'] = @$_REQUEST['district'];
    $request_data['parish'] = @$_REQUEST['parish'];
    $request_data['village'] = @$_REQUEST['village'];
    $request_data['actype'] = @$_REQUEST['actype'];
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

$account_type_name = '';
if (@$_REQUEST['actype']) {
    $key = array_search(@$_REQUEST['actype'], array_column($actypes, 'id'));
    $account_type_name = $actypes[$key]['ucode'] . '-' . $actypes[$key]['name'];
}

?>
<?php
$title = 'SHARE TRANSFERS REPORT';
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



                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Gender *</label>

                                        <select name="gender" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All</option>
                                            <option value="Male" <?= @$_REQUEST['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                                            <option value="Female" <?= @$_REQUEST['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Age Group *</label>

                                        <select name="age_group" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="">All</option>
                                            <option value="17" <?= @$_REQUEST['age_group'] == "17" ? "selected" : "" ?>>0 to 17 Yrs</option>
                                            <option value="18+" <?= @$_REQUEST['age_group'] == "18+" ? "selected" : "" ?>>18 to 35 Yrs</option>
                                            <option value="35+" <?= @$_REQUEST['age_group'] == "35+" ? "selected" : "" ?>>Above 35 Yrs</option>
                                        </select>
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
                            Share Transfers Report
                        </h4>

                        <?php if (count($members)) :
                            $request_string = 'branchName=' . @$branch_name . '&accountName=' . @$account_type_name;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>
                                <!-- <a href="#" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </a> -->
                                <!-- <a href="export_report.php?exportFile=report_client&<?= $request_string ?>" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
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
                                <td> <strong> Share Transfers Report: </strong> </td>
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
                                    <td width="18%"> Savings Account:</td>
                                    <td> <strong> <?= @$account_type_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['region']) : ?>
                                <tr>
                                    <td width="18%"> Client Region:</td>
                                    <td> <strong> <?= strtoupper(@$_REQUEST['district'] . ', ' . @$_REQUEST['region'] . ', ' . @$_REQUEST['parish'] . ', ' . @$_REQUEST['village']); ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['gender']) : ?>
                                <tr>
                                    <td width="18%"> Gender:</td>
                                    <td> <strong> <?= @$_REQUEST['gender']; ?> </strong> </td>
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
                                <th>From</th>
                                <th>To</th>
                                <th>Record Date</th>
                                <th class="text-center">Shares</th>
                                <th>Description</th>
                                <th>Authorised by</th>
                            </thead>
                            <tbody>

                                <?php
                                $total_loans = 0;
                                $total_bal = 0;
                                $tot_count = 0;
                                foreach ($members as $member) {
                                    if (@$member['no_shares']) {
                                        ++$tot_count;
                                        $client_contacts = '';
                                        $use_name_value = '';
                                        $use_name_value1 = '';

                                        $acno =
                                            @$member['from_mno'] == 0 ? '-' : @$member['from_mno'];
                                        $acno1 =
                                            @$member['to_mno'] == 0 ? '-' : @$member['to_mno'];


                                        $use_name_value = $acno . ' : ' . @$member['from_name'];
                                        $use_name_value1 = $acno1 . ' : ' . @$member['to_name'];
                                ?>
                                        <tr>

                                            <td> <?= @$use_name_value ?> </td>
                                            <td> <?= @$use_name_value1; ?> </td>
                                            <td> <?= normal_date_short(@$member['record_date']) ?> </td>

                                            <td class="text-center"> <?= number_format(@$member['no_shares'] ?? 0) ?> </td>

                                            <td> <?= @$member['description']  ?> </td>
                                            <td> <?= @$member['auth_by']  ?> </td>

                                        </tr>
                                <?php
                                        $total_bal +=  @$member['no_shares'];
                                    }
                                } ?>
                                <tr>
                                    <th colspan="5">Total Shares Transfered</th>
                                    <th class="text-center"> <?= number_format((float)$total_bal, 2, '.', '') ?> </th>
                                </tr>

                                <tr>
                                    <th colspan="5">Total Share Transfers</th>
                                    <th class="text-center"> <?= number_format($tot_count) ?> </th>
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