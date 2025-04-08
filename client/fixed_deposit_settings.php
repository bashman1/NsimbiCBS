<?php
include('../backend/config/session.php');



require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');

?>
<?php
require_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $body = $_POST['account_id'];
    $charge_to = $_POST['exp_id'];
    $charge_fro = $_POST['wht_id'];

    $res = $response->setFDACIDS($body, $charge_to, $charge_fro, $_POST['branch']);

    if ($res) {
        setSessionMessage(true, 'Action Successful!');
        header('location:fixed_deposits.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Process failed');
        header('location:fixed_deposit_settings.php?branch='.$_POST['branch']);
        exit;
    }
}
$title = 'FIXED DEPOSIT SETTINGS';
require_once('includes/head_tag.php');
$branch = $user[0]['branchId']?? $_GET['branch'];
$share_details = $response->getBankFDAcids($branch);
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

                                    General Fixed Deposit Settings Form

                                </h4>


                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">

                                <div class="row">
                                    <div class="col-md-5">
                                        <h4 class="mt-0 header-title">Fixed Deposit Settings</h4>
                                        <p class="text-muted mb-3"></p>




                                    </div>


                                </div>
                                <form class="" method="POST">
                                    <input type="hidden" name="branch" value="<?= $_GET['branch']?>" />
                                    <?php

                                    $accounts = $response->getSubAccounts2($branch, '');
                                    ?>
                                    <div class="form-group">
                                        <label class="text-label form-label">Select Chart Account associated with Fixed Deposits' Principal *</label>

                                        <select id="oscategory" class="form-control" name="account_id" required>

                                            <?php foreach ($accounts as $account) {
                                                if ($account['id'] == $share_details[0]['princ_acid']) {
                                                    echo '<option value="' . $account['id'] . '" selected>' . $account['name'] . ' - Branch: '.$account['branch'].'</option> ';
                                                }
                                            ?>
                                                <option value="<?= $account['id'] ?>">
                                                    <?= $account['name'].'  - Branch: '.$account['branch'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-label form-label">Select Chart Account associated with Interest Offered Via Fixed Deposits *</label>

                                        <select id="osector" class="form-control" name="exp_id" required>

                                            <?php foreach ($accounts as $accountn) {
                                                if ($accountn['id'] == $share_details[0]['exp_acid']) {
                                                    echo '<option value="' . $accountn['id'] . '" selected>' . $accountn['name'] . ' - Branch: '.$account['branch'].'</option> ';
                                                }
                                            ?>
                                                <option value="<?= $accountn['id'] ?>">
                                                    <?= $accountn['name'] .'  - Branch: '.$account['branch'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-label form-label">Select Chart Account associated with Witholding Tax Charged Via Fixed Deposits *</label>

                                        <select id="exp_account" class="form-control" name="wht_id" required>

                                            <?php foreach ($accounts as $accountn) {
                                                if ($accountn['id'] == $share_details[0]['wht_acid']) {
                                                    echo '<option value="' . $accountn['id'] . '" selected>' . $accountn['name'] . '  - Branch: '.$account['branch'].'</option> ';
                                                }
                                            ?>
                                                <option value="<?= $accountn['id'] ?>">
                                                    <?= $accountn['name']. '  - Branch: '.$account['branch'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <br />
                                    <br />

                                    <!--end form-group-->
                                    <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit">Set Chart A/CS</button>
                                    <!--end form-->

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
    <?php include('includes/bottom_scripts.php'); ?>



</body>

</html>