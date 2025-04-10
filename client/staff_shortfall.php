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
$title = 'STAFF SHORTFALLS';
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
                                    Staffs with Shortfalls
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#onetoone" data-bs-toggle="tab" class="nav-link  <?= !in_array(@$_REQUEST['current_tab'], ['onetomany']) ? 'active' : '' ?> ">Staff Shortfalls</a>
                                        </li>



                                    </ul>
                                    <div class="tab-content">
                                        <div id="products" class="tab-pane fade <?= !in_array(@$_REQUEST['current_tab'], ['onetomany']) ? 'show active' : '' ?>" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Staff Shortfalls</h4>
                                                    <?php
                                                    if($permissions->hasPermissions('register_staff_shortfalls')):
                                                    ?>
                                                    <a href="register_shortfall.php" class="btn btn-primary light btn-xs mb-1">Register Shortfall</a>
<?php endif;?>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="example3" class="table table-striped" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Narration</th>
                                                                    <th>Status</th>
                                                                    <th>Amount</th>
                                                                    <th>Amount Paid</th>
                                                                    <th>Affected Cash A/C</th>
                                                                    <th>Affected Journal A/C</th>
                                                                    <th>Branch</th>

                                                                    <th>Date Created</th>

                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $frs = $response->getStaffShortfalls($user[0]['bankId'], $user[0]['branchId']);
                                                                if ($frs != '') {
                                                                    foreach ($frs as $fr) {
                                                                        echo ' 
        <tr>
        <td>' . $fr['id'] . '</td>
        <td>' . $fr['name'] . '</td>
        <td>' . $fr['notes'] . '</td>
        <td>' . $fr['status'] . '</td>
        <td>' . $fr['amount'] . '</td>
        <td>' . $fr['amount_paid'] . '</td>
        <td>' . $fr['cash'] . '</td>
        <td>' . $fr['journal'] . '</td>
        <td>' . $fr['branch'] . '</td>
       
        <td>' . normal_date($fr['date']) . '</td>
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