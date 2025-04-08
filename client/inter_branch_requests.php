<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_transactions')) {
    return $permissions->isNotPermitted(true);
}
include_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);

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


                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

                        <form class="ajax_results_form" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="form-control " id="branchselect" name="branchId">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branchId'] == $row['id'] ? "selected" : "";
                                                ?>
                                                        <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                            <?= $row['name'] ?>
                                                        </option>
                                                <?php }
                                                } ?>

                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="text-label form-label">Start Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?php @$_REQUEST['start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="text-label form-label">End Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?php @$_REQUEST['end_date'] ?>" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch
                                            Entries</button>
                                    </div>
                                </div>


                            </div><br />


                        </form>


                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            All Inter-Branch Requisitions
                            <a href="add_inter_branch_request.php" class="btn btn-primary light btn-xs mb-1">Create Inter-Branch Request</a>
                        </h4>


                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="cashtrans2" class="display dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Mode of Payment</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $trxns  = $response->getInterBranchRequests($user[0]['bankId'], $user[0]['branchId']);

                                    if ($trxns != '') {
                                        $count = 1;
                                        $st = '';
                                        foreach ($trxns as $trxn) {

                                            if ($trxn['status'] == 0) {
                                                $st =   '<span class="badge badge-rounded badge-danger">Pending</span>';
                                            } else if ($trxn['status'] == 1) {
                                                $st =   '<span class="badge badge-rounded badge-primary">Completed</span>';
                                            } else {
                                                $st =   '<span class="badge badge-rounded badge-danger">Declined</span>';
                                            }


                                            echo '
                                            <tr>
                                            <td>' . $count++ . '</td>
                                           
                                            <td>' . number_format($trxn['amount']) . '</td>
                                            <td>' . $st . '</td>
                                            <td>' . $trxn['fname'] . '</td>
                                            <td>' . $trxn['tname'] . '</td>
                                            <td>' . $trxn['pmode'] . '</td>
                                            <td>' . normal_date($trxn['date']) . '</td>
                                            <td>
                                            <div class="dropdown custom-dropdown mb-0"><div class="btn sharp btn-primary tp-btn" data-bs-toggle="dropdown"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"> <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="12" cy="5" r="2"> </circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="19" r="2"></circle></g></svg>
                </div><div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item text-primary" href="approve_request.php?id=' . $trxn['id'] . '">Approve Request</a><a class="dropdown-item text-danger" href="decline_request.php?id=' . $trxn['id'] . '">Decline Request</a> </div></div>
                                            </td>
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
    <?php require_once('includes/bottom_scripts.php'); ?>


</body>

</html>