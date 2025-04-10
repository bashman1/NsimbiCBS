<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'AGENT PERFORMANCE REPORT';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'ClientReport', 'ReportModelMethod' => 'getAgentPerformanceReport'];

if (@$_REQUEST['filtered']) {
    $request_data['branchId'] = @$_REQUEST['branchId'];
    $request_data['start_date'] = @$_REQUEST['start_date'];
    $request_data['end_date'] = @$_REQUEST['end_date'];
}
$_REQUEST['start_date'] = @$_REQUEST['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$_REQUEST['end_date'] = @$_REQUEST['end_date'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport($request_data);
$members = @$report_reponse['data'];
// var_dump($members);
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
                                        <label class="text-label form-label" for="exampleInputEmail3">Report Start
                                            Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= @$_REQUEST['start_date'] ?? null; ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Report End
                                            Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?? null; ?>" id="exampleInputEmail4" placeholder="End Date">
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
                            Agent Banking Performance Report
                        </h4>

                        <?php if (count($members)) :
                            $request_string = 'branchName=' . $branch_name . '&accountName=' . $account_type_name;
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
                                <td> <strong> Agent Banking Performance Report: </strong> </td>
                            </tr>
                        </table>
                        <?php if (@$_REQUEST['branchId']) : ?>
                            <div> Branch: <strong> <?= $branch_name; ?> </strong> </div>
                        <?php endif ?>


                        <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
                            <div>
                                From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                                To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                            </div>
                        <?php endif ?>


                        <div style="font-size:14px; margin-bottom:5px;">
                            Total Agents: <?= number_format(count($members) ?? 0) ?>
                        </div>

                        <table class="report_table">
                            <thead>
                                <th>ID</th>
                                <th>Names</th>
                                <th>Location</th>
                                <th>Contacts</th>
                                <th>Branch</th>
                                <th>Deposits Taken</th>
                                <th>Customers Served</th>
                                <th>New Members</th>
                                <th>Loan Applications</th>
                                <th>Total Commision</th>
                            </thead>
                            <tbody>
                                <?php
                                $total_deposits = 0;
                                $total_loans = 0;
                                $total_commision = 0;
                                $total_new_members = 0;
                                $total_custs = 0;
                                foreach ($members as $member) {
                                    $client_contacts = '';
                                    $use_name_value = '';
                                    if ($member['primaryCellPhone']) {
                                        $client_contacts = $member['primaryCellPhone'];
                                    }
                                    if ($member['secondaryCellPhone']) {
                                        $client_contacts = $client_contacts . ' / ' . $member['secondaryCellPhone'];
                                    }
                                    if ($member['otherCellPhone']) {
                                        $client_contacts = $client_contacts . ' / ' . $member['otherCellPhone'];
                                    }

                                    $use_name_value =  @$member['client_names'];




                                ?>
                                    <tr>
                                        <td> <?= @$member['user_id'] ?> </td>
                                        <td> <?= @$use_name_value; ?> </td>
                                        <td> <?= @$member['addressLine1'] . ' , ' . @$member['addressLine2'] . ' , ' . @$member['village'] . ' , ' . @$member['parish'] . ' , ' . @$member['subcounty'] . ' , ' . @$member['district']; ?> </td>
                                        <td><?= $client_contacts ?></td>
                                        <td> <?= $member['bname'] ?> </td>
                                        <td> <?= number_format($member['deposits'] ?? 0) ?> </td>
                                        <td> <?= number_format($member['customers'] ?? 0) ?> </td>
                                        <td> <?= number_format($member['new_members'] ?? 0) ?> </td>
                                        <td> <?= number_format($member['loan_aplns'] ?? 0) ?> </td>
                                        <td> <?= number_format(0) ?> </td>
                                    </tr>
                                <?php
                                    $total_deposits += (int) @$member['deposits'] ?? 0;
                                    $total_new_members += (int) @$member['new_members'] ?? 0;
                                    $total_loans += (int) @$member['loan_aplns'] ?? 0;
                                    $total_commision += (int)  0;
                                    $total_custs += (int) @$member['customers'] ?? 0;
                                } ?>

                                <tr>
                                    <th colspan="5">Totals </th>
                                    <th> <?= number_format($total_deposits) ?> </th>
                                    <th><?= number_format($total_custs) ?></th>
                                    <th><?= number_format($total_new_members) ?></th>
                                    <th><?= number_format($total_loans) ?></th>
                                    <th> <?= number_format($total_commision) ?> </th>

                                    <th> </th>
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