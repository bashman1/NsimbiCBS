<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('data_importer_clients')) {
    return $permissions->isNotPermitted(true);
}

$responser = new Response();


// var_dump($resources);
// exit;

$import_file_name = 'clients_data_importer';
?>

<?php
$title = 'CLIENTS IMPORTER';
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
                                    Clients Data Importer
                                </h4>

                                <div>
                                    <!-- <a href="data_importer_clients_error_log" class="btn btn-danger light btn-xs">
                                        <i class="fas fa-bug"></i> View Import Error Logs
                                    </a> -->

                                    <a href="export_report.php?exportFile=export_data_importer_resources&useFile=1" target="_blank" class="btn btn-primary light btn-xs">
                                        <i class="fas fa-file-pdf"></i> Data Importer Resources
                                    </a>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a href="#individuals" data-bs-toggle="tab" class="nav-link  active">Individuals</a>
                                                </li>

                                                <li class="nav-item">
                                                    <a href="#institutions" data-bs-toggle="tab" class="nav-link">Institutions</a>
                                                </li>

                                                <li class="nav-item">
                                                    <a href="#groups" data-bs-toggle="tab" class="nav-link">Groups</a>
                                                </li>

                                            </ul>

                                            <div class="tab-content">
                                                <div id="individuals" class="tab-pane fade active show">
                                                    <?php require_once('data_importer_individual_clients.php') ?>
                                                </div>

                                                <div id="institutions" class="tab-pane fade">
                                                    <?php require_once('data_importer_institution_clients.php') ?>
                                                </div>

                                                <div id="groups" class="tab-pane fade">
                                                    <?php require_once('data_importer_group_clients.php') ?>
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
        <?php include('includes/bottom_scripts.php'); ?>
</body>

