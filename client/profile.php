<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

if (isset($_POST['submit'])) {
    include_once('includes/handler.php');
    include_once('includes/response.php');
    $handler = new Handler();
    $response = new Response();

    if ($handler->Encoding(md5($_POST['password'])) == $user[0]['password']) {
        $res = $response->updateStaff($_POST['fname'], $_POST['lname'], $_POST['gender'], $_POST['email'], $_POST['address1'], $_POST['address2'], $_POST['district'], $_POST['subcounty'], $_POST['parish'], $_POST['village'], $_POST['primaryCellPhone'], $_POST['secondaryCellPhone'], $_POST['dob'], $_POST['nin'], $_POST['spousename'], $_POST['spouseNin'], $_POST['spousePhone'], $_POST['id'], $_POST['country']);
        if ($res) {
            setSessionMessage(true, 'Profile Details Updated Successfully!');
            header('location:profile');
            exit;
        } else {
            setSessionMessage(false, 'Profile not Updated! Try again.');
            header('location:profile');
            exit;
        }
    } else {
        header('location:profile?password');
    }


    // header('location:all_banks.php');
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Profile</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">My Details Page</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Profile Page
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="profile-tab">
                                    <div class="custom-tab-1">
                                   
                                        <ul class="nav nav-tabs">

                                            <li class="nav-item"><a href="#about-me" data-bs-toggle="tab" class="nav-link active show">My Profile Info</a>
                                            </li>
                                            <li class="nav-item"><a href="#profile-settings" data-bs-toggle="tab" class="nav-link ">Update Profile</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">

                                            <div id="about-me" class="tab-pane fade active show">
                                                <br /><br /><br />
                                                <div class="profile-personal-info">
                                                    <h4 class="text-primary mb-4">Personal Information</h4>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Name <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['firstName'] . ' ' . $user[0]['lastName']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Position <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['positionTitle']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Bank <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['bankName2']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Branch <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['branchName']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Email <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['email']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Gender <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['gender']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Address <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['addressLine1'] . ' / ' . $user[0]['addressLine2']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Country - District - Parish - SubCounty
                                                                - Village <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['country'] . ' - ' . $user[0]['district'] . ' - ' . $user[0]['parish'] . ' - ' . $user[0]['subcounty'] . ' - ' . $user[0]['village']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Contacts <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['primaryCellPhone'] . ' / ' . $user[0]['secondaryCellPhone']; ?></span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Date of Birth <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo date('Y-m-d', strtotime($user[0]['dateOfBirth'])); ?></span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Account Status <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7"><?php echo $user[0]['status'] == 'ACTIVE' ? '
                                                        <span class="badge badge-pill badge-primary">' . $user[0]['status'] . '</span>
                                                        ' : '
                                                        <span class="badge badge-pill badge-danger">' . $user[0]['status'] . '</span>
                                                        '; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">NIN <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['nin']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Spouse <span class="pull-end">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo $user[0]['spouseName'] . ' TEL - ' . $user[0]['spouseCell'] . ' NIN - ' . $user[0]['spouseNin']; ?></span>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 col-5">
                                                            <h5 class="f-w-500">Staff Since <span class="pull-end">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-9 col-7">
                                                            <span><?php echo date('Y-m-d', strtotime($user[0]['createdAt'])); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="profile-settings" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <h4 class="text-primary">Update your Account Details</h4>
                                                        <form method="POST">
                                                            <input type="hidden" placeholder="" class="form-control" name="id" value="<?php echo $user[0]['userId']; ?>">
                                                            <div class="row">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">First Name</label>
                                                                    <input type="text" placeholder="" class="form-control" name="fname" value="<?php echo $user[0]['firstName']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Last Name</label>
                                                                    <input type="text" placeholder="" class="form-control" name="lname" value="<?php echo $user[0]['lastName']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Gender</label>
                                                                    <select class="form-control" name="gender" required>
                                                                        <option value="<?php echo $user[0]['gender']; ?>" selected><?php echo $user[0]['gender']; ?>
                                                                        </option>
                                                                        <option value="M">Male</option>
                                                                        <option value="F">Female</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Email (* This can only be changed by the Admin)</label>
                                                                    <input type="email" placeholder="" class="form-control" name="email" value="<?php echo $user[0]['email']; ?>" required readonly>
                                                                </div>

                                                            </div>
                                                            <div class="row">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Address Line 1</label>
                                                                    <input type="text" placeholder="" class="form-control" name="address1" value="<?php echo $user[0]['addressLine1']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Address 2</label>
                                                                    <input type="text" placeholder="" name="address2" value="<?php echo $user[0]['addressLine2']; ?>" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">District</label>
                                                                    <input type="text" class="form-control" name="district" value="<?php echo $user[0]['district']; ?>" required>
                                                                </div>

                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Sub County</label>
                                                                    <input type="text" class="form-control" name="subcounty" value="<?php echo $user[0]['subcounty']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Parish</label>
                                                                    <input type="text" class="form-control" name="parish" value="<?php echo $user[0]['parish']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Village</label>
                                                                    <input type="text" class="form-control" name="village" value="<?php echo $user[0]['village']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Country</label>
                                                                    <input type="text" class="form-control" name="country" value="<?php echo $user[0]['country']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Primary Cell Phone</label>
                                                                    <input type="text" class="form-control" name="primaryCellPhone" value="<?php echo $user[0]['primaryCellPhone']; ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Secondary Cell
                                                                        Phone</label>
                                                                    <input type="text" class="form-control" name="secondaryCellPhone" value="<?php echo $user[0]['secondaryCellPhone']; ?>">
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Date of Birth</label>
                                                                    <input type="date" class="form-control" name="dob" value="<?php echo date('Y-m-d', strtotime($user[0]['dateOfBirth'])); ?>" required>
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">NIN #</label>
                                                                    <input type="text" class="form-control" name="nin" value="<?php echo $user[0]['nin']; ?>" maxlength="11">
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Spouse Name</label>
                                                                    <input type="text" class="form-control" name="spousename" value="<?php echo $user[0]['spouseName']; ?>">
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Spouse NIN</label>
                                                                    <input type="text" class="form-control" name="spouseNin" value="<?php echo $user[0]['spouseNin']; ?>" maxlength="11">
                                                                </div>
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Spouse's Cell Phone</label>
                                                                    <input type="text" class="form-control" name="spousePhone" value="<?php echo $user[0]['spouseCell']; ?>" maxlength="11">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="mb-3 col-md-6">
                                                                    <label class="form-label">Enter your Current
                                                                        Password to make Updates</label>
                                                                    <input type="password" class="form-control" value="" name="password" required="" id="id_password" style="display:inline-block;margin-right:10px;" autocomplete="off">
                                                                    <i class="fas fa-eye" id="togglePassword" style="margin-left: -30px !important; cursor: pointer !important; display:inline-block;"></i>
                                                                </div>
                                                                <div class="row d-flex justify-content-between mt-4 mb-2">
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-primary" type="submit" name="submit">Update</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal -->

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
        <!-- <script src="./js/styleSwitcher.js"></script> -->
       
        <script>
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#id_password');

            togglePassword.addEventListener('click', function(e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
            });
        </script>

</body>

</html>