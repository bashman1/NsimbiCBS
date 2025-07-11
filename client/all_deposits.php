<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_all_deposits')) {
    return $permissions->isNotPermitted(true);
}
$title = 'DEPOSITS';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);
$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
$actypes = $response->getAllSavingsAccounts($user[0]['bankId'], $user[0]['branchId']);

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
                                            <select class="form-control" id="select2" name="branchId" style="display: none;">
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

                                        <label class="text-label form-label">Select Savings Account *</label>

                                        <select class="form-control" id="village" name="account_id">
                                            <option value="">All</option>
                                            <?php
                                            foreach ($actypes as $row) { ?>
                                                <option value="<?= $row['id'] ?>" <?= @$_REQUEST['actype'] == $row['id'] ? 'selected' : '' ?>>
                                                    <?= $row['ucode'] . ' - ' . $row['name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Deposited Method *</label>

                                        <select name="deposit_method" class=" form-control " id="countries">
                                            <option value="">All </option>
                                            <option value="cash" <?= @$_REQUEST['deposit_method'] == 'cash' ? "selected" : "" ?>>Cash</option>
                                            <option value="bank" <?= @$_REQUEST['deposit_method'] == 'bank' ? "selected" : "" ?>>Cheque/Bank Account/Mobile Money</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Approved By *</label>

                                        <select id="regions" class=" form-control" name="approved_by_id">
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
                            

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-label form-label">Start Date *</label>
                                    <input type="date" class="form-control" name="start_date" value="<?php @$_REQUEST['start_date'] ?>" placeholder="Start Date">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="text-label form-label">End Date *</label>
                                    <input type="date" class="form-control" name="end_date" value="<?php @$_REQUEST['end_date'] ?>" placeholder="End Date">
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
                        All Deposits
                    </h4>


                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dep" class="table table-striped" style="min-width: 845px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>A/C No.</th>
                                    <th>Client's Name</th>
                                    <th>Receipt</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>A/C Balance</th>
                                    <th>Description</th>
                                    <th>Authorised by</th>
                                    <th>Deposited by</th>
                                    <th>Branch</th>
                                    <th>Date Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>




                            </tbody>
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

    <script type="text/javascript">
        $(document).ready(function() {



            bindtoDatatable();

        });

        function bindtoDatatable(data) {

            var table = $('#dep').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,
                "lengthMenu": [
                    [10, 25, 50, 100, 150, 200, 500, -1],
                    [10, 25, 50, 100, 150, 200, 500, "All"]
                ],

                ajax: {
                    url: `<?= BACKEND_BASE_URL ?>Bank/get_all_transactions_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>&branchId=<?= @$_REQUEST['branchId'] ?>&transaction_type=D&deposit_method=<?= @$_REQUEST['deposit_method'] ?>&approved_by_id=<?= @$_REQUEST['approved_by_id'] ?>&account_id=<?= @$_REQUEST['account_id'] ?>&start_date=<?= @$_REQUEST['start_date'] ?>&end_date=<?= @$_REQUEST['end_date'] ?>`,

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
                                'membership_no': record.m_no,
                                'name': `${record.firstName ? record.firstName : ''} ${record.lastName ? record.lastName : ''} ${record.shared_name ? record.shared_name : ''}`,
                                'print': ` <button class="btn btn-success btn-sm edit btn-flat"> <a target="_blank" href="receipt.php?id=${record.tid}&type=D" style="color:#fff !important;">Print</a></button>`,
                                'status': status_label,
                                'amount': number_format(record.amount),
                                'balance': number_format(record.left_balance),
                                'description': record.description,
                                'authorized_by': `${record.authorized_by ? record.authorized_by :''} - ${record.authorized_by_position ? record.authorized_by_position : ''}`,
                                'actionby': record._actionby,
                                'branch': record.branch_name,
                                'dateCreated': to_normal_date(record.date_created),
                                'actions': `
                                    <div class="dropdown custom-dropdown mb-0">
                                        <div class="btn sharp btn-primary tp-btn"
                                            data-bs-toggle="dropdown">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                height="18px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none"
                                                    fill-rule="evenodd">
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
                                           <a class="dropdown-item text-primary confirm delete-record"
                                                href="edit_deposit?tid=${record.tid}&t=${encrypt_data(record.mid)}"> <i class="fa fa-eye"></i> Edit Deposit </a>
                                                <a class="dropdown-item text-danger confirm delete-record"
                                                href="trash_deposit?id=${record.tid}"> <i class="fa fa-trash"></i> Trash Deposit </a>
                                        </div>
                                    </div>
                                `,
                            })
                        }

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
                    "data": "print"
                }, {
                    "data": "status"
                }, {
                    "data": "amount"
                }, {
                    "data": "balance"
                }, {
                    "data": "description"
                }, {
                    "data": "authorized_by"
                }, {
                    "data": "actionby"
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