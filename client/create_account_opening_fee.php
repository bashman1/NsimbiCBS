<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('create_account_opening_fee')) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_POST['submit'])) {
    $_POST['amount'] = str_replace(",", "", $_POST['amount']);
    $_POST['passbook'] = str_replace(",", "", $_POST['passbook']);
    $res = $response->createAccountOpeningFee($_POST);

    // var_dump($res);
    // exit;
    if ($res) {
        setSessionMessage();
        header('Location:fees_tab.php?current_tab=account_opening_fees');
    } else {
        setSessionMessage(false);
        header('Location:fees_tab.php?current_tab=account_opening_fees');
    }
    exit;
}

$accounts = $response->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);
$title = 'SET A/C OPENING FEES';
require_once('includes/head_tag.php');
$fee = null;
$current_savings_accounts_ids = [];
if ($_REQUEST['id']) {
    $fee = $response->getAccountOpeningFee($_REQUEST['id']);
    $current_savings_accounts_ids = array_column($fee['saving_accounts'], 'account_id');
}
$saving_accounts = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);
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
                                    <?= @$_REQUEST['id'] ? 'Edit' : 'Add' ?> Account Opening Fee
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
                                            <input type="hidden" name="fee_id" value="<?= @$fee['fee_id'] ?>">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" name="fee_name" id="fee_name" placeholder=" " required value="<?= @$fee['fee_name'] ?>">
                                                <label for="fee_name">Fee Name</label>
                                            </div>
                                            <div class="form-floating">
                                                <input type="text" name="amount" id="amount" min="0" class="form-control comma_separated" required value="<?= number_format(@$fee['amount'] ?? 0) ?>">
                                                <label for="amount">Membership Fee</label>
                                            </div>
                                            <div class="form-floating">
                                                <input type="text" name="passbook" id="passbook" min="0" class="form-control comma_separated" required value="<?= number_format(@$fee['passbook'] ?? 0) ?>">
                                                <label for="passbook">Passbook Fees</label>
                                            </div>

                                            <div class="form-floating">
                                                <input type="text" name="shares" id="shares" min="0" class="form-control" required value="<?= number_format(@$fee['shares'] ?? 0) ?>">
                                                <label for="shares">Number of Shares</label>
                                            </div>

                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <div>
                                                        Applies To
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input activate-sections" data-sections="savings-products" type="radio" name="applies_to" value="all_clients" required <?= @$fee['applies_to'] == 'all_clients' ? 'checked' : '' ?> id="applies_to_all_clients">
                                                        <label class="form-check-label" for="applies_to_all_clients">All Clients</label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input activate-sections" data-sections="savings-products" data-activate="1" type="radio" name="applies_to" value="savings_product" required <?= @$fee['applies_to'] == 'savings_product' ? 'checked' : '' ?> id="applies_to_savings_product">
                                                        <label class="form-check-label" for="applies_to_savings_product"> Savings Product </label>
                                                    </div>

                                                    <div class="row mt-3 section-savings-products <?= @$fee['applies_to'] == 'savings_product' ? '' : 'hide' ?>">
                                                        <div class="col-md-12">
                                                            <?php foreach ($saving_accounts as $saving_account) { ?>
                                                                <div class="mb-3 row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" value="<?= $saving_account['id'] ?>" id="saving_account<?= $saving_account['id'] ?>" name="saving_accounts[]" <?= in_array($saving_account['id'], $current_savings_accounts_ids) ? 'checked' : '' ?>>

                                                                            <label class="form-check-label" for="saving_account<?= $saving_account['id'] ?>" style="">
                                                                                <?= $saving_account['name'] ?>
                                                                            </label>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <br />

                                            <?php if (!@$fee) : ?>

                                                <div class="mb-3">
                                                    <label class="text-label form-label">Select Associated Chart Account ( Membership Fees) *</label>

                                                    <select id="oscategory" class="form-control" name="account_id" required>
                                                        <option> Select </option>
                                                        <?php foreach ($accounts as $account) { ?>
                                                            <option value="<?= $account['id'] ?>">
                                                                <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>



                                                <div class="mb-3">
                                                    <label class="text-label form-label">Select Associated Chart Account ( Passbook Fees) *</label>

                                                    <select id="osector" class="form-control" name="pass_acid" required>
                                                        <option> Select </option>
                                                        <?php foreach ($accounts as $account) { ?>
                                                            <option value="<?= $account['id'] ?>">
                                                                <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            <?php endif; ?>

                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit mt-4">
                                                <?= @$fee ? 'Update' : 'Add' ?> Fee
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

        <script>
            function setExist() {
                var x = document.getElementById("pc");
                var y = document.getElementById("pc1");
                x.style.display = "block";
                y.style.display = "none";
            }


            function setCreate() {
                var x = document.getElementById("pc");
                var y = document.getElementById("pc1");

                x.style.display = "none";
                y.style.display = "block";
            }
        </script>
</body>

</html>