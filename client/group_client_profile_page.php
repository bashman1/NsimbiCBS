<?php
include('../backend/config/session.php');
require_once('includes/functions.php');

require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('clients')) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();


// $memberl = $response->hasLoan($_GET['id']);

if (isset($_POST['submit'])) {


    $_POST['client_type'] = 'group';

    $_POST['disability_status'] = '';
    $_POST['disability_cat'] = '';
    $_POST['disability_other'] = '';
    $_POST['disability_desc'] = '';

    $res = $response->updateClientDetails($_POST);

    // var_dump($res);
    // exit;

    if ($res) {
        setSessionMessage(true, 'Group Updated Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Client not Updated.');
    }

    RedirectCurrent();
    exit;
}

if (isset($_GET['did'])) {
    $res = $response->updateAccountStatus($_GET['did'], $_GET['status']);

    if ($res) {
        setSessionMessage(true, 'Account Updated Successfuly!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
    }
    header('location: group_client_profile_page.php?id=' . encrypt_data($_GET['did']));
    exit;
}
$title = 'GROUP DETAILS';
require_once('includes/head_tag.php');

$client_id = $_GET['id'] = parsed_id($_GET['id']);

$member = $response->getMemberDetails($_GET['id'])[0];
$is_induvidual = false;
$is_institution = false;
$is_group = false;
if ($member['client_type'] == 'institution') {
    $is_institution = true;
} else if ($member['client_type'] == 'group') {
    $is_group = true;
} else {
    $is_induvidual = true;
}

$sms_phone_numbers = $member['sms_phone_numbers'] ? json_decode($member['sms_phone_numbers']) : [];
// $sms_phone_numbers = json_decode($member['sms_phone_numbers']);

// var_dump($member);
// exit;

?>


<body>

    <!--*******************
 Preloader start
 ********************-->
    <?php include_once('includes/preloader.php'); ?>
    <!--*******************
 Preloader end
 ********************-->


    <!--**********************************
 Main wrapper start
 ***********************************-->
    <div id="main-wrapper">

        <?php
        include_once('includes/nav_bar.php');
        include_once('includes/side_bar.php');
        ?>
        <!--**********************************
 Content body start
 ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card">

                            <div class="row ps-5 mt-4">
                                <div class="col-md-2 pe-0">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active"> <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> </li>
                                    </ol>
                                    <a class="rounded-circle" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">
                                        <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['profilePhoto']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                    </a>
                                </div>

                                <div class="col-md-3 ps-0 pt-5">
                                    <h3 class="text-primary mb-0">
                                        <?= @$member['shared_name'] ?>
                                    </h3>

                                    <p class="mb-0">
                                        <span class="text-muted"> Client Since: </span>
                                        <?php echo $response->read_date2($member['createdAt']); ?>
                                    </p>

                                    <p class="pb-0 mb-0">
                                        Account Status <a class="btn btn-<?= $member['status'] == 'ACTIVE' ? 'primary' : 'danger' ?> light btn-xs mb-1"><?= $member['status'] == 'INACTIVE' ? 'DEACTIVATED' : $member['status']; ?></a>
                                    </p>
                                    <p class="mb-0">
                                        <span class="text-muted"> Client Type: </span>
                                        <?php echo strtoupper($member['client_type']); ?>
                                    </p>
                                    <p class="mb-0">
                                        <span class="text-muted">A/C Balance: </span>
                                        <?php echo number_format($member['balance'] ?? 0); ?>
                                    </p>
                                </div>

                                <div class="col  ps-0 pt-3">
                                    <div id="lightgallery" class="row lightgallery">
                                        <a href="https://eaoug.org/<?php echo $member['profilePhoto']; ?>" data-exthumbimage="https://eaoug.org/<?php echo $member['profilePhoto']; ?>" data-src="https://eaoug.org/<?php echo $member['profilePhoto']; ?>" class="col-md-4 mb-4">


                                            <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['profilePhoto']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                            <!-- <div class="description">Identification</div> -->
                                        </a>

                                        <a href="https://eaoug.org/<?php echo $member['sign']; ?>" data-exthumbimage="https://eaoug.org/<?php echo $member['sign']; ?>" data-src="https://eaoug.org/<?php echo $member['sign']; ?>" class="col-md-4 mb-4">

                                            <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['sign']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                            <!-- <div class="description">Signature</div> -->
                                        </a>

                                        <a href="https://eaoug.org/<?php echo $member['other_attachments']; ?>" data-exthumbimage="https://eaoug.org/<?php echo $member['other_attachments']; ?>" data-src="https://eaoug.org/<?php echo $member['other_attachments']; ?>" class="col-md-4 mb-4">

                                            <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['other_attachments']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                            <!-- <div class="description">Other attachments</div> -->
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header">
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <a href="add_client_account.php?id=<?= encrypt_data(@$client_id); ?>" class="btn btn-primary light btn-xs mb-1 me-2">Saving A/Cs (<?= number_format(@$member['accs']); ?>)</a>
                                        <?php
                                        if ($permissions->hasSubPermissions('reconcile_saving_statement')) : ?>
                                            <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#account_balance_reconciliation_form">Update A/C Balance</a>
                                            <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#biometrics">Enrolled Biometrics</a>
                                        <?php endif; ?>

                                        <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#stmtsModal">View Statements</a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="stmtsModal">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Select the Statement you need</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <a href="member_statement_range.php?id=<?= encrypt_data(@$client_id); ?>" class="list-group-item load_via_ajax"> Account / General Statement</a>
                                                            <a href="saving_statement.php?id=<?= encrypt_data(@$client_id); ?>" class="list-group-item load_via_ajax"> Savings Statement</a>
                                                            <a href="user_fixed_deposits.php?id=<?= $member['userId'] ?>" class="list-group-item load_via_ajax"> Fixed Deposit Statement</a>
                                                            <a href="" class="list-group-item load_via_ajax"> Shares Statement</a>
                                                            <a href="over_draft_statement.php?id=<?= encrypt_data(@$client_id); ?>" class="list-group-item load_via_ajax"> Over Drafts Statement</a>
                                                            <a href="user_loans.php?id=<?= $member['userId'] ?>" class="list-group-item load_via_ajax"> Loans Statement</a>
                                                            <a href="user_loans.php?id=<?= $member['userId'] ?>" class="list-group-item load_via_ajax"> Credit History</a>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#gmModal">View Group Members</a>
                                        <div class="modal fade" id="gmModal">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">

                                                        <h4 class="text-muted mb-3 modal-title text-primary">Group's Members <a href="manage_group_members.php?id=<?= @$client_id ?>&name=<?= $member['shared_name'] ?>">&nbsp;&nbsp;&nbsp;<i class="fa fa-edit"></i> Add / Edit Members</a>
                                                        </h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <?php
                                                            $gms = $response->getGroupMembers(@$client_id);
                                                            if ($gms) {
                                                                foreach ($gms as $gm) {
                                                                    echo '
                                                                <label class="list-group-item"><a href="manage_group_members.php?id=' . @$client_id . '&name=' . $member['shared_name'] . '">' . $gm['name'] . '  -      ' . $gm['role'] . ' (' . $gm['phone'] . ')</a></label>
                                                  
                                                
                                                ';
                                                                }
                                                            }
                                                            ?>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <?php
                                        if ($member['status'] == 'PENDING') {
                                            echo '<form id="activate" method="get"> <input type="hidden" class="form-control" name="did"
                                            value="' . $member['userId'] . '"><input type="hidden" class="form-control" name="status"
                                            value="ACTIVE">  <button class="btn btn-primary light btn-xs mb-1">Approve A/C</button></form>';
                                        } else if ($member['status'] == 'ACTIVE') { ?>

                                            <?php if ($permissions->hasSubPermissions('delete_client')) { ?>
                                                <form id="deactivate" method="get"> <input type="hidden" class="form-control" name="did" value="<?= $member['userId'] ?>"><input type="hidden" class="form-control" name="status" value="INACTIVE"> <button type="submit" class="btn btn-danger light btn-xs mb-1">Deactivate A/C</button></form>
                                            <?php } ?>

                                        <?php } else {
                                            echo '<form id="activate" method="get"> <input type="hidden" class="form-control" name="did"
                                            value="' . $member['userId'] . '"><input type="hidden" class="form-control" name="status"
                                            value="ACTIVE">  <button type="submit" class="btn btn-primary light btn-xs mb-1">Activate A/C</button></form>';
                                        }
                                        ?>
                                        <?php if ($permissions->hasSubPermissions('delete_client')) { ?>
                                            <form id="delete" method="get"> <input type="hidden" class="form-control" name="del_id" value="<?= $member['userId'] ?>"> <button type="submit" class="btn btn-danger light btn-xs mb-1">Delete A/C</button></form>



                                        <?php } ?>

                                    </div>
                                </div>

                                <div class="basic-form">
                                    <form method="POST" enctype="multipart/form-data">

                                        <input type="hidden" class="form-control" name="uid" value="<?php echo $member['userId']; ?>">
                                        <input type="hidden" class="form-control" name="cid" value="<?php echo $member['cid']; ?>">
                                        <input type="hidden" class="form-control" name="branch" value="<?php echo @$user[0]['branchId']; ?>">
                                        <input type="hidden" class="form-control" name="bank_id" value="<?php echo @$user[0]['bankId']; ?>">
                                        <input type="hidden" class="form-control" name="auth_id" value="<?php echo @$user[0]['userId']; ?>">
                                        <div class="card">
                                            <div class="card-body btc-price">

                                                <h4 class="mb-3 card-title "><span class=" text-muted text-primary">General Information</span>
                                                </h4>
                                                <hr class="hr-dashed">


                                                <div class="row">

                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label"> Group Name*</label>

                                                        <input type="text" class="form-control" name="name" placeholder="" value="<?= $member['client_names'] ?>" required <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">SMS Message
                                                                Consent*</label>

                                                            <select class="me-sm-2 default-select form-control wide activate-sections" id="inlineFormCustomSelect" name="message" style="display: none;" required>
                                                                <option value="1" <?= $member['message_consent'] ? 'selected' : '' ?> data-sections="phone-number-sms-consent" data-activate="1">Yes</option>
                                                                <option value="0" <?= !$member['message_consent'] ? 'selected' : '' ?> data-sections="phone-number-sms-consent" data-activate="0">No</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Address Line 1</label>

                                                        <input type="text" class="form-control" name="address" placeholder="" value="<?php echo $member['addressLine1']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Address Line 2</label>

                                                        <input type="text" class="form-control" name="address2" placeholder="" value="<?php echo $member['addressLine2']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Country</label>

                                                        <input type="text" class="form-control" name="country" placeholder="" value="<?php echo $member['country']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>

                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Region</label>

                                                        <input type="text" class="form-control" name="region" placeholder="" value="<?php echo $member['region']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Estimated Monthly Income</label>

                                                        <input type="text" class="form-control comma_separated" name="income" placeholder="" value="<?php echo $member['income']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Occupation /
                                                            Profession</label>

                                                        <input type="text" class="form-control" name="prof" placeholder="" value="<?php echo $member['prof']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">District</label>

                                                        <input type="text" class="form-control" name="district" placeholder="" value="<?php echo $member['district']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Sub-County</label>

                                                        <input type="text" class="form-control" name="subcounty" placeholder="" value="<?php echo $member['subcounty']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Parish</label>

                                                        <input type="text" class="form-control" name="parish" placeholder="" value="<?php echo $member['parish']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Village</label>

                                                        <input type="text" class="form-control" name="village" placeholder="" value="<?php echo $member['village']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Primary Phone
                                                            Number*</label>

                                                        <input type="text" class="form-control" name="phone" placeholder="" value="<?php echo $member['primaryCellPhone']; ?>" required <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Any Other Phone
                                                            Number</label>

                                                        <input type="text" class="form-control" name="other_phone" placeholder="" value="<?php echo $member['secondaryCellPhone']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-1">
                                                            <label class="text-label form-label">Primary Phone
                                                                Number* (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" name="primaryCellPhone" class="form-control" placeholder="" required value="<?= @$member['primaryCellPhone'] ?>">
                                                        </div>

                                                        <div class="form-check form-check-inline section-phone-number-sms-consent <?= $member['message_consent'] ? '' : 'hide' ?>">
                                                            <input class="form-check-input" type="checkbox" <?= in_array(@$member['primaryCellPhone'], $sms_phone_numbers ?? []) ? 'checked' : '' ?> name="phone_1_send_sms" value="1" id="phone_1_send_sms">
                                                            <label class="form-check-label text-danger" for="phone_1_send_sms">
                                                                <strong> Send SMS to Primary Phone Number </strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-1">
                                                            <label class="text-label form-label"> Secondary Phone
                                                                Number (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" class="form-control" placeholder="" name="secondaryCellPhone" value="<?= @$member['secondaryCellPhone'] ?>">
                                                        </div>

                                                        <div class="form-check form-check-inline section-phone-number-sms-consent <?= $member['message_consent'] ? '' : 'hide' ?>">
                                                            <input class="form-check-input" <?= in_array(@$member['secondaryCellPhone'], $sms_phone_numbers ?? []) ? 'checked' : '' ?> type="checkbox" name="phone_2_send_sms" value="1" id="phone_2_send_sms">
                                                            <label class="form-check-label text-danger" for="phone_2_send_sms">
                                                                <strong> Send SMS to Secondary Phone Number </strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-1">
                                                            <label class="text-label form-label">Any Other Phone
                                                                Number (Include Country Code e.g 256707xxxxxx)</label>
                                                            <input type="text" class="form-control" placeholder="" name="otherCellPhone" value="<?= @$member['otherCellPhone'] ?>">
                                                        </div>

                                                        <div class="form-check form-check-inline section-phone-number-sms-consent <?= $member['message_consent'] ? '' : 'hide' ?>">
                                                            <input class="form-check-input" type="checkbox" name="phone_3_send_sms" value="1" id="phone_3_send_sms" <?= in_array(@$member['otherCellPhone'], $sms_phone_numbers ?? []) ? 'checked' : '' ?>>
                                                            <label class="form-check-label text-danger" for="phone_3_send_sms">
                                                                <strong> Send SMS to Other Phone Number </strong>
                                                            </label>
                                                        </div>

                                                    </div>

                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Email Address</label>

                                                        <input type="email" class="form-control" name="email" placeholder="" value="<?php echo $member['email']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-lg-6">
                                                        <label class="text-label form-label">Additional Notes / Comments</label>

                                                        <textarea class="form-control" name="notes" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>><?php echo $member['notes']; ?></textarea>
                                                    </div>

                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Mobile Banking PIN</label>

                                                        <input type="text" class="form-control" name="mpin" placeholder="" value="<?php echo ($member['mpin'] ? '****' : ''); ?>" <?= 'readonly' ?>><br />
                                                        <?php

                                                        if ($member['mpin'] && $member['mpin'] != 0) {
                                                            echo '<a  class="btn btn-outline-warning btn-xs" data-bs-toggle="modal" data-bs-target="#set_mpin_form">Reset mPIN</a>';
                                                        } else {
                                                            echo '<a  class="btn btn-outline-primary btn-xs" data-bs-toggle="modal" data-bs-target="#set_mpin_form">Set mPIN</a>';
                                                        }

                                                        ?>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <?php

                                                        $branches = $response->getBankBranches2($user[0]['bankId'], $user[0]['branchId']);

                                                        echo '
                          
                              <label class="text-label form-label">Associated Branch *</label>
                              <select id="branchselect"  class="form-control"  name="branch" required>
                             
                                  ';
                                                        if ($branches !== '') {
                                                            foreach ($branches as $row) {
                                                                if ($row['id'] == $member['branchId']) {
                                                                    echo '
                              <option value="' . $row['id'] . '" selected>' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                                } else {
                                                                    echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                                                }
                                                            }
                                                        }

                                                        echo
                                                        '
                              </select>
                          ';
                                                        ?>
                                                    </div>
                                                </div>

                                                <br />


                                                <h4 class="text-muted mb-3 card-title text-primary">Group Information
                                                </h4>
                                                <hr class="hr-dashed">

                                                <div class="row">

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Number of Members #</label>
                                                            <input type="text" name="number_of_members" required class="form-control" placeholder="" value="<?php echo $member['number_of_members']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">City</label>
                                                        <input type="text" class="form-control" name="businessCity" placeholder="" value="<?php echo $member['bcity']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Address Line 1</label>

                                                        <input type="text" class="form-control" name="baddress" placeholder="" value="<?php echo $member['baddress']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Address Line 2</label>
                                                        <input type="text" class="form-control" name="baddress2" placeholder="" value="<?php echo $member['baddress2']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>

                                                </div>


                                                <div class="row">
                                                    <div class="col-lg-12 mb-2 mt-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Additional Notes
                                                            </label>
                                                            <textarea name="notes" class="form-control" rows="20"><?= $member["notes"] ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="text-label form-label">Old Membership No.</label>

                                                        <input type="text" class="form-control" name="old_mem" placeholder="" value="<?php echo $member['old_membership_no']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <br />

                                        <?php if ($permissions->hasSubPermissions('update_client')) { ?>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="sweetalert mt-5">
                                                        <button type="submit" name="submit" class="btn btn-primary btn sweet-confirm">Update
                                                            Details</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </form>



                                    <br />
                                    <!-- <div class="card">
                                <div class="card-body btc-price"> -->

                                    <h4 class="text-muted mb-3 card-title text-primary">Attachments & Others
                                    </h4>
                                    <hr class="hr-dashed">
                                    <form method="POST" enctype="multipart/form-data" action="https://eaoug.org/group_profile_page.php">
                                        <input type="hidden" name="cid" value="<?= @$member['cid'] ?>">
                                        <input type="hidden" name="uid" value="<?= @$member['userId'] ?>">
                                        <input type="hidden" name="sign_orig" value="<?= @$member['sign'] ?>">
                                        <input type="hidden" name="pass_orig" value="<?= @$member['profilePhoto']  ?>">
                                        <input type="hidden" name="other_orig" value="<?= @$member['other_attachments']  ?>">
                                        <input type="hidden" name="fing_orig" value="<?= @$member['fingerprint']  ?>">
                                        <input type="hidden" name="fing_orig2" value="<?= @$member['fingerprint2']  ?>">
                                        <input type="hidden" name="fing_orig3" value="<?= @$member['fingerprint3']  ?>">

                                        <div class="row ">
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Group Photo</label>
                                                    <!-- <input type="file" name="photo" accept="image/*" class="form-control" placeholder=""> -->
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">


                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="is_client" value="1" id="1" onClick="setDown()" checked required>
                                                        <label class="form-check-label">
                                                            Upload Photo from your Computer
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="is_client" value="0" id="0" onclick="setUp()" required>
                                                        <label class="form-check-label">
                                                            Take Photo from Camera
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="is_client" value="2" id="2" onclick="setUpn()" required>
                                                        <label class="form-check-label">
                                                            Skip
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <br /><br />
                                            <div class="col-lg-12 mb-3" id="upload">
                                                <div class="mb-3">

                                                    <input type="file" class="form-control" name="photo" accept="image/*" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    <input type="hidden" name="captured_image_data_2" id="captured_image_data_2">

                                                </div>
                                            </div>

                                            <div class="col-lg-12 mb-3" id="photo" style="display: none;">
                                                <div class="mb-3">
                                                    <a href="javascript:void(0);" class="btn btn-primary light me-1 px-3" data-bs-toggle="modal" data-bs-target="#photoModal" id="accesscamera"><i class="fa fa-camera m-0"></i> </a>
                                                </div>
                                            </div>

                                            <!-- start Modal-->
                                            <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Capture Photo</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modalClos()">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div>
                                                                <div id="my_camera" class="d-block mx-auto rounded overflow-hidden"></div>
                                                                <input type="hidden" name="captured_image_data" id="captured_image_data">
                                                            </div>
                                                            <div id="results" class="d-none">
                                                                <img style="width: 320px;" class="after_capture_frame" src="images/avatar/1.png" />
                                                            </div>
                                                            <!-- <form method="post" id="photoForm">
                                                                        <input type="hidden" id="photoStore" name="photoStore" value="">
                                                                    </form> -->
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-warning mx-auto text-white" id="takephoto" onClick="take_snapshot()">Capture Photo</button>
                                                            <button type="button" class="btn btn-warning mx-auto text-white d-none" id="retakephoto">Retake</button>
                                                            <button type="submit" class="btn btn-warning mx-auto text-white d-none" id="uploadphoto" form="photoForm" onclick="saveSnap()">Upload</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end modal -->

                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Group Signatories</label>
                                                    <input type="file" name="sign" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Signatory 1 Fingerprint Biometrics</label>
                                                    <input type="file" name="fingerprint" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Signatory 2 Fingerprint Biometrics</label>
                                                    <input type="file" name="fingerprint2" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Signatory 3 Fingerprint Biometrics</label>
                                                    <input type="file" name="fingerprint3" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Any Other
                                                        Attachments</label>
                                                    <input type="file" name="otherattach" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                </div>
                                            </div>
                                            <?php if ($permissions->hasSubPermissions('update_client')) { ?>
                                                <div class="col-lg-6 mb-3">
                                                    <div class="mb-3">
                                                        <button type="submit" name="submit" class="btn btn-primary">Update Attachments</button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <br /><br />
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
        <div class="modal fade" id="set_mpin_form">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Register for Mobile Banking (App, USSD & Member Portal)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= BACKEND_BASE_URL ?>Accounting/register_mobile_banking.php" class="custom-form" id="set_mpin" data-reload-page="1" data-confirm-action="1">
                            <div class="row">

                                <input type="hidden" name="client_id" value="<?= @$client_id; ?>">

                                <div class="col-md-12">
                                    Fill & submit the form below to register member to start using Mobile Banking services (App,USSD & Member Portal).
                                    <br>

                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <label for="">Primary Phone Number: <b><?= @$member['primaryCellPhone']  ?></b></label>
                                    </div>
                                </div><br /><br />
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="phone" class="form-control" placeholder=" " required value="<?= @$member['primaryCellPhone'] ?>">
                                        <label for="phone">Confirm the right Mobile Phone Number here?</label>
                                    </div>
                                </div>
                                <br />
                                <div class="col-md-12">
                                    <div class="form-floating">

                                        <label for="" class="text-primary">mPIN (Random 4 digits) will be sent to Client via SMS on the Number entered above</label>
                                    </div>
                                </div>
                                <br />
                                <br />
                                <br />
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Register</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="account_balance_reconciliation_form">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reconcile Account Balance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= BACKEND_BASE_URL ?>Accounting/account_balance_reconciliation.php" class="custom-form" id="account_balance_reconciliation" data-reload-page="1" data-confirm-action="1">
                            <div class="row">

                                <input type="hidden" name="account_balance" value="<?= @$member['acc_balance'] ?? 0; ?>">
                                <input type="hidden" name="client_id" value="<?= @$client_id; ?>">

                                <div class="col-md-12">
                                    Fill & submit the form below to update Customer's Balance.
                                    <br>

                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="acc_balance" class="form-control amount comma_separated" value="<?= @$member['balance'] ?? 0; ?>" placeholder=" " disabled>
                                        <label for="acc_balance">Account Balance</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="amount" class="form-control amount comma_separated" placeholder=" " required value="0">
                                        <label for="amount">Enter the right Account Balance here?</label>
                                    </div>
                                </div>
                                <br />
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="date" name="rd" class="form-control" placeholder=" " required value="<?= date('Y-m-d') ?>">
                                        <label for="date">Reconciliation Date</label>
                                    </div>
                                </div>
                                <br />

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Reconcile Account Balance</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="biometrics">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Fingerprint Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" class="custom-form" id="biometric_enrollment" data-reload-page="1" data-confirm-action="1" enctype="multipart/form-data">
                            <div class="row">

                                <input type="hidden" name="client_id" value="<?= @$client_id; ?>">
                                <input type="hidden" name="original" value="<?= $member['fingerprint'] ?>">
                                <input type="hidden" name="original2" value="<?= $member['fingerprint2'] ?>">
                                <input type="hidden" name="original3" value="<?= $member['fingerprint3'] ?>">

                                <a target="_blank" href="https://eaoug.org/<?php echo $member['fingerprint']; ?>" data-exthumbimage="https://eaoug.org/<?php echo $member['fingerprint']; ?>" data-src="https://eaoug.org/<?php echo $member['fingerprint']; ?>" class="col-md-4 mb-4">

                                    <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['fingerprint']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                </a>
                                <a target="_blank" href="https://eaoug.org/<?php echo $member['fingerprint2']; ?>" data-exthumbimage="https://eaoug.org/<?php echo $member['fingerprint2']; ?>" data-src="https://eaoug.org/<?php echo $member['fingerprint2']; ?>" class="col-md-4 mb-4">

                                    <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['fingerprint2']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                </a>
                                <a target="_blank" href="https://eaoug.org/<?php echo $member['fingerprint3']; ?>" data-exthumbimage="https://eaoug.org/<?php echo $member['fingerprint3']; ?>" data-src="https://eaoug.org/<?php echo $member['fingerprint3']; ?>" class="col-md-4 mb-4">

                                    <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['fingerprint3']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
                                </a>
                                <!-- <div class="col-md-12">
                                    Fill & submit the form below to update Customer's Biometrics.
                                    <br>

                                </div>
                                <br /><br /> -->
                                <!-- <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="file" name="fingerprint" class="form-control" placeholder=" " required>
                                        <label for="fingerprint">Select the Fingerprint Scan Image</label>
                                    </div>
                                </div> -->
                                <!-- <br /> -->
                                <!-- <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" name="biometrics" class="btn btn-primary form-control">Enroll Biometrics</button>
                                    </div>
                                </div> -->


                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!--**********************************
 Scripts
 ***********************************-->
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
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
            document.querySelector('#deactivate').addEventListener('submit', function(e) {
                var form = this;

                e.preventDefault();

                swal({
                    title: "Confirm",
                    text: "Are you sure you want to Deactivate this Client's A/C? The Account will not be able to transact again!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#44814E",
                    cancelButtonColor: "#f72b50",
                    confirmButtonText: "Yes, I am sure!",
                    cancelButtonText: "No, cancel!",
                    closeOnCancel: true,
                    // dangerMode: true,
                }).then(result => {
                    if (result.value) {
                        form.submit();
                        alert_loading();
                    }
                    // if (willDelete) {

                    // } else {
                    //     swal({
                    //                 title: "Cancelled!",
                    //                 text: 'A/C not Deactivated',
                    //                 type: "error"
                    //             });
                    // }
                });
            });
        </script>

        <script type="text/javascript">
            document.querySelector('#activate').addEventListener('submit', function(e) {
                var form = this;

                e.preventDefault();

                swal({
                    title: "Confirm",
                    text: "Are you sure you want to Activate this Client's A/C? The Account will be able to transact again!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#44814E",
                    cancelButtonColor: "#f72b50",
                    confirmButtonText: "Yes, I am sure!",
                    cancelButtonText: "No, cancel!",
                    closeOnCancel: true,
                    // dangerMode: true,
                }).then(result => {
                    if (result.value) {
                        form.submit();
                        alert_loading();
                    }
                    // } else {
                    //     swal({
                    //                 title: "Cancelled!",
                    //                 text: 'A/C not Activated',
                    //                 type: "error"
                    //             });
                    // }
                })
            });
        </script>

        <style>
            .swal-button--confirm {
                background-color: #DD6B55;
            }
        </style>


</body>

</html>