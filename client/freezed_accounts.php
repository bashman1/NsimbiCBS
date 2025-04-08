<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once './includes/response.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();
$title = 'FREEZED A/Cs';
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
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Accounts with Freezed Balances
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#onetoone" data-bs-toggle="tab" class="nav-link  <?= !in_array(@$_REQUEST['current_tab'], ['onetomany']) ? 'active' : '' ?> ">Freezed Accounts</a>
                                        </li>



                                    </ul>
                                    <div class="tab-content">
                                        <div id="products" class="tab-pane fade <?= !in_array(@$_REQUEST['current_tab'], ['onetomany']) ? 'show active' : '' ?>" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Freezed Accounts</h4>
                                                    <a href="freeze_account_search.php" class="btn btn-primary light btn-xs mb-1">Freeze Account</a>

                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="example3" class="table table-striped" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Freezed Amount</th>
                                                                    <th>Category</th>
                                                                    <th>Reason</th>

                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $frs = $response->getFreezedAccounts($user[0]['bankId'], $user[0]['bankId'] == '' ? $user[0]['branchId'] : '');
                                                                if ($frs != '') {
                                                                    foreach ($frs as $fr) {
                                                                        echo ' 
        <tr>
        <td>' . $fr['id'] . '</td>
        <td>' . $fr['name'] . '</td>
        <td>' . $fr['amount'] . '</td>
        <td>' . $fr['fr_cat'] . '</td>
        <td>' . $fr['reason'] . '</td>
        <td>' . $fr['actions'] . '</td>
        </tr>
        
        
        ';
                                                                    }
                                                                }
                                                                ?>



                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>

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