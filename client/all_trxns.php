<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('waive_interest')) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();



?>
<?php
$title ='VIEW ALL TRXNS';
include('includes/head_tag.php');


$branches = $response->getBankBranches($user[0]['bankId']);
$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
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

                <h4 class="text-primary mb-4">All Transactions</h4>

                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

                        <form class="ajax_results_form" method="post">
                            <input type="hidden" name="transactionsFilter" value="1">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class=" form-control" name="branchId" id="branchselect">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branchId'] == $row['id'] ? "selected" : "";
                                                ?>
                                                        <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                            <?= $row['name'] ?>
                                                        </option>
                                                <?php }
                                                } ?>

                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="form-group">

                                        <label class="text-label form-label">
                                            Transaction Type *
                                        </label>

                                        <select class=" form-control " name="transaction_type" id="reserveacc">
                                            <option value="">All </option>
                                            <option value="L" <?= $_REQUEST['transaction_type'] == 'L' ? 'selected' : '' ?>>Loan Repayments</option>

                                            <option value="A" <?= $_REQUEST['transaction_type'] == 'A' ? 'selected' : '' ?>>Loan Disbursements</option>

                                            <option value="LP" <?= $_REQUEST['transaction_type'] == 'LP' ? 'selected' : '' ?>>Loan Disbursement Charges</option>

                                            <option value="LP" <?= $_REQUEST['transaction_type'] == 'LP' ? 'selected' : '' ?>>Loan Penalty</option>

                                            <option value="D" <?= $_REQUEST['transaction_type'] == 'D' ? 'selected' : '' ?>>Deposits</option>

                                            <option value="W" <?= $_REQUEST['transaction_type'] == 'W' ? 'selected' : '' ?>>Withdraws</option>

                                            <option value="I" <?= $_REQUEST['transaction_type'] == 'I' ? 'selected' : '' ?>>Other Incomes</option>

                                            <option value="E" <?= $_REQUEST['transaction_type'] == 'E' ? 'selected' : '' ?>>Expenses</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">

                                        <label class="text-label form-label">Trxn Method *</label>

                                        <select name="transaction_method" class=" form-control " id="payment_methods">
                                            <option value="">All</option>
                                            <option value="cash" <?= $_REQUEST['transaction_method'] == 'cash' ? 'selected' : '' ?>>Cash</option>
                                            <option value="bank" <?= $_REQUEST['transaction_method'] == 'bank' ? 'selected' : '' ?>>Cheque/Bank Account/Mobile Money</option>
                                            <option value="savings" <?= $_REQUEST['transaction_method'] == 'savings' ? 'selected' : '' ?>>Via Savings A/C</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Sub-Account *</label>

                                        <select id="credit_account" class="form-control" name="sub_account_id">
                                            <option value=""> All </option>
                                            <?php
                                            $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                            if ($sub_accs) {
                                                foreach ($sub_accs as $acc) { ?>
                                                    <option value="<?= $acc['id'] ?> <?= $_REQUEST['sub_account_id'] == $acc['id'] ? 'selected' : '' ?>"><?= $acc['name'] ?> - Branch: <?= $acc['branch'] ?> -  Balance:<?= number_format($acc['balance']) ?></option>
                                            <?php }
                                            }
                                            ?>

                                        </select>


                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Start
                                            Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?php $_REQUEST['start_date']; ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">End
                                            Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?php $_REQUEST['end_date']; ?>" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch
                                            Entries</button>
                                    </div>
                                </div>

                            </div>

                        </form>


                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <h1 style="font-size: 16px"> <small>All Transactions </small></h1>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="transactions_table" class="display fixed-layout" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount ( UGX )</th>
                                        <th>Account</th>
                                        <th>Vendor</th>
                                        <th>Entry Type</th>
                                        <th>Entered by</th>
                                        <th>Branch</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount ( UGX )</th>
                                        <th>Account</th>
                                        <th>Vendor</th>
                                        <th>Entry Type</th>
                                        <th>Entered by</th>
                                        <th>Branch</th>
                                    </tr>
                                </tfoot>
                            </table>
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

    <script type="text/javascript">
        $(document).ready(function() {



            bindtoDatatable();

        });

        function bindtoDatatable(data) {

            var table = $('#transactions_table').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,

                ajax: {
                    url: `<?= BACKEND_BASE_URL ?>/Bank/get_all_transactions_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>&branchId=<?= @$_REQUEST['branchId'] ?>&transaction_type=<?= @$_REQUEST['transaction_type'] ?>&transaction_method=<?= @$_REQUEST['transaction_method'] ?>&sub_account_id=<?= @$_REQUEST['sub_account_id'] ?>&next_due_date=<?= @$_REQUEST['next_due_date'] ?>&start_date=<?= $_REQUEST['start_date']?? date('Y-m-d') ?>&end_date=<?= $_REQUEST['end_date']?? date('Y-m-d') ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];
                        for (let record of data) {

                            var trasaction_type_label = '';
                            if (record.t_type == 'D') {
                                trasaction_type_label = '<span class="badge light badge-primary">DEBIT</span>';
                            } else {
                                trasaction_type_label = '<span class="badge light badge-danger">CREDIT</span>';
                            }

                            datatable_data.push({
                                'transaction_id': record.tid,
                                'date': to_normal_date(record.date_created),
                                'description': record.description ? record.description.toUpperCase() : '',
                                'amount': `<span class="text-danger"> ${number_format(record.amount)} </span>`,
                                'account': record.aname ? record.aname.toUpperCase() : '',
                                'vendor': record.acc_name ? record.acc_name.toUpperCase() : '',
                                'type': trasaction_type_label,
                                'auth': `${record.firstName ? record.firstName.toUpperCase() : ''} ${record.lastName ? record.lastName.toUpperCase() : ''}`,
                                'branch': record.branch_name ? record.branch_name : '',
                            });
                        }
                        return datatable_data;
                    },
                },

                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                "aaData": data,

                "columns": [{
                    "data": "transaction_id",
                    "width": "40px"
                }, {
                    "data": "date",
                    "width": "70px"
                }, {
                    "data": "description",
                    "width": "300px"
                }, {
                    "data": "amount",
                    "width": "126px"
                }, {
                    "data": "account",
                    "width": "150px"
                }, {
                    "data": "vendor",
                    "width": "130px"
                }, {
                    "data": "type",
                    "width": "85px"
                }, {
                    "data": "auth",
                    "width": "130px"
                }, {
                    "data": "branch",
                    "width": "160px"
                }]
            })

        }
    </script>

</body>

</html>