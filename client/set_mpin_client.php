<?php
require_once(__DIR__ . '/includes/functions.php');

?>
<?php

require_once(__DIR__ . '/includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {

    $res = $response->setClientPassword($_POST['password'], $_POST['id']);
    if ($res) {
        setSessionMessage(true, 'mPIN Set Successfully!');
        header('location:client_portal_index?cid=' . $_POST['cid'] . '&uid=' . $_POST['id']);
        exit;
    } else {
        setSessionMessage(false, 'Process failed! Try again');
        header('location:set_mpin_client?id=' . $_POST['id']);
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
                                    Update mPIN Form
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
                                        <input type="hidden" name="cid" value="<?= $_GET['cid'] ?>" />
                                        <div class="mb-3">
                                            <label class="text-label form-label">New mPIN (Must be Only 4 digits) *</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="password" maxlength="4">
                                        </div>
                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->
                                        <button type="submit" name="submit" class="btn btn-primary">Update mPIN</button>
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