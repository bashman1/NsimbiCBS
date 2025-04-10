<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankAdmin()) {
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



$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $bank = $_POST['bid'];
    $blogo = $_POST['blogo'];

    $take_logo = '';

    $res = $response->updateBankDetails($bank, $name, $email, $address, $contact, $take_logo, $_POST['tname']);

    if ($res) {

        setSessionMessage(true, 'Bank Details Updated Successfully!');
        header('location:bank_settings.php');
        // exit;
    } else {
        setSessionMessage(false, 'Process failed! Details not updated');
        header('location:bank_settings.php');
        // exit;
    }
}
require_once('includes/head_tag.php');

$binfo = $response->getBankDetails($user[0]['bankId']);

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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Bank Settings</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Bank Settings</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    All your Institutions' Main Settings
                                </h4>


                                <!-- <button type="button" class="btn btn-primary"><a href="create_bank_admin" style="color:#fff !important;">Add New Admin</a></button> -->

                            </div>
                            <div class="card-body">
                                <h4 class="card-title " style="color:#005a4b;">General Information</h4>
                                <label>**NOTE: Details below shall be used in reports</label>
                                <form method="POST" enctype='multipart/form-data'>
                                    <input type="hidden" name="bid" class="form-control" placeholder="" value="<?php echo $binfo[0]['id']; ?>">
                                    <input type="hidden" name="blogo" class="form-control" placeholder="" value="<?php echo $binfo[0]['logo']; ?>">
                                    <div class="col-lg-12 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Institution's Name*</label>
                                            <input type="text" name="name" class="form-control" placeholder="" value="<?php echo $binfo[0]['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Trade Name (This is the name your institution uses to do business other than the legal name. This is mainly used in scenarios like SMS to avoid having long messages) *</label>
                                            <input type="text" name="tname" class="form-control" placeholder="" value="<?php echo $binfo[0]['trade_name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Institution's Contacts *</label>
                                            <input type="text" name="contact" class="form-control" placeholder="" value="<?php echo $binfo[0]['contacts']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Institution's Email Address *</label>
                                            <input type="text" name="email" class="form-control" placeholder="" value="<?php echo $binfo[0]['email']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Institution's Address *</label>
                                            <input type="text" name="address" class="form-control" placeholder="" value="<?php echo $binfo[0]['location']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Institution's Logo *</label>
                                            <input type="file" name="logo" class="form-control" placeholder="" value="<?php echo $binfo[0]['logo']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-2">
                                        <div class="mb-3">
                                            <button type="submit" name="submit" class="btn btn-primary mb-2">Submit Changes</button>
                                        </div>
                                    </div>
                                </form>

                                <h4 class="card-title " style="color:#005a4b;">Open Days in a week</h4>
                                <label>**NOTE: By default all days in a week are marked as open but you can change a given day to closed</label>
                                <div class="table-responsive">

                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>

                                                <th><strong>Day</strong></th>
                                                <th><strong>Status</strong></th>
                                                <th><strong>Actions</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 0;
                                            $actions = '';
                                            foreach ($days as $row) {
                                                $dayStatus =  $response->getDayStatus($user[0]['bankId'], $row);
                                                if ($dayStatus) {
                                                    $status = '
<div class="d-flex align-items-center"><i class="fa fa-circle text-danger me-1"></i> Closed</div>
';

                                                    $actions = '
<a href="open_day.php?id=' . $row . '" class="btn btn-primary shadow btn-xs sharp me-1">On</a>
';
                                                } else {
                                                    $status = '
                                                    <div class="d-flex align-items-center"><i class="fa fa-circle text-success me-1"></i> Open</div>
                                                    ';
                                                    $actions = '
                                                    
														<a href="close_day.php?id=' . $row . '" class="btn btn-danger shadow btn-xs sharp">Off</a>
                                                    ';
                                                }
                                                echo '
                                                <tr>    
                                                <td><div class="d-flex align-items-center"><span class="w-space-no">' . $row . '</span></div></td>
                                                
                                                <td>' . $status . '</td>
                                                <td>
													<div class="d-flex">
														' . $actions . '
													</div>
												</td>
                                            </tr>
                                                
                                                ';
                                                $count++;
                                            }
                                            ?>



                                        </tbody>
                                    </table>

                                </div>

                                <h4 class="card-title " style="color:#005a4b;">Open Holidays on a year Calendar</h4>
                                <label>**NOTE: Date displayed is the date of the holiday for the current year. By default all holidays are marked as open but you can change a given holiday to closed</label>
                                <div class="table-responsive">

                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>

                                                <th><strong>Day</strong></th>
                                                <th><strong>Name</strong></th>
                                                <th><strong>Status</strong></th>
                                                <th><strong>Actions</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $days = $response->getPublicHolidays();
                                            foreach ($days as $row) {
                                                $dayStatus =  $response->getHolidayStatus($user[0]['bankId'], $row['date']);
                                                if ($dayStatus) {
                                                    $status = '
<div class="d-flex align-items-center"><i class="fa fa-circle text-danger me-1"></i> Closed</div>
';

                                                    $actions = '
<a href="open_holiday.php?id=' . $row['date'] . '" class="btn btn-primary shadow btn-xs sharp me-1">On</a>
';
                                                } else {
                                                    $status = '
                                                    <div class="d-flex align-items-center"><i class="fa fa-circle text-success me-1"></i> Open</div>
                                                    ';
                                                    $actions = '
                                                    
														<a href="close_holiday.php?id=' . $row['date'] . '" class="btn btn-danger shadow btn-xs sharp">Off</a>
                                                    ';
                                                }
                                                echo '
                                                <tr>
                                               
                                                <td><strong>' . $row['date'] . '</strong></td>
                                                <td><div class="d-flex align-items-center"> <span class="w-space-no">' . $row['name'] . ' </span></div></td>
                                                <td>' . $status . '</td>
                                                <td>
													<div class="d-flex">
														' . $actions . '
													</div>
												</td>
                                            </tr>
                                                
                                                
                                                ';
                                            }
                                            ?>

                                        </tbody>
                                    </table>

                                </div>
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
    <?php include('includes/bottom_scripts.php'); ?>



</body>

</html>