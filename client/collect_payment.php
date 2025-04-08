<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('create_loan_repay')) {
    return $permissions->isNotPermitted(true);
}


include_once('includes/functions.php');
include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $amount = amount_to_integer($_POST['amount']);
    $ac_bal = amount_to_integer($_POST['ac_bal']);
    if ($amount <= 0) {
        setSessionMessage(false, "Amount can not be less than 0");
        // header('location: loan_details_page?id=' . $_POST['lno']);
        RedirectCurrent();
    } else if (($ac_bal < $amount) && $_POST['pay_method'] == 'saving') {

        setSessionMessage(false, "Insufficient funds on savings account");
        // header('location: loan_details_page?id=' . $_POST['lno']);
        RedirectCurrent();
    } else {
        $res = $response->createLoanRepay($_POST);
        // var_dump($res);
        // exit;
        if ($res['success']) {
            setSessionMessage(true, "Loan Repayment & Update Completed Successfully!");
            header('location: loan_details_page.php?id=' . $_POST['lno']);
        } else {
            setSessionMessage(false, $res['message']);
            RedirectCurrent();
        }
    }
    exit;
}
$title = 'LOAN PAYMENT';
include('includes/head_tag.php');

$loan_id = $_GET['id'];
$selected_loan = @$response->getLoanDetails($loan_id)[0];

$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
// $cash_accounts = $response->getAllCashAccounts($user[0]['bankId'],$user[0]['branchId']);
$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);

