<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('add_deposit')) {
    return $permissions->isNotPermitted(true);
}
$title = 'BULK TRANSFER';
require_once('includes/head_tag.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->registerBulkTransfer($_POST);
    if ($res) {
        setSessionMessage(true, 'Transfer Completed Successfully!');

        header('location:transfers_tab.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to register bulk transfer.');
        header('location:search_client_transfer.php');
        exit;
    }
}
$member = [];
$client_id = @$_GET['t'] = parsed_id(@$_GET['t']);
if (isset($_GET['t'])) {
    $member = $response->getClientDetails($_GET['t'])[0];
}

$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);

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
                    <div class="card-body">


                        <div class="row">

                            <div class="col-md-4">

                                <h4 class="mt-0 header-title"><a href="search_client" class="btn btn-primary light btn-xs mb-1 "><i class="fa fa-arrow-left"></i> Back</a> | One to Many Transfer Form</h4>
                                <p class="text-muted mb-3">Transferer's Details</p>

                                <hr class="hr-dashed">

                                <div class="mb-3 pricingTable1">

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li style="line-height: 40px !important;"><b>SAVING PRODUCT: </b><?= $member['savingaccount']; ?></li>
                                        <li style="line-height: 40px !important;"><b>A/C: </b><?= $member['accno']; ?></li>
                                        <li style="line-height: 40px !important;"><b>A/C NAME: </b><?= $member['name']; ?></li>
                                        <li style="line-height: 40px !important;"><b>CURRENCY: </b><?= 'UGANDA SHILLINGS'; ?></li>
                                        <li style="line-height: 45px !important;"><b>STATUS: </b><?= $member['status']; ?></li>
                                        <li style="line-height: 40px !important;"><b>LAST TRANSACTED: </b><?= $member['last_transaction']; ?></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-4" style="padding-top: 66px !important">
                                <h4 class="mt-0 header-title"></h4>
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">

                                <div class="text-center">
                                    <div class="met-profile-main-pic">
                                        <img src="<?= is_null($member['image']) ? 'icons/favicon.png' : $member['image'] ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" alt="" height="100" width="100" class="rounded-circle">
                                    </div>

                                    <div class="">
                                        <h5 class="mb-0"><?= $member['name']; ?></h5>
                                        <small class="text-muted">Mem No: <?= $member['accno']; ?> | CLIENT TYPE : <?= ($member['actype']); ?></small>
                                    </div>
                                </div>

                                <hr class="hr-dashed">

                                <h4 class="mt-0 header-title">Account Balance</h4>
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">

                                <div class="btc-price">

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <span class="text-muted">Available</span>
                                            <h3 class="mt-0">UGX <?= number_format(max($member['acc_balance'] - $member['min_balance'], 0)) ?></h3>
                                        </div>
                                        <div class="col-lg-6">
                                            <span class="text-muted">Actual</span>
                                            <h3 class="mt-0">UGX <?= number_format($member['acc_balance']); ?></h3>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-4 single-pro-detail" style="padding-top: 66px !important">
                                <h4 class="mt-0 header-title"></h4>
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">


                                <div class="text-center">
                                    <a href="<?= 'client_profile_page.php?id=' . $member['userId']; ?>" class="btn btn-primary light btn-xs mb-1">View Client's
                                        Profile</a>
                                </div>

                            </div>



                        </div>
                        <!--end row-->

                    </div>
                    <!--end card-body-->
                </div>
                <!--end card-->

                <div class="row">

                    <div class="col-md-7">

                        <div class="card">
                            <div class="card-body">
                                <h4 class="mt-0 header-title">Savings Transfer Form (Total: UGX <span id="total_amount">0</span>)</h4>
                                <p class="text-muted mb-3">Selected Accounts (accounts with (0) will be ignored)</p>

                                <form method="post" class="submit_with_ajax">
                                    <input type="hidden" name="client" value="<?= $member['userId']; ?>">

                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>A/C No</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody id="selectedAccounts">

                                        </tbody>
                                    </table>

                                    <hr class="hr-dashed">

                                    <div class="form-group">
                                        <label>Transfer Reason <i>*</i>: </label>
                                        <input id="reason" type="text" placeholder="Transfer Reason" value="" name="reason" required="" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>Transaction Date:</label>
                                        <input type="date" class="form-control" value="2023-08-02" name="record_date" placeholder="Enter trx date">
                                    </div>

                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" rows="2" name="comment" placeholder="writing here.."></textarea>
                                    </div>
                                    <br /><br />
                                    <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm">Process Transaction</button>

                                </form>
                            </div>
                        </div>
                        <!--end form-->
                    </div>
                    <!--end col-->

                    <div class="col-md-5">

                        <div class="card">
                            <div class="card-body">
                                <h4 class="mt-0 header-title">Search Receiving Accounts</h4>
                                <p class="text-muted mb-3"></p>

                                <form method="post" action="<?= BACKEND_BASE_URL ?>Bank/search_clients_transfer_2.php?branch=<?php echo $user[0]['branchId']; ?>&bank=<?php echo $user[0]['bankId']; ?>">
                                    <div class="form-group">
                                        <input type="text" value="" placeholder="Search accounts" class="form-control" onkeyup="makeLiveSearch2($(this))" name="search_term">
                                    </div>
                                </form>

                                <div class="search_results">
                                    <i>Results Appear Here</i>
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
        <?php
        include('includes/bottom_scripts.php');
        ?>

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
        <script type="text/javascript">
            function bulk_transfer_populateTableRows(input_field) {

                $('#selectedAccounts').append("<tr><td>" + input_field.attr('account-id') + "</td><td>" + input_field.attr('account-no') + "</td><td>" + input_field.attr('account-name') + "</td><td> <input name='accounts[" + input_field.attr('account-id') + "]' class='form-control' account-id='" + input_field.attr('account-id') + "' onkeyup='bulk_transfer_populateRowAmount($(this))' type='number' value='0'>  </td><td onClick='bulk_transfer_remove_item(" + input_field.attr('account-id') + ");'><i class='fa fa-close'></i></td></tr>");


            }
        </script>


</body>

</html>