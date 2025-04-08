<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/response.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}


$title = 'TRIAL BALANCE';
require_once('includes/head_tag.php');

$response = new Response();
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
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Trial Balance
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#fixed" data-bs-toggle="tab" class="nav-link active">Branches</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">

                                        <div id="fixed" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Select the Branch </h4>



                                                <?php
                                                $dates = $response->getBankDetails($user[0]['bankId']);

                                                $use_fdate = date('Y') . '-' . $dates[0]['fin_month'] . '-' . $dates[0]['fin_day'];
                                                $effectiveDate = date('Y-m-d', strtotime("+12 months", strtotime($use_fdate)));

                                                $effectiveDate = date('Y-m-d', strtotime('-1 day', strtotime($effectiveDate)));
                                                $branches = $response->getBankBranches($user[0]['bankId']);
                                                foreach ($branches as $b) {
                                                    echo '
                                                    <a href="report_trial_balance.php?branch=' . $b['id'] . '&transaction_start_date=&transaction_end_date=" class="btn btn-primary light btn-xs mb-1">' . $b['name'] . '</a>
                                                    ';
                                                }

                                                ?>
                                                <?php if (@$user[0]['bankId'] == 'f5c30c7f-a28d-4b2e-a44a-2d354f2aaff1') {
                                                    echo '<a href="report_trial_balance.php?branch=d5119096-3499-4630-87ab-ee7fb412e49a&transaction_start_date=&transaction_end_date=" class="btn btn-primary light btn-xs mb-1">General</a>';
                                                } else {
                                                    echo '<a href="report_trial_balance.php" class="btn btn-primary light btn-xs mb-1">General</a>';
                                                }

                                                ?>


                                            </div>
                                            <!-- </div> -->
                                        </div>




                                    </div>
                                    <!-- </div> -->
                                    <!-- Modal -->

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