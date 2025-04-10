<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_transactions')) {
    return $permissions->isNotPermitted(true);
}

$responser = new Response();


// var_dump($resources);
// exit;
?>

<?php
$title = 'FIXED DEPOSIT IMPORTER';
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

                                <a href="export_report?exportFile=export_data_importer_resources&useFile=1" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Data Importer Resources
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a href="#fds" data-bs-toggle="tab" class="nav-link  active">Fixed Deposits</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div id="fds" class="tab-pane fade active show">

                                                    <div class="d-flex align-items-center bd-highlight mt-2">
                                                        <h3 class="mb-2">Import Fixed Deposits</h3>
                                                       

                                                        <a href="documents/data_importer_fds.xlsx" class="btn btn-primary light btn-xs mb-1 p-2  ms-4 bd-highlight">
                                                            <i class="fas fa-file-excel"></i> Download Excel Template
                                                        </a>

                                                    </div>
                                                    <hr>
                                                    <form action="data_importer_fds" id="data_importer_fds" class="mt-1 data-importer-form" method="post" enctype="multipart/form-data" data-reload-page="1">

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-floating">
                                                                    <input type="text" class="form-control" name="batch_name" required placeholder=" ">
                                                                    <label> Enter Batch Name </label>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-5">
                                                                <div class="input-group mb-2">
                                                                    <span class="input-group-text">Upload Template</span>
                                                                    <div class="form-file">
                                                                        <input type="file" name="fds_data_file" class="form-file-input form-control" id="fds_upload">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3 hide" id="fds_upload_data_actions">
                                                                <div class="d-grid">
                                                                    <button type="submit" id="submit_btn" name="fds_data" class="btn btn-primary btn-sm btn-submit">
                                                                        Import Data
                                                                    </button>

                                                                    <button type="button" class="btn btn-warning btn-sm mt-1 clear_data_importer_data">
                                                                        Clear Imported Data
                                                                    </button>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <input type="hidden" name="actual_data" class="actual_data">

                                                        <input type="hidden" name="accepted_files" class="accepted_files" value="data_importer_fds">

                                                    </form>
                                                    <div class="table-responsive mb-4">
                                                        <div id="fds_upload_data_section">
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <div id="fds_upload_data_importer_totals">
                                                        </div>
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

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mt-0 header-title">
                                    Current Batches
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table display dataTable" id="batches_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Naration</th>
                                                <th>Import Date</th>
                                                <th>Imported By</th>
                                                <th>Number of Deposits</th>
                                                <th>Total Principal Amount</th>
                                                <th>Total Principal Balance</th>
                                                <th>Total Interest Balance</th>
                                                <th> Exported To Main </th>
                                                <th>Pending</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <?php include('includes/footer.php'); ?>


        </div>
        <?php include('includes/bottom_scripts.php'); ?>
</body>


<script type="text/javascript">
    $(document).ready(function() {
        getLoansBatches();
    });

    function getLoansBatches() {
        var table = $('#batches_table').dataTable({
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BACKEND_BASE_URL ?>Bank/get_data_importer_fd_batches.php?bank_id=<?= $_SESSION['user']['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>',
                type: 'GET',
                dataType: 'json',
                dataSrc: function(response) {
                    // console.log(response);
                    var data = response.data;
                    var datatable_data = [];
                    for (let record of data) {
                        let status_text = record.batch_status ? '<span class="badge light badge-success"> IMPORTED </span>' : '<span class="badge light badge-danger">PENDING</span>';
                        let actions = `
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
                                        <div class="dropdown-menu dropdown-menu-end">`;
                        actions += `    
                                            <a class="dropdown-item"
                                                href="data_importer_batch_loans?id=${record.batch_id}"> <i class="fa fa-eye"></i> View Loans </a>`;
                        if (record.batch_status == false) {
                            actions += `<a class="dropdown-item confirm-action"
                                                href="data_importer_batch_loans_dispatch?id=${record.batch_id}"> <i class="fa fa-share"></i> Move to main database </a>
                                                
                                                <a class="dropdown-item text-danger delete-record"
                                                href="delete_loan_batch?id=${record.batch_id}"> <i class="fa fa-trash"></i> Trash </a>


                                                `;
                        }

                        actions += `</div> </div>`;

                        datatable_data.push({
                            'batch_id': record.batch_id,
                            'batch_name': record.batch_name,
                            'imported_at': to_normal_date(record.imported_at),
                            'imported_by': record.imported_by,
                            'number_of_loans': number_format(record.number_of_loans),
                            'total_loan_amount': number_format(record.total_loan_amount),
                            'total_principal_balance': number_format(record.total_principal_balance),
                            'total_interest_balance': number_format(record.total_interest_balance),
                            'exported_to_main': number_format(record.exported_to_main),
                            'total_pending': number_format(record.total_pending),
                            'status': status_text,
                            'actions': actions,
                        })
                    }
                    return datatable_data;
                }
            },

            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                }
            },

            // "aaData": data,

            "columns": [{
                    "data": "batch_id"
                }, {
                    "data": "batch_name"
                }, {
                    "data": "imported_at"
                }, {
                    "data": "imported_by"
                }, {
                    "data": "number_of_loans"
                },
                {
                    "data": "total_loan_amount"
                }, {
                    "data": "total_principal_balance"
                },
                {
                    "data": "total_interest_balance"
                }, {
                    "data": "exported_to_main"
                }, {
                    "data": "total_pending"
                }, {
                    "data": "status"
                },
                {
                    "data": "actions",
                }
            ]
        })

    }
</script>

</html>