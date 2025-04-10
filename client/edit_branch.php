<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

?>
<?php
include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {
    $res = $response->editBranch($_POST['name'], $_POST['location'], $_POST['id']);
    if ($res) {
        setSessionMessage(true, 'Branch Updated Successfully!');
        header('location:all_branches.php');
        exit;
    } else {
        setSessionMessage(false, 'Branch Update Failed!');
        header('location:all_branches.php');
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
                                    Branch Details Update form
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['id']; ?>" name="id">

                                        <div class="mb-3">
                                            <label class="text-label form-label">Branch Name*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['name']; ?>" name="name">
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Location of the Branch*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" value="<?php echo $_GET['location']; ?>" name="location">
                                        </div>



                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Update Branch</button>
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