$total_loan_balance = (int)@$selected_loan['loan']['current_balance'] + (int)@$selected_loan['loan']['penalty_balance'];
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Loans</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Manual Repayment</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Manual Loan Repayment Form
                                </h4>

                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    Using the form below, you can add a loan Repayment to a customer's loan portifolio
                                </p>

                                <hr class="hr-dashed">

                                <div class="btc-price">
                                    <p class="text-muted mb-3">Loan Summary</p>
                                    <input type="hidden" name="officer" value="<?= @$loan_id ?>">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <span class="text-muted">Loan No</span>
                                            <h6 class="mt-0">
                                                <?= @$selected_loan['loan']['loan_no'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Customer</span>
                                            <h6 class="mt-0">
                                                <?= @$selected_loan['client']['firstName'] . ' ' . @$selected_loan['client']['lastName'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">A/C No.</span>
                                            <h6 class="mt-0">
                                                <?= @$selected_loan['client']['membership_no'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">A/C - Wallet - Balance (UGX)</span>
                                            <h6 class="mt-0">
                                                <?= number_format(@$selected_loan['client']['acc_balance'] + @$selected_loan['client']['loan_wallet']) ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Loan Balance (UGX)</span>
                                            <h6 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['current_balance']) ?>
                                            </h6>
                                        </div>

                                        <div class="col-lg-2 text-danger">
                                            <span>Total Due (UGX)</span>
                                            <h6 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['principal_due'] + $selected_loan['loan']['interest_due']) ?>
                                            </h6>
                                        </div>

                                    </div>

                                    <hr class="hr-dashed">

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <span class="text-muted">Outstanding Interest (UGX)</span>
                                            <h6 class="mt-0"><?= number_format(@$selected_loan['loan']['interest_balance']??0) ?></h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Interest Due (UGX)</span>
                                            <h6 class="mt-0"><?= number_format(@$selected_loan['loan']['interest_due']??0) ?></h6>
                                        </div>
                                        <div class="col-lg-3">
                                            <span class="text-muted">Outstanding Principal (UGX)</span>
                                            <h6 class="mt-0"><?= number_format(@$selected_loan['loan']['principal_balance']??0) ?></h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Principal Due (UGX)</span>
                                            <h6 class="mt-0"><?= number_format(@$selected_loan['loan']['interest_due']??0) ?></h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Outstanding Penalty (UGX)</span>
                                            <h6 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['penalty_balance']??0) ?></h6>
                                        </div>
                                    </div>
                                    <hr class="hr-dashed">
                                </div><br />

                                <?php if ($total_loan_balance > 0) { ?>
                                    <div class="basic-form">
                                        <form method="POST" id="LoanPaymentForm">


                                            <input type="hidden" class="form-control" name="uid" value="<?= @$selected_loan['client']['userId'] ?>">
                                            <input type="hidden" class="form-control" name="branch" value="<?= @$selected_loan['client']['branchId'] ?>">
                                            <input type="hidden" class="form-control" name="lno" value="<?php echo $_GET['id']; ?>">
                                            <input type="hidden" class="form-control" name="auth_id" value="<?php echo $user[0]['userId']; ?>">
                                            <input type="hidden" class="form-control" name="bank_id" value="<?php echo $user[0]['bankId']; ?>">
                                            <input type="hidden" class="form-control" name="branch_id" value="<?php echo $user[0]['branchId']; ?>">
                                            <input type="hidden" id="actual_loan_balance" value="<?= @$selected_loan['loan']['current_balance'] ?>">

                                            <input type="hidden" class="form-control" name="ac_bal" value="<?= number_format(@$selected_loan['client']['acc_balance'] + @$selected_loan['client']['loan_wallet']) ?>">

                                            <div class="mb-3">
                                                <label class="col-form-label pt-0" for="amount"> Amount (in
                                                    Ugx)</label><br>

                                                <input type="text" class="form-control comma_separated" name="amount" value="<?= @$selected_loan['loan']['principal_due'] + @$selected_loan['loan']['interest_due'] ?>" id="amount" data-max="<?= @$total_loan_balance ?>" required>
                                            </div>

                                            <?php if ($selected_loan['loan']['penalty_balance']) { ?>
                                                <div class="mb-3">
                                                    <div class="form-check custom-checkbox mb-3">
                                                        <input type="checkbox" class="form-check-input activate-sections" id="clear_penalty" name="clear_penalty" value="1" data-sections="penalty-amount">
                                                        <label class="form-check-label" for="clear_penalty">Clear Penalty</label>
                                                        <div class="text-muted">
                                                            <em> <small> <strong> Note: </strong> The system will priotize clearance of <strong> Penalty First </strong> </small> </em>
                                                        </div>
                                                    </div>

                                                    <div class="section-penalty-amount hide">

                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control comma_separated" name="penalty_amount" data-is-required="1" data-max="<?= $selected_loan['loan']['penalty_balance'] ?>" placeholder=" ">
                                                            <label for="amount">Penalty Amount</label>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <!-- <div class="mb-3">
                                            <label class="col-form-label pt-0" for="interest"> Interest (in
                                                Ugx)</label><br>
                                            <input type="tex" class="form-control" name="interest" value="" id="interest" max="" required data-type="amount">

                                        </div> -->

                                                <div class="mb-3">
                                                    <label class="col-form-label pt-0" for="balance">New Outstanding Balance (in
                                                        Ugx)</label><br>
                                                    <input type="text" class="form-control" value="<?= number_format(@$selected_loan['loan']['current_balance']) ?>" id="balance" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="col-form-label pt-0" for="date">Collection Date</label><br>
                                                    <input type="date" class="form-control" name="collection_date" value="<?php echo date('Y-m-d'); ?>">
                                                </div>

                                                <div class="mb-3">
                                                    <label>Payment Method</label>
                                                    <select id="payment_methods" name="pay_method" class="form-control" required="">
                                                        <option value="saving" selected>Deduct from Savings</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="cheque">Cheque/Bank/Mobile Money
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="form-group" id="dest_bank" style="display: none;">
                                                    <label>Affected Bank Account:</label>
                                                    <select id="bank_acc" name="bank_acc" class="form-control">

                                                        <?php
                                                        if ($bank_accounts) {

                                                            foreach ($bank_accounts as $b_acc) {
                                                                echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Bank: ' . $b_acc['bank_name'] . '</option>';
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
                                                                <option value="<?= $cash_account['cid'] ?>">
                                                                    <?= $cash_account['acname'] ?>
                                                                </option>
                                                        <?php }
                                                        } else {
                                                            foreach ($cash_accounts as $c_acc) {
                                                                if ($c_acc['userid'] == $user[0]['userId']) {
                                                                    echo '<option value="' . $c_acc['cid'] . '"> ' . $c_acc['acname'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="col-form-label pt-0" for="notes">Notes</label><br>
                                                    <input type="text" class="form-control" name="notes" value="">
                                                </div>
                                                <br /><br />
                                                <?php if ($permissions->hasSubPermissions('waive_interest')) { ?>
                                                    <div class="mb-3">
                                                        <div class="form-check custom-checkbox mb-3">
                                                            <input type="checkbox" class="form-check-input" id="clear_loan" name="clear_loan" value="1">
                                                            <label class="form-check-label" for="clear_loan">Mark this Loan as Closed after this Payment</label>
                                                            <div class="text-muted mb-3">
                                                                <em> <small> <strong> Note: </strong> If this payment is meant to clear off the loan by foregoing interest that is not yet due, then an approval request shall be sent to the <strong>entitled staff to complete this transaction </strong> </small> </em>
                                                            </div>

                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <div class="mb-3">
                                                    <div class="form-check custom-checkbox mb-3">
                                                        <input type="checkbox" class="form-check-input" id="customCheckBox1" name="send_sms" checked>
                                                        <label class="form-check-label" for="customCheckBox1">Send SMS to
                                                            Client</label>
                                                        <div class="text-muted mb-3">
                                                            <em> <small> <strong> Note: </strong> If un-checked system won't attempt to
                                                                    send an<strong> SMS </strong> </small> </em>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- <div class="mb-3">
                                            <label for="arrears">
                                                <input type="checkbox" name="is_arrears" value="arrears"
                                                    id="is_arrears"> Loan Arrears Payment
                                            </label>
                                            <br>
                                            <em> <small> <strong> Note: </strong> Check if payment is intended to clear
                                                    <strong> </strong> Loan Arrears </small> </em>
                                        </div> -->
                                                <br /><br /><br />
                                                <!-- <div class="mb-3"> -->

                                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                                <!-- </div> -->

                                        </form>
                                    </div>
                                <?php } ?>
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
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>

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
        </script>
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
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>
        <script>
            $('body').on('keyup', 'input#amount', function() {
                let form = $("#LoanPaymentForm");
                let actual_loan_balance = $("#actual_loan_balance").val() || 0
                let amount = $("#amount").val() || 0
                // let interest = $("#interest").val() || 0
                let interest = 0

                amount = amount.replace(/\,/g, ''); // 1125, but a string, so convert it to number
                amount = parseInt(amount, 10);

                // interest = interest.replace(/\,/g, ''); // 1125, but a string, so convert it to number
                interest = parseInt(interest, 10);

                let loan_balance = actual_loan_balance - amount - interest;

                loan_balance = loan_balance.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $("#balance").val(loan_balance);

            })
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