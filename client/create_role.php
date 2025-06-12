<?php
include('../backend/config/session.php');


if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
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

    // $res = $response->createRole($_POST['name'], $_POST['description'], $user[0]['bankId'], $_POST['branch'], $subPermissions, $mainPermissions);

    $res = $response->saveRole($_POST);
    // var_dump($res);
    // exit;
    if ($res['success']) {
        setSessionMessage(true, 'Role Created Successfully');
        Redirect('roles.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Check the Client\'s table to confirm if all the client\'s were created right.');
        RedirectCurrent();
    }
    exit;
}

$permissions = $response->getPermissions();
// foreach ($permissions as $permission){
// var_dump($permission['name']);
// exit;

// }
// $permissions = $permissions_response['data'];

require_once('includes/head_tag.php');
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Roles</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Create New Role</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Add New Role
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="form-validation">
                                    <form class="needs-validation" novalidate="" method="POST">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label" for="validationCustom01">Associated Bank
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="default-select wide form-control" id="validationCustom05" style="display: none;" disabled name="bank">

                                                        <?php
                                                        echo '
                                                            <option value="' . $user[0]['bankId'] . '" selected>' . $user[0]['bankName'] . '</option>
                                                            ';
                                                        ?>
                                                        <option>Select Bank</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label" for="validationCustom01">Branch
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="default-select wide form-control" id="validationCustom05" style="display: none;" name="branch">
                                                        <option value="">None</option>
                                                        <?php
                                                        echo '
                                                            <option value="' . $user[0]['branchId'] . '" selected>' . $user[0]['branchName'] . '</option>
                                                            ';
                                                        foreach ($response->getBankBranches($user[0]['bankId']) as $row) {
                                                            echo '
                                                                <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                                                                ';
                                                        }
                                                        ?>

                                                    </select>
                                                </div>
                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label" for="validationCustom02">Role
                                                        Name <span class="text-danger">*</span>
                                                    </label>
                                                    <!-- <div class="col-lg-6"> -->
                                                    <input type="text" class="form-control" id="validationCustom02" placeholder="" required name="name">
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
                                                    <textarea class="form-control" id="validationCustom02" required name="description"></textarea>
                                                    <div class="invalid-feedback">
                                                        Please enter the role description.
                                                    </div>
                                                    <!-- </div> -->
                                                </div>

                                                <div class="mb-3 row">
                                                    <label class="col-lg-4 col-form-label"><a href="javascript:void()">Permissions</a> <span class="text-danger">*</span>
                                                    </label>

                                                </div>
                                                <div class="mb-3 row">
                                                    <?php
                                                    $i = 0;
                                                    foreach ($permissions as $permission) { ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input set-permissions" type="checkbox" value="<?= $permission['id'] ?>" id="permission_<?= $permission['id'] ?>" name="permissions[]" data-child-permissions="parent-permission-<?= $permission['id'] ?>">
                                                            <label class="form-check-label text-primary" for="permission_<?= $permission['id'] ?>" style="font-weight:700 !important; font-size:20px !important;">
                                                                <?= $permission['name'] ?>
                                                            </label>
                                                        </div><br />
                                                        <?php foreach ($permission['child_permissions'] as $child_permission) { ?>
                                                            <div class="col-lg-4">
                                                                <div class="form-check">
                                                                    <input class="form-check-input child-permission parent-permission-<?= $permission['id'] ?>" type="checkbox" value="<?= $child_permission['sid'] ?>" id="child_permission_<?= $permission['id'] ?><?= $child_permission['sid'] ?>" name="child_permissions[]" data-parent-permission="permission_<?= $permission['id'] ?>">
                                                                    <label class="form-check-label" for="child_permission_<?= $permission['id'] ?><?= $child_permission['sid'] ?>">
                                                                        <?= $child_permission['sname'] ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                    <?php }
                                                        echo '<br/><br/><br/>';
                                                    }

                                                    ?>



                                                </div>


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

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>
        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>

</body>

</html>