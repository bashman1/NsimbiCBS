<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
require_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_POST['submit'])) {

    $res = $response->updateTrxnAccountBalance($_POST);
    // var_dump($res);
    // exit;
    if ($res['success']) {
        setSessionMessage(true, "Cash A/c Balance Reconciled Successfully!");
        header('location: trxn_accounts.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to reconcile account balance');
        RedirectCurrent();
    }
}

$title = 'ACCOUNT BALANCE RECONCILIATION';

require_once('includes/head_tag.php');

$actypes = $response->getAccountDetails($_REQUEST['acid']);
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
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Reconciliation
                                </h4>
                                <?php
                                if (isset($_GET['success#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                                    // unset($_SESSION['success']);
                                }
                                if (isset($_GET['error#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                                }

                                ?>
                            </div>
                            <div class="card-body">

                                <p class="text-muted mb-3">
                                    Using the form below, you can reconcile the balance of a trxn account<br />
                                    <b class="text-danger">(NOTE: This reconciliation shall affect the selected suspense account).</b>
                                </p>

                                <hr class="hr-dashed">

                                <div class="btc-price">
                                    <p class="text-muted mb-3">A/C Name: <?= @$actypes[0]['aname']; ?><br />
                                        A/C Code: <?= @$actypes[0]['acode']; ?><br />
                                        Balance: <?= number_format($actypes[0]['balance'] ?? 0); ?><br />
                                        A/C Type: <?= @$actypes[0]['type']; ?><br />
                                        Branch: <?= @$actypes[0]['bname']; ?><br />
                                        Description: <?= @$actypes[0]['description']; ?><br />
                                    </p>


                                    <hr class="hr-dashed">

                                </div><br />
                                <form method="POST">
                                    <input type="hidden" name="user" class="form-control" placeholder="" value="<?= $user[0]['userId'] ?>">
                                    <input type="hidden" name="id" class="form-control" placeholder="" value="<?= $_REQUEST['id'] ?>">
                                    <input type="hidden" name="acid" class="form-control" placeholder="" value="<?= $_REQUEST['acid'] ?>">
                                    <input type="hidden" name="orig_bal" class="form-control" placeholder="" value="<?= $actypes[0]['balance'] ?? 0 ?>">

                                    <div class="tab-content">

                                        <div class="row ">


                                            <div class="form-group">
                                                <label>Select Suspense Journal Account: </label>
                                                <select name="main_acc" class="form-control " id="journalacc" required>
                                                    <option value="">Select....</option>
                                                    <?php
                                                    $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                                    if ($sub_accs) {

                                                        foreach ($sub_accs as $acc) {
                                                            if ($acc['type'] == 'SUSPENSES'&& $acc['is_main_account'] == 0) {

                                                                echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  Branch: ' . $acc['branch'] . ' -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">New Account Balance *</label>
                                                    <input type="text" name="bal" class="form-control amount comma_separated" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Notes *</label>
                                                    <input type="text" name="notes" class="form-control" placeholder="" required>
                                                </div>
                                            </div>



                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <button type="submit" name="submit" value="submit" class="btn btn-primary">Update Balance</button>
                                                </div>
                                            </div>
                                            <br /><br />
                                        </div>
                                        <!-- </div> -->

                                </form>
                                <!-- </div> -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('includes/footer.php'); ?>



        </div>


        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#smartwizard').smartWizard();


            });
        </script>



</body>

</html>