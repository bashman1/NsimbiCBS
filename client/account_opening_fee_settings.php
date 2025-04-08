<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankAdmin()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();
if (isset($_POST['fees_general'])) {
    $success = $response->setBankMembershipFeeSettings($_POST);
    if ($success) {
        setSessionMessage();
        header('Location:fees_tab?current_tab=account_opening_fees');
    } else {
        setSessionMessage(false);
        header('Location:fees_tab?current_tab=account_opening_fees');
    }
    exit;
}

require_once('includes/head_tag.php');
$bank_details = $response->getBankDetails()[0];

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
                                    Account Opening Fee Settings
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php if (@$fee) : ?>
                                    <div class="row mt-4 mb-3">
                                        <div class="col-md-12">
                                            Branch Name: <?= @$fee['branch_name'] ?>
                                        </div>
                                    </div>
                                <?php endif ?>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <form method="post">
                                            <input type="hidden" name="fees_general" value="1">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div>
                                                        Do you want to charge?
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input activate-sections" data-sections="membership-collection-fee" data-activate="1" type="radio" name="charges_membership_fee" id="apply_charge_yes" value="1" required <?= $bank_details['charges_membership_fee'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="apply_charge_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input activate-sections" data-sections="membership-collection-fee" type="radio" name="charges_membership_fee" id="apply_charge_no" value="0" required <?= !$bank_details['charges_membership_fee'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="apply_charge_no">No</label>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row mt-3 section-membership-collection-fee <?= !$bank_details['charges_membership_fee'] ? 'hide' : '' ?>">
                                                <div class="col-md-12">
                                                    <div>
                                                        How should Account Opening fees be collected?
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="membership_fee_chanel" id="membership_collection_savings" value="savings" data-is-required="1" <?= $bank_details['membership_fee_chanel'] == 'savings' ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="membership_collection_savings">
                                                            Offset from client's savings(For members)
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="membership_fee_chanel" id="membership_collection_wallet" value="loan_wallet" data-is-required="1" <?= $bank_details['membership_fee_chanel'] == 'loan_wallet' ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="membership_collection_wallet">
                                                            Offset from Client's loan wallet(For non-members)
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="membership_fee_chanel" id="membership_collection_register" value="register" data-is-required="1" <?= $bank_details['membership_fee_chanel'] == 'register' ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="membership_collection_register">
                                                            Register Income yourself
                                                        </label>
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="row mt-3 section-membership-collection-fee <?= !$bank_details['charges_membership_fee'] ? 'hide' : '' ?>">
                                                <div class="col-md-12">
                                                    <div>
                                                        Is it mandatory for clients to clear Account Opening fees to activate their accounts
                                                    </div>
                                                    <div>
                                                        <em>
                                                            Note: Please note that clients' accounts will stay in pending state until they have fully cleared account opening fees
                                                        </em>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" data-activate="1" type="radio" name="membership_fee_required" id="membership_fee_required" value="1" required <?= $bank_details['membership_fee_required'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="membership_fee_required">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="membership_fee_required" id="membership_fee_not_required" value="0" required <?= !$bank_details['membership_fee_required'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="membership_fee_not_required">No</label>
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="row mt-4">
                                                <div class="col-lg-12">
                                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>

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