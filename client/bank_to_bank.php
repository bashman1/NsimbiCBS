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
    $_POST['ttype'] = 'BTB';
    $_POST['notes'] =  'BANK TO BANK TRANSFER:  ' . $_POST['notes'];
    $res = $response->cashTransfer($_POST);
    if ($res) {

        setSessionMessage(true, 'Cash Transfered successfully!');
        header('location:bank_to_bank.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:bank_to_bank.php');
    }
    exit;
}

$title = 'Bank to Bank Transfer';
require_once('includes/head_tag.php');

$bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Fees</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">New Fee</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Bank To Bank Transfer
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">

                                        <input type="hidden" name="bank" value="<?= $user[0]['bankId']; ?>" />
                                        <input type="hidden" name="bid" value="<?= $user[0]['branchId']; ?>" />
                                        <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>" />
                                        <?php
                                        if (!$user[0]['branchId']) {


                                            $branches = $response->getBankBranches($user[0]['bankId']);

                                            echo '
                          <div class="col-lg-6 mb-2">
                          <div class="mb-3">
                              <label class="text-label form-label">Branch *</label>
                              <select  class="me-sm-2 default-select form-control wide"
                              id="branchselect" name="branch"
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
                                        } else {
                                            echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                        }
                                        ?>


                                        <div class="col-lg-6 mb-2">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Select Bank A/C to transfer from:
                                                    *</label>
                                                <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="sender" required>
                                                    <option selected></option>
                                                    <?php




                                                    if ($bank_accounts) {

                                                        foreach ($bank_accounts as $b_acc) {
                                                            echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Balance: ' . number_format($b_acc['balance']) . '</option>';
                                                        }
                                                    }


                                                    ?>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-lg-6 mb-2">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Select Bank A/C to transfer to:
                                                    *</label>
                                                <select class="me-sm-2 default-select form-control wide" id="cash_trans" name="receiver" required>
                                                    <option selected></option>
                                                    <?php

                                                    if ($bank_accounts) {

                                                        foreach ($bank_accounts as $b_acc) {
                                                            echo '<option value="' . $b_acc['cid'] . '">' . $b_acc['accno'] . ' - ' . $b_acc['account_name'] . ' - Balance: ' . number_format($b_acc['balance']) . '</option>';
                                                        }
                                                    }


                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Amount:
                                                    *</label>
                                                <input type="text" name="amount" class="form-control comma_separated input-rounded" min="0" value="0" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Notes:
                                                </label>
                                                <textarea class="form-control input-rounded" placeholder="" name="notes" col="5"></textarea>

                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-2">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Trxn Date:
                                                    *</label>
                                                <input type="date" name="trxndate" class="form-control" value="<?= date('Y-m-d'); ?>" />
                                            </div>
                                        </div>

                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <input type="submit" name="submit" class="btn btn-primary" value="Process Transfer" onclick=" this.value='Processing…'; this.form.submit(); this.disabled=true; " />
                                        <!-- </div> -->
                                        <!-- onclick=" this.value='Processing…'; this.form.submit(); this.disabled=true; " -->
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