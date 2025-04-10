<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_transactions')) {
    return $permissions->isNotPermitted(true);
}
include_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->createInterBranchRequest($_POST);

    if ($res) {
        setSessionMessage(true, 'Inter-Branch Request Submitted Successfully!');
        header('Location:inter_branch_requests.php');
        exit;
    } else {
        setSessionMessage(false, 'Inter-Branch Request not created! Try again.');
        header('Location:inter_branch_requests.php');
        exit;
    }
}
$branches = $response->getBankBranches($user[0]['bankId']);

?>


<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php require_once('includes/preloader.php'); ?>

    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        require_once('includes/nav_bar.php');
        require_once('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Branch to Branch Cash Transfer Request

                        </h4>


                    </div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="mt-0 header-title">Inter Branch Fund Transfer Request </h4>
                                <p class="text-muted mb-3">Fill in the form and proceed</p>

                                <form method="post" class="submit_with_ajax" action="">

                                    <input type="hidden" name="bank" value="<?= $user[0]['bankId']; ?>" />
                                    <input type="hidden" name="bid" value="<?= $user[0]['branchId']; ?>" />
                                    <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>" />
                                    <div class="row">


                                        <?php

                                        $branches = $response->getBankBranches2($user[0]['bankId'], $user[0]['branchId']);

                                        echo '
                          <div class="col-md-6">
                          <div class="mb-3">
                              <label class="text-label form-label">Request Funds From:  *</label>
            <select  class="me-sm-2 default-select form-control wide"
                              id="osector" name="fr"
                               required>
                             
                                  ';
                                        if ($branches !== '') {
                                            foreach ($branches as $row) {
                                                echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                            }
                                        } else {
                                            echo '
                              <option readonly>No Branches Added yet</option>
                              ';
                                        }

                                        echo
                                        '
                          
                              </select>
                          </div>
                          </div>
                          
                          ';

                                        if (!$user[0]['branchId']) {
                                            $branches = $response->getBankBranches($user[0]['bankId']);

                                            echo '
                                            <div class="col-md-6">
                                            <div class="form-group">
     
          <label class="text-label form-label">Request Funds for:  *</label>
          <select id="branchselect" class="form-control"  name="tr" required>
         
              ';
                                            if ($branches !== '') {
                                                foreach ($branches as $row) {
                                                    echo '
          <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
          
          ';
                                                }
                                            } else {
                                                echo '
          <option readonly>No Branches Added yet</option>
          ';
                                            }

                                            echo
                                            '
      
          </select>
      </div>
      </div>
   
      
      ';
                                        } else {
                                            echo '

        <input type="hidden" name="tr" value="' . $user[0]['branchId'] . '" class="form-control" >

        
        ';
                                        }

                                        ?>




                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="text" name="amount" value="" step="any" class="form-control comma_separated">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Mode of payment: </label>
                                                <select id="oscategory" name="payment_mode" class="form-control">
                                                    <option value=""> Select.....</option>
                                                    <option value="cash">Cash</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Date</label>
                                                <input type="date" name="trxn_date" value="<?= date('Y-m-d') ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <br /><br />
                                            <button type="submit" class="btn btn-primary" name="submit">Forward Request <i class="ti-arrow-circle-right"></i></button>
                                        </div>

                                    </div>

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
    <?php require_once('includes/bottom_scripts.php'); ?>


</body>

</html>