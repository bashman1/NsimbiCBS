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
    $_POST['ttype'] = 'STS';
    $_POST['notes'] =  'SAFE TO SAFE TRANSFER:  ' . $_POST['notes'];
    $res = $response->cashTransfer($_POST);
    if ($res) {

        setSessionMessage(true, 'Cash Transfered successfully!');
        header('location:safe_to_safe.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:safe_to_safe.php');
    }
    exit;
}
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
                                    Reserve To Reserve Transfer
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
                                                <label class="text-label form-label">Select Receiving Reserve A/C:
                                                    *</label>
                                                <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="receiver" required>
                                                    <option selected></option>
                                                    <?php

                                                    $reserve_accounts = $response->getAllBranchReserveAccounts($user[0]['bankId'], $user[0]['branchId']);
                                                    if ($reserve_accounts != '') {
                                                        foreach ($reserve_accounts as $c_acc) {

                                                            echo '<option value="' . $c_acc['cid'] . '"> ' . $c_acc['acname'] . ':  -  Balance: ' . number_format($c_acc['balance']) . '</option>';
                                                        }
                                                    }

                                                    ?>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-lg-6 mb-2">
                                            <div class="mb-3">
                                                <label class="text-label form-label">Select Reserve A/C being affected:
                                                    *</label>
                                                <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;" name="sender" required>
                                                    <option selected data-bal="0"></option>
                                                    <?php

                                                    $reserve_accounts = $response->getAllBranchReserveAccounts($user[0]['bankId'], $user[0]['branchId']);
                                                    if ($reserve_accounts != '') {
                                                        foreach ($reserve_accounts as $c_acc) {

                                                            echo '<option value="' . $c_acc['cid'] . '" data-bal="' . $c_acc['balance'] . '"> ' . $c_acc['acname'] . ':  -  Balance: ' . number_format($c_acc['balance']) . '</option>';
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
                                                <input type="text" name="amount" class="form-control comma_separated input-rounded" min="0" value="0" data-type="amount" data-max="0" id="tr_amount" />
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

                                        <input type="submit" name="submit" class="btn btn-primary" onclick=" this.value='Processing…'; this.form.submit(); this.disabled=true;   " value="Process Transfer" />
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
        <script>
            $(document).ready(function() {
                // Watch for changes in the select element
                $('#inlineFormCustomSelect').on('change', function() {
                    // Get the selected option using jQuery
                    var selectedOption = $(this).find('option:selected');

                    // Retrieve the data-bal attribute using jQuery
                    var dataBalValue = selectedOption.attr("data-bal");

                    //   update the dat-max
                    $('#tr_amount').attr('data-max', dataBalValue);
                });

                // Trigger the change event to handle the default selected option on page load
                $('#single-select').trigger('change');
            });
        </script>
</body>

</html>