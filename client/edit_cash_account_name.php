<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
require_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_POST['submit'])) {

    $res = $response->updateTrxnAccountName($_POST);
    // var_dump($res);
    // exit;
    if ($res['success']) {
        setSessionMessage(true, "Cash A/c Name Updated Successfully!");
        header('location: trxn_accounts.php#transactions');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to update account name');
        RedirectCurrent();
    }
}

$title = 'ACCOUNT NAME CHANGE';

require_once('includes/head_tag.php');

$actypes = $response->getAccountDetails($_REQUEST['acid']);
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
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Update A/C Name
                                </h4>
                            
                            </div>
                            <div class="card-body">

                                <p class="text-muted mb-3">
                                    Using the form below, you can update the name of a trxn account<br />
                                    <b class="text-danger">(NOTE: The new name shall be displayed even on the previous trxns of with the old name).</b>
                                </p>

                                <hr class="hr-dashed">

                                <div class="btc-price">
                                    <p class="text-muted mb-3">A/C Name: <?= @$actypes[0]['aname']; ?><br />
                                        A/C Code: <?= @$actypes[0]['acode']; ?><br />
                                        Balance: <?= number_format($actypes[0]['balance'] ?? 0); ?><br />
                                        A/C Type: <?= @$actypes[0]['type']; ?><br />
                                        Branch: <?= @$actypes[0]['bname']; ?><br />
                                        Description: <?= @$actypes[0]['description']; ?><br />
                                    </p>


                                    <hr class="hr-dashed">

                                </div><br />
                                <form method="POST">
                                    <input type="hidden" name="user" class="form-control" placeholder="" value="<?= $user[0]['userId'] ?>">
                                    <input type="hidden" name="id" class="form-control" placeholder="" value="<?= $_REQUEST['id'] ?>">
                                    <input type="hidden" name="acid" class="form-control" placeholder="" value="<?= $_REQUEST['acid'] ?>">
                                  

                                    <div class="tab-content">

                                        <div class="row ">


                                            

                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">New Account Name *</label>
                                                    <input type="text" name="name" class="form-control" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Reason for Change *</label>
                                                    <input type="text" name="notes" class="form-control" placeholder="" required>
                                                </div>
                                            </div>



                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <button type="submit" name="submit" value="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                            <br /><br />
                                        </div>
                                        <!-- </div> -->

                                </form>
                                <!-- </div> -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('includes/footer.php'); ?>



        </div>


        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#smartwizard').smartWizard();


            });
        </script>



</body>

</html>