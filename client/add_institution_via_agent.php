<?php
// include('../backend/config/session.php');
// require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
require_once('includes/functions.php');

// $permissions = new PermissionMiddleware();
// if (!$permissions->IsBankStuff()) {
//     return $permissions->isNotPermitted(true);
// }

$response = new Response();

if (isset($_POST['submit'])) {
    // $actype = isset($_POST['actype']) ? $_POST['actype'] : ''; 

    $_POST['otherattach'] = null;
    $_POST['passport'] = null;
    $_POST['signature'] = null;
    $_POST['fing'] = null;

    $_POST['client_type'] = 'institution';
    $res = $response->addClient($_POST);

    if ($res['success']) {
        setSessionMessage(true, 'Institution Created Successfully');
        Redirect('account_opening_success_page.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Check the Institution\'s table to confirm if all the client\'s were created right.');
        RedirectSelf();
    }
    exit();
}

require_once('includes/head_tag.php');

$staff_details = $response->getStaffDetails($_GET['id'])[0];
$actypes = $response->getAllSavingsAccounts($staff_details['bankId'], $staff_details['branchId']);
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
        // include('includes/nav_bar.php');
        // include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Clients</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Create New Client</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Add New Institution
                                </h4>

                            </div>
                            <div class="card-body">

                                <div id="smartwizard" class="form-wizard order-create">
                                    <ul class="nav nav-wizard">
                                        <li><a class="nav-link" href="#wizard_Service">
                                                <span>1</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_Time">
                                                <span>2</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_files">
                                                <span>3</span>
                                            </a></li>

                                        <li><a class="nav-link" href="#wizard_confirm">
                                                <span>✓</span>
                                            </a></li>
                                    </ul>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="tab-content">
                                            <div id="wizard_Service" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">General Information</h4>
                                                <input type="hidden" name="uid" value="<?php echo $staff_details['userId']; ?>" class="form-control">
                                                <div class="row">

                                                    <?php
                                                    if (!$staff_details['branchId']) {
                                                        $branches = $response->getBankBranches($staff_details['bankId']);

                                                        echo '
                          <div class="col-lg-6 mb-2">
                          <div class="mb-3">
                              <label class="text-label form-label">Branch *</label>
                              <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch" required>
                             
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

                            <input type="hidden" name="branch" value="' . $staff_details['branchId'] . '" class="form-control" >

                            
                            ';
                                                    }
                                                    ?>


                                                    <?php
                                                    // if (isset($_GET['1'])) {
                                                    echo '
<div class="col-lg-6 mb-2">
<div class="mb-3">
    <label class="text-label form-label">Choose Saving Account
        Type*</label>

   

        <select class="me-sm-2 default-select form-control wide"
        id="inlineFormCustomSelect" name="actype"
        style="display: none;" required>
        <option selected=""></option>

   

        ';

                                                    foreach ($actypes as $row) {
                                                        echo '
                                                        <option value="' . $row['id'] . '">' . $row['ucode'] . ' - ' . $row['name'] . '</option>
                                                        
                                                        ';
                                                    }


                                                    echo
                                                    '

    </select>
</div>
</div>

