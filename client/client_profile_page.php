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
    // 
    $_POST['client_type'] = 'individual';

    $res = $response->updateClientDetails($_POST);

    if ($res) {
        setSessionMessage(true, 'Client Details Updated Successfully!');
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
    header('location: client_profile_page.php?id=' . encrypt_data($_GET['did']));
    exit;
}

if (isset($_GET['del_id'])) {
    $res = $response->deleteCustomerAcc($_GET['del_id'], $user[0]['userId'], $user[0]['branchId'], $user[0]['bankId']);

    if ($res) {
        setSessionMessage(true, 'Account Deleted Successfully!');
        header('location: index.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location: client_profile_page.php?id=' . encrypt_data($_GET['del_id']));
        exit;
    }
}

$client_id = @$_GET['id'] = parsed_id(@$_GET['id']);
$member = $response->getMemberDetails(@$client_id)[0];


$title = 'CLIENT DETAILS';
require_once('includes/head_tag.php');
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
                <!-- row -->
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
                                        <?php echo $member['firstName'] . ' ' . $member['lastName']; ?>
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
                                        <a href="add_client_account.php?id=<?= encrypt_data(@$client_id); ?>" class="btn btn-primary light btn-xs mb-1 me-2">Savings A/Cs (<?= number_format(@$member['accs']); ?>)</a>
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
                                                            <a href="share_statement.php?id=<?= $member['userId'] ?>" class="list-group-item load_via_ajax"> Shares Statement</a>
                                                            <a href="over_draft_statement.php?id=<?= encrypt_data(@$client_id); ?>" class="list-group-item load_via_ajax"> Over Drafts Statement</a>
                                                            <a href="user_loans.php?id=<?= $member['userId'] ?>" class="list-group-item load_via_ajax"> Loans Statement</a>
                                                            <a href="user_loans.php?id=<?= $member['userId'] ?>" class="list-group-item load_via_ajax"> Credit History</a>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        if ($permissions->hasSubPermissions('update_client')) {
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
                                            value="ACTIVE">  <button type="submit" class="btn btn-primary light btn-xs mb-1">Activate A/C</button></form>'; ?>


                                            <?php } ?>

                                            <?php if ($permissions->hasSubPermissions('delete_client')) { ?>
                                                <form id="delete_member" method="get"> <input type="hidden" class="form-control" name="del_id" value="<?= $member['userId'] ?>"> <button type="submit" class="btn btn-danger light btn-xs mb-1 confirm ">Delete A/C</button></form>



                                            <?php } ?>
                                        <?php  }
                                        ?>

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
                                                        <label class="text-label form-label">First Name*</label>

                                                        <input type="text" class="form-control" name="fname" placeholder="" value="<?php echo $member['firstName']; ?>" required <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Last Name*</label>

                                                        <input type="text" class="form-control" name="lname" placeholder="" value="<?php echo $member['lastName']; ?>" required <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
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

                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Country</label>

                                                        <select name="country" id="countries" class="form-control">
                                                            <option value="<?php echo $member['country']; ?>" selected><?php echo $member['country']; ?></option>
                                                            <option value="Afghanistan">Afghanistan</option>
                                                            <option value="Albania">Albania</option>
                                                            <option value="Algeria">Algeria</option>
                                                            <option value="Andorra">Andorra</option>
                                                            <option value="Angola">Angola</option>
                                                            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                                            <option value="Argentina">Argentina</option>
                                                            <option value="Armenia">Armenia</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Austria">Austria</option>
                                                            <option value="Azerbaijan">Azerbaijan</option>
                                                            <option value="Bahamas">Bahamas</option>
                                                            <option value="Bahrain">Bahrain</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Barbados">Barbados</option>
                                                            <option value="Belarus">Belarus</option>
                                                            <option value="Belgium">Belgium</option>
                                                            <option value="Belize">Belize</option>
                                                            <option value="Benin">Benin</option>
                                                            <option value="Bhutan">Bhutan</option>
                                                            <option value="Bolivia">Bolivia</option>
                                                            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                                            <option value="Botswana">Botswana</option>
                                                            <option value="Brazil">Brazil</option>
                                                            <option value="Brunei">Brunei</option>
                                                            <option value="Bulgaria">Bulgaria</option>
                                                            <option value="Burkina Faso">Burkina Faso</option>
                                                            <option value="Burundi">Burundi</option>
                                                            <option value="Cambodia">Cambodia</option>
                                                            <option value="Cameroon">Cameroon</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="Cape Verde">Cape Verde</option>
                                                            <option value="Central African Republic">Central African Republic</option>
                                                            <option value="Chad">Chad</option>
                                                            <option value="Chile">Chile</option>
                                                            <option value="China">China</option>
                                                            <option value="Colombia">Colombia</option>
                                                            <option value="Comoros">Comoros</option>
                                                            <option value="Congo">Congo</option>
                                                            <option value="Costa Rica">Costa Rica</option>
                                                            <option value="Croatia">Croatia</option>
                                                            <option value="Cuba">Cuba</option>
                                                            <option value="Cyprus">Cyprus</option>
                                                            <option value="Czech Republic">Czech Republic</option>
                                                            <option value="Denmark">Denmark</option>
                                                            <option value="Djibouti">Djibouti</option>
                                                            <option value="Dominica">Dominica</option>
                                                            <option value="Dominican Republic">Dominican Republic</option>
                                                            <option value="Ecuador">Ecuador</option>
                                                            <option value="Egypt">Egypt</option>
                                                            <option value="El Salvador">El Salvador</option>
                                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                            <option value="Eritrea">Eritrea</option>
                                                            <option value="Estonia">Estonia</option>
                                                            <option value="Ethiopia">Ethiopia</option>
                                                            <option value="Fiji">Fiji</option>
                                                            <option value="Finland">Finland</option>
                                                            <option value="France">France</option>
                                                            <option value="Gabon">Gabon</option>
                                                            <option value="Gambia">Gambia</option>
                                                            <option value="Georgia">Georgia</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="Ghana">Ghana</option>
                                                            <option value="Greece">Greece</option>
                                                            <option value="Grenada">Grenada</option>
                                                            <option value="Guatemala">Guatemala</option>
                                                            <option value="Guinea">Guinea</option>
                                                            <option value="Guinea-Bissau">Guinea-Bissau</option>
                                                            <option value="Guyana">Guyana</option>
                                                            <option value="Haiti">Haiti</option>
                                                            <option value="Honduras">Honduras</option>
                                                            <option value="Hungary">Hungary</option>
                                                            <option value="Iceland">Iceland</option>
                                                            <option value="India">India</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="Iran">Iran</option>
                                                            <option value="Iraq">Iraq</option>
                                                            <option value="Ireland">Ireland</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Italy">Italy</option>
                                                            <option value="Jamaica">Jamaica</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="Jordan">Jordan</option>
                                                            <option value="Kazakhstan">Kazakhstan</option>
                                                            <option value="Kenya">Kenya</option>
                                                            <option value="Kiribati">Kiribati</option>
                                                            <option value="Kuwait">Kuwait</option>
                                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                            <option value="Laos">Laos</option>
                                                            <option value="Latvia">Latvia</option>
                                                            <option value="Lebanon">Lebanon</option>
                                                            <option value="Lesotho">Lesotho</option>
                                                            <option value="Liberia">Liberia</option>
                                                            <option value="Libya">Libya</option>
                                                            <option value="Liechtenstein">Liechtenstein</option>
                                                            <option value="Lithuania">Lithuania</option>
                                                            <option value="Luxembourg">Luxembourg</option>
                                                            <option value="Madagascar">Madagascar</option>
                                                            <option value="Malawi">Malawi</option>
                                                            <option value="Malaysia">Malaysia</option>
                                                            <option value="Maldives">Maldives</option>
                                                            <option value="Mali">Mali</option>
                                                            <option value="Malta">Malta</option>
                                                            <option value="Marshall Islands">Marshall Islands</option>
                                                            <option value="Mauritania">Mauritania</option>
                                                            <option value="Mauritius">Mauritius</option>
                                                            <option value="Mexico">Mexico</option>
                                                            <option value="Micronesia">Micronesia</option>
                                                            <option value="Moldova">Moldova</option>
                                                            <option value="Monaco">Monaco</option>
                                                            <option value="Mongolia">Mongolia</option>
                                                            <option value="Montenegro">Montenegro</option>
                                                            <option value="Morocco">Morocco</option>
                                                            <option value="Mozambique">Mozambique</option>
                                                            <option value="Myanmar">Myanmar</option>
                                                            <option value="Namibia">Namibia</option>
                                                            <option value="Nauru">Nauru</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="Netherlands">Netherlands</option>
                                                            <option value="New Zealand">New Zealand</option>
                                                            <option value="Nicaragua">Nicaragua</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Nigeria">Nigeria</option>
                                                            <option value="North Macedonia">North Macedonia</option>
                                                            <option value="Norway">Norway</option>
                                                            <option value="Oman">Oman</option>
                                                            <option value="Pakistan">Pakistan</option>
                                                            <option value="Palau">Palau</option>
                                                            <option value="Panama">Panama</option>
                                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                                            <option value="Paraguay">Paraguay</option>
                                                            <option value="Peru">Peru</option>
                                                            <option value="Philippines">Philippines</option>
                                                            <option value="Poland">Poland</option>
                                                            <option value="Portugal">Portugal</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Romania">Romania</option>
                                                            <option value="Russia">Russia</option>
                                                            <option value="Rwanda">Rwanda</option>
                                                            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                                            <option value="Saint Lucia">Saint Lucia</option>
                                                            <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                                            <option value="Samoa">Samoa</option>
                                                            <option value="San Marino">San Marino</option>
                                                            <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                                            <option value="Senegal">Senegal</option>
                                                            <option value="Serbia">Serbia</option>
                                                            <option value="Seychelles">Seychelles</option>
                                                            <option value="Sierra Leone">Sierra Leone</option>
                                                            <option value="Singapore">Singapore</option>
                                                            <option value="Slovakia">Slovakia</option>
                                                            <option value="Slovenia">Slovenia</option>
                                                            <option value="Solomon Islands">Solomon Islands</option>
                                                            <option value="Somalia">Somalia</option>
                                                            <option value="South Africa">South Africa</option>
                                                            <option value="South Sudan">South Sudan</option>
                                                            <option value="Spain">Spain</option>
                                                            <option value="Sri Lanka">Sri Lanka</option>
                                                            <option value="Sudan">Sudan</option>
                                                            <option value="Suriname">Suriname</option>
                                                            <option value="Sweden">Sweden</option>
                                                            <option value="Switzerland">Switzerland</option>
                                                            <option value="Syria">Syria</option>
                                                            <option value="Tajikistan">Tajikistan</option>
                                                            <option value="Tanzania">Tanzania</option>
                                                            <option value="Thailand">Thailand</option>
                                                            <option value="Timor-Leste">Timor-Leste</option>
                                                            <option value="Togo">Togo</option>
                                                            <option value="Tonga">Tonga</option>
                                                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                            <option value="Tunisia">Tunisia</option>
                                                            <option value="Turkey">Turkey</option>
                                                            <option value="Turkmenistan">Turkmenistan</option>
                                                            <option value="Tuvalu">Tuvalu</option>
                                                            <option value="Uganda">Uganda</option>
                                                            <option value="Ukraine">Ukraine</option>
                                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="United States">United States</option>
                                                            <option value="Uruguay">Uruguay</option>
                                                            <option value="Uzbekistan">Uzbekistan</option>
                                                            <option value="Vanuatu">Vanuatu</option>
                                                            <option value="Vatican City">Vatican City</option>
                                                            <option value="Venezuela">Venezuela</option>
                                                            <option value="Vietnam">Vietnam</option>
                                                            <option value="Yemen">Yemen</option>
                                                            <option value="Zambia">Zambia</option>
                                                            <option value="Zimbabwe">Zimbabwe</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Region</label>

                                                        <select name="region" id="regions" class="form-control">
                                                            <option value="<?php echo $member['region']; ?>"><?php echo $member['region']; ?></option>
                                                            <option value="Central">Central</option>
                                                            <option value="Eastern">Eastern</option>
                                                            <option value="Northern">Northern</option>
                                                            <option value="Western">Western</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Estimated Monthly Income</label>

                                                        <input type="text" class="form-control comma_separated" name="income" placeholder="" value="<?php echo $member['income']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>


                                                </div>

                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">District</label>

                                                        <select name="district" id="district" class="form-control">
                                                            <option value="<?php echo $member['district']; ?>" selected><?php echo $member['district']; ?></option>
                                                            <option value="Abim">Abim</option>
                                                            <option value="Adjumani">Adjumani</option>
                                                            <option value="Agago">Agago</option>
                                                            <option value="Alebtong">Alebtong</option>
                                                            <option value="Amolatar">Amolatar</option>
                                                            <option value="Amudat">Amudat</option>
                                                            <option value="Amuria">Amuria</option>
                                                            <option value="Amuru">Amuru</option>
                                                            <option value="Apac">Apac</option>
                                                            <option value="Arua">Arua</option>
                                                            <option value="Budaka">Budaka</option>
                                                            <option value="Bududa">Bududa</option>
                                                            <option value="Bugiri">Bugiri</option>
                                                            <option value="Bugweri">Bugweri</option>
                                                            <option value="Buhweju">Buhweju</option>
                                                            <option value="Buikwe">Buikwe</option>
                                                            <option value="Bukedea">Bukedea</option>
                                                            <option value="Bukomansimbi">Bukomansimbi</option>
                                                            <option value="Bukwo">Bukwo</option>
                                                            <option value="Bulambuli">Bulambuli</option>
                                                            <option value="Buliisa">Buliisa</option>
                                                            <option value="Bundibugyo">Bundibugyo</option>
                                                            <option value="Bunyangabu">Bunyangabu</option>
                                                            <option value="Bushenyi">Bushenyi</option>
                                                            <option value="Busia">Busia</option>
                                                            <option value="Butaleja">Butaleja</option>
                                                            <option value="Butambala">Butambala</option>
                                                            <option value="Butebo">Butebo</option>
                                                            <option value="Buvuma">Buvuma</option>
                                                            <option value="Buyende">Buyende</option>
                                                            <option value="Dokolo">Dokolo</option>
                                                            <option value="Gomba">Gomba</option>
                                                            <option value="Gulu">Gulu</option>
                                                            <option value="Hoima">Hoima</option>
                                                            <option value="Ibanda">Ibanda</option>
                                                            <option value="Iganga">Iganga</option>
                                                            <option value="Isingiro">Isingiro</option>
                                                            <option value="Jinja">Jinja</option>
                                                            <option value="Kaabong">Kaabong</option>
                                                            <option value="Kabale">Kabale</option>
                                                            <option value="Kabarole">Kabarole</option>
                                                            <option value="Kaberamaido">Kaberamaido</option>
                                                            <option value="Kagadi">Kagadi</option>
                                                            <option value="Kakumiro">Kakumiro</option>
                                                            <option value="Kalangala">Kalangala</option>
                                                            <option value="Kaliro">Kaliro</option>
                                                            <option value="Kalungu">Kalungu</option>
                                                            <option value="Kampala">Kampala</option>
                                                            <option value="Kamuli">Kamuli</option>
                                                            <option value="Kamwenge">Kamwenge</option>
                                                            <option value="Kanungu">Kanungu</option>
                                                            <option value="Kapchorwa">Kapchorwa</option>
                                                            <option value="Kapelebyong">Kapelebyong</option>
                                                            <option value="Karenga">Karenga</option>
                                                            <option value="Kasanda">Kasanda</option>
                                                            <option value="Kasese">Kasese</option>
                                                            <option value="Katakwi">Katakwi</option>
                                                            <option value="Kayunga">Kayunga</option>
                                                            <option value="Kazo">Kazo</option>
                                                            <option value="Kibaale">Kibaale</option>
                                                            <option value="Kiboga">Kiboga</option>
                                                            <option value="Kibuku">Kibuku</option>
                                                            <option value="Kiruhura">Kiruhura</option>
                                                            <option value="Kiryandongo">Kiryandongo</option>
                                                            <option value="Kisoro">Kisoro</option>
                                                            <option value="Kitagwenda">Kitagwenda</option>
                                                            <option value="Kitgum">Kitgum</option>
                                                            <option value="Koboko">Koboko</option>
                                                            <option value="Kole">Kole</option>
                                                            <option value="Kotido">Kotido</option>
                                                            <option value="Kumi">Kumi</option>
                                                            <option value="Kwania">Kwania</option>
                                                            <option value="Kween">Kween</option>
                                                            <option value="Kyankwanzi">Kyankwanzi</option>
                                                            <option value="Kyegegwa">Kyegegwa</option>
                                                            <option value="Kyenjojo">Kyenjojo</option>
                                                            <option value="Kyotera">Kyotera</option>
                                                            <option value="Lamwo">Lamwo</option>
                                                            <option value="Lira">Lira</option>
                                                            <option value="Luuka">Luuka</option>
                                                            <option value="Luwero">Luwero</option>
                                                            <option value="Lwengo">Lwengo</option>
                                                            <option value="Lyantonde">Lyantonde</option>
                                                            <option value="Manafwa">Manafwa</option>
                                                            <option value="Maracha">Maracha</option>
                                                            <option value="Masaka">Masaka</option>
                                                            <option value="Masindi">Masindi</option>
                                                            <option value="Mayuge">Mayuge</option>
                                                            <option value="Mbale">Mbale</option>
                                                            <option value="Mbarara">Mbarara</option>
                                                            <option value="Mitooma">Mitooma</option>
                                                            <option value="Mityana">Mityana</option>
                                                            <option value="Moroto">Moroto</option>
                                                            <option value="Moyo">Moyo</option>
                                                            <option value="Mpigi">Mpigi</option>
                                                            <option value="Mubende">Mubende</option>
                                                            <option value="Mukono">Mukono</option>
                                                            <option value="Nabilatuk">Nabilatuk</option>
                                                            <option value="Nakapiripirit">Nakapiripirit</option>
                                                            <option value="Nakaseke">Nakaseke</option>
                                                            <option value="Nakasongola">Nakasongola</option>
                                                            <option value="Namayingo">Namayingo</option>
                                                            <option value="Namisindwa">Namisindwa</option>
                                                            <option value="Namutumba">Namutumba</option>
                                                            <option value="Napak">Napak</option>
                                                            <option value="Nebbi">Nebbi</option>
                                                            <option value="Ngora">Ngora</option>
                                                            <option value="Ntoroko">Ntoroko</option>
                                                            <option value="Ntungamo">Ntungamo</option>
                                                            <option value="Nwoya">Nwoya</option>
                                                            <option value="Obongi">Obongi</option>
                                                            <option value="Omoro">Omoro</option>
                                                            <option value="Otuke">Otuke</option>
                                                            <option value="Oyam">Oyam</option>
                                                            <option value="Pader">Pader</option>
                                                            <option value="Pakwach">Pakwach</option>
                                                            <option value="Pallisa">Pallisa</option>
                                                            <option value="Rakai">Rakai</option>
                                                            <option value="Rubanda">Rubanda</option>
                                                            <option value="Rubirizi">Rubirizi</option>
                                                            <option value="Rukiga">Rukiga</option>
                                                            <option value="Rukungiri">Rukungiri</option>
                                                            <option value="Sembabule">Sembabule</option>
                                                            <option value="Serere">Serere</option>
                                                            <option value="Sheema">Sheema</option>
                                                            <option value="Sironko">Sironko</option>
                                                            <option value="Soroti">Soroti</option>
                                                            <option value="Tororo">Tororo</option>
                                                            <option value="Wakiso">Wakiso</option>
                                                            <option value="Yumbe">Yumbe</option>
                                                            <option value="Zombo">Zombo</option>
                                                        </select>
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
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Email Address</label>

                                                        <input type="email" class="form-control" name="email" placeholder="" value="<?php echo $member['email']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">NIN</label>

                                                        <input type="text" class="form-control" name="nin" placeholder="" value="<?php echo $member['nin']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Date of Birth</label>

                                                        <input type="date" class="form-control" name="dob" value="<?php echo $member['dateOfBirth']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Gender
                                                            </label>

                                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="gender" style="display: none;" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                                <option selected value="<?php echo $member['gender']; ?>">
                                                                    <?php echo $member['gender']; ?></option>
                                                                <option value="Other">Male</option>
                                                                <option value="Male">Male</option>
                                                                <option value="Female">Female</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="text-label form-label">Notes / Comments</label>

                                                        <textarea class="form-control" name="notes" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>><?php echo $member['notes']; ?></textarea>
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

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Marital Status*</label>

                                                            <select class="me-sm-2 default-select form-control wide activate-sections" id="inlineFormCustomSelect" name="marital" style="display: none;">
                                                                <option value="married" <?= $member['marital'] == 'married' ? 'selected' : '' ?>>Maried</option>
                                                                <option value="not married" <?= $member['marital'] == 'not married' ? 'selected' : '' ?>>Not Married</option>
                                                                <option value="divorced" <?= $member['marital'] == 'divorced' ? 'selected' : '' ?>>Divorced</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Savings Officer
                                                                *</label>

                                                            <select class=" form-control" id="clientsselect" name="saving_officer" required>
                                                                <?php

                                                                $staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
                                                                if ($staffs !== '') {
                                                                    foreach ($staffs as $row) {
                                                                        if ($row['id'] == $member['entered_by']) {
                                                                            echo '
                                                                            <option value="' . $row['id'] . '" selected>' . $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] . '</option>
                                                                            
                                                                            ';
                                                                        } else {
                                                                            echo '
                                                                            <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] . '</option>
                                                                            
                                                                            ';
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo '
                                <option readonly>No Staffs Added yet</option>
                                ';
                                                                }
                                                                ?>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="current_actype" value="<?= $member['actype2'] ?>" />
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Saving Product*</label>

                                                            <select class="form-control" id="inlineFormCustomSelect" name="saving_product" required>
                                                                <?php
                                                                $actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);
                                                                foreach ($actypes as $row) {
                                                                    if ($row['id'] == $member['actype2']) {
                                                                        echo '
                                                                    <option value="' . $row['id'] . '" selected>' . $row['ucode'] . ' - ' . $row['name'] . '</option>
                                                                    
                                                                    ';
                                                                    }
                                                                    echo '
                                                            <option value="' . $row['id'] . '">' . $row['ucode'] . ' - ' . $row['name'] . '</option>
                                                            
                                                            ';
                                                                }
                                                                ?>

                                                            </select>
                                                        </div>
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

                                                    <div class="col-md-6">
                                                        <label class="text-label form-label">Old Membership No.</label>

                                                        <input type="text" class="form-control" name="old_mem" placeholder="" value="<?php echo $member['mno']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <!-- <div class="mb-3"> -->

                                                        <label class="text-label form-label">Level of Education *</label>

                                                        <select name="education_level" class="me-sm-2 default-select form-control wide" id="osector">

                                                            <option value="<?php echo $member['education_level']; ?>" selected><?php echo $member['education_level']; ?></option>
                                                            <option value="Primary Level">Primary Level</option>
                                                            <option value="Ordinary Level">O-Level (Ordinary Level)</option>
                                                            <option value="Advanced Level">A-Level (Advanced Level)</option>
                                                            <option value="Diploma">Diploma</option>
                                                            <option value="Bachelors">Bachelor's Degree</option>
                                                            <option value="Masters">Master's Degree</option>
                                                            <option value="Doctorate">Doctorate (PhD)</option>
                                                            <option value="No Formal Education">No Formal Education</option>

                                                        </select>
                                                        <!-- </div> -->
                                                    </div>



                                                </div>



                                                <!-- </div>
                                        </div> -->

                                                <br />
                                                <!-- <div class="card">
                                            <div class="card-body btc-price"> -->
                                                <!-- <hr class="hr-dashed"> -->
                                                <h4 class="text-muted mb-3 card-title text-primary">Business Information
                                                </h4>
                                                <hr class="hr-dashed">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Business Name</label>

                                                        <input type="text" class="form-control" name="bname" placeholder="" value="<?php echo $member['bname']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>


                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">
                                                                Is the Business registered?
                                                            </label>

                                                            <br>

                                                            <div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="is_registered" value="1" <?= $member['is_registered'] ? 'checked' : '' ?> required id="IsRegistered" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                                    <label class="form-check-label" for="IsRegistered">YES</label>
                                                                </div>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" name="is_registered" value="0" required id="IsNotRegistered" <?= !@$member['is_registered'] ? 'checked' : '' ?> <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                                    <label class="form-check-label" for="IsNotRegistered"> NO</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Business Registration
                                                            Number</label>
                                                        <input type="text" class="form-control" name="businessreg" placeholder="" value="<?php echo $member['registrationNumber']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Address Line 1</label>

                                                        <input type="text" class="form-control" name="baddress" placeholder="" value="<?php echo $member['baddress']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Address Line 2</label>
                                                        <input type="text" class="form-control" name="baddress2" placeholder="" value="<?php echo $member['baddress2']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Country</label>



                                                        <select name="businesscountry" id="countries" class="form-control">
                                                            <option value="<?php echo $member['bcountry']; ?>" selected><?php echo $member['bcountry']; ?></option>
                                                            <option value="Afghanistan">Afghanistan</option>
                                                            <option value="Albania">Albania</option>
                                                            <option value="Algeria">Algeria</option>
                                                            <option value="Andorra">Andorra</option>
                                                            <option value="Angola">Angola</option>
                                                            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                                            <option value="Argentina">Argentina</option>
                                                            <option value="Armenia">Armenia</option>
                                                            <option value="Australia">Australia</option>
                                                            <option value="Austria">Austria</option>
                                                            <option value="Azerbaijan">Azerbaijan</option>
                                                            <option value="Bahamas">Bahamas</option>
                                                            <option value="Bahrain">Bahrain</option>
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Barbados">Barbados</option>
                                                            <option value="Belarus">Belarus</option>
                                                            <option value="Belgium">Belgium</option>
                                                            <option value="Belize">Belize</option>
                                                            <option value="Benin">Benin</option>
                                                            <option value="Bhutan">Bhutan</option>
                                                            <option value="Bolivia">Bolivia</option>
                                                            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                                            <option value="Botswana">Botswana</option>
                                                            <option value="Brazil">Brazil</option>
                                                            <option value="Brunei">Brunei</option>
                                                            <option value="Bulgaria">Bulgaria</option>
                                                            <option value="Burkina Faso">Burkina Faso</option>
                                                            <option value="Burundi">Burundi</option>
                                                            <option value="Cambodia">Cambodia</option>
                                                            <option value="Cameroon">Cameroon</option>
                                                            <option value="Canada">Canada</option>
                                                            <option value="Cape Verde">Cape Verde</option>
                                                            <option value="Central African Republic">Central African Republic</option>
                                                            <option value="Chad">Chad</option>
                                                            <option value="Chile">Chile</option>
                                                            <option value="China">China</option>
                                                            <option value="Colombia">Colombia</option>
                                                            <option value="Comoros">Comoros</option>
                                                            <option value="Congo">Congo</option>
                                                            <option value="Costa Rica">Costa Rica</option>
                                                            <option value="Croatia">Croatia</option>
                                                            <option value="Cuba">Cuba</option>
                                                            <option value="Cyprus">Cyprus</option>
                                                            <option value="Czech Republic">Czech Republic</option>
                                                            <option value="Denmark">Denmark</option>
                                                            <option value="Djibouti">Djibouti</option>
                                                            <option value="Dominica">Dominica</option>
                                                            <option value="Dominican Republic">Dominican Republic</option>
                                                            <option value="Ecuador">Ecuador</option>
                                                            <option value="Egypt">Egypt</option>
                                                            <option value="El Salvador">El Salvador</option>
                                                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                            <option value="Eritrea">Eritrea</option>
                                                            <option value="Estonia">Estonia</option>
                                                            <option value="Ethiopia">Ethiopia</option>
                                                            <option value="Fiji">Fiji</option>
                                                            <option value="Finland">Finland</option>
                                                            <option value="France">France</option>
                                                            <option value="Gabon">Gabon</option>
                                                            <option value="Gambia">Gambia</option>
                                                            <option value="Georgia">Georgia</option>
                                                            <option value="Germany">Germany</option>
                                                            <option value="Ghana">Ghana</option>
                                                            <option value="Greece">Greece</option>
                                                            <option value="Grenada">Grenada</option>
                                                            <option value="Guatemala">Guatemala</option>
                                                            <option value="Guinea">Guinea</option>
                                                            <option value="Guinea-Bissau">Guinea-Bissau</option>
                                                            <option value="Guyana">Guyana</option>
                                                            <option value="Haiti">Haiti</option>
                                                            <option value="Honduras">Honduras</option>
                                                            <option value="Hungary">Hungary</option>
                                                            <option value="Iceland">Iceland</option>
                                                            <option value="India">India</option>
                                                            <option value="Indonesia">Indonesia</option>
                                                            <option value="Iran">Iran</option>
                                                            <option value="Iraq">Iraq</option>
                                                            <option value="Ireland">Ireland</option>
                                                            <option value="Israel">Israel</option>
                                                            <option value="Italy">Italy</option>
                                                            <option value="Jamaica">Jamaica</option>
                                                            <option value="Japan">Japan</option>
                                                            <option value="Jordan">Jordan</option>
                                                            <option value="Kazakhstan">Kazakhstan</option>
                                                            <option value="Kenya">Kenya</option>
                                                            <option value="Kiribati">Kiribati</option>
                                                            <option value="Kuwait">Kuwait</option>
                                                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                            <option value="Laos">Laos</option>
                                                            <option value="Latvia">Latvia</option>
                                                            <option value="Lebanon">Lebanon</option>
                                                            <option value="Lesotho">Lesotho</option>
                                                            <option value="Liberia">Liberia</option>
                                                            <option value="Libya">Libya</option>
                                                            <option value="Liechtenstein">Liechtenstein</option>
                                                            <option value="Lithuania">Lithuania</option>
                                                            <option value="Luxembourg">Luxembourg</option>
                                                            <option value="Madagascar">Madagascar</option>
                                                            <option value="Malawi">Malawi</option>
                                                            <option value="Malaysia">Malaysia</option>
                                                            <option value="Maldives">Maldives</option>
                                                            <option value="Mali">Mali</option>
                                                            <option value="Malta">Malta</option>
                                                            <option value="Marshall Islands">Marshall Islands</option>
                                                            <option value="Mauritania">Mauritania</option>
                                                            <option value="Mauritius">Mauritius</option>
                                                            <option value="Mexico">Mexico</option>
                                                            <option value="Micronesia">Micronesia</option>
                                                            <option value="Moldova">Moldova</option>
                                                            <option value="Monaco">Monaco</option>
                                                            <option value="Mongolia">Mongolia</option>
                                                            <option value="Montenegro">Montenegro</option>
                                                            <option value="Morocco">Morocco</option>
                                                            <option value="Mozambique">Mozambique</option>
                                                            <option value="Myanmar">Myanmar</option>
                                                            <option value="Namibia">Namibia</option>
                                                            <option value="Nauru">Nauru</option>
                                                            <option value="Nepal">Nepal</option>
                                                            <option value="Netherlands">Netherlands</option>
                                                            <option value="New Zealand">New Zealand</option>
                                                            <option value="Nicaragua">Nicaragua</option>
                                                            <option value="Niger">Niger</option>
                                                            <option value="Nigeria">Nigeria</option>
                                                            <option value="North Macedonia">North Macedonia</option>
                                                            <option value="Norway">Norway</option>
                                                            <option value="Oman">Oman</option>
                                                            <option value="Pakistan">Pakistan</option>
                                                            <option value="Palau">Palau</option>
                                                            <option value="Panama">Panama</option>
                                                            <option value="Papua New Guinea">Papua New Guinea</option>
                                                            <option value="Paraguay">Paraguay</option>
                                                            <option value="Peru">Peru</option>
                                                            <option value="Philippines">Philippines</option>
                                                            <option value="Poland">Poland</option>
                                                            <option value="Portugal">Portugal</option>
                                                            <option value="Qatar">Qatar</option>
                                                            <option value="Romania">Romania</option>
                                                            <option value="Russia">Russia</option>
                                                            <option value="Rwanda">Rwanda</option>
                                                            <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                                            <option value="Saint Lucia">Saint Lucia</option>
                                                            <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                                            <option value="Samoa">Samoa</option>
                                                            <option value="San Marino">San Marino</option>
                                                            <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                                            <option value="Senegal">Senegal</option>
                                                            <option value="Serbia">Serbia</option>
                                                            <option value="Seychelles">Seychelles</option>
                                                            <option value="Sierra Leone">Sierra Leone</option>
                                                            <option value="Singapore">Singapore</option>
                                                            <option value="Slovakia">Slovakia</option>
                                                            <option value="Slovenia">Slovenia</option>
                                                            <option value="Solomon Islands">Solomon Islands</option>
                                                            <option value="Somalia">Somalia</option>
                                                            <option value="South Africa">South Africa</option>
                                                            <option value="South Sudan">South Sudan</option>
                                                            <option value="Spain">Spain</option>
                                                            <option value="Sri Lanka">Sri Lanka</option>
                                                            <option value="Sudan">Sudan</option>
                                                            <option value="Suriname">Suriname</option>
                                                            <option value="Sweden">Sweden</option>
                                                            <option value="Switzerland">Switzerland</option>
                                                            <option value="Syria">Syria</option>
                                                            <option value="Tajikistan">Tajikistan</option>
                                                            <option value="Tanzania">Tanzania</option>
                                                            <option value="Thailand">Thailand</option>
                                                            <option value="Timor-Leste">Timor-Leste</option>
                                                            <option value="Togo">Togo</option>
                                                            <option value="Tonga">Tonga</option>
                                                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                            <option value="Tunisia">Tunisia</option>
                                                            <option value="Turkey">Turkey</option>
                                                            <option value="Turkmenistan">Turkmenistan</option>
                                                            <option value="Tuvalu">Tuvalu</option>
                                                            <option value="Uganda">Uganda</option>
                                                            <option value="Ukraine">Ukraine</option>
                                                            <option value="United Arab Emirates">United Arab Emirates</option>
                                                            <option value="United Kingdom">United Kingdom</option>
                                                            <option value="United States">United States</option>
                                                            <option value="Uruguay">Uruguay</option>
                                                            <option value="Uzbekistan">Uzbekistan</option>
                                                            <option value="Vanuatu">Vanuatu</option>
                                                            <option value="Vatican City">Vatican City</option>
                                                            <option value="Venezuela">Venezuela</option>
                                                            <option value="Vietnam">Vietnam</option>
                                                            <option value="Yemen">Yemen</option>
                                                            <option value="Zambia">Zambia</option>
                                                            <option value="Zimbabwe">Zimbabwe</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label class="text-label form-label">City</label>
                                                        <input type="text" class="form-control" name="businessCity" placeholder="" value="<?php echo $member['bcity']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>

                                                </div>

                                                <!-- </div>
                                        </div> -->
                                                <br />

                                                <!-- <div class="card">
                                            <div class="card-body btc-price"> -->
                                                <!-- <hr class="hr-dashed"> -->
                                                <h4 class="text-muted mb-3 card-title text-primary">Family / Next of Kin
                                                    Information
                                                </h4>
                                                <hr class="hr-dashed">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Next of Kin's Name</label>
                                                        <input type="text" class="form-control" name="kname" placeholder="" value="<?php echo $member['spouseName']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Physical Address</label>

                                                        <input type="text" class="form-control" name="paddress" placeholder="" value="<?php echo $member['kaddress']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <label class="text-label form-label">Next of Kin's Phone
                                                            Number</label>
                                                        <input type="text" class="form-control" name="kphone" placeholder="" value="<?php echo $member['spouseCell']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Next of Kin's NIN</label>

                                                        <input type="text" class="form-control" name="knin" placeholder="" value="<?php echo $member['spouseNin']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-12 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Describe the
                                                                relationship with Client?</label>
                                                            <input type="text" name="relationship" class="form-control" placeholder="" value="<?php echo $member['krelationship']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- </div>
                                        </div> -->
                                                <br />
                                                <!-- <div class="card">
                                            <div class="card-body btc-price"> -->
                                                <!-- <hr class="hr-dashed"> -->
                                                <h4 class="text-muted mb-3 card-title text-primary">Occupation / Profession
                                                    Information
                                                </h4>
                                                <hr class="hr-dashed">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Economic Sector</label>

                                                            <select class="form-control " id="osector" name="ocsector">

                                                                <?php
                                                                $sectorsx = $response->getOccupationOptions($user[0]['bankId'], $user[0]['branchId'], 'sectors');

                                                                if ($sectorsx) {
                                                                    foreach ($sectorsx as $sector) {
                                                                        if ($sector['osid'] == $member['occupation_sector']) {
                                                                            echo '
                                                                        <option value="' . $sector['osid'] . '" selected>' . $sector['os_name'] . '</option>
                                                                        ';
                                                                        } else {
                                                                            echo '
                                                                        <option value="' . $sector['osid'] . '">' . $sector['os_name'] . '</option>
                                                                        ';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <option value="0">Others (Specify below)</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0" style="display: none;" id="sect_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Enter the Economic Sector</label>
                                                            <input type="text" name="sect_other" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Occupation Category</label>

                                                            <select class="form-control " id="ocategory" name="ocategory">

                                                                <?php
                                                                $sectors = $response->getOccupationOptions($user[0]['bankId'], $user[0]['branchId'], "category");

                                                                if ($sectors) {
                                                                    foreach ($sectors as $sector) {
                                                                        if ($sector['oscid'] == $member['occupation_category']) {
                                                                            echo '
                                                                        <option value="' . $sector['oscid'] . '" selected>' . $sector['osc_name'] . '</option>
                                                                        ';
                                                                        } else {
                                                                            echo '
                                                                        <option value="' . $sector['oscid'] . '">' . $sector['osc_name'] . '</option>
                                                                        ';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <option value="0">Others (Specify below)</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 mt-2 mt-sm-0" style="display: none;" id="cat_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Enter the Occupation Category</label>
                                                            <input type="text" name="cat_other" class="form-control" placeholder="">
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-6 mb-3">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Occupation Sub-Category</label>

                                                            <select class="form-control " id="oscategory" name="oscategory">

                                                                <?php
                                                                $sectorsy = $response->getOccupationOptions($user[0]['bankId'], $user[0]['branchId'], 'subcategory');

                                                                if ($sectorsy) {
                                                                    foreach ($sectorsy as $sector) {
                                                                        if ($sector['ocid'] == $member['occupation_sub_category']) {
                                                                            echo '
                                                                        <option value="' . $sector['ocid'] . '" selected>' . $sector['oc_name'] . '</option>
                                                                        ';
                                                                        } else {
                                                                            echo '
                                                                        <option value="' . $sector['ocid'] . '">' . $sector['oc_name'] . '</option>
                                                                        ';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <option value="0">Others (Specify below)</option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-3" style="display: none;" id="sub_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Enter the Occupation Sub-Category</label>
                                                            <input type="text" name="sub_other" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 mt-2 mt-sm-0">
                                                        <label class="text-label form-label">Occupation /
                                                            Profession</label>

                                                        <input type="text" class="form-control" name="prof" placeholder="" value="<?php echo $member['prof']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                    </div>
                                                </div>

                                                <br />
                                                <!-- <div class="card">
                                <div class="card-body btc-price"> -->

                                                <h4 class="text-muted mb-3 card-title text-primary">Disability Status
                                                </h4>
                                                <hr class="hr-dashed">

                                                <div class="row">
                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Disability Status</label>

                                                            <select class="form-control " id="authby" name="disability_status" required>


                                                                <option value="yes">Yes (I'm Disabled)</option>
                                                                <option value="no">No</option>
                                                                <option value="<?php echo @$member['disability_desc']; ?>" selected><?php echo @$member['disability_desc']; ?></option>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Disability Category</label>

                                                            <select class="form-control " id="disability" name="disability_category" required>


                                                                <option value="<?php echo @$member['disability_cat']; ?>" selected><?php echo @$member['disability_cat']; ?></option>
                                                                <option value="none">None</option>
                                                                <option value="Deafness">Deafness</option>
                                                                <option value="Blindness">Blindness</option>
                                                                <option value="Specific learning disability">Specific learning disability</option>
                                                                <option value="Emotional disturbance">Emotional disturbance</option>
                                                                <option value="Deafblind">Deafblind</option>
                                                                <option value="Cognitive or learning disabilities">Cognitive or learning disabilities</option>
                                                                <option value="Traumatic brain injury">Traumatic brain injury</option>
                                                                <option value="Intellectual disability">Intellectual disability</option>
                                                                <option value="Speech or language impairment">Speech or language impairment</option>
                                                                <option value="Multiple disabilities">Multiple disabilities</option>
                                                                <option value="Vision">Vision</option>
                                                                <option value="Paralysis">Paralysis</option>
                                                                <option value="Autism">Autism</option>
                                                                <option value="Orthopedic impairment">Orthopedic impairment</option>
                                                                <option value="Other health Impairment">Other health Impairment</option>
                                                                <option value="Developmental delay">Developmental delay</option>
                                                                <option value="Psychiatric">Psychiatric</option>
                                                                <option value="Physical">Physical</option>
                                                                <option value="Others">Others</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 mb-2" id="disabled_cat_other">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">If you selected Others above</label>
                                                            <input type="text" name="disabled_cat_other" class="form-control" placeholder="" value="<?php echo @$member['disability_other']; ?>" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <div class="mb-3">
                                                            <label class="text-label form-label">Describe the Disability Details of the Client</label>
                                                            <textarea cols="10" name="disability_details" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>><?php echo @$member['disability_desc']; ?></textarea>
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
                                    <!-- </div>
                            </div> -->

                                    <br />
                                    <!-- <div class="card">
                                <div class="card-body btc-price"> -->

                                    <h4 class="text-muted mb-3 card-title text-primary">Attachments & Others
                                    </h4>
                                    <hr class="hr-dashed">
                                    <form method="POST" enctype="multipart/form-data" action="https://eaoug.org/client_profile_page.php">
                                        <input type="hidden" name="cid" value="<?= @$member['cid'] ?>">
                                        <input type="hidden" name="uid" value="<?= @$member['userId'] ?>">
                                        <input type="hidden" name="sign_orig" value="<?= @$member['sign'] ?>">
                                        <input type="hidden" name="pass_orig" value="<?= @$member['profilePhoto']  ?>">
                                        <input type="hidden" name="other_orig" value="<?= @$member['other_attachments']  ?>">
                                        <input type="hidden" name="fing_orig" value="<?= @$member['fingerprint']  ?>">

                                        <div class="row ">
                                            <div class="col-lg-12 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Client's Passport-Sized
                                                        Photo</label>
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
                                                    <label class="text-label form-label">Client's Scanned
                                                        Signature</label>
                                                    <input type="file" name="sign" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Client's Fingerprint Biometrics</label>
                                                    <input type="file" name="fingerprint" class="form-control" placeholder="" <?= ($permissions->hasSubPermissions('update_client')) ? '' : 'readonly' ?>>
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

                            <input type="hidden" name="client_id" value="<?= @$client_id; ?>">
                            <input type="hidden" name="acc_balance" value="<?= @$member['balance'] ?? 0 ?>">

                            <div class="col-md-12">
                                Fill & submit the form below to update Customer's Balance.
                                <br>

                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <label for="">Account Balance: <b><?= number_format(@$member['balance'] ?? 0); ?></b></label>
                                </div>
                            </div><br /><br />
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
                                <div class="form-floating">
                                    <input type="text" name="reason" class="form-control" placeholder=" " required value="">
                                    <label for="reason">Reason</label>
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
                                    <label for="">Primary Phone Number: <b><?= @$member['primaryCellPhone']; ?></b></label>
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

                            <a target="_blank" href="https://eaoug.org/<?php echo $member['fingerprint']; ?>" data-exthumbimage="https://eaoug.org/<?php echo $member['fingerprint']; ?>" data-src="https://eaoug.org/<?php echo $member['sign']; ?>" class="col-md-4 mb-4">

                                <img class="rounded-circle" src="https://eaoug.org/<?php echo $member['fingerprint']; ?>" alt="" width="80" height="80" onerror="this.onerror=null; this.src='images/account.png'" />
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


    <script type="text/javascript">
        $(document).ready(function() {
            pay_method_change();
        });
    </script>

    <script>
        function pay_method_change() {
            var sect_other = $('#sect_other');
            var cat_other = $('#cat_other');
            var subs_other = $('#sub_other');

            $('#osector').change(function() {

                if ($(this).find('option:selected').val() == 0) {
                    sect_other.show();
                } else {
                    sect_other.hide();
                }


            });

            $('#ocategory').change(function() {

                if ($(this).find('option:selected').val() == 0) {
                    cat_other.show();
                } else {
                    cat_other.hide();
                }


            });

            $('#oscategory').change(function() {

                if ($(this).find('option:selected').val() == 0) {
                    subs_other.show();
                } else {
                    subs_other.hide();
                }


            });





        }
        // -------------end --------------
    </script>


</body>

</html>