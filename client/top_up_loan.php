<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('create_loan_topup')) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
    // $lno = @$_POST['lid'];
    // $amount = @$_POST['loan_amount'];
    // $duration = @$_POST['loan_period'];
    // $date = @$_POST['loan_date'];
    // $comments = @$_POST['comment'];
    // $send_sms = @$_POST['send_sms'];
    //   $is_arrears = @$_POST['is_arrears'];
    // exit;
    $_POST['loan_amount'] = (int)str_replace(',', '', $_POST['loan_amount']);
    $res = $response->createLoanTopup($_POST);
    if ($res["success"]) {
        setSessionMessage(true, "Loan Topup Created Successfully!");
        header('Location: loan_details_page.php?id=' . $_POST['loan_id']);
    } else {
        setSessionMessage(false, $res["message"]);
        RedirectCurrent();
    }
    // exit;
}

include('includes/head_tag.php');

$loan_id = $_GET['id'];
$selected_loan = @$response->getLoanDetails($loan_id)[0];

if ($selected_loan['loan']['repay_cycle_id'] == 1) {
    $ftype = 'DAYS';
} else if ($selected_loan['loan']['repay_cycle_id']  == 2) {
    $ftype = 'WEEKS';
} else if ($selected_loan['loan']['repay_cycle_id']  == 3) {
    $ftype = 'MONTHS';
} else if ($selected_loan['loan']['repay_cycle_id']  == 4) {
    $ftype = 'DAYS';
} else if ($selected_loan['loan']['repay_cycle_id']  == 5) {
    $ftype = 'YEARS';
}
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
                        <li class="breadcrumb-item active"><a href="active_loans.php">Loans</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"> Loan Top Up</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">

                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-body">


                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Loan Details
                                </h4>

                                <hr class="hr-dashed">

                                <div class="met-profile">
                                    <ul class="list-unstyled personal-detail">
                                        <li class="mt-2"><i class="ti-user mr-2 text-primary font-22 align-middle"></i> <b>Member's Name </b>: <?= strtoupper(@$selected_loan['client']['firstName'] . ' ' . @$selected_loan['client']['lastName']) ?></li>
                                        <li class="mt-2"><i class="ti-money mr-2 text-primary font-22 align-middle"></i> <b>A/C No </b>: <?= @$selected_loan['client']['membership_no'] ?></li>
                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i> <b>Loan Product </b>: <?= @$selected_loan['product']['type_name'] ?></li>

                                        <li class="mt-2"><i class="ti-user mr-2 text-primary font-22 align-middle"></i> <b>Officer </b>: <?= @$selected_loan['staff']['firstName'] . ' ' . @$selected_loan['staff']['lastName'] . ' - ' . @$selected_loan['staff']['positionTitle'] ?></li>

                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i> <b>Loan Branch </b>: <?= @$selected_loan['branch'] ?></li>
                                    </ul>

                                </div>



                                <hr class="hr-dashed">

                                <h4 class="mt-0 header-title">Loan Summary Details</h4>
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">

                                <div class="btc-price">

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <span class="text-muted">Principal</span>
                                            <h3 class="mt-0"> <?= number_format(@$selected_loan['loan']['principal']) ?></h3>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="text-muted">Interest</span>
                                            <h3 class="mt-0"><?= number_format(@$selected_loan['loan']['interest_amount']) ?></h3>
                                        </div>

                                        <div class="col-lg-4">
                                            <span class="text-muted">Penalty Balance</span>
                                            <h3 class="mt-0"><?= number_format(@$selected_loan['loan']['penalty_balance']) ?></h3>
                                        </div>
                                    </div>

                                    <hr class="hr-dashed">

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <span class="text-muted">Principal Due</span>
                                            <h3 class="mt-0"><?= number_format(@$selected_loan['loan']['principal_balance']) ?></h3>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="text-muted">Interest Due</span>
                                            <h3 class="mt-0"><?= number_format(@$selected_loan['loan']['interest_balance']) ?></h3>
                                        </div>

                                        <div class="col-lg-4">
                                            <span class="text-muted">Total Due</span>
                                            <h3 class="mt-0"> <?= number_format(@$selected_loan['loan']['principal_balance'] + @$selected_loan['loan']['interest_balance']) ?></h3>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">

                        <div class="card">
                            <div class="card-body">

                                <h4 class="mt-0 header-title">Loan Top Up Form</h4>
                                <p class="text-muted mb-3"></p>

                                <?php
                                $total_outstanding = $selected_loan['loan']['principal_balance'] + @$selected_loan['loan']['interest_balance'] + @$selected_loan['loan']['penalty_balance']
                                ?>

                                <?php if ($selected_loan['loan']['status'] == 5) { ?>
                                    <div class="alert alert-warning left-icon-big alert-dismissible fade show">
                                        <div class="media">
                                            <div class="alert-left-icon-big">
                                                <span><i class="mdi mdi-help-circle-outline"></i></span>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="mt-1 mb-2">Alert</h6>
                                                <p class="mb-0">This Loan was Closed</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <form method="post" class="submit_with_ajax">
                                        <div class="row">
                                            <input type="hidden" name="loan_id" value="<?= @$selected_loan['loan']['loan_no'] ?>">
                                            <input type="hidden" name="min_amount" value="<?= $total_outstanding ?>">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Additional Loan Amount: * </label>
                                                    <input type="text" value="<?= number_format(@$total_outstanding) ?>" min="<?= $total_outstanding ?>" data-min="<?= $total_outstanding ?>" name="loan_amount" class="form-control comma_separated amount_big_field_" required="">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="text-label form-label">Requested Repayment Frequency *</label>

                                                    <select class="me-sm-2 default-select form-control wide" id="freqType" name="freq" style="display: none;" required>

                                                        <option value="<?= $selected_loan['loan']['repay_cycle_id']; ?>" selected><?= $ftype ?></option>
                                                        <option value="1">Daily</option>
                                                        <option value="2">Weekly</option>
                                                        <option value="3">Monthly</option>
                                                        <option value="4">Bi-Monthly</option>
                                                        <option value="5">Yearly</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="periodInputField">Loan Period in <label id="dtype"> - <?= @$ftype ?></label><i>*</i></label>
                                                    <input type="number" class="form-control" value="<?= @$selected_loan['loan']['approved_loan_duration'] ?>" id="periodInputField" placeholder="" name="loan_period" required="" min="1">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Application Date: * </label>
                                                    <input id="loan_date" type="date" value="<?= date('Y-m-d') ?>" name="application_date" class="form-control" required="">
                                                </div>


                                                <div class="form-group">
                                                    <label>Application Comment: * </label>
                                                    <textarea id="comment" name="comment" rows="2" class="form-control" required=""></textarea>
                                                </div>
                                                <br />
                                                <div class="mb-3">
                                                    <div class="form-check custom-checkbox mb-3">
                                                        <input type="checkbox" class="form-check-input" id="customCheckBox1" name="send_sms">
                                                        <label class="form-check-label" for="customCheckBox1">Send SMS to Client</label>
                                                        <p class="text-muted mb-3">If un-checked system won't attempt to send an sms</p>
                                                    </div>
                                                </div>
                                                <br /><br />
                                                <!-- send sms section -->
                                                <button type="submit" name="submit" class="btn btn-primary">Submit Loan Topup Application</button>

                                            </div>
                                        </div>
                                    </form>
                                <?php } ?>
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
            $('#freqType').change(function() {

                if ($(this).find('option:selected').val() == 1) {
                    $('#dtype').html(' DAYS');
                } else if ($(this).find('option:selected').val() == 2) {
                    $('#dtype').html(' WEEKS');

                } else if ($(this).find('option:selected').val() == 3) {
                    $('#dtype').html(' MONTHS');

                } else if ($(this).find('option:selected').val() == 4) {
                    $('#dtype').html(' DAYS');

                } else if ($(this).find('option:selected').val() == 5) {
                    $('#dtype').html(' YEARS');
                } else {
                    $('#dtype').html(' DAYS');
                }

            });
        </script>
    <script>
        $(document).ready(function() {
            // SmartWizard initialize
            $('#smartwizard').smartWizard();
        });
    </script>


</body>

</html>