<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}


$response = new Response();


$title = 'SAVINGS DIVIDENDS';
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">


                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> Interest on Savings
                                </h4>

                                <p class="text-muted mb-3"></p>


                                <div class="row">
                                    <div class="col-md-3">


                                        <ul class="list-group">
                                          
                                            <a href="savings_list_initiations.php" class="list-group-item load_via_ajax"><i class="ti-home"></i> Initiations</a>
                                            <a href="savings_initiate.php" class="list-group-item load_via_ajax"><i class="ti-plus"></i> Initiate New Disbursement</a>
                                            <a href="disbursement_report_interest_savings.php" class="list-group-item load_via_ajax"><i class="ti-plus"></i> Disbursement Report</a>

                                        </ul>

                                    </div>
                                    <div class="col-md-9">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row pricingTable1">

                                                    <div class="col-md-6">

                                                        <h4 class="mt-0 header-title">Disbursement Details</h4>
                                                        <p class="text-muted mb-3"></p>

                                                        <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                                            <li><small>TOTAL INTEREST DISBURSED: </small>0</li>
                                                            <li><small>TOTAL BENEFICIARY A/Cs: </small><?= number_format(0) ?></li>

                                                        </ul>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->


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