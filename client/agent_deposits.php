<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_all_deposits')) {
    return $permissions->isNotPermitted(true);
}
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();


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
                            Deposits Entered Today
                        </h4>
                        <?= $_GET['n'] . '<br/>' ?? '' ?>

                        <?php if ($permissions->hasSubPermissions('approve_agent_transactions')) { ?>
                            <a href="approve_all_agent_deposits.php?id=<?= $_GET['id'] ?>&user=<?= $user[0]['userId'] ?>&amount=<?= $_GET['amount'] ?>" class="btn btn-primary light btn-xs mb-1">Approve All Deposits</a>
                        <?php } ?>
                        <?php if ($permissions->hasSubPermissions('delete_agent_transactions')) { ?>
                            <a href="trash_all_agent_deposits.php?id=<?= $_GET['id'] ?>" class="btn btn-danger light btn-xs mb-1">Delete All Entries</a>

                        <?php } ?>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Amount (Savings)</th>
                                        <th>Amount (Loan Repayment)</th>
                                        <th>Total Amount</th>
                                        <th>Client</th>
                                        <th>Payment Method</th>
                                        <th>Narration</th>
                                        <th>Status</th>
                                        <th>Trxn Date</th>
                                        <th>Authorised by</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $trxns = $response->getAllAgentTrxns($_GET['id']);

                                    if ($trxns != '') {
                                        foreach ($trxns as $trxn) {
                                            echo '
          <tr>
                                        <th>' . $trxn['_did'] . '</th>
                                        <th>' . $trxn['_amount'] . '</th>
                                        <th>' . $trxn['loan'] . '</th>
                                        <th>' . $trxn['total_amount'] . '</th>
                                        <th>' . $trxn['account_name'] . '</th>
                                        <th>' . $trxn['pay_method'] . '</th>
                                        <th>' . $trxn['_reason'] . '</th>
                                        <th>' . $trxn['_status'] . '</th>
                                        <th>' . $trxn['_date_created'] . '</th>
                                        <th> ' . $trxn['_authorisedby'] . '</th>
                                        <th>' . $trxn['actions'] . '</th>
                                    </tr>
        
        ';
                                        }
                                    }

                                    ?>



                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>



            <!-- </div>
            </div> -->
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
    <?php include('includes/bottom_scripts.php'); ?>





</body>

</html>