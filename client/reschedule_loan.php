<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('reschedule_loans')) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->rescheduleLoan($_POST);
    if ($res['success']) {
        setSessionMessage(true, 'Loan Rescheduled Successfully!');
    } else {
        setSessionMessage(false, $res['message']);
    }
    header('Location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit;
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
                        <li class="breadcrumb-item"><a href="">Reschedule Loan</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">

                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                        </h4>

                        <hr class="hr-dashed">

                        <div class="row">
                            <div class="col-md-3">

                                <h4 class="mt-0 header-title">Applicant Details</h4>
                                <p class="text-muted mb-3"></p>

                                <div class="text-centers">
                                    <div class="met-profile-main-pic">
                                        <img src="images/account.png" alt="" height="100" width="100" class="rounded-circle">
                                    </div>

                                    <div class="">
                                        <h5 class="mb-0">
                                            <?= strtoupper(@$selected_loan['client']['firstName'] . ' ' . @$selected_loan['client']['lastName']) ?>
                                        </h5>
                                        <small class="text-muted">A/C No:
                                            <?= @$selected_loan['client']['membership_no'] ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">Telephone:
                                            <?= @$selected_loan['client']['primaryCellPhone'] . ' / ' . @$selected_loan['client']['secondaryCellPhone'] ?></small>
                                        <br>
                                        <small class="text-muted">Email:
                                            <?= @$selected_loan['client']['email'] ?></small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">

                                <h4 class="mt-0 header-title">Loan Details</h4>
                                <p class="text-muted mb-3"></p>

                                <div class="met-profile">
                                    <ul class="list-unstyled personal-detail">
                                        <li class="mt-2"><i class="ti-money mr-2 text-primary font-22 align-middle"></i>
                                            <b>Automatic Payments From Savings </b>:
                                            <?= @$selected_loan['loan']['auto_pay'] > 0 ? '<span class="badge badge-rounded badge-primary">ON</span>' : '<span class="badge badge-rounded badge-danger">OFF</span>'; ?>
                                        </li>
                                        <li class="mt-2"><i class="ti-money mr-2 text-primary font-22 align-middle"></i>
                                            <b>Loan Amount </b>:
                                            <?= number_format(@$selected_loan['loan']['principal']) ?>
                                        </li>
                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Product
                                            </b>: <?= @$selected_loan['product']['type_name'] ?>
                                        </li>
                                        <li class="mt-2"><i class="ti-user mr-2 text-primary font-22 align-middle"></i>
                                            <b>Officer </b>:
                                            <?= @$selected_loan['staff']['firstName'] . ' ' . @$selected_loan['staff']['lastName'] . ' - ' . @$selected_loan['staff']['positionTitle'] ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Branch
                                            </b>: <?= @$selected_loan['branch'] ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Interest Rate </b>:
                                            <?= @$selected_loan['loan']['monthly_interest_rate'] . ' % PER ANNUM' ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Interest Method </b>: <?= @$selected_loan['product']['interestmethod'] ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-calendar mr-2 text-primary font-22 align-middle"></i>
                                            <b> Application Date </b>:
                                            <?= date('Y-m-d', strtotime(@$selected_loan['loan']['requesteddisbursementdate'])) ?>
                                        </li>
                                    </ul>

                                </div>

                            </div>


                            <div class="col-md-5">

                                <h4 class="mt-0 header-title">Loan Summary</h4>
                                <p class="text-muted mb-3"></p>

                                <div class="btc-price">

                                    <div class="row">
                                        <div class="col-lg-4">
                                            <span class="text-muted">Principal Balance</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['principal_balance']) ?></h3>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="text-muted">Interest Balance</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['interest_balance']) ?></h3>
                                        </div>

                                        <div class="col-lg-4">
                                            <span class="text-muted">Penalty Balance</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['penalty_balance']) ?></h3>
                                        </div>

                                    </div>

                                    <hr class="hr-dashed">

                                    <div class="row">

                                        <div class="col-lg-8">
                                            <span class="text-muted">AMOUNT TO RESCHEDULE (Principal Balance + Interest
                                                Due)</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['principal_balance'] + @$selected_loan['loan']['interest_due']) ?>
                                            </h3>
                                        </div>

                                        <div class="col-lg-4">
                                            <span class="text-muted">Interest Due</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['interest_due']) ?></h3>
                                        </div>


                                    </div>

                                    <hr class="hr-dashed">
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Loan Reschedule Form</h4>
                        <p class="text-muted mb-3"></p>

                        <?php if ($selected_loan['loan']['penalty_balance'] > 0) { ?>
                            <div class="alert alert-warning left-icon-big alert-dismissible fade show">
                                <div class="media">
                                    <div class="alert-left-icon-big">
                                        <span><i class="mdi mdi-help-circle-outline"></i></span>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="mt-1 mb-2">Alert</h6>
                                        <p class="mb-0">
                                            You can not reschedule a loan having a penalty
                                            <a href="waive_penalty?id=<?= $selected_loan['loan']['loan_no'] ?>" class="btn btn-primary light btn-xs mb-1">Waive Penalty</a>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">

                                </div>
                            </div>

                        <?php } else { ?>
                            <form method="post" class="submit_with_ajax">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="hidden" name="loan_id" value="<?= @$selected_loan['loan']['loan_no'] ?>">
                                        <input type="hidden" name="amount" value="<?= @$selected_loan['loan']['principal_balance'] + @$selected_loan['loan']['interest_balance'] ?>">

                                        <div class="row">
                                            <div class="col-xs-6">

                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Repayment Frequency: </label>
                                                    <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="frequency" required>
                                                        <option value="<?= @$selected_loan['loan']['repay_cycle_id'] ?>" selected><?= @$selected_loan['product']['frequency'] ?></option>
                                                        <option value="1">Daily</option>
                                                        <option value="2">Weekly</option>
                                                        <option value="3">Monthly</option>
                                                        <option value="4">Bi-Monthly</option>
                                                        <option value="5">Annually</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="periodInputField">New Loan Period in <label id="dtype"> <?= @$ftype ?></label>
                                                        <i>*</i></label>
                                                    <input type="number" class="form-control" value="<?= @$selected_loan['loan']['approved_loan_duration'] ?>" id="period" data-name="period" name="duration" placeholder="New Loan Period" required="">
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="col-md-4">


                                        <div class="form-group ">
                                            <label for="exampleInputEmail1">Interest Per Annum </label>
                                            <input type="number" id="interest" step="any" value="<?= @$selected_loan['loan']['monthly_interest_rate'] ?>" min="0" name="interest_rate" class="form-control" placeholder="Interest Per Annum" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Interest Method: </label>
                                            <select class="form-control" name="interest_method" required>
                                                <option value="<?= @$selected_loan['loan']['interest_method_id'] ?>" selected><?= @$selected_loan['product']['interestmethod'] ?></option>
                                                <option value="1">Flat Rate</option>
                                                <option value="2">Declining Balance</option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Reschedule Date <i>*</i>: </label>
                                            <input type="date" class="form-control" id="record_date" name="reschedule_date" value="<?= date('Y-m-d') ?>" placeholder="" required>
                                        </div>

                                        <div class="form-group account_no_insert">
                                            <label for="exampleInputEmail1">Comment <i>*</i>: </label>
                                            <input id="comment" value="" name="comment" class="form-control" placeholder="Comment" required>
                                        </div>

                                    </div>
                                </div>

                                <hr class="hr-dashed">

                                <div class="row">
                                    <!-- <div class="col-md-4"><button class="btn btn-info btn-block" type="button" id="calculate_btn"> Preview New Schedule</button></div> -->
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4"><button type="submit" class="btn btn-primary confirm_action" name="submit"> Reschedule Loan</button></div>

                                </div>
                            </form>
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
    <script src="./vendor/global/global.min.js"></script>

    <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
    <script src="./vendor/jquery-validation/jquery.validate.min.js"></script>
    <!-- Form validate init -->
    <script src="./js/plugins-init/jquery.validate-init.js"></script>


    <!-- Form Steps -->
    <script src="./vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js"></script>
    <script src="vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

    <?php include('includes/bottom_scripts.php'); ?>

    <!-- <script src="./js/styleSwitcher.js"></script> -->
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
    <script type="text/javascript">
        var $expected_int = 0;
    </script>
    <script>
        $('#single-select').change(function() {

            if ($(this).find('option:selected').val() == 1) {
                $('#dtype').html(' DAYS');
            } else if ($(this).find('option:selected').val() == 2) {
                $('#dtype').html(' WEEKS');

            } else if ($(this).find('option:selected').val() == 3) {
                $('#dtype').html(' MONTHS');

            } else if ($(this).find('option:selected').val() == 5) {
                $('#dtype').html(' YEARS');
            } else {
                $('#dtype').html(' BI-MONTHS');
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