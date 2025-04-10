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
    $res = $response->advancedJournalEntry($_POST);
    if ($res) {
        setSessionMessage(true, 'Journal Entry Registered Successfully!');
        header('location:accounting_tab.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again.');
        header('location:advanced_journal_entry.php');
    }
    exit;
}
$title = 'ADVANCED JOURNAL ENTRY';
require_once('includes/head_tag.php');
// $cash_accounts = $response->getAllBranchCashAccounts($user[0]['bankId'], $user[0]['branchId']);
// $bank_accounts = $response->getAllBankAccounts($user[0]['bankId'], $user[0]['branchId']);

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
                            Advanced Journal Entry
                        </h4>

                        <hr class="hr-dashed">
                        <form method="post" class="submit_with_ajax">
                            <input type="hidden" name="bank" value="<?= $user[0]['bankId']; ?>" />
                            <input type="hidden" name="bid" value="<?= $user[0]['branchId']; ?>" />
                            <input type="hidden" name="user" value="<?= $user[0]['userId']; ?>" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">

                                            <h4 class="mt-0 header-title">Sender:</h4>
                                            <p class="text-muted mb-3">Select Account <i>*</i></p>

                                            <div class="form-group">
                                                <label></label>
                                                <select name="debit_account" id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                                    <option value="">Select....</option>
                                                    <?php
                                                    // $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                                    $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                                    if ($sub_accs) {

                                                        foreach ($sub_accs as $acc) {
                                                            if ($acc['is_main_account'] == 0) {

                                                                echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ': Branch: ' . $acc['branch'] . '  -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">

                                            <h4 class="mt-0 header-title">Receiver:</h4>
                                            <p class="text-muted mb-3">Select Account <i>*</i></p>

                                            <div class="form-group">
                                                <label></label>
                                                <select name="credit_account" class="form-control" id="credit_account">
                                                    <option value="">Select....</option>
                                                    <?php
                                                    // $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                                    if ($sub_accs) {

                                                        foreach ($sub_accs as $acc) {

                                                            if ($acc['is_main_account'] == 0) {
                                                                echo '<option value="' . $acc['id'] . '">' . $acc['name'] . ':  Branch: ' . $acc['branch'] . '  -  Balance: ' . number_format($acc['balance']) . '</option>';
                                                            }
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-md-6">

                                                    <!-- <h4 class="mt-0 header-title"></h4> -->
                                                    <p class="text-muted mb-3">Journal Entry Amount:</p>

                                                    <div class="form-group">
                                                        <!-- <label></label> -->
                                                        <input type="text" class="form-control" name="amount" value="" required="">
                                                    </div>
                                                </div>
                                                <?php
                                                if (!$user[0]['branchId']) {
                                                    $branches = $response->getBankBranches($user[0]['bankId']);

                                                    echo '
                                                     <div class="col-md-6">
                          <div class="form-group">
                            
                                            <p class="text-muted mb-3">Affected Branch: *</p>
                              <select id="branchselect"  class="form-control" name="branch" required>
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
                          </div>
                          
                          ';
                                                } else {
                                                    echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                                }
                                                ?>

                                                <div class="col-md-6">
                                                    <!-- <h4 class="mt-0 header-title"></h4> -->
                                                    <p class="text-muted mb-3">Narration:</p>

                                                    <div class="form-group">
                                                        <!-- <label></label> -->
                                                        <input type="text" class="form-control" name="heading" value="" required="">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <!-- <h4 class="mt-0 header-title"></h4> -->
                                                    <p class="text-muted mb-3">Entry Date:</p>

                                                    <div class="form-group">
                                                        <!-- <label></label> -->
                                                        <input type="date" class="form-control" name="date_of_p" value="<?= date('Y-m-d'); ?>" required="">
                                                    </div>
                                                    <!-- <br /><br /><br /> -->

                                                </div>
                                                <div class="col-md-6">
                                                    <p class="text-muted mb-3">&nbsp;</p>
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary btn-block" name="submit">Enter Journal Entry</button>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
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