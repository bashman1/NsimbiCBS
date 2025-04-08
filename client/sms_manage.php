<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsSuperAdmin()) {
    
    header('location:sms_manage.php');
    exit;
}

?>
<?php
$title = 'SMS MANAGEMENT';
require_once('includes/head_tag.php');

include_once('includes/response.php');
$response = new Response();

$clients = $response->getTotalSystemClients()[0]['total'];
$smsclients = $response->getTotalSystemSMSClients()[0]['total'];
$smswallet = $response->getSystemSMSWalletDetails()[0];
$smsbalance = $response->SMSAccountBalance('Balance', 'ucscucbs', 'smsmanage');
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
                                    SMS Banking Management
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#chart_of_accounts" data-bs-toggle="tab" class="nav-link active">SMS Settings</a>
                                        </li>
                                        <li class="nav-item"><a href="#transactions" data-bs-toggle="tab" class="nav-link ">SMS Outbox</a>
                                        </li>
                                        <li class="nav-item"><a href="#sub" data-bs-toggle="tab" class="nav-link ">Banks' SMS Subscriptions</a>
                                        </li>


                                    </ul>
                                    <div class="tab-content">

                                        <div id="chart_of_accounts" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="card">
                                                <div class="card-body">

                                                    <div class="profile-skills mb-5">
                                                        <h4 class="text-primary mb-4">SMS Banking Details</h4>
                                                        <a href="general_sms_settings.php" class="btn btn-primary light btn-xs mb-1">General SMS
                                                            Settings</a>
                                                        <a href="branch_sms_balances.php" class="btn btn-primary light btn-xs mb-1">SMS
                                                            Balance</a>
                                                        <a href="sms_types.php" class="btn btn-primary light btn-xs mb-1">Manage Automatic SMS </a>
                                                        <a href="sms_purchase_transactions.php" class="btn btn-primary light btn-xs mb-1">SMS Purchase Transactions</a>

                                                        <a class="btn btn-primary light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">SMS Purchase Form</a>
                                                    </div>


                                                    <p class="text-muted mb-3"></p>

                                                    <hr class="hr-dashed">

                                                    <div class="btc-price">
                                                        <p class="text-muted mb-3">Summary</p>

                                                        <div class="row">
                                                            <div class="col-lg-2">
                                                                <span class="text-muted">Account Balance (UGX)</span>
                                                                <h3 class="mt-0">
                                                                    <?= number_format($smsbalance??0); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <span class="text-muted">SMS Sales (UGX)</span>
                                                                <h3 class="mt-0">
                                                                    <?= number_format($smswallet['sms_purchased']); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">Income Earned (UGX)</span>
                                                                <h3 class="mt-0">
                                                                    <?= number_format($smswallet['sms_used']); ?>
                                                                </h3>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">Client Balances (UGX)</span>
                                                                <h3 class="mt-0">
                                                                    <?= number_format($smswallet['sms_balance']); ?>
                                                                </h3>
                                                            </div>
                                                        </div>

                                                        <hr class="hr-dashed">

                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">All Banks' Clients</span>
                                                                <h3 class="mt-0"><?= number_format($clients); ?></h3>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">Subscribed Clients</span>
                                                                <h3 class="mt-0"><?= number_format($smsclients); ?></h3>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">UnSubscribed Clients</span>
                                                                <h3 class="mt-0">
                                                                    <?= number_format($clients - $smsclients); ?></h3>
                                                            </div>
                                                        </div>
                                                    </div>




                                                </div>
                                            </div>



                                            <!-- </div> -->
                                        </div>

                                        <div id="transactions" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">SMS Outbox Categories</h4>
                                                <a href="send_single_sms.php" class="btn btn-primary light btn-xs mb-1">Send Single SMS</a>
                                                <a href="send_bulk_sms.php" class="btn btn-primary light btn-xs mb-1">Send Bulk SMS</a>
                                                <a href="sms_outbox.php" class="btn btn-primary light btn-xs mb-1">View SMS OutBox</a>
                                                <a href="scheduled_sms.php" class="btn btn-primary light btn-xs mb-1">Scheduled SMS</a>

                                            </div>





                                        </div>

                                        <div id="sub" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4"></h4>
                                                <a href="search_bank_sms.php" class="btn btn-primary light btn-xs mb-1" aria-expanded="false">Subscribe a Bank</a>
                                                <a href="search_bank_sms.php" class="btn btn-primary light btn-xs mb-1" aria-expanded="false">Unscubscribe a Bank</a>

                                            </div>





                                        </div>
                                        <!-- </div> -->







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

            <div class="modal fade bd-example-modal-lg3" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">SMS Purchase Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST" action="sms_purchase_form.php">


                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Enter amount you would like to topup*
                                    </label>
                                    <input type="number" name="amount" class="form-control" placeholder="" min="0">
                                </div>
                                <?php
                                if (!$user[0]['bankId']) {
                                    $banks = $response->getBanks();

                                    echo '
                        
                          <div class="mb-3">
                              <label class="text-label form-label">Select Bank *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="bid" required>
                            
                                  ';
                                    if ($banks !== '') {
                                        foreach ($banks as $row) {
                                            echo '
                              <option value="' . $row['bid'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                        }
                                    } else {
                                        echo '
                              <option readonly>No Banks Added yet</option>
                              ';
                                    }

                                    echo
                                    '
                          
                              </select>
                          </div>
                         
                          
                          ';
                                } else {
                                    echo '

                            <input type="hidden" name="bid" value="' . $user[0]['bankId'] . '" class="form-control" >

                            
                            ';
                                }
                                ?>



                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="submit" class="btn btn-primary">Continue</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>










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

        <script type="text/javascript">
            $(document).ready(function() {

                $.ajax({
                    url: '<?= BACKEND_BASE_URL?>Bank/get_all_bank_bank_accounts.php?bank=<?php echo $user[0]['bankId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bindtoDatatable(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function bindtoDatatable(data) {

                var table = $('#example3').dataTable({
                    destroy: true,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },

                    "aaData": data,

                    "columns": [{
                        "data": "id"
                    }, {
                        "data": "bank"
                    }, {
                        "data": "acname"
                    }, {
                        "data": "acno"
                    }, {
                        "data": "acc_balance"
                    }, {
                        "data": "branch"
                    }, {
                        "data": "status"
                    }, {
                        "data": "actions",
                    }]
                })

            }
        </script>






</body>

</html>