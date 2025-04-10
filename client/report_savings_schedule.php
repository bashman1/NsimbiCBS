<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_membership_schedule')) {
    return $permissions->isNotPermitted(true);
}
$title = 'SAVINGS SCHEDULE';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'ClientReport', 'ReportModelMethod' => 'getMemberhipScheduleReport'];

if (@$_REQUEST['filtered']) {
    $request_data['branchId'] = @$_REQUEST['branchId'];
    $request_data['gender'] = @$_REQUEST['gender'];
    $request_data['actype'] = @$_REQUEST['actype'];
    $request_data['start_date'] = @$_REQUEST['start_date'];
    $request_data['end_date'] = @$_REQUEST['end_date'];
    $request_data['reg_renew'] = @$_REQUEST['reg_renew'];
}
$_REQUEST['start_date'] = @$_REQUEST['start_date'] ?? '1900-01-01';
$_REQUEST['end_date'] = @$_REQUEST['end_date'] ?? date('Y-m-d');
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
    $key = array_search(@$_REQUEST['actype'], array_column($actypes, 'id'));
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
                                                <option value="0"> All</option>
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

                                        <label class="text-label form-label">Savings Officer*</label>
                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="savings_officer_id">
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

                                        <label class="text-label form-label">Gender *</label>

                                        <select name="gender" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="0">All</option>
                                            <option value="Male" <?= @$_REQUEST['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                                            <option value="Female" <?= @$_REQUEST['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Select Savings Account *</label>

                                        <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="actype" style="display: none;">

                                            <option value="0"> All</option>

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
                            </div><br />

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
                            Savings Schedule Report
                        </h4>

                        <?php if (count($members)) :
                            $request_string = 'branchName=' . $branch_name . '&accountName=' . $account_type_name;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>

                                <!-- <a href="export_report?exportFile=report_savings_schedule&<?= $request_string ?>" target="_blank" class="btn btn-primary light btn-xs">
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
                                <td> <strong> Savings Schedule Report: </strong> </td>
                            </tr>
                        </table>
                        <?php if (@$_REQUEST['branchId']) : ?>
                            <div> Branch: <strong> <?= $branch_name; ?> </strong> </div>
                        <?php endif ?>

                        <?php if (@$_REQUEST['actype']) : ?>
                            <div> Savings Account: <strong> <?= $account_type_name; ?> </strong> </div>
                        <?php endif ?>

                        <?php if (@$_REQUEST['gender']) : ?>
                            <div> Gender: <strong> <?= $_REQUEST['gender'] ?> </strong> </div>
                        <?php endif ?>

                        <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
                            <div>
                                From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                                To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                            </div>
                        <?php endif ?>


                        <div style="font-size:14px; margin-bottom:5px;">
                            Total Members: <?= number_format(count($members) ?? 0) ?>
                        </div>

                        <table class="report_table">
                            <thead>
                                <th>ID</th>
                                <th>A/C NO</th>
                                <th>Names</th>
                                <th>Contacts</th>
                                <th>Client Type</th>
                                <th>Saving Product</th>
                                <th>Branch</th>
                                <th>Balance (As at <?= date('Y-m-d', strtotime(@$_REQUEST['end_date'])) ?>)</th>
                                <th>Balance (As of Today)</th>
                                <!-- <th>Share Amount</th> -->
                                <!-- <th>Registration Date</th> -->
                            </thead>
                            <tbody>
                                <?php
                                $total_account_balance = 0;
                                $total_account_balance_bf = 0;

                                $sttn = '';
                                $bf = 0;
                                foreach ($members as $member) {


                                ?>
                                    <tr id="row-<?= $member['user_id'] ?>" data-id="<?= $member['user_id'] ?>">
                                        <td> <?= @$member['user_id'] ?> </td>
                                        <td> <?= @$member['membership_no'] ?> </td>
                                        <td> <?= @$member['client_names'] ?> </td>
                                        <td> <?= @$member['client_contacts'] ?? '' ?> </td>
                                        <td> <?= strtoupper(@$member['client_type'] ?? '') ?> </td>
                                        <td> <?= @$member['c_type'] ?> </td>
                                        <td> <?= @$member['branch_name'] ?> </td>

                                        <td><a class="text-primary details" href="member_statement_range.php?id=<?= @$member['user_id'] ?>&from_date=1900-01-01&to_date=<?= @$_REQUEST['end_date'] ?? date('Y-m-d') ?>"> </a></td>

                                        <td><a href="member_statement_range.php?id=<?= @$member['user_id'] ?>&from_date=<?= @$_REQUEST['start_date'] ?>&to_date=<?= @$_REQUEST['end_date'] ?>"> <?= number_format($member['acc_balance'] ?? 0) ?> </a></td>


                                    </tr>
                                <?php
                                    $total_account_balance += (int) @$member['acc_balance'] ?? 0;

                                    // $total_account_balance_bf += (int) @$bf ?? 0;
                                } ?>

                                <tr>
                                    <th colspan="7">Totals </th>
                                    <!-- <th>  </th> -->
                                    <!-- <th></th> -->
                                    <th id="totals"> <?= number_format(0) ?> </th>
                                    <th> <?= number_format($total_account_balance) ?> </th>
                                    <!-- <th> </th> -->
                                    <!-- <th>  </th> -->

                                    <!-- <th> </th> -->
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
        <script>
            $(document).ready(function() {
                let total = 0;
                $('tr[data-id]').each(function() {
                    var rowId = $(this).data('id');

                    $.ajax({
                        url: 'https://app.ucscucbs.net/backend/api/Bank/fetchClientBF.php?start=<?= @$_REQUEST['start_date'] ?>&end=<?= @$_REQUEST['end_date'] ?? date('Y-m-d') ?>',
                        method: 'GET',
                        data: {
                            id: rowId
                        },
                        success: function(response) {
                            // Assuming response is JSON with a 'details' field
                            $('#row-' + rowId + ' .details').text(response.details);
                            // Update the total
                            total += parseInt((response.details).replace(/,/g, '').split('.')[0]);
                            document.getElementById("totals").innerText = total.toLocaleString();
                        },
                        error: function() {
                            $('#row-' + rowId + ' .details').text('0');
                        }
                    });
                });
            });
        </script>
</body>

</html>