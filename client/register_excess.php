<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->registerExcess($_POST);
    if ($res) {
        setSessionMessage(true, 'Excess Registered Successfully!');
        header('location:staff_excess.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again.');
        header('location:register_excess.php');
        exit;
    }
   
}
require_once('includes/head_tag.php');
$cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);

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
                <div class="card">
                    <div class="card-body">


                        <h4 class="mt-0 header-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Staff Excess Entry
                        </h4>


                        <!-- <p class="text-mutesd mb-3">Till Cash Balance: <b></b></p> -->

                        <hr class="hr-dashed">

                        <form method="post" class="submit_with_ajax">
                            <input type="hidden" name="bank" value="<?= $user[0]['bankId'] ?>" />
                            <input type="hidden" name="branchid" value="<?= $user[0]['branchId'] ?>" />
                            <input type="hidden" name="user" value="<?= $user[0]['userId'] ?>" />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Affected Staff Account:*
                                        </label>
                                        <?php

$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
                                        echo '
                                <select id="authby" class="form-control" name="staff">
    <option value="0">None</option>
        ';
                                        if ($staffs !== '') {
                                            foreach ($staffs as $row) {
                                                echo '
    <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] . '</option>
    
    ';
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

                                    <div class="form-group">
                                        <label control-label> Narration: </label>
                                        <input type="text" id="heading" class="form-control" name="heading" placeholder="" required="">
                                    </div>

                                    <div class="form-group">
                                        <label> Amount: </label>
                                        <input type="text" id="total_amount" class="form-control comma_separated" name="amount" placeholder="" required="">
                                    </div>

                                    <?php
                                    if (!$user[0]['branchId']) {
                                        $branches = $response->getBankBranches($user[0]['bankId']);

                                        echo '
                          <div class="form-group">
                              <label class="text-label form-label">Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                              <option value="0">None</option>
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
                          
                          ';
                                    } else {
                                        echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label>Select Excess Journal Account: </label>
                                        <select name="main_acc" class="form-control select2" id="journalacc">
                                            <option value="">Select....</option>
                                            <?php
                                            $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                            if ($sub_accs) {

                                                foreach ($sub_accs as $acc) {
                                                  

                                                        echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                    
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" id="dest_cash_acc">
                                        <label>Affected Cash Account: </label>
                                        <select id="cash_acc" name="cash_acc" class="form-control select2">

                                            <?php
                                            if ($_SESSION['user']['bankId']) {
                                                foreach ($cash_accounts as $cash_account) {
                                            ?>
                                                    <option value="<?= $cash_account['id'] ?>">
                                                        <?= $cash_account['acname'] ?> - Balance:  <?= number_format($cash_account['balance']) ?>
                                                    </option>
                                            <?php }
                                            } else {
                                                foreach ($cash_accounts as $c_acc) {
                                                    if ($c_acc['userid'] == $user[0]['userId']) {
                                                        echo '<option value="' . $c_acc['cid'] . '"> ' . $c_acc['acname'] . ' - Balance: '.number_format($c_acc['balance']).'</option>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>



                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Record Date: </label>
                                        <input type="date" class="form-control" name="date_of_p" value="<?= date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="" required="">
                                    </div>



                                    <br /><br />

                                    <button type="submit" class="btn btn-primary btn-block" name="submit">Enter Excess</button>

                                </div>
                            </div>
                        </form>
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