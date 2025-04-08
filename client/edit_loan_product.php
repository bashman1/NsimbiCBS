<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $prate = 0;
    $pfamount = 0;
    $gracedays = 0;
    $maxdays = 0;
    $gracetype = 'pay_none';
    $penaltybased = 'both';

    $round_off = 0;
    $auto_repay = 0;
    $auto_penalty = 0;
    if (@$_POST['auto_repay'] == 'true') {
        $auto_repay = 1;
    }

    if ($_POST['auto_penalty'] == 'true') {
        $auto_penalty = 1;
    }

    if ($_POST['round_off'] == 'true') {
        $round_off = 0;
    }
    if ($_POST['gridRadios'] == 'true') {
        $prate = $_POST['prate'];
        $pfamount = $_POST['pfamount'];
        $gracedays = $_POST['gracedays'];
        $maxdays = $_POST['maxdays'];
        $gracetype = $_POST['gracetype'];
        $penaltybased = $_POST['penaltybased'];
    } else {
        $feeAmount = @$_POST['famount'] ?? 0;
    }
    $res = $response->editLoanProduct(
        $_POST['lpid'],
        $_POST['name'],
        $_POST['intrate'],
        $_POST['freq'],
        $_POST['interestMethod'],
        $_POST['gridRadios'],
        $_POST['fee'],
        $prate,
        $pfamount,
        $gracedays,
        $maxdays,
        $user[0]['bankId'],
        $auto_repay,
        $auto_penalty,
        $round_off,
        $gracetype,
        $penaltybased
    );
    if ($res) {
        setSessionMessage(true, 'Loan Product Updated Successfully!');
    } else {
        setSessionMessage(false, 'Loan Product Update failed!');
    }
    RedirectCurrent();

    exit;
}

require_once('includes/head_tag.php');

