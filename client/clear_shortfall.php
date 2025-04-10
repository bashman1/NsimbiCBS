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

if (isset($_POST['submit'])) {
    $res = $response->clearShortfall($_POST);
    if ($res) {
        setSessionMessage(true, 'Shortfall Payment Submitted Successfully!');
        header('location:staff_shortfall.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again.');
        header('location:staff_shortfall.php');
    }
    exit;
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

                <!-- row -->
                <div class="card">
                    <div class="card-body">


                        <h4 class="mt-0 header-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Staff Shortfall Clearance Form
                        </h4>


                        <!-- <p class="text-mutesd mb-3">Till Cash Balance: <b></b></p> -->

                        <hr class="hr-dashed">

                        <form method="post" class="submit_with_ajax">
                            <input type="hidden" name="bank" value="<?= $user[0]['bankId'] ?>" />
                            <input type="hidden" name="branchid" value="<?= $user[0]['branchId'] ?>" />
                            <input type="hidden" name="user" value="<?= $user[0]['userId'] ?>" />
                            <input type="hidden" name="ssid" value="<?= $_GET['id'] ?>" />
                            <div class="row">
                                <div class="col-md-4">
                                  

                                   

                                    <div class="form-group">
                                        <label> Amount Paid: </label>
                                        <input type="text" id="total_amount" class="form-control comma_separated" name="amount" placeholder="" required="">
                                    </div>

                                 

                                  
                                </div>

                               

                                <div class="col-md-4">
                                  



                                    <br /><br />

                                    <button type="submit" class="btn btn-primary btn-block" name="submit">Submit</button>

                                </div>
                            </div>
                        </form>
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