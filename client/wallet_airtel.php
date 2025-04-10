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
                            Mobile Money Wallet Status
                        </h4>


                        <?php if ($permissions->hasSubPermissions('initiate_ussd_push')) { ?>
                            <a href="https://ucscucbs.herokuapp.com/mm_deposit.php" class="btn btn-primary light btn-xs mb-1" target="_blank">Initiate USSD Push</a>

                        <?php } ?>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>Particular</th>
                                        <th>Status</th>



                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sm = $response->getWalletDetails();
                                    ?>

                                    <tr>
                                        <td>Wallet Balance</td>
                                        <td><?php echo number_format($sm['data']['balance']); ?></td>


                                    </tr>
                                    <tr>
                                        <td>Currency</td>
                                        <td><?php echo $sm['data']['currency']; ?></td>


                                    </tr>
                                    <tr>
                                        <td>Account Status</td>
                                        <td><?= '<span class="badge light badge-primary">' . $sm['data']['account_status'] . '</span>' ?></td>


                                    </tr>





                                </tbody>

                            </table>
                        </div>
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