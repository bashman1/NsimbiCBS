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


$title = 'CONVERT CLIENT';
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
                                    Over-Drafts
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#over_drafts" data-bs-toggle="tab" class="nav-link active">Over Drafts</a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">

                                        <div id="over_drafts" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Client Type Conversions </h4>
                                                <?php
                                                if ($permissions->hasSubPermissions('create_overdraft_application')) {
                                                    echo '
                                                     <a href="search_client_overdraft.php" class="btn btn-primary light btn-xs mb-1">Enter Over-Draft Application</a>
                                                    ';
                                                }

                                                if ($permissions->hasSubPermissions('view_all_overdrafts')) {
                                                    echo '
                                                     <a href="all_over_drafts.php" class="btn btn-primary light btn-xs mb-1">View All Over-Drafts</a>
                                                    ';
                                                }

                                                if ($permissions->hasSubPermissions('manage_over_draft_products')) {
                                                    echo '
                                                    <a href="over_draft_products.php" class="btn btn-primary light btn-xs mb-1">Over Draft Products</a>
                                                    ';
                                                }


                                                ?>





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