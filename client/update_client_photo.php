<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}
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
                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="all_clients.php">Clients</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Client's Details Update Page</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-primary">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Update Client's
                                    Photo
                                </h4>

                            </div>
                            <div class="card-body">

                                <div class="basic-form">
                                    <form>
                                        <?php
                                        if (isset($_GET['photo'])) {
                                            echo '
<h4 class="card-title text-primary">Update Client\'s Passport-Sized
Photo</h4>
                                        <div class="row">

                                        <div class="col-lg-12 mb-3">
                                        <div class="mb-3">
                                           
                                            <input type="file" name="photo" class="form-control"
                                                placeholder="">
                                        </div>
                                    </div>
                                        </div>
                                        <button type="submit" name="submit"
                                        class="btn btn-primary">Submit</button>
';
                                        } else if (isset($_GET['sign'])) {
                                            echo '
<h4 class="card-title text-primary">Update Client\'s Scanned
Signature</h4>
                                        <div class="row">

                                        <div class="col-lg-12 mb-3">
                                        <div class="mb-3">
                                        <input type="file" name="sign" class="form-control"
                                        placeholder="">
                                        </div>
                                    </div>
                                        </div>
                                        
                                            <button type="submit" name="submit"
                                                class="btn btn-primary">Submit</button>
                                        
';
                                        } else {
                                            echo '
<h4 class="card-title text-primary">Update Client\'s Any Other
Attachments</h4>
                                        <div class="row">

                                        <div class="col-lg-12 mb-3">
                                        <div class="mb-3">
                                        <input type="file" name="otherattach" class="form-control"
                                        placeholder="">
                                        </div>
                                    </div>
                                        </div>
                                        <button type="submit" name="submit"
                                        class="btn btn-primary">Submit</button>
';
                                        }
                                        ?>







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
        <?php include('includes/bottom_scripts.php'); ?>

       


</body>

</html>