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

    $res = $response->setSMSACIDS($body, $charge_to, $user[0]['bankId']);

    if ($res) {
        setSessionMessage(true, 'Action Successful!');
        header('location:general_sms_settings.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Process failed');
        header('location:general_sms_settings.php');
        exit;
    }
}
$title = 'SMS SETTINGS';
require_once('includes/head_tag.php');
$share_details = $response->getBankSMSAcids($user[0]['bankId'], $user[0]['branchId']);
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

                                    General SMS Settings Form

                                </h4>
                                <?php
                                $details = $response->getBankSMSDetails($user[0]['bankId'])[0];
                                ?>

                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">

                                <div class="row">
                                    <div class="col-md-5">
                                        <h4 class="mt-0 header-title">SMS Settings</h4>
                                        <p class="text-muted mb-3"></p>

                                        <div class="mb-3 pricingTable1">
                                            <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                                <li><b>System SMS Status</b> : <i class="fa fa-toggle-on" style="color: #0ad40a;font-size: 20px;"></i></li>
                                                <li><b>Institution SMS Status</b> : <?= @$details['status']; ?></li>
                                                <li><b>Manually Select to Send SMS </b> : <?= @$details['m_status']; ?></li>

                                            </ul>


                                        </div>


                                    </div>


                                </div>
                                <form class="" method="POST">
                                    <?php

                                    $accounts = $response->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);
                                    ?>
                                    <div class="form-group">
                                        <label class="text-label form-label">Select Chart Account associated with SMS Charges Income *</label>

                                        <select id="oscategory" class="form-control" name="account_id" required>

                                            <?php foreach ($accounts as $account) {
                                                if ($account['id'] == $share_details[0]['income_acid']) {
                                                    echo '<option value="' . $account['id'] . '" selected>' . $account['name'] . '</option> ';
                                                }
                                            ?>
                                                <option value="<?= $account['id'] ?>">
                                                    <?= $account['name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-label form-label">Select Chart Account associated with SMS Purchase Expenses *</label>

                                        <select id="osector" class="form-control" name="exp_id" required>

                                            <?php foreach ($accounts as $accountn) {
                                                if ($accountn['id'] == $share_details[0]['exp_acid']) {
                                                    echo '<option value="' . $accountn['id'] . '" selected>' . $accountn['name'] . '</option> ';
                                                }
                                            ?>
                                                <option value="<?= $accountn['id'] ?>">
                                                    <?= $accountn['name'] ?>
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