<?php
include('../backend/config/session.php');
include_once('includes/response.php');
// require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

$title = 'SMS PAYMENT VERIFICATION';
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
                                    SMS Payment Verification Page
                                </h4>
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
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="ref" value="<?php echo $_GET['ref']; ?>">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Amount *</label>
                                            <input type="text" disabled class="form-control input-rounded" placeholder="" name="amount" value="<?php echo $_GET['amount']; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Mobile Number (e.g 2567xxxxxxx) *</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="phone" required>
                                        </div>


                                        <br /><br /><br />
                                        <label>
                                            NOTE: Kindly ensure you have enough money (inclusive of transaction charges) on the mobile number stipulated above
                                        </label>
                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Make Payment</button>
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