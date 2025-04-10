<?php
include('../backend/config/session.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}

include_once('includes/response.php');
?>
<?php


$responser = new Response();

if (isset($_POST['submit'])) {
    $res = $responser->createClientAccount($_POST);

    // var_dump($res);
    // exit;

    if ($res) {
        setSessionMessage(true);
        // header('location: add_client_account?id=' . $_POST['uid'] . '&success');
    } else {
        setSessionMessage(false, "Something went wrong");
        // header('location: add_client_account?id=' . $_POST['uid'] . '&error');
    }
    RedirectCurrent();
}

$_GET['id'] = parsed_id($_GET['id']);
$response = $responser->createClientAccountResources($_GET['id']);
$data = @$response['data'];
$accounts = @$data['accounts'];
$member = @$data['client'];
$client_accounts = @$data['client_accounts'];

$current_accounts_ids = array_column($client_accounts, 'id');

// var_dump($current_accounts_ids);
// exit;
$title = 'SAVIING A/Cs';
require_once('includes/head_tag.php');
?>

<body>

    <!--*******************
 Preloader start
 ********************-->
    <?php include_once('includes/preloader.php'); ?>
    <!--*******************
 Preloader end
 ********************-->


    <!--**********************************
 Main wrapper start
 ***********************************-->
    <div id="main-wrapper">

        <?php
        include_once('includes/nav_bar.php');
        include_once('includes/side_bar.php');
        ?>
        <!--**********************************
 Content body start
 ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a></li>

                    </ol>
                </div>
                <!-- row -->
                <div class="row">
                    <div class="col-xl-5 col-xxl-5">
                        <div class="card">
                            <div class="card-header">

                                <div class="profile-info">
                                    <div class="profile-photo">

                                        <a class="rounded-circle" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">
                                            <img class="rounded-circle" src="<?php echo @$member['profilePhoto']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                        </a>

                                    </div>
                                    <div class="profile-details">
                                        <div class="profile-name px-3 pt-2">
                                            <h4 class="text-primary mb-0">
                                                <?php echo @$member['firstName'] . ' ' . @$member['lastName'] . @$member['shared_name']; ?></h4>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <h4> Client's Saving Accounts </h4>
                                <ul>
                                    <?php
                                    foreach ($client_accounts as $client_account) { ?>
                                        <li>
                                            <i class="fa fa-dot-circle"></i> &nbsp; <a href="member_statement_range.php?id=<?= $client_account['userId'] ?>"><?php echo $client_account['name'] . ' ( ' . $client_account['membership_no'] . ' : Bal ' . number_format($client_account['acc_balance']) . ' )' ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="basic-form">
                            <form method="POST">
                                <input type="hidden" name="uid" value="<?php echo @$_GET['id'] ?>">
                                <input type="hidden" name="membership_no" value="<?php echo @$member['membership_no'] ?>">
                                <div class="card">
                                    <div class="card-body btc-price">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="text-label form-label">Account Type *</label>
                                                    <select name="account_id" class="me-sm-2 default-select form-control wide" style="display: none;" required>
                                                        <option value=""> Select </option>
                                                        <?php
                                                        foreach ($accounts as $account) {
                                                            if (!in_array($account['id'], $current_accounts_ids)) {
                                                        ?>
                                                                <option value="<?php echo $account['id'] ?>">
                                                                    <?php echo @$account['name'] ?>
                                                                </option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="sweetalert">
                                                    <button type="submit" name="submit" class="btn btn-primary btn sweet-confirm">Add Account</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
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
        <!-- <script src="./vendor/global/global.min.js"></script>

        <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
        <script src="./vendor/jquery-validation/jquery.validate.min.js"></script>
        <script src="./js/plugins-init/jquery.validate-init.js"></script>
        <script src="./vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="./js/plugins-init/sweetalert.init.js"></script>

        <script src="./vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js"></script>
        <script src="vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

        <script src="./js/custom.min.js"></script>
        <script src="./js/dlabnav-init.js"></script>
        <script src="./js/demo.js"></script> -->

        <?php
        include('includes/bottom_scripts.php');
        ?>

        <?php
        if (isset($_GET['success'])) {
            echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
            // unset($_SESSION['success']);
        }

        if (isset($_GET['error'])) {
            echo '<script type="text/javascript">
                                    myError();
                                   </script>';
        }

        ?>

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


</body>

</html>