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

    $res = $response->rectifyShareMoyo($_POST);
}
$title = 'SHARE RECTIFICATION';
include('includes/head_tag.php');

$user_id = $_GET['id'];
$selected_loan = @$response->getClientDetails($user_id)[0];

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
                                    Manual Share Amount Adjustment Form
                                </h4>

                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    Using the form below, you can adjust the share amount of a given client
                                </p>

                                <hr class="hr-dashed">

                                <div class="btc-price">
                                    <p class="text-muted mb-3">Client Summary</p>
                                    <input type="hidden" name="officer" value="<?= @$loan_id ?>">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <span class="text-muted">Client</span>
                                            <h6 class="mt-0">
                                                <?= @$selected_loan['accno'] . ' : ' . @$selected_loan['name'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Share Amount</span>
                                            <h6 class="mt-0">
                                                <?= @$selected_loan['shareamount'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">No. of Shares</span>
                                            <h6 class="mt-0">
                                                <?= @$selected_loan['shares'] ?>
                                            </h6>
                                        </div>

                                        <div class="col-lg-2">
                                            <span class="text-muted">Client's Branch</span>
                                            <h6 class="mt-0">
                                                <?= @$selected_loan['branchName'] ?>
                                            </h6>
                                        </div>





                                    </div>

                                    <hr class="hr-dashed">

                                </div><br />

                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" class="form-control" name="uid" value="<?= @$selected_loan['userId'] ?>">

                                        <input type="hidden" class="form-control" name="auth_id" value="<?php echo $user[0]['userId']; ?>">

                                        <input type="hidden" class="form-control" name="branch_id" value="<?php echo $selected_loan['branchId']; ?>">


                                        <div class="mb-3">
                                            <label class="col-form-label pt-0" for="difference">Share Amount Difference</label><br>
                                            <input type="text" class="form-control" name="difference">
                                        </div>

                                        <div class="mb-3">
                                            <label class="col-form-label pt-0" for="shares">Shares</label><br>
                                            <input type="text" class="form-control" name="shares" value="<?= $selected_loan['shares'];  ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="col-form-label pt-0" for="date">Record Date</label><br>
                                            <input type="date" class="form-control" name="collection_date" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                                        </div>


                                        <div class="form-group">
                                            <label>Affected Journal Account: </label>
                                            <select name="main_acc" class="form-control select2" id="journalacc">
                                                <option value="">Select....</option>
                                                <?php
                                                $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                                if ($sub_accs) {

                                                    foreach ($sub_accs as $acc) {
                                                        if ($acc['type'] == 'LIABILITIES') {

                                                            echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  Branch: ' . $acc['branch'] . ' -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="col-form-label pt-0" for="notes">Notes</label><br>
                                            <input type="text" class="form-control" name="notes">
                                        </div>



                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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

</body>

</html>