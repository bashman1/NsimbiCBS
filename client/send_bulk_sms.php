<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require('./middleware/PermissionMiddleware.php');
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

require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
$ssdate = '';
if (isset($_POST['submit'])) {
    $ssdate = $_POST['date'];
    $res = $response->scheduleBulkSMS($_POST);
    if ($res) {
        setSessionMessage(true, 'SMS Scheduled Successfully on ' . $ssdate . ' Check Scheduled SMS');
        header('location:send_bulk_sms.php');
        // exit;
    } else {
        setSessionMessage(false, 'SMS Scheduling failed! Try again to schedule SMS');
        header('location:send_bulk_sms.php');
        // exit;
    }
}


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

                        <div class="row">
                            <div class="col-md-4">

                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Bulk SMS Scheduling
                                </h4>
                                <p class="text-muted mb-3">Summary</p>

                                <hr class="hr-dashed">
                                <?php
                                $clients = $response->getTotalClients($user[0]['bankId'], $user[0]['branchId'])[0]['total'];
                                $smsclients = $response->getTotalSMSClients($user[0]['bankId'], $user[0]['branchId'])[0]['total'];
                                $smswallet = $response->getBankSMSWalletDetails($user[0]['branchId']);
    $smsbal = $response->getBranchSMSBalance($user[0]['bankId'], $user[0]['branchId'])[0]['total'];
                                $actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);

                                ?>
                                <div class="mb-3 pricingTable1">
                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>Clients</b> : <?= number_format(@$clients); ?></li>
                                        <li><b>Clients Subscribed to SMS Banking</b> : <?= number_format(@$smsclients); ?></li>
                                        <li><b>Un-Subscribed Clients</b> : <?= number_format(@$clients - @$smsclients); ?>
                                        </li><br />
                                        <li><b>Expected Expense (If all Clients are to receive SMS) </b> :<br /> <span style="color:#7f32a8 !important;"><?= number_format(@$clients * 50); ?> (With Default SenderID) | <?= number_format(@$clients * 100); ?> (With your Own SenderID)<span></li><br />
                                        <li><b>SMS Wallet Balance </b> : <?= number_format(@$smsbal); ?></li><br />
                                        <li style="color:#44814E !important;">** Note that for Bulk SMS , they're scheduled and the system will automatically send them at Midday (12:00 noon) or Evening at 4:00PM of the scheduled date below</li>
                                        <li style="color:#44814E !important;"><b>You can always check out sent or failed SMS in the 'SMS OUTBOX'.</b></li>
                                    </ul>
                                </div>
                                <hr class="hr-dashed">

                                <div class="alert alert-info">
                                    <?php
                                    if ($smsbal > ($smsclients * 50)) {
                                        echo '<span class="text-primary">You have sufficient SMS to Cover All Clients: Bal: ' . number_format($smsbal) . '</span>';
                                    } else {
                                        echo '<span class="text-danger">You have Insufficient SMS to Cover All Clients: Bal: ' . number_format($smsbal) . '<br/>Endeavour to Purchase more SMS before the scheduled SMS sending date.</span>';
                                    }
                                    ?>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <h4 class="mt-0 header-title">Compose SMS Form </h4>
                                <p class="text-muted mb-3">Bulk sms sending</p>

                                <hr class="hr-dashed">

                                <form method="post">
                                    <input type="hidden" class="form-control" name="user" value="<?php echo $user[0]['userId']; ?>">
                                    <div class="form-group">
                                        <label class=" control-label"> Sender ID </label>
                                        <select id="senderid" name="senderid" class="form-control" required>

                                            <option value="0" selected>Default Sender ID</option>
                                            <?php
                                            $senderids = $response->getBankSenderIds($user[0]['bankId'], $user[0]['branchId']);
                                            if ($senderids != '') {
                                                foreach ($senderids as $row) {
                                                    echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . '</option>
                              
                              ';
                                                }
                                            }

                                            ?>

                                        </select>
                                    </div>
                                    <br />

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Select Category<i>*</i>:</label>
                                        <select id="type" name="type" class="form-control" required>

                                            <option value="all" selected>General / All Clients</option>
                                            <option value="sp"> Based on Savings Product</option>
                                        </select>
                                    </div><br />

                                    <div id="target_audience_div">
                                        <div class="form-group" id="customer_categories_div" style="display: none;">
                                            <label>Select Savings Product <i>*</i>:</label>
                                            <?php
                                            echo '

   

   

        <select class="me-sm-2 default-select form-control wide"
        id="inlineFormCustomSelect" name="actype"
        style="display: none;">
        <option selected value="0"></option>

   

        ';

                                            foreach ($actypes as $row) {
                                                echo '
                                                        <option value="' . $row['id'] . '">' . $row['ucode'] . ' - ' . $row['name'] . '</option>
                                                        
                                                        ';
                                            }


                                            echo
                                            '

    </select>