<script type="text/javascript">
    $(document).ready(function() {
        getIndividualsBatches();
        getInstitutionBatches();
        getGroupBatches();
        // getInstitutionsErrors();
    });


    function getIndividualsBatches() {

        var table = $('#individuals_batches_table').dataTable({
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BACKEND_BASE_URL ?>Bank/get_data_importer_batches.php?bank_id=<?= $_SESSION['user']['bankId']; ?>&branch=<?= $_SESSION['user']['branchId'] ?>&type=clients&section=individual',
                type: "POST",
                datatype: "json",
                dataSrc: function(response) {
                    var data = response.data || [];
                    var datatable_data = [];
                    for (let record of data) {

                        let actions = `<div class="dropdown custom-dropdown mb-0">
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

                        actions += `<a class="dropdown-item"
                        href="data_importer_batch_records.php?id=${encrypt_data(record.batch_id)}&type=clients&section=${record.client_type}"> <i class="fa fa-eye"></i> View all Records </a>`;


                        if (!record.is_imported) {

                            actions += `<a class="dropdown-item"
                        href="data_importer_batch_records.php?id=${encrypt_data(record.batch_id)}&type=clients&section=${record.client_type}&status=failed"> <i class="fa fa-eye"></i>  View failed Records</a>`;

                            actions += `<a class="dropdown-item confirm-action"
                        data-href="import_data_importer_batch_db.php?id=${encrypt_data(record.batch_id)}&type=clients"> <i class="fa fa-share"></i> Export to main database </a>`;

                            actions += `<a class="dropdown-item text-danger confirm-action"
                        data-href="delete_data_importer_batch.php?id=${encrypt_data(record.batch_id)}&type=clients"> <i class="fa fa-trash"></i> Delete Batch </a>`;
                        }


                        actions += `</div>
                            </div>`;


                        let status_text = record.is_imported ? '<span class="badge light badge-success"> IMPORTED </span>' : '<span class="badge light badge-danger">PENDING</span>';

                        datatable_data.push({
                            'id': record.batch_id,
                            'batch_name': record.batch_name,
                            'imported_at': to_normal_date(record.created_at.date),
                            'imported_by': record.imported_by,
                            'number_of_records': record.number_of_records,
                            'num_imported': record.num_imported,
                            'num_pending': record.number_of_records - record.num_imported,
                            'num_failed': record.num_failed,
                            'import_status': status_text,
                            'actions': actions,
                        });
                    }

                    return datatable_data;
                },
            },

            "columns": [{
                    "data": "id",
                    "width": "5px"
                }, {
                    "data": "batch_name",
                    "width": "144px",
                },
                {
                    "data": "imported_at",
                    "width": "114px"
                }, {
                    "data": "imported_by",
                    "width": "114px"
                }, {
                    "data": "number_of_records",
                    "width": "114px"
                }, {
                    "data": "num_imported",
                    "width": "88px"
                },
                {
                    "data": "num_pending",
                    "width": "88px"
                },
                {
                    "data": "num_failed",
                    "width": "128px"
                }, {
                    "data": "import_status",
                    "width": "120px"
                },
                {
                    "data": "actions",
                    "width": "49px"
                }
            ],

            language: datatable_language,
        })

    }


    function getInstitutionBatches() {
        var table = $('#instututions_batches_table').dataTable({
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BACKEND_BASE_URL ?>Bank/get_data_importer_batches.php?bank_id=<?= $_SESSION['user']['bankId']; ?>&branch=<?= $_SESSION['user']['branchId'] ?>&type=clients&section=institution',
                type: "POST",
                datatype: "json",
                dataSrc: function(response) {
                    var data = response.data || [];
                    var datatable_data = [];
                    for (let record of data) {

                        let actions = `<div class="dropdown custom-dropdown mb-0">
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

                        actions += `<a class="dropdown-item"
                        href="data_importer_batch_records.php?id=${encrypt_data(record.batch_id)}&type=clients&section=${record.client_type}"> <i class="fa fa-eye"></i> View all Records </a>`;


                        if (!record.is_imported) {

                            actions += `<a class="dropdown-item"
                        href="data_importer_batch_records.php?id=${encrypt_data(record.batch_id)}&type=clients&section=${record.client_type}&status=failed"> <i class="fa fa-eye"></i>  View failed Records</a>`;

                            actions += `<a class="dropdown-item confirm-action"
                        data-href="import_data_importer_batch_db.php?id=${encrypt_data(record.batch_id)}&type=clients"> <i class="fa fa-share"></i> Export to main database </a>`;

                            actions += `<a class="dropdown-item text-danger confirm-action"
                        data-href="delete_data_importer_batch.php?id=${encrypt_data(record.batch_id)}&type=clients"> <i class="fa fa-trash"></i> Delete Batch </a>`;
                        }


                        actions += `</div>
                            </div>`;


                        let status_text = record.is_imported ? '<span class="badge light badge-success"> IMPORTED </span>' : '<span class="badge light badge-danger">PENDING</span>';

                        datatable_data.push({
                            'id': record.batch_id,
                            'batch_name': record.batch_name,
                            'imported_at': to_normal_date(record.created_at.date),
                            'imported_by': record.imported_by,
                            'number_of_records': record.number_of_records,
                            'num_imported': record.num_imported,
                            'num_pending': record.number_of_records - record.num_imported,
                            'num_failed': record.num_failed,
                            'import_status': status_text,
                            'actions': actions,
                        });
                    }

                    return datatable_data;
                },
            },

            "columns": [{
                    "data": "id",
                    "width": "5px"
                }, {
                    "data": "batch_name",
                    "width": "144px",
                },
                {
                    "data": "imported_at",
                    "width": "114px"
                }, {
                    "data": "imported_by",
                    "width": "114px"
                }, {
                    "data": "number_of_records",
                    "width": "114px"
                }, {
                    "data": "num_imported",
                    "width": "88px"
                },
                {
                    "data": "num_pending",
                    "width": "88px"
                },
                {
                    "data": "num_failed",
                    "width": "128px"
                }, {
                    "data": "import_status",
                    "width": "120px"
                },
                {
                    "data": "actions",
                    "width": "49px"
                }
            ],

            language: datatable_language,
        })
    }



    function getGroupBatches() {
        var table = $('#groups_batches_table').dataTable({
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BACKEND_BASE_URL ?>Bank/get_data_importer_batches.php?bank_id=<?= $_SESSION['user']['bankId']; ?>&branch=<?= $_SESSION['user']['branchId'] ?>&type=clients&section=group',
                type: "POST",
                datatype: "json",
                dataSrc: function(response) {
                    var data = response.data || [];
                    var datatable_data = [];
                    for (let record of data) {

                        let actions = `<div class="dropdown custom-dropdown mb-0">
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

                        actions += `<a class="dropdown-item"
                        href="data_importer_batch_records.php?id=${encrypt_data(record.batch_id)}&type=clients&section=${record.client_type}"> <i class="fa fa-eye"></i> View all Records </a>`;


                        if (!record.is_imported) {

                            actions += `<a class="dropdown-item"
                        href="data_importer_batch_records.php?id=${encrypt_data(record.batch_id)}&type=clients&section=${record.client_type}&status=failed"> <i class="fa fa-eye"></i>  View failed Records</a>`;

                            actions += `<a class="dropdown-item confirm-action"
                        data-href="import_data_importer_batch_db.php?id=${encrypt_data(record.batch_id)}&type=clients"> <i class="fa fa-share"></i> Export to main database </a>`;

                            actions += `<a class="dropdown-item text-danger confirm-action"
                        data-href="delete_data_importer_batch.php?id=${encrypt_data(record.batch_id)}&type=clients"> <i class="fa fa-trash"></i> Delete Batch </a>`;
                        }


                        actions += `</div>
                            </div>`;


                        let status_text = record.is_imported ? '<span class="badge light badge-success"> IMPORTED </span>' : '<span class="badge light badge-danger">PENDING</span>';

                        datatable_data.push({
                            'id': record.batch_id,
                            'batch_name': record.batch_name,
                            'imported_at': to_normal_date(record.created_at.date),
                            'imported_by': record.imported_by,
                            'number_of_records': record.number_of_records,
                            'num_imported': record.num_imported,
                            'num_pending': record.number_of_records - record.num_imported,
                            'num_failed': record.num_failed,
                            'import_status': status_text,
                            'actions': actions,
                        });
                    }

                    return datatable_data;
                },
            },

            "columns": [{
                    "data": "id",
                    "width": "5px"
                }, {
                    "data": "batch_name",
                    "width": "144px",
                },
                {
                    "data": "imported_at",
                    "width": "114px"
                }, {
                    "data": "imported_by",
                    "width": "114px"
                }, {
                    "data": "number_of_records",
                    "width": "114px"
                }, {
                    "data": "num_imported",
                    "width": "88px"
                },
                {
                    "data": "num_pending",
                    "width": "88px"
                },
                {
                    "data": "num_failed",
                    "width": "128px"
                }, {
                    "data": "import_status",
                    "width": "120px"
                },
                {
                    "data": "actions",
                    "width": "49px"
                }
            ],

            language: datatable_language,
        })

    }
</script>

</html>