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

if (isset($_POST['submit'])) {
    // $amount = str_replace(",", "", $_POST['amount']);
    $res = $response->purchaseShares($_POST);
    if ($res) {
        setSessionMessage(true, 'Share Purchase Completed Successfully!');
        header('location:share_purchase_trxns');
        exit;
    } else {
        setSessionMessage(false, 'Share Purchase failed!');
        header('location:share_purchase');
        exit;
    }
}
$title = 'SHARE DIVIDENDS';
require_once('includes/head_tag.php');
$member = [];
if (isset($_GET['t'])) {
    $member = $response->getClientDetails($_GET['t'])[0];
}

$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);
$mobile_accounts = $response->getAllBankMobileAccounts($user[0]['bankId'], $user[0]['branchId']);

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
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> Share-Holder's Dividends
                                </h4>

                                <p class="text-muted mb-3"></p>


                                <div class="row">
                                    <div class="col-md-3">


                                        <!-- <h4 class="mt-0 header-title">Options</h4> -->
                                        <!-- <p class="text-muted mb-3"></p> -->

                                        <ul class="list-group">
                                           
                                            <a href="share_list_initiations.php" class="list-group-item load_via_ajax"><i class="ti-home"></i> Initiations</a>
                                            <a href="share_initiate.php" class="list-group-item load_via_ajax"><i class="ti-plus"></i> Initiate New Share</a>
                                            <a href="disbursement_report.php" class="list-group-item load_via_ajax"><i class="ti-plus"></i> Disbursement Report</a>

                                        </ul>

                                    </div>
                                    <div class="col-md-9">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row pricingTable1">
                                                    <div class="col-md-6">
                                                        <?php
                                                        $details = $response->getBankSharesDetails($user[0]['bankId'], $user[0]['branchId'])[0];
                                                        ?>
                                                        <h4 class="mt-0 header-title">Dividend Details</h4>
                                                        <p class="text-muted mb-3"></p>

                                                        <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                                            <li><small>SHARE HOLDER(s): </small><?= number_format(@$details['share_holders']) ?></li>
                                                            <li><small>NON SHARE HOLDER(s): </small><?= number_format(@$details['non_shares']) ?></li>
                                                            <li><small>TOTAL SHARES: </small><?= number_format(@$details['shares'], 2, '.', '') ?></li>
                                                            <li><small>TOTAL SHARE AMOUNT: </small><?= number_format(@$details['shareamount']) ?></li>
                                                        </ul>

                                                    </div>
                                                    <div class="col-md-6">

                                                        <h4 class="mt-0 header-title">Profitabilty Details</h4>
                                                        <p class="text-muted mb-3"></p>

                                                        <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                                            <li><small>TOTAL PROFITS SHARED SINCE: </small>0</li>
                                                            <li><small>TOTAL SAVING DIVIDENDS: </small><?= number_format(@$details['savings']) ?></li>
                                                            <li><small>TOTAL SHARES DIVIDENDS: </small><?= number_format(@$details['sharesdividends']) ?></li>
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