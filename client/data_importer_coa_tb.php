<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_coa_tb')) {
    return $permissions->isNotPermitted(true);
}

$responser = new Response();

$accounts = $responser->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);

$branches = [];
if (@$_SESSION['user']['bankId']) {
    $branches = $responser->getBankBranches($_SESSION['user']['bankId']);
}

?>

<?php
$title = 'IMPORT TRIAL BALANCE';
include('includes/head_tag.php');
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
                                    Data Importer
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a href="#transactions" data-bs-toggle="tab" class="nav-link  active">Chart of Accounts</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div id="transactions" class="tab-pane fade active show">
                                                    <h3 class="mt-3 mb-2">Chart of Accounts
                                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_new_account_modal">Add New Account</button>
                                                    </h3>

                                                    <div class="basic-form">
                                                        <form action="<?= BACKEND_BASE_URL ?>Bank/create_chart_of_accounts.php" class="custom-form" data-reset-form="1" data-confirm-action="1" id="chart_of_accounts_entry_form">
                                                            <div class="mb-3">
                                                                <label class="text-label form-label">Chart Account *</label>

                                                                <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="account_id" required>
                                                                    <option> Select </option>
                                                                    <?php foreach ($accounts as $account) { ?>
                                                                        <option value="<?= $account['id'] ?>">
                                                                            <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <div class="form-floating mt-3">
                                                                <input type="text" name="amount" id="amount" class="form-control" required placeholder=" " data-allow-decimal="1">
                                                                <label for="amount">Amount (Closing Balance)</label>
                                                            </div>

                                                            <div class="form-floating mt-3">
                                                                <input type="date" class="form-control" name="record_date" placeholder="End Date">
                                                                <label for="record_date"> Record Date </label>
                                                            </div>

                                                            <div class="form-floating mt-3">
                                                                <textarea class="form-control" name="notes" rows="20"></textarea>
                                                                <label for="notes"> Notes </label>
                                                            </div>

                                                            <div class="mt-3">
                                                                <button type="submit" name="deposit" class="btn btn-primary action-btn">Save</button>
                                                            </div>

                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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


        <div class="modal fade" id="add_new_account_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= BACKEND_BASE_URL ?>Accounting/add_new_account_data_importer.php" class="custom-form" id="add_new_account_form" data-reload-page="1" data-confirm-action="1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="account_id" class="form-control" placeholder=" " required>
                                        <label for="account_id">Account Code / A/C ID</label>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <div class="form-floating">
                                        <input type="text" name="account_name" class="form-control" placeholder=" " required>
                                        <label for="amount">Account Name</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <label class="text-label form-label">Branch*</label>
                                    <?php if ($_SESSION['user']['bankId']) { ?>
                                        <select class="form-control wide" name="account_branch_id" id="branchselect">
                                            <option value="all" selected> All</option>
                                            <?php
                                            foreach ($branches as $row) {
                                            ?>
                                                <option value="<?= @$row['id'] ?>">
                                                    <?= $row['name'] ?>
                                                </option>
                                            <?php }
                                            ?>
                                        </select>
                                    <?php } else { ?>
                                        <?= $_SESSION['user']['branchName'] ?>
                                        <input type="text" name="account_branch_id" value="<?= @$_SESSION['user']['branchId'] ?>" required>
                                    <?php } ?>
                                </div>


                                <div class="col-md-12 mt-4">
                                    <label class="text-label form-label">Account Type*</label>
                                    <select id="journalacc" class="form-control" name="account_type" required>
                                        <option> Select </option>
                                        <option value="EXPENSES">Expense</option>
                                        <option value="INCOMES">Income</option>
                                        <option value="ASSETS">Asset</option>
                                        <option value="LIABILITIES">Liability</option>
                                        <option value="CAPITAL">Capital</option>
                                        <option value="SUSPENSES">Suspense & Error Accounts</option>
                                    </select>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <label class="text-label form-label">Parent Account</label>
                                    <select id="cash_acc" class="form-control" name="parent_id">
                                        <option value="" selected> None </option>
                                        <?php foreach ($accounts as $account) { ?>
                                            <option value="<?= @$account['id'] ?>">
                                                <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Create Account</button>
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

        <?php include('includes/bottom_scripts.php'); ?>
</body>

</html>