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

$title = 'ADD COLLATERAL';
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
                                    Loan Collateral Form
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
                                    <form method="POST" enctype="multipart/form-data" action="https://eaoug.org/add_collateral.php">


                                        <!-- <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                    <option value="AL" data-select2-id="2">Alabama</option>
                                    <option value="WY" data-select2-id="104">Wyoming</option>
                                </select> -->


                                        <div class="mb-3">
                                            <label class="text-label form-label">Type of Collateral
                                                *</label>

                                            <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="catid" required>
                                                <option selected></option>
                                                <?php
                                                foreach ($response->getCollateralCategories($user[0]['bankId'], $user[0]['branchId']) as $row) {

                                                    echo '
                                                    <option value="' . $row['_catid'] . '">' . $row['_catname'] . '</option>
                                                    ';
                                                }
                                                ?>


                                            </select>
                                        </div>
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="lid" value="<?php echo $_GET['id']; ?>">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="uid" value="<?php echo $user[0]['userId']; ?>">

                                        <div class="mb-3">
                                            <label class="text-label form-label">Name of Collateral *</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="name" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Location of Collateral *</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="location" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Market Value *</label>

                                            <input type="number" class="form-control input-rounded" placeholder="" name="mv" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-label form-label">Forced Sale Value *</label>

                                            <input type="number" class="form-control input-rounded" placeholder="" name="fv" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Attachment of the collateral </label>

                                            <input type="file" class="form-control input-rounded" placeholder="" name="attach">
                                        </div>




                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Add
                                            Collateral</button>
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