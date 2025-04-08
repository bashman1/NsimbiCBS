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
    $res = $response->updateBatchLoan($_POST);
    // var_dump($res);
    // exit;
    if ($res) {
        setSessionMessage();
    } else {
        setSessionMessage(false);
    }
    header('Location:edit_batch_loan.php?id=' . $_POST['loan_id']);
    exit;
}

$loan = $response->getBatchLoanDetails($_GET['id']);
$loan_products = $response->getAllBankLoanProducts($_SESSION['user']['bankId'], $_SESSION['user']['branchId']);

$staff = $response->getBankStaff($_SESSION['user']['bankId'], $_SESSION['user']['branchId']);

// var_dump($loan);
// exit;

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
                                    Edit Loan
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form method="post">
                                            <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-label form-label">Loan Product *</label>
                                                        <select class="me-sm-2 default-select form-control wide" name="loan_product_id" style="display: none;" required>
                                                            <option value="">All</option>
                                                            <?php
                                                            foreach ($loan_products as $row) { ?>
                                                                <option value="<?= $row['id'] ?>" id="<?= $row['frequency'] ?>" <?= $loan['loan_product_id'] == $row['id'] ? 'selected' : '' ?>>
                                                                    <?= $row['name'] . '  - ' . $row['rate'] ?>
                                                                </option>
                                                                ';
                                                            <?php }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-label form-label">Credit Officer*</label>

                                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="credit_officer_id">
                                                            <option value="">--- All ---</option>
                                                            <?php
                                                            foreach ($staff as $row) { ?>
                                                                <option value="<?= $row['id'] ?>" <?= $loan['credit_officer_id'] == $row['id'] ? 'selected' : '' ?>>
                                                                    <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                                </option>
                                                            <?php }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['loan_amount']) ?>" name="loan_amount" id="loan_amount" min="0" class="form-control comma_separated">
                                                        <label for="loan_amount">Loan Amount :</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <label for="interest_method">Interest Method :</label>
                                                    <select class="me-sm-2 default-select form-control wide" name="interest_method" style="display: none;" required>
                                                        <option value="flat_rate" <?= $loan['interest_method'] == 'flat_rate' ? 'selected' : '' ?>>FLAT RATE</option>
                                                        <option value="declining_balance" <?= $loan['interest_method'] == 'declining_balance' ? 'selected' : '' ?>>DECLINING BALANCE</option>
                                                        <!-- <option value="amortization" <?= $loan['interest_method'] == 'amortization' ? 'selected' : '' ?>>AMORTIZATION</option> -->
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['interest_rate'], 1) ?>" name="interest_rate" id="interest_rate" class="form-control">
                                                        <label for="interest_rate">Interest Rate(%)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['amount_paid']) ?>" name="amount_paid" id="amount_paid" min="0" class="form-control comma_separated">
                                                        <label for="amount_paid">Amount Paid</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['principal_balance']) ?>" name="principal_balance" id="principal_balance" min="0" class="form-control comma_separated">
                                                        <label for="principal_balance">Principal Balance</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['principal_arrears']) ?>" name="principal_arrears" id="principal_arrears" min="0" class="form-control comma_separated">
                                                        <label for="principal_arrears">Principal In Arrears</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['interest_balance']) ?>" name="interest_balance" id="interest_balance" min="0" class="form-control comma_separated">
                                                        <label for="interest_balance">Interest Balance</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['interest_arrears']) ?>" name="interest_arrears" id="interest_arrears" min="0" class="form-control comma_separated">
                                                        <label for="interest_arrears">Interest In Arrears</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="date" name="disbursement_date" class="form-control" value="<?= $loan['disbursement_date'] ?>" placeholder="Disbursement Date">
                                                        <label class="text-label form-label" for="disbursement_date">Disbursement
                                                            Date *</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-label form-label">
                                                            Frequency
                                                        </label>

                                                        <select class="me-sm-2 default-select form-control wide" name="frequency" style="display: none;" required>
                                                            <option value="">--- Select ---</option>
                                                            <?php
                                                            foreach (loan_frequencies() as $loan_frequency) { ?>
                                                                <option value="<?= $loan_frequency ?>" <?= $loan['frequency'] == $loan_frequency ? 'selected' : '' ?>>
                                                                    <?= $loan_frequency ?>
                                                                </option>
                                                            <?php }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['duration']) ?>" name="duration" id="duration" min="0" class="form-control comma_separated">
                                                        <label for="duration">Duration</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="text-label form-label">
                                                            Duration Type
                                                        </label>

                                                        <select class="me-sm-2 default-select form-control wide" name="recycle_type" style="display: none;" required>
                                                            <option value="">--- Select ---</option>
                                                            <?php
                                                            foreach (loan_recycle_types() as $recycle_type) { ?>
                                                                <option value="<?= $recycle_type ?>" <?= $loan['recycle_type'] == $recycle_type ? 'selected' : '' ?>>
                                                                    <?= $recycle_type ?>
                                                                </option>
                                                            <?php }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="text" value="<?= number_format($loan['penalty_balance']??0) ?>" name="penalty_balance" id="penalty_balance" min="0" class="form-control comma_separated">
                                                        <label for="penalty_balance">
                                                            Penalty Balance
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <input type="date" name="next_due_date" class="form-control" value="<?= $loan['next_due_date'] ?>" placeholder="Next Due Date">
                                                        <label class="text-label form-label" for="next_due_date">Next Due
                                                            Date *</label>
                                                    </div>
                                                </div>
                                            </div>


                                            <!--end form-group-->
                                            <button type="submit" id="submit_btn" name="submit" class="btn btn-primary btn-sm btn-submit mt-4">
                                                Save
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