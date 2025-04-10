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

include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->sendSingleSMS($_POST['send_to'], $_POST['sms_phone'], $_POST['client'], $_POST['sms_text'], $_POST['branch'], $user[0]['userId'], $_POST['charge'], $_POST['senderid']);
    if ($res) {
        setSessionMessage(true, 'SMS Sent Successfully!');
        header('location:send_single_sms.php');
        // exit;
    } else {
        setSessionMessage(false, 'SMS Sending failed! Check your SMS OutBox to resend the SMS');
        header('location:send_single_sms.php');
        // exit;
    }
}

include('includes/head_tag.php');


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
                            <!-- <div class="col-md-4"></div> -->

                            <div class="col-md-6">

                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Compose SMS Form

                                </h4>
                                <p class="text-muted mb-3">Single sms Sending Form</p>

                                <hr class="hr-dashed">

                                <form class="submit_with_ajax" loading-text="Sending Sms" method="post">
                                    <?php
                                    if ($user[0]['branchId'] != '') {
                                        echo '
                             <input type="hidden" class="form-control" name="branch" value="' . $user[0]['branchId'] . '">
                            ';
                                    } else {
                                        $branches = $response->getBankBranches($user[0]['bankId']);

                                        echo '
                          <div class="form-group">
                         
                              <label class="text-label form-label">Branch (SMS Charge Shall be attached to this Branch) *</label>
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

                                    <br />
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
                                        <label class=" control-label"> Send to </label>
                                        <select id="send_to" name="send_to" class="form-control">

                                            <option value="sub" selected>Client / Member</option>
                                            <option value="other"> Others</option>
                                        </select>
                                    </div>
                                    <br />
                                    <div class="form-group" id="other" style="display: none;">
                                        <label>Telephone no (Include Country Code e.g 256707xxxxxx) : </label>
                                        <input type="number" class="form-control" name="sms_phone" value="">
                                    </div>
                                    <br />
                                    <div id="subscribed_member" style="display: none;">
                                        <div class="form-group">
                                            <label>Search Client :</label>
                                            <select id="clientsselect" class="form-control select2x" name="client" required>
                                            </select>
                                        </div>
                                    </div>
                                    <br />

                                    <div class="form-group" style="display: none;" id="charge">
                                        <label>Charge : (If > 0 , system shall offset from member's savings (for Members) or loan wallet (for non-members) ) </label>
                                        <input type="number" class="form-control" name="charge" value="0" min="0">
                                    </div><br />

                                    <div class="form-group">
                                        <label>SMS Body (<i class="text-danger" id="char_count">160 remaining</i>): </label>
                                        <p class="text-muted mb-3">Your message body is limited to 160 Characters for every single SMS </p>
                                        <textarea class="form-control" id="sms_text" rows="9" cols="9" name="sms_text" minlength="5" maxlength="160" required=""></textarea>
                                    </div>
                                    <br /><br />
                                    <button type="submit" name="submit" class="btn btn-block btn-primary"><i class="ti-envelope"></i>
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


    <script type="text/javascript">
        $(document).ready(function() {
            var send_to = $('#send_to');
            var subscribed_member = $('#subscribed_member');
            var other = $('#other');
            var charge = $('#charge');

            recipientTypeField();

            send_to.on('change', recipientTypeField);

            function recipientTypeField() {
                subscribed_member.hide();
                other.hide();
                charge.hide();
                var type = send_to.val();

                if (type == 'other') {
                    other.show();
                } else {
                    subscribed_member.show();
                    charge.show();
                }
            }

            $('#sms_text').on('keyup', function() {
                var char_count = $(this).val().length;
                var max_length = $(this).attr('maxlength');

                if (char_count > max_length) return false;

                $('#char_count').text((max_length - char_count) + ' remaining');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // SmartWizard initialize
            $('#smartwizard').smartWizard();
        });

        $(document).ready(function() {
            $("select.select2x").select2({
                ajax: {
                    url: "<?php echo BACKEND_BASE_URL ?>User/get_all_bank_clients_search.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>",
                    dataType: 'json',
                    data: (params) => {
                        return {
                            q: params.term,
                        }
                    },

                    processResults: (data, params) => {
                        const results = data.data.map(item => {
                            return {
                                id: item.userId,
                                text: item.accno + ' : ' + item.name + ' - UGX ' + item.tot_balance + '  - Branch: ' + item.branchName,
                            };
                        });
                        return {
                            results: results,
                        }
                    },
                },
            });
        })
    </script>


</body>

</html>