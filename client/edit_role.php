<?php
include('../backend/config/session.php');


if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}

require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$middleware_permissions = new PermissionMiddleware();
if (!$middleware_permissions->IsBankAdmin()) {
    return $middleware_permissions->isNotPermitted(true);
}
?>
<?php


include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {

    $res = $response->saveRole($_POST);
    // var_dump($res);
    // exit;
    if ($res) {
        setSessionMessage(true,'Role Updated Successfully!');
        header('location:roles.php');
        exit;
    } else {
        setSessionMessage(false,'Role Update failed!');
        header('location:roles.php');
        exit;
    }

    // header('location:all_banks.php');
}
require_once('includes/head_tag.php');
$permissions = $response->getPermissions();
$role = $response->getRole($_REQUEST['id']);
$permissions_ids = array_column($role['permissions'], 'permission_id');
$child_permissions_ids = array_column($role['child_permissions'], 'sub_permission_id');
// var_dump($child_permissions_ids);
// exit;

$branches = $response->getBankBranches($_SESSION['session_user']['bankId'])

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

                <!-- row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Edit Role
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-validation">
                                    <form class="needs-validation" novalidate="" method="POST">
                                        <input type="hidden" name="role_uuid" value="<?= $role['id'] ?>">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label" for="validationCustom01">Associated Bank
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="default-select wide form-control" id="validationCustom05" style="display: none;" disabled name="bank">
                                                        <option>Select Bank</option>
                                                        <option value="<?= $_SESSION['session_user']['bankId'] ?>" selected> <?= $_SESSION['session_user']['bankName'] ?> </option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label" for="validationCustom01">Branch
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="default-select wide form-control" id="validationCustom05" style="display: none;" name="branch">
                                                        <option value="">None</option>
                                                        <option value="<?= $_SESSION['session_user']['branchId'] ?>" <?= $role['branchId'] == $_SESSION['session_user']['branchId'] ? 'selected' : '' ?>>
                                                            <?= $_SESSION['session_user']['branchName'] ?>
                                                        </option>
                                                        <?php
                                                        foreach ($branches as $branch) { ?>
                                                            <option value="<?= $branch['id'] ?>" <?= $role['branchId'] == $branch['id'] ? 'selected' : '' ?>>
                                                                <?= $branch['name'] . ' - ' . $branch['location'] ?>
                                                            </option>
                                                        <?php } ?>

                                                    </select>
                                                </div>
                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label" for="validationCustom02">Role
                                                        Name <span class="text-danger">*</span>
                                                    </label>
                                                    <!-- <div class="col-lg-6"> -->
                                                    <input type="text" class="form-control" id="validationCustom02" placeholder="" required name="name" value="<?= $role['name'] ?>">
                                                    <div class="invalid-feedback">
                                                        Please enter the role name.
                                                    </div>
                                                    <!-- </div> -->
                                                </div>
                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label" for="validationCustom02">Role
                                                        Description <span class="text-danger">*</span>
                                                    </label>
                                                    <!-- <div class="col-lg-6"> -->
                                                    <textarea class="form-control" id="validationCustom02" required name="description"><?= $role['description'] ?></textarea>
                                                    <div class="invalid-feedback">
                                                        Please enter the role description.
                                                    </div>
                                                    <!-- </div> -->
                                                </div>

                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label"><a href="javascript:void()">Permissions</a> <span class="text-danger">*</span>
                                                    </label>

                                                </div>
                                                <?php
                                                $i = 0;
                                                foreach ($permissions as $permission) { ?>

                                                    <div class="mb-3 row">
                                                        <div class="form-check">
                                                            <input class="form-check-input set-permissions" type="checkbox" value="<?= $permission['id'] ?>" id="permission_<?= $permission['id'] ?>" name="permissions[]" data-child-permissions="parent-permission-<?= $permission['id'] ?>" <?= in_array($permission['id'], $permissions_ids) ? 'checked' : '' ?>>

                                                            <label class="form-check-label text-primary" for="permission_<?= $permission['id'] ?>" style="font-weight:700 !important; font-size:20px !important;">
                                                                <?= $permission['name'] ?>
                                                            </label>

                                                        </div><br />
                                                        <?php foreach ($permission['child_permissions'] as $child_permission) { ?>
                                                            <div class="col-lg-4">
                                                                <div class="form-check">

                                                                    <input class="form-check-input child-permission parent-permission-<?= $permission['id'] ?>" type="checkbox" value="<?= $child_permission['sid'] ?>" id="child_permission_<?= $permission['id'] ?><?= $child_permission['sid'] ?>" name="child_permissions[]" data-parent-permission="permission_<?= $permission['id'] ?>" <?= in_array($child_permission['sid'], $child_permissions_ids) ? 'checked' : '' ?>>

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
                                                    <button type="submit" class="btn btn-primary" name="submit">Create
                                                        Role</button>
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
        <?php
        include('includes/bottom_scripts.php');
        ?>


       

</body>

</html>