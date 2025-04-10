<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankAdmin() || !$permissions->hasPermissions('fees_settings')) {
    return $permissions->isNotPermitted(true);
}

require_once('includes/head_tag.php');

include_once('includes/response.php');
$response = new Response();

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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>

                                    Accounting
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                 
                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item active">
                                            <a href="#general" data-bs-toggle="tab" class="nav-link ">
                                                General
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#account_opening" data-bs-toggle="tab" class="nav-link">
                                                Account Opening
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">

                                        <div id="general" class="tab-pane fade show open">
                                            General
                                        </div>

                                        <div id="account_opening" class="tab-pane fade ">

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <?php include('includes/footer.php'); ?>

        </div>

        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                // bindtoDatatable();
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
                        url: `<?= BACKEND_BASE_LOCALLY_URL ?>/Bank/get_all_transactions_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>&branchId=<?= @$_REQUEST['branchId'] ?>&transaction_type=<?= @$_REQUEST['transaction_type'] ?>&transaction_method=<?= @$_REQUEST['transaction_method'] ?>&sub_account_id=<?= @$_REQUEST['sub_account_id'] ?>&next_due_date=<?= @$_REQUEST['next_due_date'] ?>&start_date=<?= @$_REQUEST['start_date'] ?>&end_date=<?= @$_REQUEST['end_date'] ?>`,

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

        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>


</body>

</html>