';
                                                    // } else {
                                                    //     echo '
                                                    //     <input type="hidden" name="actype" class="form-control" placeholder="" value="0">

                                                    //     ';
                                                    // }
                                                    ?>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Institution Name*</label>
                                                            <input type="text" name="name" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">SMS Message
                                                                Consent*</label>

                                                            <select class="me-sm-2 default-select form-control wide activate-sections" id="inlineFormCustomSelect" name="message" style="display: none;" required>
                                                                <option value="1" data-sections="phone-number-sms-consent" data-activate="1">Yes</option>
                                                                <option value="0" data-sections="phone-number-sms-consent" data-activate="0" selected>No</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 1</label>
                                                            <input type="text" name="address" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 2</label>
                                                            <input type="text" name="address2" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Country</label>
                                                            <input type="text" name="country" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">District</label>
                                                            <input type="text" name="district" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Sub-County</label>
                                                            <input type="text" name="subcounty" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Parish</label>
                                                            <input type="text" name="parish" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Village</label>
                                                            <input type="text" name="village" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-1">
                                                            <label class="text-label form-label">Primary Phone
                                                                Number* (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" name="primaryCellPhone" class="form-control" placeholder="" required>
                                                        </div>

                                                        <div class="form-check form-check-inline section-phone-number-sms-consent hide">
                                                            <input class="form-check-input" type="checkbox" name="phone_1_send_sms" value="1" id="phone_1_send_sms">
                                                            <label class="form-check-label text-danger" for="phone_1_send_sms">
                                                                <strong> Send SMS to Primary Phone Number </strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-1">
                                                            <label class="text-label form-label"> Secondary Phone
                                                                Number (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" class="form-control" placeholder="" name="secondaryCellPhone">
                                                        </div>

                                                        <div class="form-check form-check-inline section-phone-number-sms-consent hide">
                                                            <input class="form-check-input" type="checkbox" name="phone_2_send_sms" value="1" id="phone_2_send_sms">
                                                            <label class="form-check-label text-danger" for="phone_2_send_sms">
                                                                <strong> Send SMS to Secondary Phone Number </strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-1">
                                                            <label class="text-label form-label">Any Other Phone
                                                                Number (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" class="form-control" placeholder="" name="otherCellPhone">
                                                        </div>

                                                        <div class="form-check form-check-inline section-phone-number-sms-consent hide">
                                                            <input class="form-check-input" type="checkbox" name="phone_3_send_sms" value="1" id="phone_3_send_sms">
                                                            <label class="form-check-label text-danger" for="phone_3_send_sms">
                                                                <strong> Send SMS to Other Phone Number </strong>
                                                            </label>
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-6 mb-2 mt-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Email Address</label>
                                                            <input type="email" class="form-control" placeholder="" name="email">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="wizard_Time" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Business Information</h4>
                                                <div class="row">

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Choose Business
                                                                Type
                                                            </label>

                                                            <select class="me-sm-2 default-select form-control wide activate-sections" name="business_type" required>
                                                                <option value="">Select....</option>
                                                                <?php foreach (business_types() as $key => $business_type) { ?>
                                                                    <option value="<?= $key ?>" data-sections="business-type-other" data-activate="0">
                                                                        <?= $business_type ?>
                                                                    </option>
                                                                <?php } ?>
                                                                <option value="other" data-sections="business-type-other" data-activate="1">Other</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2 section-business-type-other hide">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Business Type Other</label>
                                                            <input type="text" name="business_type_other" class="form-control" data-is-required="1" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Business is Registered
                                                            </label>

                                                            <br>

                                                            <div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="is_registered" value="1" required id="IsRegistered">
                                                                    <label class="form-check-label" for="IsRegistered">YES</label>
                                                                </div>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="is_registered" value="0" required id="IsNotRegistered">
                                                                    <label class="form-check-label" for="IsNotRegistered"> NO</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Business Registration
                                                                Number #</label>
                                                            <input type="text" name="businessreg" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">City</label>
                                                            <input type="text" name="businessCity" class="form-control" placeholder="">
                                                        </div>
                                                    </div>


                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 1</label>
                                                            <input type="text" name="baddress" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Address Line 2</label>
                                                            <input type="text" name="baddress2" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Explain nature of business
                                                            </label>
                                                            <textarea name="business_nature_description" class="form-control" rows="20"></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Additional Notes
                                                            </label>
                                                            <textarea name="notes" class="form-control" rows="20"></textarea>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div id="wizard_files" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">Attachments & Others</h4>
                                                <div class="row ">
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Institution Signatories
                                                            </label>
                                                            <input type="file" name="signatories" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Any Other
                                                                Attachments</label>
                                                            <input type="file" name="otherattach" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div id="wizard_confirm" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">Confirm & Submit</h4>
                                                <div class="row ">
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
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
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#smartwizard').smartWizard();


            });
        </script>

</body>

</html>