<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('share_purchase')) {
    return $permissions->isNotPermitted(true);
}
$title = 'SHARE PURCHASE';
require_once('includes/head_tag.php');
$response = new Response();

if (isset($_POST['submit'])) {
    // $amount = str_replace(",", "", $_POST['amount']);
    $res = $response->purchaseShares($_POST);
    if ($res) {
        setSessionMessage(true, 'Shares Purchased Successfully!');
        header('location:share_purchase_trxns.php');
        // exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to purchase shares.');
        header('location:share_purchase.php');
        // exit;
    }
}
$member = [];
if (isset($_GET['t'])) {
    $member = $response->getClientDetails($_GET['t'])[0];
}

$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);
$mobile_accounts = $response->getAllBankMobileAccounts($user[0]['bankId'], $user[0]['branchId']);
$share_details = $response->getBankShareValue($user[0]['bankId'], $user[0]['branchId']);
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
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Shares Purchase Form
                                </h4>

                                <p class="text-muted mb-3"></p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <form method="post" class="submit_with_ajax">
                                            <input type="hidden" name="client" value="<?= $_GET['t']; ?>">
                                            <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>">

                                            <?php
                                            if (!$user[0]['branchId']) {
                                                $branches = $response->getBankBranches($user[0]['bankId']);

                                                echo '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Branch *</label>
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
                                            ?><br />

                                            <div class="form-group">
                                                <label for="projectName">Current Share Value :</label>
                                                <input type="text" value="<?php echo $share_details[0]['value'] ?? 0 ?>" name="share_value" id="amount" min="0" class="form-control" required data-type="amount">
                                            </div><br />

                                            <div class="form-group">
                                                <label for="projectName">Enter Share Amount :</label>
                                                <input type="text" value="0" name="amount" id="amount" min="0" class="form-control" required data-type="amount">
                                            </div><br />
                                            <!--end form-group-->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-6 col-6 mb-2 mb-lg-0">
                                                        <label>Trxn Date</label>
                                                        <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6 col-6">
                                                        <label>Payment Method</label>
                                                        <select id="payment_methods" name="pay_method" class="form-control" required="">
                                                            <option value="cash" selected>Cash</option>
                                                            <option value="cheque">Cheque/Bank</option>
                                                            <option value="mobile">Mobile Money</option>
                                                            <option value="savings">Offset from Customer's Savings</option>
                                                        </select>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div><br />
                                            <!--end form-group-->

                                            <div class="form-group" id="dest_bank" style="display: none;">
                                                <label>Affected Bank Account:</label>
                                                <select id="bank_acc" name="bank_acc" class="form-control">

                                                    <?php
                                                    if ($bank_accounts) {

                                                        foreach ($bank_accounts as $b_acc) {
                                                            echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Bank: ' . $b_acc['bank_name'] . '  Balance: ' . number_format($b_acc['balance']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div><br />
                                            <div class="form-group" id="dest_mobile" style="display: none;">
                                                <label>Affected Mobile Money Account:</label>
                                                <select id="mobile_acc" name="mobile_acc" class="form-control">

                                                    <?php
                                                    if ($mobile_accounts) {

                                                        foreach ($mobile_accounts as $b_acc) {
                                                            echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . '  Balance:  ' . number_format($b_acc['balance']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div><br />

                                            <div class="form-group" id="insert_cheque_no" style="display: none;">
                                                <label>Cheque No: </label>
                                                <input id="cheque_no" name="cheque_no" class="form-control">
                                            </div>


                                            <div class="form-group" id="dest_cash_acc" style="display: none;">
                                                <label>Affected Cash Account: </label>
                                                <select id="cash_acc" name="cash_acc" class="form-control">


                                                    <?php
                                                    if ($_SESSION['user']['bankId']) {
                                                        foreach ($cash_accounts as $cash_account) {
                                                    ?>
                                                            <option value="<?= $cash_account['cid'] ?>">
                                                                <?= $cash_account['acname'] ?> Balance: <?= number_format($cash_account['balance']) ?>
                                                            </option>
                                                    <?php }
                                                    } else {
                                                        foreach ($cash_accounts as $c_acc) {
                                                            if ($c_acc['userid'] == $user[0]['userId']) {
                                                                echo '<option value="' . $c_acc['cid'] . '"> ' . $c_acc['acname'] . ' Balance:  ' . number_format($c_acc['balance']) . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div><br />



                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" rows="5" name="comment" placeholder="write here.."></textarea>
                                            </div>

                                            <!-- <br /> -->
                                            <!-- <div class="mb-3">
                                                <div class="form-check custom-checkbox mb-3">
                                                    <input type="checkbox" class="form-check-input" id="customCheckBox1" name="send_sms" checked>
                                                    <label class="form-check-label" for="customCheckBox1">Send SMS to
                                                        Client</label>
                                                    <p class="text-muted mb-3">If un-checked system won't attempt to
                                                        send an sms</p>
                                                </div>
                                            </div> -->
                                            <br /><br />

                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit">Process
                                                Transaction</button>
                                            <!--end form-->
                                    </div>
                                    <!--end col-->

                                    <div class="col-lg-6 align-self-center">

                                        <!-- <div class="card">
                                            <div class="card-body">
                                                <div class="text-center">
                                                    <div class="met-profile-main-pic">
                                                        <img src="<?= is_null($member['image']) ? 'icons/favicon.png' : $member['image'] ?>" onerror="this.onerror=null; this.src='icons/favicon.png'" alt="" height="100" width="100" class="rounded-circle">
                                                    </div>

                                                    <div class="">
                                                        <h5 class="mb-0"><?= $member['name'] ?></h5>
                                                        <small class="text-muted">A/C No: <?= $member['accno']; ?>
                                                            | CLIENT TYPE : <?= ($member['actype']); ?></small>
                                                    </div>
                                                    <div class="mb-3 pricingTable1">

                                                        <hr class="hr-dashed">

                                                        <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                                            <li><b>A/C TYPE: </b><?= $member['savingaccount']; ?> | <b>A/C BAL: </b><?= number_format($member['acc_balance'] + $member['loan_wallet']); ?>
                                                            </li><br />

                                                            <li><b><?= $member['shares'] ?> Shares | Share Amount: <?= number_format($member['shareamount']) ?> </li><br />
                                                        </ul>
                                                    </div>
                                                    <a href="<?= 'client_profile_page.php?id=' . ($_GET['t'] ?? ''); ?>" class="btn btn-sm btn-primary load_supplement_ajax">View
                                                        Profile</a>
                                                </div>
                                            </div>
                                        
                                        </div> -->


                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

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

        <script>
            $(document).ready(function() {
                $("input[data-type='amount']").keyup(function(event) {
                    // skip for arrow keys
                    if (event.which >= 37 && event.which <= 40) {
                        event.preventDefault();
                    }
                    var $this = $(this);
                    var num = $this.val().replace(/,/gi, "");
                    var num2 = num.split(/(?=(?:\d{3})+$)/).join(",");
                    // console.log(num2);
                    // the following line has been simplified. Revision history contains original.
                    $this.val(num2);
                });
            });

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

            // $('body').on('submit', 'form', function(event) {
            //     $(this).find('.btn-submit').text('Processing...').prop('disabled',true);
            //     return true;
            // });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                pay_method_change();
            });
        </script>
        <script>
            function pay_method_change() {
                var pay_method = $('#payment_methods');
                var dest_bank = $('#dest_bank');
                var dest_mobile = $('#dest_mobile');
                var insert_cheque_no = $('#insert_cheque_no');
                var dest_cash_acc = $('#dest_cash_acc');
                var offset_savings = $('#offset_savings');
                var payable_account = $('#payable_account');


                if (pay_method.val() == 'cash' || pay_method.val() == 'dr_cash' || pay_method.val() == 'cr_cash') {
                    dest_bank.hide();
                    dest_mobile.hide();
                    payable_account.hide();
                    insert_cheque_no.hide();
                    offset_savings.hide();
                    dest_cash_acc.show();
                } else if (pay_method.val() == 'cheque' || pay_method.val() == 'dr_cheque' || pay_method.val() ==
                    'cr_cheque') {
                    dest_cash_acc.hide();
                    dest_mobile.hide();
                    payable_account.hide();
                    offset_savings.hide();
                    dest_bank.show();
                    insert_cheque_no.show();
                } else if (pay_method.val() == 'offset' || pay_method.val() == 'credit') {
                    dest_bank.hide();
                    dest_mobile.hide();

                    payable_account.hide();
                    insert_cheque_no.hide();
                    dest_cash_acc.hide();
                    offset_savings.show();
                } else if (pay_method.val() == 'mobile') {
                    dest_bank.hide();
                    dest_mobile.show();
                    payable_account.hide();
                    insert_cheque_no.hide();
                    offset_savings.hide();
                    dest_cash_acc.hide();
                } else {
                    dest_cash_acc.hide();
                    payable_account.hide();
                    dest_bank.hide();
                    offset_savings.hide();
                    insert_cheque_no.hide();
                }

                pay_method.on('change', function() {
                    if (pay_method.val() == 'cash' || pay_method.val() == 'dr_cash' || pay_method.val() ==
                        'cr_cash') {
                        dest_bank.hide();
                        payable_account.hide();
                        insert_cheque_no.hide();
                        offset_savings.hide();
                        dest_cash_acc.show();
                    } else if (pay_method.val() == 'cheque' || pay_method.val() == 'dr_cheque' || pay_method
                        .val() == 'cr_cheque') {

                        dest_cash_acc.hide();
                        payable_account.hide();
                        offset_savings.hide();
                        dest_bank.show();
                        insert_cheque_no.show();
                    } else if (pay_method.val() == 'offset' || pay_method.val() == 'credit') {
                        dest_bank.hide();
                        payable_account.hide();
                        insert_cheque_no.hide();
                        dest_cash_acc.hide();
                        offset_savings.show();
                    } else if (pay_method.val() == 'on_credit') {
                        dest_bank.hide();
                        payable_account.show();
                        insert_cheque_no.hide();
                        dest_cash_acc.hide();
                        offset_savings.hide();
                    } else {
                        dest_cash_acc.hide();
                        payable_account.hide();
                        dest_bank.hide();
                        offset_savings.hide();
                        insert_cheque_no.hide();
                    }

                });
            }
            // -------------end --------------
        </script>

</body>

</html>