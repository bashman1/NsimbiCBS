<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('waive_interest')) {
    return $permissions->isNotPermitted(true);
}
$title = 'WRITE-OFF LOAN';
include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
  
    $res = $response->write_off_loan($_POST);
    if ($res) {
        setSessionMessage(true, "Loan Written Off successfully");
    } else {
        setSessionMessage(false, $res['message']);
    }
    header('location:' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
    exit;
}

$loan_id = $_GET['id'];
$selected_loan = @$response->getLoanDetails($loan_id)[0];

?>
<?php
include('includes/head_tag.php');
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
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                        </h4>

                        <hr class="hr-dashed">

                        <div class="row">
                            <div class="col-md-3">

                                <h4 class="mt-0 header-title">Client Details</h4>
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
                                            <?= number_format(@$selected_loan['loan']['approvedamount']) ?>
                                        </li>
                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Product </b>: <?= @$selected_loan['product']['type_name'] ?>
                                        </li>
                                        <li class="mt-2"><i class="ti-user mr-2 text-primary font-22 align-middle"></i>
                                            <b>Officer </b>:
                                            <?= @$selected_loan['staff']['firstName'] . ' ' . @$selected_loan['staff']['lastName'] . ' - ' . @$selected_loan['staff']['positionTitle'] ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Branch </b>: <?= @$selected_loan['branch'] ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Interest Rate </b>:
                                            <?= @$selected_loan['loan']['monthly_interest_rate'] . ' % PER ANNUM' ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-check-box mr-2 text-primary font-22 align-middle"></i>
                                            <b>Interest Method </b>: <?= @$selected_loan['product']['interestmethod'] ?>
                                        </li>

                                        <li class="mt-2"><i class="ti-calendar mr-2 text-primary font-22 align-middle"></i> <b>
                                                Application Date </b>:
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
                                            <h3 class="mt-0"><?= number_format(@$selected_loan['loan']['principal_balance']) ?>
                                            </h3>
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
                                        <div class="col-lg-4">
                                            <span class="text-muted">Principal Due</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['principal_due']) ?></h3>
                                        </div>
                                        <div class="col-lg-4">
                                            <span class="text-muted">Interest Due</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['interest_due']) ?></h3>
                                        </div>

                                        <div class="col-lg-4">
                                            <span class="text-muted">Total Due</span>
                                            <h3 class="mt-0">
                                                <?= number_format(@$selected_loan['loan']['principal_due'] + @$selected_loan['loan']['interest_due']) ?>
                                            </h3>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Loan Write-off Form</h4>
                        <p class="text-muted mb-3"></p>

                        <form method="post" class="submit_with_ajax" action="">
                            <input type="hidden" name="loan_id" value="<?= @$selected_loan['loan']['loan_no'] ?>">

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Date of Write-Off <i>*</i>: </label>
                                        <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" placeholder="" required="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Write Off Method <i>*</i>: </label>
                                        <select class="form-control" name="method">
                                            <option value="expense">Directly Expensed</option>
                                            <option value="allowance">Allowance for Doubtful Loans</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Affected branch chart account <i>*</i>: </label>
                                        <select name="debit_account" id="cash_acc" class="form-control">
                                            <option value="">Select chart account</option>
                                            <?php

                                            $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                            if ($sub_accs) {

                                                foreach ($sub_accs as $acc) {

                                                    echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ': Branch: ' . $acc['branch'] . '  -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Comment <i>*</i>: </label>
                                        <input type="text" class="form-control" name="comment" value="" placeholder="" required="">
                                    </div>
                                    <br /><br />
                                    <button type="submit" name="submit" class="btn btn-primary"> Process Request </button>
                                </div>

                            </div>

                        </form>

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
        $(document).ready(function() {
            // SmartWizard initialize
            $('#smartwizard').smartWizard();
        });
    </script>


</body>

</html>