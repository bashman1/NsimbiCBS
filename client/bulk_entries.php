<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'BULK ENTRIES';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();



// var_dump($branches);

// exit;

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

                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>

                                    Bulk Entries
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">



                                        <li class="nav-item"><a href="#journal_entries" data-bs-toggle="tab" class="nav-link active">Bulk Entries</a>
                                        </li>
                                      

                                    </ul>
                                    <div class="tab-content">


                                        <div id="journal_entries" class="tab-pane fade show active">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Journal Entries</h4> -->

                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Bulk Entries</h4>
                                                <?php
                                                // if (!$permissions->hasPermissions('view_everything')) :
                                                ?>
                                                <a href="bulk_deposits.php" class="btn btn-primary light btn-xs mb-1">Deposits</a>
                                                <a href="bulk_withdraws.php" class="btn btn-primary light btn-xs mb-1">Withraws</a>
                                                <a href="bulk_salary.php" class="btn btn-primary light btn-xs mb-1">Salary Payments</a>
                                                
                                                <?php
                                                //  endif;
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

        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>


</body>

</html>