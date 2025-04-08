<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
require_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'REGISTER GROUP';
$response = new Response();

if (isset($_POST['submit'])) {

    $_POST['otherattach'] = null;
    $_POST['passport'] = null;
    $_POST['signature'] = null;
    $_POST['fing'] = null;

    $_POST['client_type'] = 'group';

    $res = $response->addClient($_POST);

    // var_dump($res);
    // exit;

    if ($res['success']) {
        setSessionMessage(true, 'Group Created Successfully!');
        Redirect('group_clients_attachments.php?id=' . $res['message']);
    } else {
        setSessionMessage(false, 'Something went wrong! Check the Group\'s table to confirm if all the client\'s were created right.');
        RedirectCurrent();
    }
    exit();
}

require_once('includes/head_tag.php');
$actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);
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
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Add New Group
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

                                      

                                        <li><a class="nav-link" href="#wizard_confirm">
                                                <span>✓</span>
                                            </a></li>
                                    </ul>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="tab-content">
                                            <div id="wizard_Service" class="tab-pane" role="tabpanel">
                                                <h4 class="card-title " style="color:#005a4b;">General Information</h4>
                                                <input type="hidden" name="uid" value="<?php echo $user[0]['userId']; ?>" class="form-control">
                                                <div class="row">

                                                    <?php
                                                    if (!$user[0]['branchId']) {
                                                        $branches = $response->getBankBranches($user[0]['bankId']);

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

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                                    }
                                                    ?>


                                                    <?php
                                                    if (isset($_GET['1'])) {
                                                        echo '
                                                        <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Choose Saving Account
                                                                Type*</label>

                                                        

                                                                <select class=" form-control wide"
                                                                id="oscategory" name="actype" required>
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
                                                    } else {
                                                        echo '
                                                        <input type="hidden" name="actype" class="form-control" placeholder="" value="0">
                                                        
                                                        ';
                                                    }
                                                    ?>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Group Name*</label>
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

                                                    <div class="col-lg-6 mb-2 mt-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Expected Group's Income Per Month</label>
                                                            <input type="text" class="form-control comma_separated" placeholder="" value="0" name="income">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="wizard_Time" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Group Information</h4>
                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Number of Members *</label>
                                                            <input type="text" name="number_of_members" class="form-control" id="countn" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Region</label>
                                                            <input type="text" name="region" class="form-control" placeholder="">
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

                                                    <div class="col-lg-12 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Additional Notes
                                                            </label>
                                                            <textarea name="notes" class="form-control" rows="20"></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="row">
                                                        <div class="col-lg-6 mb-2">
                                                            <div class="mb-3">
                                                                <label class="text-primary form-label">
                                                                    Add Group Members
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6 mb-2">
                                                            <div class="mb-3">

                                                                <a href="javascript:void(0);" class="add_button text-primary" title="Add field"><i class="fa fa-plus-circle"></i>Add</a>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <!-- 
                                                    <div class="field_wrapper">
                                                        <div class="row">
                                                            <div class="col-lg-3 mb-2">
                                                                <div class="mb-3">
                                                                    <label class="text-label form-label">Name</label>
                                                                    <input type="text" name="field_name[]" class="form-control" placeholder="">

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-2">
                                                                <div class="mb-3">
                                                                    <label class="text-label form-label">Contact</label>
                                                                    <input type="text" name="field_contact[]" class="form-control" placeholder="">

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-2">
                                                                <div class="mb-3">
                                                                    <label class="text-label form-label">Address</label>
                                                                    <input type="text" name="field_address[]" class="form-control" placeholder="">

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-2">
                                                                <div class="mb-3">
                                                                    <label class="text-label form-label">Role in Group</label>
                                                                    <input type="text" name="field_role[]" class="form-control" placeholder="">

                                                                </div>
                                                            </div>


                                                            <hr>
                                                        </div>
                                                    </div> -->


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

        <script type="text/javascript">
            // $(document).ready(function() {
            //     var maxField = 5000; //Input fields increment limitation
            //     var addButton = $('.add_button'); //Add button selector
            //     var wrapper = $('.field_wrapper'); //Input field wrapper
            //     var fieldHTML = `<div class="row"><div class ="col-lg-3 mb-2"><div class ="mb-3"><label class = "text-label form-label"> Name </label> <input type ="text" name ="field_name[]" class ="form-control" placeholder ="" ></div></div><div class ="col-lg-3 mb-2" ><div class = "mb-3" ><label class = "text-label form-label"> Contact </label> <input type ="text" name ="field_contact[]" class ="form-control" placeholder ="" ></div> </div> <div class ="col-lg-3 mb-2" ><div class = "mb-3" ><label class = "text-label form-label"> Address </label> <input type = "text" name ="field_address[]" class ="form-control" placeholder ="" ></div> </div><div class = "col-lg-3 mb-2" ><div class ="mb-3"><label class ="text-label form-label" > Role in Group </label> <input type = "text" name ="field_role[]" class ="form-control" placeholder=""></div></div><div class = "col-lg-6 mb-2" ><div class = "mb-3" ><a href = "javascript:void(0);" class = "remove_button text-primary" title ="Remove field"><i class ="fa fa-minus-circle"></i>Remove</a ></div> </div> <hr></div>`;
            //     var x = 1; //Initial field counter is 1

            //     //Once add button is clicked
            //     $(addButton).click(function() {
            //         //Check maximum number of input fields
            //         if (x < maxField) {
            //             x++; //Increment field counter
            //             $(wrapper).append(fieldHTML); //Add field html
            //         }
            //     });

            //     //Once remove button is clicked
            //     $(wrapper).on('click', '.remove_button', function(e) {
            //         e.preventDefault();
            //         $(this).closest('.row').remove(); //Remove field html
            //         x--; //Decrement field counter
            //     });
            // });
        </script>

</body>

</html>