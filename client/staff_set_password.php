<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

// if (isset($_POST['submit'])) {

//     $res = $response->setStaffPassword2($_POST['password'], $_POST['opassword'],$_POST['id']);
//     if ($res) {
//         setSessionMessage(true, 'Password Updated Successfully!');
//         header('location:index');
//         exit;
//     } else {
//         setSessionMessage(false, 'Process failed! Try again');
//         header('location:index');
//         exit;
//     }
// }

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
                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Fees</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">New Fee</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Update Password Form
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" action="change_password.php">
                                        <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
                                        <div class="mb-3">
                                            <label class="text-label form-label">Current / Old Password*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="opassword" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-label form-label">New Password*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="password" required>
                                        </div>







                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Update Password</button>
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