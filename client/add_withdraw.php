<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('add_withdraw')) {
    return $permissions->isNotPermitted(true);
}
include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $amount = amount_to_integer($_POST['amount']);
    $cash_bal = amount_to_integer($_POST['cash_bal']);
    if (($amount > $cash_bal) && $_POST['pay_method'] == 'cash') {
        setSessionMessage(false, "You've Insufficient funds in your selected cash account to offer this withdraw! Please get a cash assignment from your supervisor to continue with this transaction.");
        // RedirectCurrent();
        echo '<script>alert("You\'ve Insufficient funds in your selected cash account to offer this withdraw! Please get a cash assignment from your supervisor to continue with this transaction.");</script>';
        // exit();
        // header('location:withdraw_search_client.php');
    } else {
        $res = $response->createWithdraw($_POST['client'], $amount, $_POST['comment'], $_POST['depositor_name'], $_POST['record_date'], $_POST['pay_method'], $_POST['bank_acc'], $_POST['cheque_no'], $_POST['cash_acc'], $_POST['send_sms'], $_POST['branch'], $user[0]['userId'], $_POST['make_charges'], $_POST['is_verified'], $_POST['depositor_phone']);
        if ($res['success']) {
            // header('location: receipt?id='.$res['message'].'&type=W');
            // setSessionMessage(true, 'Withdraw Trxn Created Successfully!');
            setSessionMessageWithConfirm(true, 'Withdraw Trxn Created Successfully!', $res['message'], 'W');

            // exit;

            // header('location:all_withdraws');
            // exit;
        } else {
            setSessionMessage(false, 'Something went wrong! Try again to withdraw.');
            header('location:withdraw_search_client.php');
            exit;
        }
    }
}
$member = [];
$client_id = @$_GET['t'] = parsed_id(@$_GET['t']);
if (isset($_GET['t'])) {
    $member = $response->getClientDetails($_GET['t'])[0];
}

$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
// $cash_accounts = $response->getAllCashAccounts($user[0]['bankId'],$user[0]['branchId']);
$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);

$title = ' WITHDRAW FORM';
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
                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="all_deposits.php">Savings</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Create Deposit</a></li>
                    </ol>
                </div> -->
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
                                    <a href="withdraw_search_client.php" class="btn btn-primary light btn-xs mb-1 "><i class="fa fa-arrow-left"></i> Back</a> | Savings Withdraw Form
                                </h4>

                                <p class="text-muted mb-3"><?= $is_inter_branch; ?></p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <form method="post" class="submit_with_ajax">
                                            <input type="hidden" name="client" value="<?= $member['userId']; ?>">

                                            <input type="hidden" name="is_verified" value="<?= @$_GET['verify'] ?? 0; ?>">

                                            <input type="hidden" name="cash_bal" value="0" id="selected_cash_bal">

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
                                            ?>

                                            <div class="form-group">
                                                <label for="projectName">Withdraw Amount :</label>
                                                <input type="text" value="0" name="amount" min="0" class="form-control comma_separated" required data-type="amount" data-max="<?= max($member['acc_balance'] - $member['min_balance'], 0) ?>">
                                            </div>
                                            <!--end form-group-->
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-6 col-6 mb-2 mb-lg-0">
                                                        <label>Trxn Date</label>
                                                        <input type="date" name="record_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-lg-6 col-6">
                                                        <label>Withdraw Method</label>
                                                        <select id="payment_methods" name="pay_method" class="form-control" required="">
                                                            <option value="cash" selected>Cash</option>
                                                            <option value="cheque" >Cheque/Bank/Mobile Money
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
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
                                            </div>

                                            <div class="form-group" id="insert_cheque_no" style="display: none;">
                                                <label>Cheque No: </label>
                                                <input id="cheque_no" name="cheque_no" class="form-control">
                                            </div>


                                            <div class="form-group" id="dest_cash_acc" style="display: none;">
                                                <label>Affected Cash Account:</label>
                                                <select id="cash_acc" name="cash_acc" class="form-control">
                                                    <?php
                                                    if ($_SESSION['user']['bankId']) {
                                                        foreach ($cash_accounts as $cash_account) {
                                                    ?>
                                                            <option value="<?= $cash_account['cid'] ?>" data-bal="<?= $cash_account['balance'] ?? 0 ?>">
                                                                <?= $cash_account['acname'] ?> Balance: <?= number_format($cash_account['balance']) ?>
                                                            </option>
                                                            <?php }
                                                    } else {
                                                        foreach ($cash_accounts as $c_acc) {
                                                            if ($c_acc['userid'] == $user[0]['userId']) { ?>
                                                                <option value="<?= $c_acc['cid'] ?>" data-bal="<?= $c_acc['balance'] ?? 0 ?>">
                                                                    <?= $c_acc['acname'] ?> Balance: <?= number_format($c_acc['balance']) ?>
                                                                </option>
                                                    <?php }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group account_no_insert">
                                                <label>Withdrawer Name <i>*</i>: </label>
                                                <input id="depositor_name" type="text" value="<?= $member['name'] ?>" name="depositor_name" required class="form-control">
                                            </div>
                                            <div class="form-group account_no_insert">
                                                <label>Withdrawer Contact <i>*</i>: </label>
                                                <input id="depositor_phone" type="text" value="<?= @$member['primaryCellPhone'] ?>" name="depositor_phone" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" rows="5" name="comment" placeholder="write here.."></textarea>
                                            </div>

                                            <br />

                                            <div class="mb-3">
                                                <div class="form-check custom-checkbox mb-3">
                                                    <input type="checkbox" class="form-check-input" id="customCheckBox1" name="make_charges" checked>
                                                    <label class="form-check-label" for="customCheckBox1">Make Withdraw
                                                        Charge if it's set</label>
                                                    <p class="text-muted mb-3">If un-checked system won't attempt to charge
                                                        the transaction, un check if authorized by Admin</p>
                                                </div>
                                            </div>
                                            <input type="hidden" class="form-control" id="withdraw_sms" name="send_sms" value="1">
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
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm">Process
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
                                                        <h3 class="mt-0"><?= number_format(max($member['acc_balance'] - $member['min_balance'], 0)) ?>
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
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Over-Draft</span>
                                                        <h3 class="mt-0"><?= number_format($member['over_draft']); ?></h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Min. A/C Balance</span>
                                                        <h3 class="mt-0"><?= number_format($member['min_balance']); ?></h3>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span class="text-muted">Fixed Deposits</span>
                                                        <h3 class="mt-0"><a class="text-primary" href=""><?= number_format($member['fixed']); ?></a></h3>
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
                                                    <a href="<?= 'client_profile_page.php?id=' . $member['userId']; ?>" class="btn btn-primary light btn-xs mb-1">View Client's
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
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script>
            $(document).ready(function() {
                // Watch for changes in the select element
                $('#cash_acc').on('change', function() {
                    // Get the selected option using jQuery
                    var selectedOption = $(this).find('option:selected');

                    // Retrieve the data-bal attribute using jQuery
                    var dataBalValue = selectedOption.attr("data-bal");

                    // Set the value of the input using jQuery
                    $('#selected_cash_bal').val(dataBalValue);
                });

                // Trigger the change event to handle the default selected option on page load
                $('#cash_acc').trigger('change');
            });

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
        </script>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
        <script type="text/javascript">
            $(document).ready(function() {

                pay_method_change();
                // var dest_cash_acc2 = $('#cash_acc');
                // var selectedOption = dest_cash_acc2.options[dest_cash_acc2.selectedIndex];
                // var dataBalValue = selectedOption.attr("data-bal");
                // $('#selected_cash_bal').val(dataBalValue);



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