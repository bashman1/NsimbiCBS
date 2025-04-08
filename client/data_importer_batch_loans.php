<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_loans')) {
    return $permissions->isNotPermitted(true);
}

$responser = new Response();


if (isset($_POST['change_account'])) {


    $res = $responser->editImporterLoanSavingAcc($_POST);
    if ($res) {

        setSessionMessage(true, 'Account Updated Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
    }
    header('location:data_importer_batch_loans.php?id=' . $_POST['bid']);
    exit();
}

$batch_details = $responser->getLoanBatchDetails($_GET['id']);

$batch = $batch_details['batch'] ?? [];
$batch_loans = $batch_details['loans'] ?? [];

// var_dump($batch_loans);
// exit;
?>

<?php
$title = 'BATCH LOANS';
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

                                <div class="btc-price">
                                    <p class="text-muted mb-3">Batch Summary</p>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <span class="text-muted">Naration</span>
                                            <h6 class="mt-0">
                                                <?= @$batch['batch_name'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Import Date</span>
                                            <h6 class="mt-0">
                                                <?= normal_date(@$batch['imported_at']) ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Imported By</span>
                                            <h6 class="mt-0">
                                                <?= @$batch['imported_by'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">No. Loans</span>
                                            <h6 class="mt-0">
                                                <?= number_format(@$batch['number_of_loans']) ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Exported To Main</span>
                                            <h6 class="mt-0"><?= number_format(@$batch['exported_to_main']) ?></h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Pending</span>
                                            <h6 class="mt-0"><?= number_format(@$batch['total_pending']) ?></h6>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-lg-2">
                                            <span class="text-muted">Total Loan Amount</span>
                                            <h6 class="mt-0">
                                                <?= number_format(@$batch['total_loan_amount']) ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Total Principal Balance</span>
                                            <h6 class="mt-0"><?= number_format(@$batch['total_principal_balance']) ?></h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Total Interest Balance</span>
                                            <h6 class="mt-0"><?= number_format(@$batch['total_interest_balance']) ?></h6>
                                        </div>

                                    </div>

                                </div><br />

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <div class="table-responsive">
                                                <table class="table display dataTable" id="batches_table">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">#</th>
                                                            <th rowspan="2">Ac/No & Names</th>
                                                            <th rowspan="2">Loan Number</th>
                                                            <th rowspan="2">Loan Amount</th>
                                                            <th rowspan="2">Total Interest</th>
                                                            <th rowspan="2">Amount Paid</th>
                                                            <th rowspan="2">Duration</th>
                                                            <th rowspan="2">Frequency</th>
                                                            <th rowspan="2">Interest Rate/Per Annum</th>
                                                            <th rowspan="2">Interest Method</th>
                                                            <th rowspan="2">Disbursement Date</th>
                                                            <th rowspan="2">Credit Officer</th>
                                                            <th rowspan="2">Loan Product</th>
                                                            <th colspan="3">Current Balance</th>
                                                            <th colspan="2">Amount In Arrears</th>
                                                            <th rowspan="2">Status</th>
                                                            <th rowspan="2">Action</th>
                                                        </tr>

                                                        <tr>
                                                            <th> Principal </th>
                                                            <th> Interest </th>
                                                            <th> Total </th>

                                                            <th> Principal </th>
                                                            <th> Interest </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($batch_loans as $loan) { ?>
                                                            <tr>
                                                                <td> <?= $loan['loan_id'] ?> </td>
                                                                <td> <?= $loan['membership_no'] . ' : ' . $loan['client_names'] ?> </td>
                                                                <td> <?= $loan['loan_number'] ?> </td>
                                                                <td> <?= number_format($loan['loan_amount']) ?> </td>
                                                                <td> </td>
                                                                <td> <?= number_format($loan['amount_paid']) ?> </td>
                                                                <td> <?= $loan['duration'] . ' ' . $loan['recycle_type'] ?> </td>
                                                                <td> <?= $loan['frequency'] ?> </td>
                                                                <td> <?= $loan['interest_rate'] / 100 ?>% </td>
                                                                <td> <?= strtoupper(str_replace('_', ' ', $loan['interest_method'])) ?> </td>
                                                                <td> <?= normal_date($loan['disbursement_date']) ?> </td>
                                                                <td> <?= $loan['credit_officer_names'] ?> </td>
                                                                <td> <?= $loan['loan_type_name'] ?> </td>
                                                                <td><?= number_format($loan['principal_balance']) ?> </td>
                                                                <td> <?= number_format($loan['interest_balance']) ?></td>
                                                                <td> <?= number_format($loan['principal_balance'] + $loan['interest_balance']) ?></td>
                                                                <td> </td>
                                                                <td> </td>

                                                                <td>
                                                                    <span class="badge light badge-<?= $loan['import_status'] ? 'success' : 'danger' ?>">
                                                                        <?= $loan['import_status'] ? 'Imported' : 'Pending Importation' ?>
                                                                    </span>
                                                                </td>

                                                                <td>
                                                                    <div class="dropdown custom-dropdown mb-0">
                                                                        <div class="btn sharp btn-primary tp-btn" data-bs-toggle="dropdown">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1">
                                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                                                    <circle fill="#000000" cx="12" cy="5" r="2">
                                                                                    </circle>
                                                                                    <circle fill="#000000" cx="12" cy="12" r="2">
                                                                                    </circle>
                                                                                    <circle fill="#000000" cx="12" cy="19" r="2">
                                                                                    </circle>
                                                                                </g>
                                                                            </svg>
                                                                        </div>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <?php if (!$loan['import_status']) { ?>


                                                                                <a class="dropdown-item confirm-action" href="approve_batch_loan.php?id=<?= $loan['loan_id'] ?>&batch=<?= @$_GET['id'] ?>"> <i class="fa fa-share"></i> Import to main database </a>
                                                                                <?php if ($permissions->IsBankAdmin() || $permissions->hasSubPermissions('edit_importer_loans')) { ?>
                                                                                    <a class="dropdown-item" href="edit_batch_loan.php?id=<?= $loan['loan_id'] ?>"> <i class="fa fa-eye"></i> Edit Loan </a>
                                                                                    <a class="dropdown-item ch_acc" aria-expanded='false' data-bs-toggle='modal' data-bs-target='.bd-example-modal-lgDate' uid="<?= $loan['client_id'] ?>" lno="<?= $loan['loan_id'] ?>"> <i class="fa fa-edit"></i> Change Client A/C </a>
                                                                                    <a class="dropdown-item text-danger delete-record" href="delete_batch_loan.php?id=<?= $loan['loan_id'] ?>"> <i class="fa fa-trash"></i> Trash </a>
                                                                                <?php } ?>

                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
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

                <div class="modal fade bd-example-modal-lgDate" tabindex="-1" role="dialog" aria-hidden="true" id="ch_acc">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Change Account Form</h5>&nbsp;&nbsp;
                                <!-- <h6 class="modal-title text-primary"> - Denial Reason</h6> -->

                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                </button>
                            </div>
                            <form method="POST">

                                <input type="hidden" name="uid" class="form-control" placeholder="" id="uid">
                                <input type="hidden" name="id" class="form-control" placeholder="" id="lno">
                                <input type="hidden" name="bid" class="form-control" placeholder="" value="<?= @$batch['batch_id'] ?>">

                                <div class="modal-body">

                                    <div class="mb-3">
                                        <p style="padding: 5px;"> ** This will change the Client Account ID of this Data Importer Loan .</p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="text-label form-label">Select the Affiliated Client Account*
                                        </label>
                                        <select id="clientsselectn" class="form-control select2x" name="clientacc" required></select>
                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="submit" name="change_account" class="btn btn-primary">Update Account</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <!-- </div> -->
                <?php include('includes/footer.php'); ?>


            </div>
            <?php include('includes/bottom_scripts.php'); ?>
            <script>
                $(document).ready(function() {

                    $(document).on("click", '.ch_acc', function(e) {

                        handle_click($(this));
                    });

                });

                function handle_click(item) {
                    var uid = item.attr('uid');
                    var lno = item.attr('lno');


                    document.getElementById("uid").value = uid;
                    document.getElementById("lno").value = lno;

                }
            </script>


            <script>
                $(document).ready(function() {
                    $("select.select2x").select2({
                        dropdownParent: $('#ch_acc'),
                        ajax: {
                            url: "<?php echo BACKEND_BASE_URL ?>User/get_all_bank_clients_search.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>",
                            dataType: 'json',
                            data: (params) => {
                                return {
                                    q: params.term,
                                }
                            },

                            processResults: (data, params) => {
                                const results = data.data.map(item => {
                                    return {
                                        id: item.userId,
                                        text: item.accno + ' : ' + item.name + ' - UGX ' + item.tot_balance + '  - Branch: ' + item.branchName,
                                    };
                                });
                                return {
                                    results: results,
                                }
                            },
                        },
                    });
                })
            </script>
</body>

</html>