'; ?>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="form-group">

                                        <?php
                                        if ($user[0]['branchId'] != '') {
                                            echo '
                             <input type="hidden" class="form-control" name="branch" value="' . $user[0]['branchId'] . '">
                            ';
                                        } else {
                                            $branches = $response->getBankBranches($user[0]['bankId']);

                                            echo '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Branch (SMS Charges Shall be attached to this Branch) *</label>
                              <select class="form-control" name="branch" required>
                             
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
                                        }
                                        ?>


                                    </div>
                                    <br />
                                    <div class="form-group" id="charge">
                                        <label>Charge : (If > 0 , system shall offset from member's savings (for Members) or loan wallet (for non-members) ) </label>
                                        <input type="number" class="form-control" name="charge" value="0" min="0">
                                    </div><br />
                                    <div class="form-group">
                                        <label for="inputPassword" class=" control-label">Unique Key : <br><small>Any
                                                random text eg: (agm_<?= date('Y'); ?>, new_service_intro,
                                                etc)</small></label>
                                        <input type="text" class="form-control" name="unique_key" value="" required="">

                                        <small class="text-muted">This is to be used to uniquely identify this message from all other messages</small>
                                    </div>
                                    <br />
                                    <div class="form-group">
                                        <label>SMS Body (<i class="text-danger" id="char_count">160 remaining</i>): </label>
                                        <p class="text-muted mb-3">You can Use [fname] for Client's First Name ; [acno] for Client's A/C No. ; [balance] for Client's Loan's Wallet / Savings Balance; [actype] for Client's Savings Product Name; </p>
                                        <textarea class="form-control" id="sms_text" rows="9" cols="9" name="sms_text" minlength="5" maxlength="160" required=""></textarea>
                                    </div>
                                    <br /><br />

                                    <div class="form-group">
                                        <label for="inputPassword" class=" control-label">Sending Date: </label>
                                        <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required id="scheduledate">

                                        <small class="text-muted">You can schedule to any date. System will automatically send these SMS on this date</small>
                                    </div>
                                    <br />
                                    <button type="submit" class="btn btn-block btn-primary" name="submit"><i class="ti-envelope"></i>
                                        Send SMS</button>

                                </form>
                            </div>
                            <!-- <div class="col-md-4"></div> -->
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

    <script>
        let tdate = new Date().toISOString().split("T")[0];
        document.getElementById('scheduledate').setAttribute("min", tdate);
    </script>
    <script type="text/javascript">
        $(document).ready(function() {


            var customer_categories_div = $('#customer_categories_div');
            customer_categories_div.hide();
            $('#type').on('change', function() {
                $(this).val() == 'sp' ? customer_categories_div.show() :
                    customer_categories_div.hide();
            });

            $('#sms_text').on('keyup', function() {
                var char_count = $(this).val().length;
                var max_length = $(this).attr('maxlength');

                if (char_count > max_length) return false;

                $('#char_count').text((max_length - char_count) + ' remaining');
            });
        });
    </script>



</body>

</html>