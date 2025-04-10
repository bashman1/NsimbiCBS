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

?>

<?php
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
                                    Clients Data Importer Error Logs
                                </h4>
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
                                                    <h3 class="mt-4"> Individuals Error Logs</h3>
                                                    <div class="table-responsive">
                                                        <table id="individuals_errors_table" class="display fixed-layout dataTable" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Membership Number</th>
                                                                    <th>First Name</th>
                                                                    <th>Last Name</th>
                                                                    <th>Branch Code</th>
                                                                    <th>Email</th>
                                                                    <th>AccountTypeID</th>
                                                                    <th>SavingsOfficerID</th>
                                                                    <th>Error</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div id="institutions" class="tab-pane fade">
                                                    <h3 class="mt-4"> Institutions Error Logs </h3>
                                                    <div class="table-responsive">
                                                        <table id="institutions_errors_table" class="display fixed-layout dataTable" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Membership Number</th>
                                                                    <th>Institution Name</th>
                                                                    <th>Branch Code</th>
                                                                    <th>Email</th>
                                                                    <th>AccountTypeID</th>
                                                                    <th>SavingsOfficerID</th>
                                                                    <th>Error</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div id="groups" class="tab-pane fade">
                                                    <h3 class="mt-4"> Groups Error Logs </h3>
                                                    <div class="table-responsive">
                                                        <table id="institutions_errors_table" class="display fixed-layout dataTable" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Membership Number</th>
                                                                    <th>Group Name</th>
                                                                    <th>Branch Code</th>
                                                                    <th>Email</th>
                                                                    <th>AccountTypeID</th>
                                                                    <th>SavingsOfficerID</th>
                                                                    <th>Error</th>
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
        getIndividualsErrors();
        getInstitutionsErrors();
    });

    function getIndividualsErrors() {

        var table = $('#individuals_errors_table').dataTable({
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BACKEND_BASE_URL ?>Bank/get_data_importer_clients_error_logs.php?bank_id=<?= $_SESSION['user']['bankId']; ?>&branch=<?= $_SESSION['user']['branchId'] ?>&client_type=individual',
                type: "POST",
                datatype: "json",
                dataSrc: function(response) {
                    var data = response.data;
                    var datatable_data = [];
                    for (let record of data) {

                        // console.log("error", record.error);
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

                        if (record.error_code != 'exists') {
                            actions += `<a class="dropdown-item"
                                href="client_error_log?id=${record.id}"> <i class="fa fa-eye"></i> View/Update Client </a>`;
                        }

                        actions += `<a class="dropdown-item text-danger confirm-action"
                                data-href="delete_data_importer_record?id=${record.id}&type=client"> <i class="fa fa-trash"></i> Delete Record </a>`;


                        actions += `</div>
                                    </div>`;


                        datatable_data.push({
                            'id': record.id,
                            'membership_no': record.old_membership_no,
                            'first_name': record.first_name,
                            'last_name': record.last_name,
                            'branch_code': record.branch_code,
                            'email': record.email,
                            'account_type_id': record.account_type_id,
                            'savings_officer_id': record.savings_officer_id,
                            'error': `<div class="text-danger">${record.error}</div>`,
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
                    "data": "membership_no",
                    "width": "144px",
                }, {
                    "data": "first_name",
                    "width": "114px"
                }, {
                    "data": "last_name",
                    "width": "114px"
                }, {
                    "data": "branch_code",
                    "width": "88px"
                },
                {
                    "data": "email",
                    "width": "128px"
                }, {
                    "data": "account_type_id",
                    "width": "120px"
                },
                {
                    "data": "savings_officer_id",
                    "width": "115px"
                }, {
                    "data": "error",
                    "width": "205px"
                },
                {
                    "data": "actions",
                    "width": "49px"
                }
            ],

            language: datatable_language,
        })

    }

    function getInstitutionsErrors() {

        var table = $('#institutions_errors_table').dataTable({
            destroy: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BACKEND_BASE_URL ?>Bank/get_data_importer_clients_error_logs.php?bank_id=<?= $_SESSION['user']['bankId']; ?>&branch=<?= $_SESSION['user']['branchId'] ?>&client_type=institution',
                type: "POST",
                datatype: "json",
                dataSrc: function(response) {
                    var data = response.data;
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

                        if (record.error_code != 'exists') {
                            actions += `<a class="dropdown-item"
                        href="client_error_log?id=${record.id}"> <i class="fa fa-eye"></i> View/Update Client </a>`;
                        }

                        actions += `<a class="dropdown-item text-danger confirm-action"
                        href="delete_client_error_log?id=${record.id}"> <i class="fa fa-trash"></i> Delete Record </a>`;


                        actions += `</div>
                            </div>`;


                        datatable_data.push({
                            'id': record.id,
                            'membership_no': record.old_membership_no,
                            'name': record.first_name || record.shared_name,
                            'branch_code': record.branch_code,
                            'email': record.email,
                            'account_type_id': record.account_type_id,
                            'savings_officer_id': record.savings_officer_id,
                            'error': `<div class="text-danger">${record.error}</div>`,
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
                    "data": "membership_no",
                    "width": "144px",
                }, {
                    "data": "name",
                    "width": "114px"
                }, {
                    "data": "branch_code",
                    "width": "88px"
                },
                {
                    "data": "email",
                    "width": "128px"
                }, {
                    "data": "account_type_id",
                    "width": "120px"
                },
                {
                    "data": "savings_officer_id",
                    "width": "115px"
                }, {
                    "data": "error",
                    "width": "205px"
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