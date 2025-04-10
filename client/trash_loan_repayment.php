<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->hasSubPermissions('can_trash_trxns')) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();
date_default_timezone_set('Africa/Kampala');
if (isset($_POST['submit'])) {
    $res = $response->deleteLoanRepayment($_POST['tid'], $_POST['uid'], $_POST['date'], $_POST['comments']);
    if ($res) {
        setSessionMessage(true, 'Loan Payment Deleted Successfuly!');
        header('location:loan_statement.php?id=' . $_GET['id']);
        exit;
    } else {
        setSessionMessage(false, 'Process failed!');
        header('location:loan_statement.php?id=' . $_GET['id']);
        exit;
    }
}


require_once('includes/head_tag.php');
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
                                    Describe reason for the deletion
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" name="tid" value="<?= @$_REQUEST['tid'] ?>">
                                        <input type="hidden" name="uid" value="<?= $user[0]['userId'] ?>">
                                        <input type="hidden" name="date" value="<?= date('Y-m-d H:i:s') ?>">

                                        <label class="text-label form-label">Reason*</label>

                                        <textarea class="form-control input-rounded" placeholder="" name="comments" col="5"></textarea>
                                        <!-- </div> -->

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