<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_dormant_acc_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'DORMANT ACCOUNTS REPORT';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'ClientReport', 'ReportModelMethod' => 'getDormantAccountsReport'];

if (@$_REQUEST['filtered']) {
    $request_data['branchId'] = @$_REQUEST['branchId'];
    $request_data['gender'] = @$_REQUEST['gender'];
    $request_data['actype'] = @$_REQUEST['actype'];
    $request_data['start_date'] = @$_REQUEST['start_date'];
    $request_data['end_date'] = @$_REQUEST['end_date'];
}

$report_reponse = $ReportService->generateReport($request_data);
$members = @$report_reponse['data'];
// var_dump($members);
// exit;
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
$actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);

// var_dump($members);
// exit;

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search(@$_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}

$account_type_name = '';
if (@$_REQUEST['actype']) {
    $key = array_search($_REQUEST['actype'], array_column($actypes, 'id'));
    $account_type_name = $actypes[$key]['ucode'] . '-' . $actypes[$key]['name'];
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
                                            <select class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
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

                                        <label class="text-label form-label">Select Savings Account *</label>

                                        <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="actype" style="display: none;">

                                            <option value=""> All</option>

                                            <?php

                                            foreach ($actypes as $row) {
                                                $selected = @$_REQUEST['actype'] == $row['id'] ? "selected" : "";
                                            ?>
                                                <option value="<?= $row['id']; ?>" <?= $selected; ?>>
                                                    <?= $row['ucode'] . ' - ' .
                                                        $row['name'] ?>
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
                                        <label class="text-label form-label" for="exampleInputEmail3">Registration Start
                                            Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= @$_REQUEST['start_date'] ?? null; ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Registration End
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
                            Dormant Accounts Report
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
                                <a href="export_report.php?exportFile=report_dormant_accounts&<?= $request_string ?>" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> Dormant Accounts Report: </strong> </td>
                            </tr>
                        </table>
                        <?php if ($_REQUEST['branchId']) : ?>
                            <div> Branch: <strong> <?= @$branch_name; ?> </strong> </div>
                        <?php endif ?>

                        <?php if ($_REQUEST['actype']) : ?>
                            <div> Savings Account: <strong> <?= @$account_type_name; ?> </strong> </div>
                        <?php endif ?>

                        <?php if ($_REQUEST['gender']) : ?>
                            <div> Gender: <strong> <?= @$_REQUEST['gender'] ?> </strong> </div>
                        <?php endif ?>

                        <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
                            <div>
                                From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                                To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                            </div>
                        <?php endif ?>

                        <div style="font-size:14px; margin-bottom:5px;">
                            Total Members: <?= number_format(count($members)??0) ?>
                        </div>

                        <table class="report_table">
                            <thead>
                                <th>ID</th>
                                <th>A/C N0</th>
                                <th>Names</th>
                                <th>Membership Fee</th>
                                <th>Savings Balance</th>
                                <th>No. of Shares</th>
                                <th>Share Amount</th>
                                <th>Registration Date</th>
                            </thead>
                            <tbody>
                                <?php
                                $total_account_balance = 0;
                                $total_shares = 0;
                                $total_number_shares = 0;
                                $total_membership_fee = 0;
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

                                    $use_name_value = $client_contacts != '' ? @$member['client_names'] . ' ( ' . $client_contacts . ' )' : @$member['client_names'];

                                ?>
                                    <tr>
                                        <td> <?= @$member['user_id'] ?> </td>
                                        <td> <?= @$member['membership_no'] ?> </td>
                                        <td> <?= @$use_name_value; ?> </td>
                                        <td> <?= number_format(@$member['membership_fee']) ?> </td>
                                        <td> <?= number_format(@$member['acc_balance']) ?> </td>
                                        <td> <?= number_format(@$member['shares']) ?> </td>
                                        <td> <?= number_format(@$member['share_amount']) ?> </td>
                                        <td> <?= normal_date_short(@$member['member_created_at']) ?> </td>
                                    </tr>
                                <?php
                                    $total_account_balance += (int) @$member['acc_balance'];
                                    $total_membership_fee += (int) @$member['membership_fee'];
                                    $total_number_shares += (int) @$member['shares'];
                                    $total_shares += (int) @$member['share_amount'];
                                } ?>

                                <tr>
                                    <th colspan="3">Totals </th>
                                    <th> <?= number_format($total_membership_fee) ?> </th>
                                    <th> <?= number_format($total_account_balance) ?> </th>
                                    <th> <?= number_format($total_number_shares) ?> </th>
                                    <th> <?= number_format($total_shares) ?> </th>

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