<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_savings_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'LOAN DISBURSEMENTS';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);
$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
$actypes = $response->getAllBankLoanProducts($user[0]['bankId'], $user[0]['branchId']);

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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Savings</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Deposits</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <!-- <div class="row">

                    <div class="col-12"> -->
                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

                        <form class="ajax_results_form" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="branchId" style="display: none;">
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


                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select class="me-sm-2 default-select form-control wide" name="account_id" style="display: none;">
                                            <option value="">All</option>
                                            <?php

                                            foreach ($actypes as $row) {
                                                $selected = @$_REQUEST['actype'] == $row['id'] ? "selected" : "";
                                            ?>
                                                <option value="<?= $row['id']; ?>" <?= $selected; ?>>
                                                    <?= $row['name'] . '  - ' . $row['rate'] . ' - ' . $row['method'] ?>
                                                </option>

                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Amount *</label>

                                        <select class="me-sm-2 default-select form-control wide" name="loan_amount" style="display: none;">
                                            <option value="0">All</option>
                                            <option value="1" <?= @$_REQUEST['loan_amount'] == 1 ? 'selected' : '' ?>>100,000 - 500,000</option>
                                            <option value="2" <?= @$_REQUEST['loan_amount'] == 2 ? 'selected' : '' ?>>500,001 - 1000,000</option>
                                            <option value="3" <?= @$_REQUEST['loan_amount'] == 3 ? 'selected' : '' ?>>1,000,001 - 5,000,000</option>
                                            <option value="4" <?= @$_REQUEST['loan_amount'] == 4 ? 'selected' : '' ?>>5,000,001 - 10,000,000</option>
                                            <option value="5" <?= @$_REQUEST['loan_amount'] == 5 ? 'selected' : '' ?>>10,000,001 - 100,000,000</option>
                                            <option value="6" <?= @$_REQUEST['loan_amount'] == 6 ? 'selected' : '' ?>>100,000,001 - 300M</option>
                                            <option value="7" <?= @$_REQUEST['loan_amount'] == 7 ? 'selected' : '' ?>>300M & Above</option>

                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Authorised By *</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="approved_by_id">
                                            <option value="">All</option>
                                            <?php
                                            if ($staffs !== '') {
                                                foreach ($staffs as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['approved_by_id'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php  }
                                            } else { ?>
                                                <option readonly>No Staff Added yet</option>
                                            <?php }
                                            ?>
                                        </select>


                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Start Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?php @$_REQUEST['start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">End Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="=" <?php @$_REQUEST['end_date'] ?>" placeholder="End Date">
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
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            All Loan Disbursements
                        </h4>


                        <?php
                        if (isset($_GET['success'])) {
                            echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                            // unset($_SESSION['success']);
                        }
                        if (isset($_GET['error'])) {
                            echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                        }
                        if (isset($_GET['updateerror'])) {
                            echo '<script type="text/javascript">
                                    myUError();
                                   </script>';
                        }
                        if (isset($_GET['updatesuccess'])) {
                            echo '<script type="text/javascript">
                                    myUSuccess();
                                   </script>';
                        }
                        // unset($_SESSION['error']);

                        ?>
                        <!-- <div class="btn-group" role="group"> -->


                        <!-- </div> -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dep" class="table table-striped dataTable" style="min-width: 845px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>A/C No.</th>
                                        <th>Client's Name</th>
                                        <th>Status</th>
                                        <th>Disbursed Amount</th>
                                        <th>Mode of Disbursement</th>
                                        <th>Savings Balance</th>
                                        <th>Description</th>
                                        <th>Authorised by</th>
                                        <th>Branch</th>
                                        <th>Disbursement Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>




                                </tbody>
                                <!-- <tfoot> -->
                                <tr class="datatable-totals" style="background: #cccccc !important">
                                    <td colspan="4" rowspan="1">Total</td>
                                    <td class="cr_totals" rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                    <td rowspan="1" colspan="1"></td>
                                </tr>
                                <!-- </tfoot> -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>



            <!-- </div>
            </div> -->
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
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>


    <script type="text/javascript">
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        $(document).ready(function() {



            bindtoDatatable();

        });

        function bindtoDatatable(data) {

            // let cr_ledger = 0;
            // for (let item of data) {
            //     cr_ledger += item.amount
            // }
            // $('.cr_total').text(numberWithCommas(cr_ledger));

            var table = $('#dep').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                paging: true,

                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5'
                ],

                ajax: {
                    url: `<?= BACKEND_BASE_URL ?>Bank/get_all_transactions_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>&branchId=<?= @$_REQUEST['branchId'] ?>&transaction_type=A&deposit_method=<?= @$_REQUEST['deposit_method'] ?>&approved_by_id=<?= @$_REQUEST['approved_by_id'] ?>&account_id=<?= @$_REQUEST['account_id'] ?>&start_date=<?= @$_REQUEST['start_date'] ?>&end_date=<?= @$_REQUEST['end_date'] ?>&loan_amount=<?= @$_REQUEST['loan_amount'] ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];
                        for (let record of data) {
                            var status_label = '';
                            if (record._status == 0) {
                                status_label = '<span class="badge light badge-danger">Pending</span>';
                            } else {
                                status_label = '<span class="badge light badge-primary">Successful</span>';
                            }

                            datatable_data.push({
                                'transaction_id': record.tid,
                                'membership_no': record.m_no ?? record.old_membership_no,
                                'name': record.acc_name,

                                'status': status_label,
                                'amount': number_format(record.amount),
                                'mode': record.pay_method ?? '',
                                'balance': number_format(record.acc_balance),
                                'description': record.description,
                                'authorized_by': `${record.authorized_by ? record.authorized_by :''} - ${record.authorized_by_position ? record.authorized_by_position : ''}`,
                                'actionby': record._actionby,
                                'branch': record.branch_name,
                                'dateCreated': to_normal_date(record.date_created),
                                'actions': `
                                 
                                `,
                            })
                        }
                        var total_dibs = data.reduce((a, b) => a + to_number(b.amount), 0);
                        $('.cr_totals').text(number_format(total_dibs));
                        // console.log("return_data ::: ", return_data);

                        return datatable_data;
                    },
                },

                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                "columns": [{
                    "data": "transaction_id"
                }, {
                    "data": "membership_no"
                }, {
                    "data": "name"
                }, {
                    "data": "status"
                }, {
                    "data": "amount"
                }, {
                    "data": "mode"
                }, {
                    "data": "balance"
                }, {
                    "data": "description"
                }, {
                    "data": "authorized_by"
                }, {
                    "data": "branch"
                }, {
                    "data": "dateCreated"
                }, {
                    "data": "actions",
                }]
            })

        }
    </script>

</body>

</html>