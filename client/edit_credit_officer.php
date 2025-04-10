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

if (isset($_POST['submit'])) {

    $res = $response->editCreditOfficer($_POST);
    if ($res) {
        setSessionMessage(true, 'Credit Officer Updated Successfully!');
        header('location:loan_details_page.php?id=' . $_POST['lid'] . '#wizard_Details');
        exit();
    } else {
        setSessionMessage(false, 'Process failed. Try again!');
        header('location:edit_credit_officer.php?lno=' . $_POST['lid'] . '&staff=' . $_POST['officer']);
        exit();
    }
}


$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);

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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Collaterals</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Add Collateral</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Credit Officer Update Form
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
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="lid" value="<?php echo $_GET['lno']; ?>">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="user" value="<?php echo $user[0]['userId']; ?>">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="branch" value="<?php echo $user[0]['branchId']; ?>">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="bank" value="<?php echo $user[0]['bankId']; ?>">

                                        <div class="mb-3">
                                            <label class="text-label form-label">Select new Credit Officer here:*
                                            </label>
                                            <?php
                                            echo '
                                <select id="osector" class="form-control" aria-hidden="true" name="officer">
                                    <option value="0">None</option>
                                                                ';
                                            if ($staffs !== '') {
                                                foreach ($staffs as $row) {
                                                    if ($row['id'] == $_GET['staff']) {
                                                        echo '
                                                        <option value="' . $row['id'] . '" selected>' . $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] . '</option>
                                                        
                                                        ';
                                                    } else {
                                                        echo '
                            <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] . '</option>
    
                                 ';
                                                    }
                                                }
                                            } else {
                                                echo '
                            <option readonly>No Staffs Added yet</option>
                                                    ';
                                            }

                                            echo
                                            '

                                    </select>
                                ';
                                            ?>

                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Reason for Change of Credit Officer *</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="reason" required>
                                        </div>
                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Update Credit Officer</button>
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