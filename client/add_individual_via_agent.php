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


    $_POST['otherattach'] = null;
    $_POST['passport'] = null;
    $_POST['signature'] = null;
    $_POST['fing'] = null;
    $_POST['client_type'] = 'individual';

    $res = $response->addClient($_POST);

    // var_dump($res);
    // exit;

    if ($res['success']) {
        setSessionMessage(true, 'Client Created Successfully');
        Redirect('account_opening_success_page.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Check the Client\'s table to confirm if all the client\'s were created right.');
        RedirectCurrent();
        // header('Location:add_individual_client');
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
                                    Add New Client
                                </h4>
                                <?php
                                if (isset($_GET['success#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                                    // unset($_SESSION['success']);
                                }
                                if (isset($_GET['error#wizard_confirm'])) {
                                    echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                                }

                                ?>
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
                                        <li><a class="nav-link" href="#wizard_Details">
                                                <span>3</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_Payment">
                                                <span>4</span>
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

                                                    ?>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">SMS Message
                                                                Consent*</label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="message" style="display: none;" required>
                                                                <option value="1">Yes</option>
                                                                <option value="0" selected>No</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">First Name*</label>
                                                            <input type="text" name="fname" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Last Name*</label>
                                                            <input type="text" name="lname" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Occupation / Profession</label>
                                                            <input type="text" name="prof" class="form-control" placeholder="">
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
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Primary Phone
                                                                Number* (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" name="primaryCellPhone" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Any Other Phone
                                                                Number (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" class="form-control" placeholder="" name="secondaryCellPhone">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Email Address</label>
                                                            <input type="email" class="form-control" placeholder="" name="email">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">NIN</label>
                                                            <input type="text" name="nin" class="form-control" placeholder="" maxlength="14">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Date of Birth</label>
                                                            <input type="date" class="form-control" name="dob" placeholder="" value="<?php echo date('Y-m-d'); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Gender *
                                                            </label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="gender" style="display: none;" required>
                                                                <option selected=""></option>
                                                                <option value="Male">Male</option>
                                                                <option value="Female">Female</option>

                                                            </select>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div id="wizard_Time" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Business Information</h4>
                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Business Name</label>
                                                            <input type="text" name="bname" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Choose Business
                                                                Type</label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="businesstype" style="display: none;">
                                                                <option selected=""></option>
                                                                <option value="Registered">Registered</option>
                                                                <option value="Not Registered">Not Registered</option>

                                                            </select>
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
                                                            <label class="text-label form-label">Country</label>
                                                            <input type="text" name="businesscountry" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">City</label>
                                                            <input type="text" name="businessCity" class="form-control" placeholder="">
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div id="wizard_Details" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Family / Next of Kin
                                                    Information</h4>
                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Next of Kin's
                                                                Name</label>
                                                            <input type="text" name="kname" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Next of Kin's Phone
                                                                Number</label>
                                                            <input type="text" name="kinphone" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Physical
                                                                Address</label>
                                                            <input type="text" name="kphysicaladdress" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Next of Kin's
                                                                NIN</label>
                                                            <input type="text" name="knin" class="form-control" placeholder="" maxlength="14">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Describe the
                                                                relationship with Client?</label>
                                                            <input type="text" name="relationship" class="form-control" placeholder="">
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div id="wizard_Payment" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">Attachments & Others</h4>
                                                <div class="row ">
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Client's Passport-Sized
                                                                Photo</label>
                                                            <input type="file" name="photo" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Client's Scanned
                                                                Signature</label>
                                                            <input type="file" name="sign" class="form-control" placeholder="">
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