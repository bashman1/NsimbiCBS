<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsSuperAdmin()) {
    return $permissions->isNotPermitted(true);
    exit;
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

?>
<?php
include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {
    $res = $response->editBank($_POST['name'], $_POST['location'], $_POST['contact'], $_POST['refered'], $_POST['id']);
    if ($res) {
        setSessionMessage(true, 'Institution Updated Successfully!');
        header('location:all_banks');
        exit;
    } else {
        setSessionMessage(false, 'Institution not Updated!');
        header('location:all_banks');
        exit;
    }

    // header('location:all_banks.php');
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
                                    Bank Details Update form
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['id']; ?>" name="id">

                                        <div class="mb-3">
                                            <label class="text-label form-label">Bank's Name*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['name']; ?>" name="name">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Head Office's Location of the Bank*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['location']; ?>" name="location">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Main Contact Person's Details (e.g Name - Contact)*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['contact']; ?>" name="contact">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Refered by</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['refered']; ?>" name="refered">
                                        </div>


                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Update Bank</button>
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