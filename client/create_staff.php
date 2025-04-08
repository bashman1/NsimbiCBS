<?php
include('../backend/config/session.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}

require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankAdmin()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php


include_once('includes/response.php');
$response = new Response();
// $banks = $response->getAllBanksList();
$branches = $response->getBankBranches($user[0]['bankId']);
$roles = $response->getBankRoles($user[0]['bankId']);


if (isset($_POST['submit'])) {
    // $actype = isset($_POST['actype']) ? $_POST['actype'] : '';

    // $files = $_FILES;

    // $passport_photo = $_FILES["photo"];



    $passport_photo_name = null;
    // if (count($passport_photo)) {
    //     $target_path_passport = "images/passport_photo";
    //     if (!is_dir($target_path_passport)) {
    //         mkdir($target_path_passport, 0755, true);
    //     }
    //     try {
    //         $temp = explode(".", $passport_photo["name"]);
    //         $newfilename = uniqid('', true) . '.' . end($temp);
    //         $passport_photo_name = $target_path_passport . "/" . $newfilename;
    //         move_uploaded_file($passport_photo["tmp_name"], $passport_photo_name);
    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }


    $res = $response->createStaff($_POST['branch'], $_POST['role'], $passport_photo_name, $_POST['position'], $_POST['fname'], $_POST['lname'], $_POST['address'], $_POST['address2'], $_POST['country'], $_POST['district'], $_POST['subcounty'], $_POST['parish'], $_POST['village'], $_POST['phone'], $_POST['other_phone'], $_POST['email'], $_POST['nin'], $_POST['dob'], $_POST['kname'], $_POST['kinphone'], $_POST['kphysicaladdress'], $_POST['knin'], $_POST['relationship'], $_POST['gender'], $_POST['enable_penalty'], $_POST['bid'], $_POST['b_level']);
    if ($res) {
        setSessionMessage(true, 'Staff Created Successfully!  Staff should check their Email Addresses to finish  setting up their accounts.');
        header('location:all_bank_staff.php');
        exit;
    } else {
        setSessionMessage(false, 'Staff not Created! Try again');
        header('location:all_bank_staff.php');
        exit;
    }
}
$title = 'CREATE STAFF';
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Staff</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Create New Staff</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"> <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> Add New Staff</h4>

                            </div>
                            <div class="card-body">

                                <div id="smartwizard" class="form-wizard order-create">
                                    <ul class="nav nav-wizard">
                                        <li><a class="nav-link" href="#wizard_Service">
                                                <span>1</span>
                                            </a></li>

                                        <li><a class="nav-link" href="#wizard_Details">
                                                <span>2</span>
                                            </a></li>
                                        <li><a class="nav-link" href="#wizard_Payment">
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
                                                <?php
                                                if($user[0]['bankId']){
                                                    echo '<input type="hidden" name="bid" value="'.$user[0]['bankId'].'" />';
                                                    echo '<input type="hidden" name="b_level" value="1" />';
                                                }else{
                                                    echo '<input type="hidden" name="bid" value="' . $user[0]['branchId'] . '" />';
                                                    echo '<input type="hidden" name="b_level" value="0" />';
                                                }
                                                ?>

                                                <div class="row">
                                                    <?php

                                                    echo '
<div class="col-lg-6 mb-2">
<div class="mb-3">
    <label class="text-label form-label">Branch *</label>
    <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch">
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




                                                    echo '
<div class="col-lg-6 mb-2">
<div class="mb-3">
    <label class="text-label form-label">Roles & Permissions *</label>
    <select  class="form-control"  name="role" id="credit_account">
        ';
                                                    if ($roles !== '') {
                                                        foreach ($roles as $row) {
                                                            echo '
    <option value="' . $row['rid'] . '">' . $row['name'] . ' - Branch: ' . $row['branch'] . '</option>
    
    ';
                                                        }
                                                    } else {
                                                        echo '
    <option>No Role Added yet ( <a href="create_role">Add Roles & Permissions first</a> )</option>
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
                                                            <label class="text-label form-label"> Position Title
                                                                *</label>
                                                            <input type="text" name="position" class="form-control" placeholder="" required>
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
                                                            <label class="text-label form-label">Active Email Address
                                                                *</label>
                                                            <input type="email" class="form-control" placeholder="" name="email" required>
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
                                                                Number*</label>
                                                            <input type="text" name="phone" class="form-control" placeholder="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Any Other Phone
                                                                Number</label>
                                                            <input type="text" class="form-control" placeholder="" name="other_phone">
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
                                                            <input type="date" class="form-control" name="dob" placeholder="" value="<?= date('Y-m-d') ?>">
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

                                            <div id="wizard_Details" class="tab-pane" role="tabpanel">

                                                <h4 class="card-title " style="color:#005a4b;">Family / Next of Kin
                                                    Information</h4>
                                                <label class="text-label form-label text-primary"> NOTE: All these
                                                    fields are Optional</label>
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
                                                                relationship with Staff?</label>
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
                                                            <label class="text-label form-label">Passport-Sized
                                                                Photo</label>
                                                            <input type="file" name="photo" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label required-field">Is Staff running a Supervisory role?</label>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input activate-sections" type="radio" name="enable_penalty" value="1" data-activate-sections="enable-penalty" id="enable_late_penalty" data-deactivate-sections="disable-penalty" required <?= @$details['is_supervisor'] ? 'checked' : '' ?>>
                                                                    <label class="form-check-label" for="enable_late_penalty">
                                                                        Yes
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input activate-sections" type="radio" name="enable_penalty" value="0" data-activate-sections="disable-penalty" data-deactivate-sections="enable-penalty" required id="unenable_late_penalty">
                                                                    <label class="form-check-label" for="unenable_late_penalty" <?= @$details['is_supervisor'] ? '' : 'checked' ?>>
                                                                        No
                                                                    </label>
                                                                </div>
                                                            </div>
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



</body>

</html>