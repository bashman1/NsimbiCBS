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
                            Mobile Banking Wallets
                        </h4>



                    </div>
                    <div class="card-body">
                        <!-- <a href="wallet_airtel.php" class="btn btn-danger  btn-xs mb-1">Airtel</a> -->
                        <!-- <a href="mobile_money_wallet.php" class="btn btn-primary  btn-xs mb-1">Mobile Money Uganda</a> -->
                        <!-- <a href="flutter_wallet.php" class="btn btn-warning  btn-xs mb-1">Flutter-wave Wallet</a> -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-9 col-xxl-8">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="card">
                                                    <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                                                        <div class="mb-3 me-3">
                                                            <h5 class="fs-18  font-w700">Main Balance (UGX)</h5>
                                                            <span class="fs-32 font-w800" id="ac_bal"><img src="images/loader.gif" alt="" class="content-loader sm"></span>
                                                        </div>
                                                        <div class="me-3 mb-3">
                                                            <p class="fs-14 mb-1 font-w700 text-primary">Total Credit (UGX)</p>
                                                            <span class=" fs-18 font-w700 text-primary">+<span id="cr"><img src="images/loader.gif" alt="" class="content-loader sm"></span></span>
                                                        </div>
                                                        <div class="me-3 mb-3">
                                                            <p class="fs-14 mb-1 font-w700 text-danger">Total Debit (UGX)</p>
                                                            <span class="text-danger fs-18 font-w700">-<span id="dr"><img src="images/loader.gif" alt="" class="content-loader sm"></span></span>
                                                        </div>

                                                        <!-- <span class="fs-18 font-w700 me-3 mb-3">**** **** **** 1234</span> -->
                                                        <div class="dropdown mb-auto">
                                                            <a href="javascript:void(0);" class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="#575757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                    <path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="#575757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                    <path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="#575757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                </svg>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item" href="javascript:void(0);">Initiate Withdraw from Wallet</a>
                                                                <a class="dropdown-item" href="mm_wallet_stmt.php">View Statement</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-xxl-4">

                                    </div>
                                </div>
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

        <script type="text/javascript">
            function numberWithCommas(x) {
                //return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                return x;
            }
            $(document).ready(function() {

                $.ajax({
                    url: '<?php echo BACKEND_BASE_URL; ?>Bank/get_mm_balances.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>&user=<?php echo $user[0]['userId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let ac_bal = data['data'][0]['ac_bal'] || 0;
                        let cr = data['data'][0]['cr'] || 0;
                        let dr = data['data'][0]['dr'] || 0;

                        $('#ac_bal').html(numberWithCommas(ac_bal));
                        $('#cr').html(numberWithCommas(cr));
                        $('#dr').html(numberWithCommas(dr));
                    }
                });

            });
        </script>


</body>

</html>