$details = $response->getLoanProductDetails($_GET['id']);
$loan_product = @$details[0];
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
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Update Loan Product Details
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" name="lpid" value="<?= $loan_product['id'] ?>" />
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Loan Product Name*</label>

                                                    <input type="text" class="form-control input-rounded" placeholder="" name="name" required value="<?= $loan_product['name'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Interest Rate per Annum*</label>

                                                    <input type="text" class="form-control input-rounded" placeholder="" name="intrate" required value="<?= $loan_product['interestrate'] ?>">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Frequency *</label>

                                                    <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect2" name="freq" style="display: none;">
                                                        <option selected value="<?= $loan_product['frequency'] ?>"><?= $loan_product['frequency'] ?></option>
                                                        <option value="DAILY">Daily</option>
                                                        <option value="WEEKLY">Weekly</option>
                                                        <option value="MONTHLY">Monthly</option>
                                                        <option value="YEARLY">Yearly</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Interest Method *</label>

                                                    <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="interestMethod" style="display: none;">
                                                        <option selected value="<?= $loan_product['interestmethod'] ?>"><?= $loan_product['interestmethod'] ?></option>
                                                        <option value="FLAT">Flat</option>
                                                        <option value="DECLINING_BALANCE">Declining Balance</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div><br />
                                        <h4 class="card-title text-primary">Penalty Information
                                        </h4>
                                        <h6 class="card-title text-primary"> Late Payment Penalty
                                        </h6>



                                        <div class="mb-3">
                                            <label class="text-label form-label">Enable Late Payment Penalty
                                                *</label>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">


                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gridRadios" value="<?= $loan_product['has_penalty'] ?>" onClick="setDown()" <?= $loan_product['has_penalty'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label">
                                                        Yes
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gridRadios" value="<?= !$loan_product['has_penalty'] ?>" onclick="setUp()" <?= !$loan_product['has_penalty'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br />

                                        <div class="row" id="headd">
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Penalty Rate % Per DAY
                                                        *</label>

                                                    <input type="text" class="form-control input-rounded" placeholder="" name="prate" value="<?php echo $loan_product['penaltyinterestrate']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Penalty Fixed Amount Per DAY
                                                        *</label>

                                                    <input type="number" class="form-control input-rounded" placeholder="" name="pfamount" value="<?php echo $loan_product['penaltyfixedamount']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Calculate Penalty Based On *</label>

                                                    <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="penaltybased">
                                                        <option value="<?php echo $loan_product['penalty_based_on']; ?>" selected><?php echo $loan_product['penalty_based_on']; ?></option>
                                                        <option value="p">Principal in Arrears</option>
                                                        <option value="i">Interest in Arrears</option>
                                                        <option value="both">Both Principal & Interest in Arrears</option>

                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row" id="headf">
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Number of Grace Period DAYS</label>

                                                    *</label>

                                                    <input type="number" class="form-control input-rounded" placeholder="" name="gracedays" value="<?php echo $loan_product['numberofgraceperioddays']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Grace Period Type *</label>

                                                    <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect2" name="gracetype">
                                                        <option value="<?php echo $loan_product['gracetype']; ?>" selected><?php echo $loan_product['gracetype']; ?></option>
                                                        <option value="pay_i">Pay Interest Only</option>
                                                        <option value="pay_p">Pay Principal Only</option>
                                                        <option value="pay_none">Pay None (Client Pays doesn't pay anything until Grace Period ends)</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Maximum Penalty DAYS

                                                        *</label>

                                                    <input type="number" class="form-control input-rounded" placeholder="" name="maxdays" value="<?php echo $loan_product['maxnumberofpenaltydays'] ?>">
                                                </div>
                                            </div>

                                        </div>
                                        <br />
                                        <h4 class="card-title text-primary">Loan Fees
                                        </h4>
                                        <div class="mb-3">
                                            <label class="text-label form-label">Loan Fees *</label>

                                            <select class="multi-select" multiple="multiple" name="fee[]">
                                                <?php
                                                if ($loan_product['fees']) {
                                                    foreach ($loan_product['fees'] as $rown) {
                                                        echo '  <option selected value="' . $rown['id'] . '">' . $rown['name'] . '</option>';
                                                    }
                                                }
                                                ?>

                                                <?php
                                                foreach ($response->getAllBankFees($user[0]['bankId']) as $row) {
                                                    echo '
                                                            <option value="' . $row['id'] . '">' . $row['name'] . '</option>
                                                            ';
                                                }
                                                ?>

                                            </select>
                                        </div>


                                        <br /><br /><br />

                                        <div class="mb-3">
                                            <div class="form-check custom-checkbox mb-3">
                                                <input type="checkbox" class="form-check-input" id="customCheckBox1" name="auto_repay" value="<?php echo $loan_product['auto_repay']; ?>" <?php echo $loan_product['auto_repay'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="customCheckBox1">Activate Automatic Loan Payments</label>
                                                <p class="text-muted mb-3">Automatically deduct savings to pay due loans</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check custom-checkbox mb-3">
                                                <input type="checkbox" class="form-check-input" id="customCheckBox1" name="auto_penalty" value="<?php echo $loan_product['auto_penalty']; ?>" <?php echo $loan_product['auto_penalty'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="customCheckBox1">Activate automatic penaly payments</label>
                                                <p class="text-muted mb-3">Automatically deduct savings to pay due penalty</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check custom-checkbox mb-3">
                                                <input type="checkbox" class="form-check-input" id="customCheckBox1" name="round_off" value="<?php echo $loan_product['round_off']; ?>" <?php echo $loan_product['round_off'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="customCheckBox1">Round off installment decimals</label>
                                                <p class="text-muted mb-3">Round off and accumulate decimals in the loan schedule</p>
                                            </div>
                                        </div>
                                        <!-- <div class="mb-3"> -->
                                        <br /><br /><br />

                                        <button type="submit" name="submit" class="btn btn-primary">Update Loan
                                            Product</button>
                                        <!-- </div> -->

                                    </form>
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
        <?php
        include('includes/bottom_scripts.php');
        ?>


        <!-- <script src="./js/styleSwitcher.js"></script> -->
        <script>
            $('#inlineFormCustomSelect2').change(function() {

                if ($(this).find('option:selected').val() == 'DAILY') {
                    $('#dtype').html(' DAY');
                    $('#dtype2').html(' DAY');
                    $('#dtype3').html(' DAYS');
                    $('#dtype4').html(' DAYS');
                } else if ($(this).find('option:selected').val() == 'WEEKLY') {
                    $('#dtype').html(' WEEK');
                    $('#dtype2').html(' WEEK');
                    $('#dtype3').html(' WEEKS');
                    $('#dtype4').html(' WEEKS');

                } else if ($(this).find('option:selected').val() == 'MONTHLY') {
                    $('#dtype').html(' MONTH');
                    $('#dtype2').html(' MONTH');
                    $('#dtype3').html(' MONTHS');
                    $('#dtype4').html(' MONTHS');

                } else if ($(this).find('option:selected').val() == 'YEARLY') {
                    $('#dtype').html(' YEAR');
                    $('#dtype2').html(' YEAR');
                    $('#dtype3').html(' YEARS');
                    $('#dtype4').html(' YEARS');
                } else {
                    $('#dtype').html(' DAY');
                    $('#dtype2').html(' DAY');
                    $('#dtype3').html(' DAYS');
                    $('#dtype4').html(' DAYS');
                }

            });
        </script>
        <script>
            function sett(valu) {
                valu ? setDown() : setUp();
            }

            function setUp() {
                var x = document.getElementById("headd");
                var y = document.getElementById("headf");
                y.style.display = "none";

                x.style.display = "none";
            }


            function setDown() {
                var x = document.getElementById("headd");
                var y = document.getElementById("headf");
                x.style.display = "block";

                y.style.display = "block";
            }
        </script>



</body>

</html>