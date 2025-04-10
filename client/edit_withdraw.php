<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('update_withdraw')) {
    return $permissions->isNotPermitted(true);
}
$title = 'EDIT WITHDRAW';
require_once('includes/head_tag.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $amount = str_replace(",", "", $_POST['amount']);
    $orig_amount = str_replace(",", "", $_POST['orig_amount']);
    $send_sms = $_POST['send_sms'] ?? 0;
    $res = $response->editWithdraw($_POST['client'], $amount, $_POST['comment'], $_POST['depositor_name'], $_POST['record_date'], $_POST['pay_method'], $_POST['bank_acc'], $_POST['cheque_no'], $_POST['cash_acc'], $send_sms, $_POST['branch'], $user[0]['userId'], $_POST['tid'], $orig_amount, $_POST['orig_acid']);
    if ($res) {
        setSessionMessage(true, 'Withdraw Updated Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to update withdraw.');
    }
    header('location:all_withdraws');
    exit;
}
$member = [];
$client_id = @$_GET['t'] = parsed_id(@$_GET['t']);
if (isset($_GET['t'])) {
    $member = $response->getClientDetails($_GET['t'])[0];
}
$trxn = $response->getTrxnDetails($_GET['tid'])[0];


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

                <!-- row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                //its a member from another branch
                                $is_inter_branch = $member['branchId'] != $user[0]['branchId'] && $user[0]['bankId'] == '' ? '<button class="btn btn-warning waves-effect waves-light">INTER BRANCH TRANSACTION : ' . $member['branchName'] . '</button>' : '';
                                ?>

                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Withdraw Update Form
                                </h4>

                                <p class="text-muted mb-3"><?= $is_inter_branch; ?></p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <form method="post" class="submit_with_ajax">
                                            <input type="hidden" name="client" value="<?= $member['userId']; ?>">
                                            <input type="hidden" name="tid" value="<?= $trxn['tid']; ?>">
                                            <input type="hidden" name="orig_amount" value="<?= $trxn['amount']; ?>">
                                            <input type="hidden" name="aid" value="<?= $trxn['said']; ?>">
                                            <input type="hidden" name="orig_acid" value="<?= $trxn['acid']; ?>">

                                            <?php

                                            $branches = $response->getBankBranches2($user[0]['bankId'], $user[0]['branchId']);

                                            echo '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                             
                                  ';
                                            if ($branches !== '') {
                                                foreach ($branches as $row) {
                                                    if ($row['id'] == $trxn['branch']) {
                                                        echo '
                              <option value="' . $row['id'] . '" selected>' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                    } else {
                                                        echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                    }
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

                                            ?>

                                            <div class="form-group">
                                                <label for="projectName">Amount :</label>
                                                <input type="text" value="<?= $trxn['amount'] ?>" name="amount" min="0" class="form-control comma_separated" required data-type="amount">
                                            </div>
                                            <!--end form-group-->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-6 col-6 mb-2 mb-lg-0">
                                                        <label>Trxn Date</label>
                                                        <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d', strtotime($trxn['trxn_date'])); ?>" required>
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6 col-6">
                                                        <label>Withdraw Method</label>
                                                        <select id="payment_methods" name="pay_method" class="form-control" required="" readonly disabled>
                                                            <option value="cash" <?= $trxn['method'] == 'cash' ? 'selected' : '' ?>>Cash</option>
                                                            <option value="cheque" <?= $trxn['method'] == 'cheque' ? 'selected' : '' ?>>Cheque/Bank/Mobile Money</option>
                                                        </select>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                            <!--end form-group-->

                                            <div class="form-group" id="dest_bank" style="display: none;">
                                                <label>Affected Bank Account:</label>
                                                <select id="bank_acc" name="bank_acc" class="form-control" readonly disabled>

                                                    <?php
                                                    if ($bank_accounts) {

                                                        foreach ($bank_accounts as $b_acc) {
                                                            if ($trxn['acid'] == $b_acc['cid']) {
                                                                echo '<option value="' . $b_acc['cid'] . '" selected>' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Bank: ' . $b_acc['bank_name'] . ' Balance: ' . number_format($b_acc['balance']) . '</option>';
                                                            } else {
                                                                echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Bank: ' . $b_acc['bank_name'] . ' Balance: ' . number_format($b_acc['balance']) . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group" id="insert_cheque_no" style="display: none;">
                                                <label>Cheque No: </label>
                                                <input id="cheque_no" name="cheque_no" class="form-control" readonly disabled>
                                            </div>


                                            <div class="form-group" id="dest_cash_acc" style="display: none;">
                                                <label>Affected Cash Account: </label>
                                                <select id="cash_acc" name="cash_acc" class="form-control" readonly disabled>

                                                    <?php
                                                    foreach ($cash_accounts as $c_acc) {
                                                        if ($trxn['acid'] == $c_acc['cid']) {

                                                            echo '<option value="' . $c_acc['cid'] . '" selected> ' . $c_acc['acname'] . ' Balance: ' . number_format($c_acc['balance']) . '</option>';
                                                        } else {
                                                            echo '<option value="' . $c_acc['cid'] . '" > ' . $c_acc['acname'] . ' Balance: ' . number_format($c_acc['balance']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group account_no_insert">
                                                <label>Withdrawn by <i>*</i>: </label>
                                                <input id="depositor_name" type="text" value="<?= $member['name'] ?>" name="depositor_name" required="" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" rows="5" name="comment" placeholder="<?= $trxn['notes'] ?>"></textarea>
                                            </div>

                                            <br />
                                            <input type="hidden" class="form-control" id="deposit_sms" name="send_sms" value="1">
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
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit">Update
                                                Transaction</button>
                                            <!--end form-->
                                    </div>
                                    <!--end col-->

                                    <div class="col-lg-6 align-self-center">
                                        <div class="card">
                                            <div class="card-body btc-price">

                                                <h4 class="mt-0 header-title">Account Balance</h4>
                                                <p class="text-muted mb-3">Summary</p>

                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Available Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['acc_balance'] - $member['freezed']) ?>
                                                        </h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Freezed Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['freezed']) ?>
                                                        </h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Actual Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['acc_balance']); ?></h3>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="card">
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
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">SAVING PRODUCT: </span>
                                                                <h6 class="mt-0"><?= $member['savingaccount']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">A/C NAME: </span>
                                                                <h6 class="mt-0"><?= $member['name']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">CURRENCY: </span>
                                                                <h6 class="mt-0"><?= 'UGANDA SHILLINGS'; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">SAVINGS OFFICER: </span>
                                                                <h6 class="mt-0"><?= $member['savings_officer']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">LAST TRANSACTED: </span>
                                                                <h6 class="mt-0"><?= $member['last_transaction']; ?>
                                                                </h6>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span class="text-muted">STATUS: </span>
                                                                <h6 class="mt-0"><?= $member['status']; ?>
                                                                </h6>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <a href="<?= 'client_profile_page?id=' . $member['userId']; ?>" class="btn btn-primary light btn-xs mb-1">View Client's
                                                        Profile</a>
                                                </div>
                                            </div>
                                            <!--end card-body-->
                                        </div>


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
        <?php
        include('includes/bottom_scripts.php');
        ?>

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
                var insert_cheque_no = $('#insert_cheque_no');
                var dest_cash_acc = $('#dest_cash_acc');
                var offset_savings = $('#offset_savings');
                var payable_account = $('#payable_account');


                if (pay_method.val() == 'cash' || pay_method.val() == 'dr_cash' || pay_method.val() == 'cr_cash') {
                    dest_bank.hide();
                    payable_account.hide();
                    insert_cheque_no.hide();
                    offset_savings.hide();
                    dest_cash_acc.show();
                } else if (pay_method.val() == 'cheque' || pay_method.val() == 'dr_cheque' || pay_method.val() ==
                    'cr_cheque') {
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