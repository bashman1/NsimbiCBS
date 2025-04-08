<?php
include('../backend/config/session.php');

require_once('./middleware/PermissionMiddleware.php');
$middleware_permissions = new PermissionMiddleware();
if (!$middleware_permissions->IsBankAdmin()) {
    return $middleware_permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {

    $res = $response->setStaffPermissions($_POST);
    // var_dump($res);
    // exit;
    setSessionMessage(true,'Staff Permissions Updated Successfully!');
    header('location:all_bank_staff.php');
    exit;
   
    if ($res) {
    } else {
        setSessionMessage(false,'Staff Permission Update failed! Try again');
        header('location:all_bank_staff.php');
        exit;
    }
    exit;

    // header('location:all_banks.php');
}
require_once('includes/head_tag.php');
$staff = $response->getStaffDetails($_GET['id'])[0];
// var_dump($staff);
// exit;
$role = $response->getRole($staff['roleId']);
$permissions = $response->getPermissions();
$permissions_ids = array_column($role['permissions'], 'permission_id');
$child_permissions_ids = array_column($role['child_permissions'], 'sub_permission_id');

$staff_permissions = $response->getStaffPermissions($staff['userId']);
$staff_permissions_ids = array_column($staff_permissions['permissions'], 'permission_id') ?? [];
$staff_sub_permissions_ids = array_column($staff_permissions['child_permissions'], 'sub_permission_id') ?? [];
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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Edit Staff Permissions
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-validation">
                                    <form class="needs-validation" novalidate="" method="POST">
                                        <input type="hidden" name="staff_id" value="<?= @$staff['userId'] ?>">
                                        <div class="row">
                                            <div class="col-xl-12">

                                                <div class="mb-3 row">
                                                    <div class="col-md-12">
                                                        Staff Names:
                                                        <strong>
                                                            <?= $staff['firstName'] . ' ' . $staff['lastName'] ?>
                                                        </strong>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <div class="col-md-12">
                                                        Staff Role: <strong> <?= @$role['name'] ?> </strong>
                                                    </div>
                                                </div>

                                                <div class="mb-3 row">
                                                    <div class="col-md-12">
                                                        Role Description:
                                                        <strong> <?= @$role['description'] ?> </strong>
                                                    </div>
                                                </div>

                                                <div class="mb-3 mt-5 row">
                                                    <div class="col-md-12">
                                                        <h3> Permissions </h3>
                                                    </div>

                                                </div>
                                                <?php
                                                $i = 0;
                                                foreach ($permissions as $permission) { ?>

                                                    <div class="mb-3 row">
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input set-permissions" type="checkbox" value="<?= $permission['id'] ?>" id="permission_<?= $permission['id'] ?>" name="permissions[]" data-child-permissions="parent-permission-<?= $permission['id'] ?>" <?= in_array($permission['id'], $permissions_ids) || in_array($permission['id'], $staff_permissions_ids) ? 'checked' : '' ?>>

                                                                <label class="form-check-label text-primary" for="permission_<?= $permission['id'] ?>" style="font-weight:700 !important; font-size:20px !important;">
                                                                    <?= $permission['name'] ?>
                                                                </label>

                                                            </div>
                                                        </div>
                                                        <br />
                                                        <?php foreach ($permission['child_permissions'] as $child_permission) { ?>
                                                            <div class="col-lg-4 ps-5 pe-5">
                                                                <div class="form-check">

                                                                    <input class="form-check-input child-permission parent-permission-<?= $permission['id'] ?>" type="checkbox" value="<?= $child_permission['sid'] ?>" id="child_permission_<?= $permission['id'] ?><?= $child_permission['sid'] ?>" name="child_permissions[]" data-parent-permission="permission_<?= $permission['id'] ?>" <?= in_array($child_permission['sid'], $child_permissions_ids) || in_array($child_permission['sid'], $staff_sub_permissions_ids) ? 'checked' : '' ?>>

                                                                    <label class="form-check-label" for="child_permission_<?= $permission['id'] ?><?= $child_permission['sid'] ?>">
                                                                        <?= $child_permission['sname'] ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <br /><br /><br />
                                                    </div>
                                                    <hr />
                                                <?php } ?>

                                            </div>

                                            <div class="mb-3 row">
                                                <div class="col-lg-8 ms-auto">
                                                    <button type="submit" class="btn btn-primary" name="submit"> Update Permissions </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!--**********************************
            Content body end
        ***********************************-->


            <!--**********************************
            Footer start
        ***********************************-->
            <?php include('includes/footer.php'); ?>
            <!--**********************************
            Footer end
        ***********************************-->

            <!--**********************************
           Support ticket button start
        ***********************************-->

            <!--**********************************
           Support ticket button end
        ***********************************-->


        </div>
        <!--**********************************
        Main wrapper end
    ***********************************-->

        <!--**********************************
        Scripts
    ***********************************-->
        <!-- Required vendors -->
        <?php include('includes/bottom_scripts.php'); ?>


       
       

</body>

</html>