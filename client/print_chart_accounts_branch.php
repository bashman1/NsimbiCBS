<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
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

$response = new Response();

$title = 'CHART ACCOUNTS';
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
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">

                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Branch Chart Accounts
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#fixed" data-bs-toggle="tab" class="nav-link active">Chart Accounts</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">

                                        <div id="fixed" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Chart Accounts </h4>
                                                <?php

                                                $general_bid = '';
                                                if ($user[0]['bankId']) {
                                                    $branches = $response->getBankBranches($user[0]['bankId']);

                                                    foreach ($branches as $b) {
                                                        if (@$b['is_main'] > 0) {
                                                            $general_bid = @$b[0]['id'] ?? '';
                                                        }
                                                        echo '
                                                    <a href="export_report.php?exportFile=export_chart_of_accounts&useFile=1&branch=' . $b['id'] . '" class="btn btn-primary light btn-xs mb-1">' . $b['name'] . '</a>
                                                    ';
                                                    }
                                                } else {
                                                    $general_bid = $user[0]['branchId'];
                                                    echo '
                                                    <a href="export_report.php?exportFile=export_chart_of_accounts&useFile=1&branch=' . $user[0]['branchId'] . '" class="btn btn-primary light btn-xs mb-1">' . $user[0]['branchName'] . '</a>
                                                    ';
                                                }
                                                ?>

                                                <a href="export_report.php?exportFile=export_chart_of_accounts_2&useFile=1&branch=<?= $general_bid ?>" class="btn btn-primary light btn-xs mb-1">General</a>


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