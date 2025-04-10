<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_loans')) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->updateDataImporterRecordDetails($_POST);
    // var_dump($res);
    // exit;
    if ($res) {
        setSessionMessage();
    } else {
        setSessionMessage(false);
    }

    RedirectCurrent();
    exit;
}

$request = ['id' => decrypt_data($_GET['id']), 'type' => 'client'];
$client = $response->getDataImporterRecordDetails($request);

// var_dump($client);
// exit;

$staff = $response->getBankStaff(@$_SESSION['user']['bankId'], @$_SESSION['user']['branchId']);
$actypes = $response->getAllSavingsAccounts(@$_SESSION['user']['bankId'], @$_SESSION['user']['branchId']);

$branches = null;
if (@$_SESSION['user']['bankId']) {
    $branches = $response->getBankBranches(@$_SESSION['user']['bankId']);
}
?>
<?php
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mt-0 header-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Data Importer | Edit <?= ucwords($client['client_type']) ?>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form method="post">
                                            <input type="hidden" name="client_id" value="<?= @$client['id'] ?>">
                                            <input type="hidden" name="client_type" value="<?= @$client['client_type'] ?>">
                                            <input type="hidden" name="type" value="client">

                                            <?php if (@$client['client_type'] == 'individual') { ?>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="text" value="<?= (@$client['first_name']) ?>" name="FirstName" class="form-control">
                                                            <label for="">First Name</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="text" value="<?= (@$client['last_name']) ?>" name="LastName" class="form-control">
                                                            <label for="">Last Name</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } else if (@$client['client_type'] == 'institution') { ?>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="text" value="<?= (@$client['shared_name']) ?>" name="InstitutionName" class="form-control">
                                                            <label for="">Institution Name</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 mb-2">
                                                        <label class="text-label form-label">
                                                            Choose Business Type
                                                        </label>

                                                        <select class="me-sm-2 default-select form-control wide activate-sections" name="BusinessType" required>
                                                            <option value="">Select....</option>
                                                            <?php foreach (business_types() as $key => $business_type) { ?>
                                                                <option value="<?= $key ?>" data-sections="business-type-other" data-activate="0" <?= $client['business_type'] == $key ? 'selected' : '' ?>>
                                                                    <?= $business_type ?>
                                                                </option>
                                                            <?php } ?>
                                                            <option value="other" <?= @$client['business_type'] == 'other' ? 'selected' : '' ?> data-sections="business-type-other" data-activate="1">Other</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-6 mt-2 section-business-type-other <?= @$client['business_type'] == 'other' ? '' : 'hide' ?>">
                                                        <div class="form-floating">
                                                            <input type="text" name="business_type_other" class="form-control" data-is-required="1" placeholder="">
                                                            <label class="text-label form-label">Business Type Other</label>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-lg-6">
                                                        <label class="text-label form-label">
                                                            Business is registered
                                                        </label>
                                                        <br>
                                                        <div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="is_registered" value="1" <?= @$client['is_registered'] ? 'checked' : '' ?> required id="IsRegistered">
                                                                <label class="form-check-label" for="IsRegistered">YES</label>
                                                            </div>

                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="is_registered" value="0" required id="IsNotRegistered" <?= @$client['is_registered'] ? '' : 'checked' ?>>
                                                                <label class="form-check-label" for="IsNotRegistered"> NO</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label class="text-label form-label">
                                                            Business Registration Number #
                                                        </label>
                                                        <input type="text" name="BusinessRegistrationNumber" class="form-control" value="<?= @$client["business_registration_number"] ?>" placeholder="">
                                                    </div>
                                                </div>

                                            <?php } else if (@$client['client_type'] == 'group') { ?>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="text" value="<?= (@$client['shared_name']) ?>" name="GroupName" class="form-control">
                                                            <label for="">Group Name</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="text" value="<?= (@$client['number_of_members']) ?>" name="NumberOfMembers" class="form-control">
                                                            <label for="">Number of Members</label>
                                                        </div>
                                                    </div>

                                                </div>
                                            <?php } ?>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= (@$client['old_membership_no']) ?>" name="MembershipNumber" class="form-control">
                                                        <label for="">Membeship Number</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-label form-label">Branch*</label>
                                                        <select class="me-sm-2 default-select form-control wide" name="BranchCode" style="display: none;" required>
                                                            <option value=""> Select </option>
                                                            <?php
                                                            $default_selected = @$_REQUEST['branch'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                            if (@$_SESSION['user']['branchId']) { ?>
                                                                <option value="<?= @$_SESSION['user']['bcode'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                                ';
                                                            <?php } ?>

                                                            <?php
                                                            if (@$branches) {
                                                                foreach ($branches as $row) {
                                                                    $is_seleceted = @$client['branch_code'] == $row['bcode'] ? "selected" : "";
                                                            ?>
                                                                    <option value="<?= @$row['bcode'] ?>" <?= $is_seleceted ?>>
                                                                        <?= $row['name'] ?>
                                                                    </option>
                                                            <?php }
                                                            } ?>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-label form-label">Savings Account Type *</label>
                                                        <select class="me-sm-2 default-select form-control wide" name="AccountTypeID" style="display: none;" required>
                                                            <option value="">Select</option>
                                                            <?php

                                                            foreach ($actypes as $row) {
                                                                $selected = @$client['account_type_id'] == $row['id'] ? "selected" : "";
                                                            ?>
                                                                <option value="<?= $row['id']; ?>" <?= $selected; ?>>
                                                                    <?= $row['ucode'] . ' - ' .
                                                                        $row['name'] ?>
                                                                </option>

                                                            <?php }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-label form-label">Savings Officer*</label>

                                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="SavingsOfficerID" required>
                                                            <option value="">Select</option>
                                                            <?php
                                                            foreach ($staff as $row) { ?>
                                                                <option value="<?= $row['id'] ?>" <?= $client['savings_officer_id'] == $row['id'] ? 'selected' : '' ?>>
                                                                    <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                                </option>
                                                            <?php }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (@$client['client_type'] == 'institution') { ?>

                                            <?php } ?>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format(@$client['account_balance']) ?>" name="AccountBalance" min="0" class="form-control comma_separated">
                                                        <label for="">Account Balance :</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format(@$client['loan_wallet']) ?>" name="LoanWallet" min="0" class="form-control comma_separated">
                                                        <label for="">Loan Wallet :</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format(@$client['freezed_amount']) ?>" name="FreezedAmount" min="0" class="form-control comma_separated">
                                                        <label for="">Freezed Amount :</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format(@$client['membership_fee']) ?>" name="MembershipFee" min="0" class="form-control comma_separated">
                                                        <label for="">Membership Fee :</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($client['client_type'] == 'individual') { ?>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="date" class="form-control" name="DateOfBirth" value="<?= db_date_format(@$client['date_of_birth']['date']); ?>">
                                                            <label class="">Date of Birth</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="text-label form-label">Gender </label>
                                                        <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="Gender" style="display: none;">
                                                            <option selected value="<?= @$client['gender']; ?>">
                                                                <?= ucwords(@$client['gender']); ?></option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['email'] ?>" name="Email" class="form-control">
                                                        <label for="">Email</label>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <label class="text-label form-label">Message Consent*</label>
                                                    <select class="me-sm-2 default-select form-control wide" name="MessageConsent" style="display: none;" required>
                                                        <option value="YES" <?= @$client['message_consent'] ? 'selected' : '' ?>>Yes</option>
                                                        <option value="NO" <?= !@$client['message_consent'] ? 'selected' : '' ?>>No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mt-3">

                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['primary_phone_number'] ?>" name="PrimaryTelephoneNumber" class="form-control">
                                                        <label for="">Primary Phone Number</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['secondary_phone_number'] ?>" name="SecondaryTelephoneNumber" class="form-control">
                                                        <label for="">Secondary Phone Number</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if ($client['client_type'] == 'individual') { ?>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="text" value="<?= (@$client['next_of_kin_names']) ?>" name="NextOfKinName" class="form-control">
                                                            <label for="">Next of Kin Names</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-floating">
                                                            <input type="text" value="<?= (@$client['next_of_kin_phone_number']) ?>" name="NextOfKinTelephone" class="form-control">
                                                            <label for="">Next of Kin Telephone</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['country'] ?>" name="Country" class="form-control">
                                                        <label for="">Country</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['district'] ?>" name="District" class="form-control">
                                                        <label for="">District</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['subcounty'] ?>" name="SubCounty" class="form-control">
                                                        <label for="">Subcounty</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['parish'] ?>" name="Parish" class="form-control">
                                                        <label for="">Parish</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= @$client['village'] ?>" name="Village" class="form-control">
                                                        <label for="">Village</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="date" class="form-control" name="RegistrationDate" value="<?= db_date_format(@$client['registration_date']['date']); ?>">
                                                        <label for="">Registration Date</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit mt-4">
                                                Update
                                            </button>
                                            <!--end form-->
                                        </form>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                            </div>
                            <!--end card-body-->
                        </div>
                        <!--end card-->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->


            </div>
            <?php include('includes/footer.php'); ?>


        </div>
        <?php include('includes/bottom_scripts.php'); ?>
</body>

</html>