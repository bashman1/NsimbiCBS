<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}


$title = 'SAVING REPORTS';
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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Saving Reports
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#fixed" data-bs-toggle="tab" class="nav-link active">Saving Reports</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">

                                        <div id="fixed" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Saving Reports </h4>
                                                <?php if ($menu_permission->hasSubPermissions('view_membership_schedule')) { ?>
                                                    <a href="report_savings_schedule.php" class="btn btn-primary light btn-xs mb-1">Savings Schedule</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_membership_schedule')) { ?>
                                                    <a href="search_general_client.php" class="btn btn-primary light btn-xs mb-1">A/C Statement</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_savings_report')) { ?>
                                                    <a href="report_savings.php" class="btn btn-primary light btn-xs mb-1">Savings Report</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_savings_report')) { ?>
                                                    <a href="report_closed_fds.php" class="btn btn-primary light btn-xs mb-1">Closed Fixed Deposits</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_savings_report')) { ?>
                                                    <a href="report_due_fds.php" class="btn btn-primary light btn-xs mb-1">Due Fixed Deposits</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_savings_report')) { ?>
                                                    <a href="report_running_fds.php" class="btn btn-primary light btn-xs mb-1">Running Fixed Deposits</a>
                                                <?php } ?>

                                            </div>
                                            <!-- </div> -->
                                        </div>




                                    </div>
                                    <!-- </div> -->
                                    <!-- Modal -->

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