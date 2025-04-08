<?php
include('../backend/config/session.php');




require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
// $permissions = new PermissionMiddleware();
// if (!$permissions->IsBankAdmin() || !$permissions->IsSuperAdmin()) {
//     return $permissions->isNotPermitted(true);
// }
?>
<?php
require_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $body = $_POST['body'];
    $charge_to = $_POST['charged_to'];
    $charge = $_POST['charge'];
    $sid = $_POST['sid'];

    $res = $response->editSMSType($sid, $body, $charge, $charge_to, $user[0]['bankId']);

    if ($res) {
        setSessionMessage(true, 'SMS Type Updated Successfully!');
        header('location:sms_types');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Trya again');
        header('location:sms_type_settings');
        exit;
    }
}
$title = 'SMS TYPE SETTINGS';
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

                    <div class="col-xl-12 col-lg-12 col-xxl-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> |

                                    SMS Type Configurations Form

                                </h4>
                                <?php
                                $details = $response->getSMSTypeDetails($_GET['t'])[0];
                                ?>

                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3"></p>

                                <hr class="hr-dashed">

                                <div class="row">
                                    <div class="col-md-5">
                                        <h4 class="mt-0 header-title">SMS Type Details</h4>
                                        <p class="text-muted mb-3"></p>

                                        <div class="mb-3 pricingTable1">
                                            <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                                <li><b>Name</b> : <?= @$details['act_name']; ?></li>
                                                <li><b>Charge</b> : <?= @$details['charge']; ?></li>
                                                <li><b>Charged to </b> : <?= @$details['charge_to']; ?></li>
                                                <li><b>Status </b> : <?= @$details['status']; ?></li>
                                            </ul>
                                        </div>


                                    </div>

                                    <div class="col-md-7">
                                        <form method="post">
                                            <h4 class="mt-0 header-title">Update Form</h4>
                                            <p class="text-muted mb-3"></p>
                                            <input type="hidden" class="form-control" name="sid" value="<?= $details['id']; ?>">
                                            <textarea col="5" class="form-control" name="body" required minlength="5" maxlength="150"><?= @$details['body']; ?></textarea>


                                            <br />

                                            <div class="form-group">
                                                <label class=" control-label">SMS Charge: </label>
                                                <input type="number" step="any" class="form-control" name="charge" value="<?= $details['charge']; ?>" required>
                                            </div>
                                            <br />
                                            <div class="form-group">
                                                <label class=" control-label"> Charged To </label>
                                                <select class="form-control" name="charged_to" required>
                                                    <option value="<?= $details['charge_to']; ?>" selected> Select.....</option>
                                                    <option value="client">Charge Client</option>
                                                    <option value="institution" selected="">Charge Institution</option>
                                                </select>
                                            </div>

                                            <br />
                                            <br />


                                            <button type="submit" class="btn btn-block btn-primary" name="submit"><span class="semibold">Save Changes</span></button>
                                        </form>
                                    </div>
                                </div>

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
    <?php include('includes/bottom_scripts.php'); ?>



</body>

</html>