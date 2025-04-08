<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('view_mobile_money_wallet')) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();

// 
?>

<?php
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

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Mobile Money USSD Push - Initiation
                        </h4>



                    </div>
                    <div class="card-body">
                        <a href="ussd_push_2.php" class="btn btn-danger  btn-xs mb-1" target="_blank">Airtel</a>
                        <a href="ussd_push_2.php" class="btn btn-warning  btn-xs mb-1" target="_blank">MTN</a>
                    </div>
                </div>
                <!-- </div> -->



                <!-- </div> -->
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
    <?php
    include('includes/bottom_scripts.php');
    ?>



</body>

</html>