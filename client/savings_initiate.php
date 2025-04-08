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

    $res = $response->createSavingInitiation($_POST);
    // var_dump($res);
    // exit;
    if ($res['success']) {
        setSessionMessage(true, "Interest On Savings Initiation Set Successfully!");
        header('location: savings_list_initiations.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to create a new Initiation');
        RedirectCurrent();
    }
}

$title = 'INITIATIONS ON INTEREST OVER SAVINGS';

require_once('includes/head_tag.php');

$actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);

$branches = $response->getBankBranches($_SESSION['session_user']['bankId']);

$accounts = $response->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);
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
                                    Initiations
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
                                    Using the form below, you can initiate interest on savings disbursement process<br />
                                    <b class="text-danger">(NOTE: This shall be scheduled as an automatic process that the system shall execute within the next 24 hours of the set execution date).</b>
                                </p>

                                <hr class="hr-dashed">

                                <div class="btc-price">
                                    <p class="text-muted mb-3">Interest On savings Initiation</p>


                                    <hr class="hr-dashed">

                                </div><br />
                                <form method="POST">

                                    <input type="hidden" name="auth_by" class="form-control" placeholder="" value="<?= $user[0]['userId'] ?>">

                                    <div class="tab-content">
                                        <!-- <div id="wizard_Payment" class="tab-pane" role="tabpanel"> -->
                                        <!-- <h4 class="card-title " style="color:#005a4b;">Attachments & Others</h4> -->
                                        <div class="row ">

                                            <div class="col-md-6">
                                                <div class="form-group">

                                                    <label class="text-label form-label">Affected Branch *</label>
                                                    <?php if ($_SESSION['session_user']['branchName']) { ?>
                                                        <div>
                                                            <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                                        </div>
                                                    <?php } else { ?>
                                                        <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
                                                            <option value=""> All</option>
                                                            <?php

                                                            $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                            if ($user[0]['branchId']) { ?>
                                                                <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                                ';
                                                            <?php } ?>

                                                            <?php
                                                            if ($branches !== '') {
                                                                foreach ($branches as $row) {
                                                                    $is_seleceted = @$_REQUEST['branchId'] == $row['id'] ? "selected" : "";
                                                            ?>
                                                                    <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                                        <?= $row['name'] ?>
                                                                    </option>
                                                            <?php }
                                                            } ?>

                                                        </select>
                                                    <?php } ?>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">

                                                    <label class="text-label form-label">Saving Product *</label>

                                                    <select id="osector" class="me-sm-2 default-select form-control wide" name="actype" style="display: none;">
                                                        <option value=""> All</option>
                                                        <?php

                                                        foreach ($actypes as $row) {
                                                            $selected = @$_REQUEST['actype'] == $row['id'] ? "selected" : "";
                                                        ?>
                                                            <option value="<?= $row['id']; ?>" <?= $selected; ?>>
                                                                <?= $row['ucode'] . ' - ' .
                                                                    $row['name'] ?>
                                                            </option>

                                                        <?php }
                                                        ?>

                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-label form-label">WHT Chart A/C *</label>

                                                <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="wht_acid" required>
                                                    <option> Select </option>
                                                    <?php foreach ($accounts as $account) { ?>
                                                        <option value="<?= $account['id'] ?>">
                                                            <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="text-label form-label">Interest Chart A/C *</label>

                                                <select id="ocategory" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="int_acid" required>
                                                    <option> Select </option>
                                                    <?php foreach ($accounts as $account) { ?>
                                                        <option value="<?= $account['id'] ?>">
                                                            <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>


                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Interest Rate (Per Annum) *</label>
                                                    <input type="text" name="rate" class="form-control" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">WHT Rate (Against the Computed Interest) *</label>
                                                    <input type="text" name="wht" class="form-control" placeholder="" value="0" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Minimum Savings Balance Required for this *</label>
                                                    <input type="text" name="min_bal" class="form-control comma_separated" placeholder="" value="0">
                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">From Date</label>
                                                    <input type="date" name="from_date" class="form-control" placeholder="" value="<?= date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Disbursement Date</label>
                                                    <input type="date" name="date" class="form-control" placeholder="" value="<?= date('Y-m-d') ?>">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check custom-checkbox mb-3">
                                                    <input type="checkbox" class="form-check-input" id="customCheckBox1" name="send_sms" checked value="1">
                                                    <label class="form-check-label" for="customCheckBox1">Send SMS to
                                                        every Client</label>
                                                    <div class="text-muted mb-3">
                                                        <em> <small> <strong> Note: </strong> If un-checked system won't attempt to
                                                                send an<strong> SMS </strong> </small> </em>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <button type="submit" name="submit" class="btn btn-primary">Initiate</button>
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