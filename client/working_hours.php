<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->hasPermissions('working_hours')) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_POST['save'])) {
    // var_dump($_POST);
    // exit;
    $res = $response->setBranchWorkingHours($_POST);
    // var_dump($res);
    // exit;
    if ($res) {
        setSessionMessage(true, "Working Hours successfully set");
    } else {
        setSessionMessage(false);
    }
    header('Location:working_hours.php?branch_id=' . $_POST['branch_id']);
    exit;
}

$branch_id = $_SESSION['user']['branchId'];
$branch = null;

// var_dump($branch_id);
// exit;

$current_savings_accounts_ids = [];
if (isset($_REQUEST['branch_id'])) {
    $branch = $response->getBranchDetails($_REQUEST['branch_id']);
} else {
    if ($branch_id) {
        $branch = $response->getBranchDetails($branch_id);
    }
}

$branches = null;
if ($_SESSION['session_user']['bankId']) {
    $branches = $response->getBankBranches($_SESSION['session_user']['bankId']);
}


?>

<?php
include('includes/head_tag.php');
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Working Hours
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form class="ajax_results_form" method="GET" id="get_branch_working_hours">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label class="text-label form-label">Branch *</label>
                                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                                            <div>
                                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                                            </div>
                                                        <?php } else { ?>
                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="branch_id" style="display: none;" required>
                                                                <option value=""> Select </option>
                                                                <?php

                                                                $default_selected = @$_REQUEST['branch_id'] == $user[0]['branchId'] || !@$_REQUEST['branch_id'] ? "selected" : "";

                                                                if ($user[0]['branchId']) { ?>
                                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                                    ';
                                                                <?php } ?>

                                                                <?php
                                                                if ($branches) {
                                                                    foreach ($branches as $row) {
                                                                        $is_seleceted = @$_REQUEST['branch_id'] == $row['id'] ? "selected" : "";
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

                                                <?php if (@$branches) { ?>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="text-label form-label"> </label>
                                                            <button type="submit" class="btn btn-primary form-control">Show Working Hours</button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </form>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                                <?php if (@$branch) { ?>
                                    <form action="" method="post" class="p-0" id="working_hours_form"> 
                                        <input type="hidden" name="branch_id" value="<?= $branch['branch_id'] ?>">
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <strong> Branch Name: <?= @$branch['branch_name'] ?> </strong>
                                            </div>
                                        </div>
                                        <div class="row mt-2 report-section light">
                                            <div class="col-md-6 ps-0">
                                                <table class="report_table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Week Days</th>
                                                            <th class="text-center">From</th>
                                                            <th class="text-center">To</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 0;
                                                        foreach (get_week_days() as $key => $text) {
                                                            $found_key = searchMultiDimensionalArrayByKey($branch['working_hours'], $key, 'day_id');
                                                            $array = $branch['working_hours'][$found_key];

                                                            $is_working_day = @$array['is_working_day'];
                                                        ?>
                                                            <tr>
                                                                <td class="align-middle">
                                                                    <input class="form-check-inpu  enable-disable" data-enable="check_working_hours_<?= $key ?>" type="checkbox" value="1" id="working_hours_day_<?= $key ?>" name="working_hours[is_working_day][<?= $i ?>]" <?= @$is_working_day ? 'checked' : '' ?>>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <input type="hidden" name="working_hours[day][<?= $i ?>]" class="form-control" value="<?= @$key ?>">

                                                                    <?= $text ?>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="working_hours[start_at][<?= $i ?>]" class="form-control timepicker check_working_hours_<?= $key ?>" value="<?= @$array['start_at'] ?>" <?= $is_working_day ? '' : 'disabled' ?> data-previous-value="<?= @$array['start_at'] ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="working_hours[end_at][<?= $i ?>]" class="form-control timepicker check_working_hours_<?= $key ?>" value="<?= @$array['end_at'] ?>" <?= $is_working_day ? '' : 'disabled' ?> data-previous-value="<?= @$array['end_at'] ?>">
                                                                </td>
                                                            </tr>
                                                        <?php $i++;
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-md-6 ps-0 pe-0">
                                                <table class="report_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Branch Roles</th>
                                                            <th class="text-center">From</th>
                                                            <th class="text-center">To</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                        $j = 0;
                                                        foreach ($branch['roles'] as $role) { ?>
                                                            <tr>
                                                                <td class="align-middle">
                                                                    <input type="hidden" name="working_hours_roles[role_id][<?= $j ?>]" class="form-control" value="<?= @$role['role_id'] ?>">

                                                                    <?= $role['name'] ?>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="working_hours_roles[start_at][<?= $j ?>]" class="form-control timepicker" value="<?= @$role['working_hours_start_at'] ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="working_hours_roles[end_at][<?= $j ?>]" class="form-control timepicker" value="<?= @$role['working_hours_end_at'] ?>">
                                                                </td>
                                                            </tr>
                                                        <?php $j++;
                                                        } ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <label class="text-label form-label"> </label>
                                                    <button type="submit" name="save" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>

                                <?php } ?>

                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->


            </div>
            <?php include('includes/footer.php'); ?>


        </div>
        <?php include('includes/bottom_scripts.php'); ?>
</body>

</html>