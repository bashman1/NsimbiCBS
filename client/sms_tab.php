<?php
include('../backend/config/session.php');



require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();

if ($permissions->IsSuperAdmin()) {
    header('location:sms_manage.php');
    // exit;
}


?>
<?php
$title = 'SMS MANAGEMENT';
require_once('includes/head_tag.php');

include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->requestpurchaseSMS($_POST['branch'], $_POST['amount']);
    if ($res) {
        setSessionMessage(true, 'SMS Purchase Requisition Submitted Successfully! Reach out to UCSCU-CBS Finance Dep\'t (+256701601305) & Make Payment.');
        header('location:sms_tab.php');
        // exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to initiate the SMS purchase Requisition');
        header('location:sms_tab.php');
        // exit;
    }
}

$clients = $response->getTotalClients($user[0]['bankId'], $user[0]['branchId'])[0]['total'];
$smsclients = $response->getTotalSMSClients($user[0]['bankId'], $user[0]['branchId'])[0]['total'];
$smswallet = $response->getBankSMSWalletDetails($user[0]['branchId']);
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
                                    SMS Banking
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#chart_of_accounts" data-bs-toggle="tab" class="nav-link active">SMS Settings</a>
                                        </li>
                                        <li class="nav-item"><a href="#transactions" data-bs-toggle="tab" class="nav-link ">SMS Outbox</a>
                                        </li>
                                        <li class="nav-item"><a href="#sub" data-bs-toggle="tab" class="nav-link ">Clients' SMS Subscriptions</a>
                                        </li>


                                    </ul>
                                    <div class="tab-content">

                                        <div id="chart_of_accounts" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="card">
                                                <div class="card-body">

                                                    <div class="profile-skills mb-5">
                                                        <h4 class="text-primary mb-4">SMS Banking Details</h4>

                                                        <?php if ($user[0]['bankId'] != '') : ?>
                                                            <a href="general_sms_settings.php" class="btn btn-primary light btn-xs mb-1">General SMS
                                                                Settings</a>
                                                            <a href="branch_sms_balances.php" class="btn btn-primary light btn-xs mb-1">SMS
                                                                Balance</a>
                                                            <a href="sms_types.php" class="btn btn-primary light btn-xs mb-1">Manage Automatic SMS </a>
                                                        <?php endif; ?>
                                                        <a class="btn btn-primary light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">SMS Purchase Form</a>
                                                        <a href="sms_purchase_transactions.php" class="btn btn-primary light btn-xs mb-1">SMS Purchase
                                                            Transactions</a>


                                                    </div>


                                                    <p class="text-muted mb-3"></p>

                                                    <hr class="hr-dashed">

                                                    <div class="btc-price">
                                                        <p class="text-muted mb-3">Summary</p>
                                                        <?php
                                                        if ($user[0]['branchId']) :
                                                        ?>
                                                            <div class="row">
                                                                <div class="col-lg-4">
                                                                    <span class="text-muted">Amount Loaded</span>
                                                                    <h3 class="mt-0">
                                                                        <?= number_format(@$smswallet[0]['sms_amount_loaded']??0); ?>
                                                                    </h3>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <span class="text-muted">Amount Spent</span>
                                                                    <h3 class="mt-0">
                                                                        <?= number_format(@$smswallet[0]['sms_amount_spent']??0); ?>
                                                                    </h3>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <span class="text-muted">Balance</span>
                                                                    <h3 class="mt-0">
                                                                        <?= number_format(@$smswallet[0]['sms_balance']??0); ?>
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php
                                                        if ($user[0]['bankId']) :
                                                            $bankSmsDetails = $response->getBankSMSDetails($user[0]['bankId'])[0];
                                                        ?>
                                                            <div class="row">
                                                                <div class="col-lg-4">
                                                                    <span class="text-muted">Cost Per SMS - Default
                                                                        SenderID</span>
                                                                    <h3 class="mt-0">
                                                                        <?= number_format(@$bankSmsDetails['no_sender_id_cost']??0); ?>
                                                                    </h3>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <span class="text-muted">Cost Per SMS - With
                                                                        SenderID</span>
                                                                    <h3 class="mt-0">
                                                                        <?= number_format(@$bankSmsDetails['sender_id_cost']??0); ?>
                                                                    </h3>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <span class="text-muted">Sender ID</span>
                                                                    <br />
                                                                    <span class="mt-0">
                                                                        <?= @$bankSmsDetails['sender_id'] . '  ' . @$bankSmsDetails['status'];  ?>
                                                                    </span>
                                                                </div>

                                                            </div>
                                                        <?php endif; ?>
                                                        <hr class="hr-dashed">

                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">Clients</span>
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
                                                <h4 class="text-primary mb-4">SMS Outbox </h4>
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
                                                <a href="sms_subscribe_single.php?act=sub" class="btn btn-primary light btn-xs mb-1">Subscribe a Client</a>
                                                <a href="sms_subscribe_single.php?act=unsub" class="btn btn-primary light btn-xs mb-1">Unscubscribe a Client</a>
                                                <?php
                                                if ($user[0]['bankId']) :
                                                ?>
                                                    <a href="sms_subscribe_all_bank_clients.php?id=<?php echo $user[0]['bankId']; ?>" class="btn btn-primary light btn-xs mb-1">Subscribe All Clients</a>
                                                    <a href="sms_unsubscribe_all_bank_clients.php?id=<?php echo $user[0]['bankId']; ?>" class="btn btn-primary light btn-xs mb-1">Un-Subscribe All
                                                        Clients</a>
                                                <?php endif; ?>
                                                <?php
                                                if ($user[0]['branchId']) :
                                                ?>
                                                    <a href="sms_subscribe_all_clients.php?id=<?php echo $user[0]['branchId']; ?>" class="btn btn-primary light btn-xs mb-1">Subscribe All Clients</a>
                                                    <a href="sms_unsubscribe_all_clients.php?id=<?php echo $user[0]['branchId']; ?>" class="btn btn-primary light btn-xs mb-1">Un-Subscribe All
                                                        Clients</a>
                                                <?php endif; ?>
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
                            <h5 class="modal-title">SMS Purchase Requisition Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">


                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Enter amount you would like to topup*
                                    </label>
                                    <input type="number" name="amount" class="form-control" placeholder="" min="0">
                                </div>
                                <?php
                                if (!$user[0]['branchId']) {
                                    $branches = $response->getBankBranches($user[0]['bankId']);

                                    echo '
                        
                          <div class="mb-3">
                              <label class="text-label form-label">Select Branch to which the SMS bundles shall belong *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                            
                                  ';
                                    if ($branches !== '') {
                                        foreach ($branches as $row) {
                                            echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                        }
                                    } else {
                                        echo '
                              <option readonly>No Branches Added yet</option>
                              ';
                                    }

                                    echo
                                    '
                          
                              </select>
                          </div>
                         
                          
                          ';
                                } else {
                                    echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                }
                                ?>


                                <div class="mb-3">
                                    <label class="text-danger form-label">NOTE: Kindly note that after submiting in this
                                        requisition form, you will have to get it approved by either making a payment via the system or reach out to the UCSCU-CBS Finance Department (+256701601305) &
                                        make payment , after which this purchase will be approved. Thanks
                                    </label>

                                </div>
                            </div>


                            <div class="modal-footer">

                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
        <!-- Required vendors -->
        <?php include('includes/bottom_scripts.php'); ?>

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>

        <script>
            //form handler
            function makeLiveSearch2(input_field, event) {
                var form = input_field.closest('form');

                form.submit(function(event) {
                    event.preventDefault();
                });

                var value = input_field.val();
                var search_results = form.next('.search_results');

                if (value.length <= 2)
                    return search_results.html('Insert Atleast (3) characters');

                search_results.html('<img src="images/preloaderImages/loading.gif"> searching.....');

                //run ajax call
                $.get(form.attr('action'), {
                    term: value,
                    requestType: 'ajax',
                    live: 1
                }, function(data) {
                    // var content = $.parseJSON(data);
                    data.redirect > 0 ? search_results.html(data.data) : search_results.html(data.message);

                });
            }


            //form handler
            function makeLiveSearch(liveSearchForm, $value, event) {
                var $search_form = $('#' + liveSearchForm);
                $search_form.submit(function(event) {
                    event.preventDefault();
                });

                var $search_results = $search_form.next('.search_results');
                var $button = $search_form.find('button');
                //get action url
                var $action_url = $search_form.attr('action');

                if ($value.length <= 3) {
                    $button.removeAttr('disabled');
                    $button.text('Manual Search');
                    $search_results.html('Insert Atleast (4) characters');
                    return;
                }

                $search_results.html('<img src="images/preloaderImages/loading.gif"> searching.....');

                //run ajax call
                $.get($action_url, {
                    term: $value,
                    requestType: 'ajax',
                    live: 1
                }, function(data) {
                    var content = $.parseJSON(data);
                    if (content.redirect > 0) {
                        handleAjaxPageRedirect(content);
                    } else {
                        $search_results.html(content.message);
                    }
                });
            }
        </script>




</body>

</html>