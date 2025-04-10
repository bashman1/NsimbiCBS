<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->createSubSubAccount($_POST);
    if ($res) {
        setSessionMessage(true, 'Sub Account Created Successfully!');
        header('location:accounting_tab');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to create the sub-account');
        header('location:add_sub_account');
        exit;
    }
}
if (isset($_GET['id'])) {
    $details = $response->getAccountDetails($_GET['id'])[0];
} else {
    setSessionMessage(false, 'Please select an existing main account');
    header('location:accounting_tab');
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
                <div class="row">

                    <!-- <div class="col-md-6"> -->

                    <div class="card">
                        <div class="card-body">
                            <h4 class="mt-0 header-title"> <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> Chart of Account Details</h4>
                            <p class="text-muted mb-3">Name: &nbsp;<?= $details['aname'] ?> &nbsp;<?= $details['issys'] ? '<span class="badge light badge-danger">System Generated</span>' : '<span class="badge light badge-success">Not System Generated</span>' ?></p>
                            <span class="text-muted">Account Type: &nbsp;<?= $details['type'] ?></span>   |  
                            <span class="text-muted">Branch: &nbsp;<?= $details['bname'] ?></span>         |   
                            <span class="text-muted">Balance: &nbsp;<?= number_format($details['balance']??0) ?></span>

                            <p class="text-muted mb-3">Description: &nbsp;<?= $details['description'] ?></p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mt-0 header-title"> Add Sub Account Form</h4>
                            <form method="post" class="panel form-horizontal form-bordered submit_with_ajax" action="">

                                <input type="hidden" name="type" value="<?php echo $details['type']; ?>" class="form-control">
                                <input type="hidden" name="branch" value="<?= $details['bid']; ?>" class="form-control">
                                <input type="hidden" name="bname" value="<?= $details['bname']; ?>" class="form-control">
                                <input type="hidden" name="bank" value="<?= $user[0]['bankId']; ?>" class="form-control">
                                <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>" class="form-control">
                                <input type="hidden" name="mid" value="<?= $details['id']; ?>" class="form-control">


                                <div class="form-group">
                                    <label>Account Name:</label>
                                    <input type="text" name="name" value="" class="form-control" required>
                                </div>



                                <div class="form-group">
                                    <label>Account Description:</label>
                                    <textarea class="form-control" rows="6" name="descr" required></textarea>
                                </div>

                                <br /><br />

                                <button type="submit" class="btn btn-primary" name="submit">Save</button>
                            </form>
                        </div>
                    </div>

                    <!-- </div> -->
                    <!-- <div class="col-md-4"></div> -->
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
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
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