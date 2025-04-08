<?php
include('../backend/config/session.php');




require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankAdmin()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php
require_once('includes/response.php');
$response = new Response();


if (isset($_POST['submit'])) {
    $body = $_POST['body'];
    $charge_to = $_POST['charged_to'];
    $charge_on = $_POST['charged_on'];
    $charge = $_POST['charge'];
    $name = $_POST['name'];

    $res = $response->addSMSType($body, $charge, $charge_to, $charge_on, $name, $user[0]['userId'], $user[0]['bankId']);

    if ($res) {
        setSessionMessage(true, 'SMS Type Created Successfully!');
        header('location:sms_types.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:sms_type_settings.php');
        exit;
    }
}
$title = 'CREATE SMS TYPE';
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




                <div class="row">

                    <div class="col-xl-12 col-lg-12 col-xxl-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> |

                                    SMS Type Form

                                </h4>


                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">

                                <div class="row">

                                    <div class="col-md-6">
                                        <form method="post">

                                            <!-- <div class="col-md-6"> -->
                                            <div class="form-group">
                                                <label class=" control-label"> SMS sent On </label>
                                                <select name="charged_on" id="ocategory" class="form-control" required>
                                                    <option value="" selected> Select.....</option>
                                                    <option value="on_deposit">On Deposit</option>
                                                    <option value="on_deposit_agent">On Agent Deposit</option>
                                                    <option value="on_withdraw">On Withdraw</option>
                                                    <option value="account_opening">On Account Opening</option>
                                                    <option value="on_subscribe_school_pay">On School Pay Subscription</option>
                                                    <option value="account_act">On Account Activation</option>
                                                    <option value="account_transfer">On Cash Transfer</option>
                                                    <option value="account_deact">On Account De-activation</option>
                                                    <option value="loan_apply">On Loan Application</option>
                                                    <option value="loan_approval">On Loan Approval</option>
                                                    <option value="loan_decline">On Loan Decline</option>
                                                    <option value="loan_disburse">On Loan Disbursement</option>
                                                    <option value="loan_repay">On Loan Repayment</option>
                                                    <option value="loan_repay_reminder">Loan Repayment Reminder</option>
                                                    <option value="loan_topup">On Loan Top-Up</option>
                                                    <option value="loan_reschedule">On Loan Reschedule</option>
                                                    <option value="int_waive">On Loan Interest Waive</option>
                                                    <option value="pen_waive">On Loan Penalty Waive</option>
                                                    <option value="share_buy">On Share Purchase</option>
                                                    <option value="share_transfer">On Share Transfer</option>
                                                    <option value="share_divid">On Share Dividends Deposit</option>
                                                    <option value="fixed_int">Fixed Savings Interest Accrued</option>
                                                    <option value="guarantor">Loan Guarantor Reminder</option>
                                                    <option value="collateral_receive">On Collateral Receive</option>
                                                    <option value="collateral_release">On Collateral Release</option>
                                                    <option value="birth_day">Birth Day Wishes</option>
                                                    <option value="on_mobile_banking_pin_set">Mobile Banking Subscription</option>
                                                    <option value="on_mobile_banking_pin_reset">Mobile Banking mPIN Reset</option>
                                                    <option value="on_internet_banking_pin_set">Internet Banking Subscription</option>
                                                    <option value="on_internet_banking_pin_reset">Internet Banking mPIN Reset</option>
                                                    <option value="on_deposit_fees_school">School Fees Payment - Institution SMS</option>
                                                    <option value="on_deposit_fees_parent">School Fees Payment - Parent/Depositor SMS</option>
                                                    <option value="share_dividends">Share Dividends SMS</option>
                                                    <option value="interest_on_savings">Interest On Savings SMS</option>
                                                    <option value="ussd_deposit">USSD Deposit SMS</option>
                                                    <option value="app_deposit">Mobile App Deposit SMS</option>
                                                    <option value="ussd_withdraw">USSD Withdraw SMS</option>
                                                    <option value="app_withdraw">Mobile App Withdraw SMS</option>
                                                    <option value="one_to_due_date">Loan Reminder (1 day to Due Date) SMS</option>
                                                    <option value="week_to_due_date">Loan Reminder (7 days to Due Date) SMS</option>
                                                    <option value="week_arrears">Loan Reminder (7 days in Arrears) SMS</option>
                                                    <option value="month_arrears">Loan Reminder (30 days in Arrears) SMS</option>



                                                </select>
                                            </div> <br />
                                            <div class="form-group">
                                                <label class=" control-label">SMS Template Body </label>

                                                <p class="text-danger">Use the SMS Body Tags on the left for dynamic texts</p>

                                                <textarea col="5" class="form-control" name="body" required minlength="5" maxlength="150"></textarea>

                                            </div>
                                            <!-- </div> -->
                                            <br />
                                            <!-- <div class="col-md-4"> -->

                                            <div class="form-group">
                                                <label class=" control-label">SMS Charge: </label>
                                                <input type="number" step="any" class="form-control" name="charge" value="0" required>
                                            </div><br />

                                            <div class="form-group">
                                                <label class=" control-label"> Charged To </label>
                                                <select class="form-control" name="charged_to" required>
                                                    <option value=""> Select.....</option>
                                                    <option value="institution" selected="">Charge Institution</option>
                                                    <option value="client">Charge Client</option>

                                                </select>
                                            </div><br />

                                            <div class="form-group">
                                                <label class=" control-label">Notes / SMS Name (e.g Account Opening SMS): </label>
                                                <input type="text" class="form-control" name="name" value="" required>
                                            </div>


                                            <!-- </div> -->

                                            <br />
                                            <br />
                                            <button type="submit" class="btn btn-block btn-primary" name="submit"><span class="semibold">Save Changes</span></button>
                                        </form>
                                    </div>

                                    <div class="col-lg-6 align-self-center">
                                        <div class="card">
                                            <div class="card-body btc-price">

                                                <h4 class="mt-0 header-title">SMS Body Tags</h4>
                                                <p class="text-muted mb-3">Type the Tag exactly as it appears here</p>

                                                <div class="row">

                                                    <p class="text-muted mb-3">Use <b>[institution]</b> - Institution's Trade Name; <b>[instphone]</b> - Institution's Phone Number; <b>[instemail]</b> - Institution's Email, <b>[branch]</b> - Branch Name ; <b>[fname]</b> - Client's First Name ; <b>[lname]</b> - Client's Last Name ; <b>[othername]</b> - Client's Other Names ; <b>[cphone]</b> - Client's Primary Contact ; <b>[acno]</b> - A/C No. ; <b>[actype]</b> - Savings Product Name; <b>[balance]</b> - Loan's Wallet / Savings Balance; <b>[amount]</b> - Trxn Amount; <b>[pay_method]</b> - Trxn Payment Method; <b>[share_amount]</b> - Share Amount; <b>[shares]</b> - No. of Shares; <b>[share_val]</b> - Current Share Value; <b>[collateralname]</b> - Collateral Name; <b>[lpname]</b> - Loan Product Name; <b>[guarantor]</b> - Guarantor's Names & A/c No.; <b>[mpin]</b> - Mobile Banking mPIN; <b>[sname]</b> - Student's Name; <b>[sno]</b> - Student's No.;<b>[sclass]</b> - Student's Class; <b>[parent]</b> - Student's Parent / Guardian Names;<b>[parent_phone]</b> - Student's Parent / Guardian Phone;</p>

                                                    <hr class="hr-dashed">
                                                    <p class="text-muted mb-3">Use <b>[requested_amount]</b> - Requested Loan Amount; <b>[approved_amount]</b> - Amount Approved; <b>[int_paid]</b> - Interest Paid; <b>[princ_paid]</b> - Principal Paid; <b>[lno]</b> - Loan No.; <b>[penalty]</b> - Penalty Balance; <b>[loan_bal]</b> - Loan Balance; <b>[date_created]</b> - Record Date; <b>[int_bal]</b> - Interest Balance; <b>[princ_bal]</b> - Principal Balance; <b>[interestbalance]</b> - Interest Balance; <b>[princbalance]</b> - Principal Balance; <b>[duration]</b> - Loan Duration; <b>[disbursemode]</b> - Disbursement Mode; <b>[startdate]</b> - Start Loan Repayment Date; </p>

                                                    <p class="text-danger">**** Contact Technical Team for any clarifications or additions ****</p>

                                                </div>

                                            </div>
                                        </div>



                                    </div>
                                </div>

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
    <?php include('includes/bottom_scripts.php'); ?>



</body>

</html>