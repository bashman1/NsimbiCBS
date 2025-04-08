<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()  || !$permissions->hasSubPermissions('view_collateral_register')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);
$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
$collateral_categories = $response->getCollateralCategories($user[0]['bankId'], $user[0]['branchId']);
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

                        <form class="ajax_results_form" method="get">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
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

                                        <label class="text-label form-label">Collateral Type *</label>

                                        <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="collateral_type_id" style="display: none;" required>
                                            <option value=""> All </option>
                                            <?php
                                            foreach ($collateral_categories as $row) { ?>
                                                <option value="<?= $row['_catid'] ?>">
                                                    <?= $row['_catname']; ?>
                                                </option>
                                                ';
                                            <?php } ?>
                                        </select>


                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Received By *</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="received_by_id">
                                            <option value="">All </option>
                                            <?php
                                            if ($staffs !== '') {
                                                foreach ($staffs as $row) { ?>
                                                    <option value="<?= $row['id'] ?>">
                                                        <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php }
                                            } else { ?>
                                                <option readonly>No Staffs Added yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Received On Start Date *</label>
                                        <input type="date" class="form-control" name="received_start_date" value="<?= @$_REQUEST['received_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Received On End Date *</label>
                                        <input type="date" class="form-control" name="received_end_date" value="<?= @$_REQUEST['received_end_date'] ?>" placeholder="End Date">
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
                            All Collaterals
                        </h4>
                        <a href="export_report.php?exportFile=report_collateral_deposit&useFile=1" target="_blank" class="btn btn-primary light btn-xs">
                            <i class="fas fa-file-pdf"></i> Download Collateral Deposit Form
                        </a>


                        <!-- </div> -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="table table-striped" style="min-width: 845px;">
                                <thead>
                                    <tr>
                                        <th style="width:80px;"><strong>#</strong></th>
                                        <th><strong>Name</strong></th>
                                        <th><strong>Loan Details</strong></th>
                                        <th><strong>Type</strong></th>
                                        <th><strong>Market Value</strong></th>
                                        <th><strong>Forced Sale Value</strong></th>
                                        <th><strong>Received by</strong></th>
                                        <th><strong>Attachment</strong></th>
                                        <th><strong>Status</strong></th>
                                        <th><strong>Date Added</strong></th>
                                        <th><strong>Action</strong></th>
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

    <script src="./vendor/select2/js/select2.full.min.js"></script>
    <script src="./js/plugins-init/select2-init.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            bindtoDatatable();

        });

        function bindtoDatatable(data) {

            var table = $('#example3').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,

                ajax: {
                    url: `<?= BACKEND_BASE_URL ?>Bank/get_bank_collaterals_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>&branchId=<?= @$_REQUEST['branchId'] ?>&collateral_type_id=<?= @$_REQUEST['collateral_type_id'] ?>&received_by_id=<?= @$_REQUEST['received_by_id'] ?>&received_start_date=<?= @$_REQUEST['received_start_date'] ?>&received_end_date=<?= @$_REQUEST['received_end_date'] ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];
                        for (let record of data) {
                            datatable_data.push({
                                '_cid': record._cid,
                                '_collateral': record._collateral,
                                'loan': `<a href="loan_details_page.php?id=${record.loanid}">${record.loan_coll}</a>`,
                                '_catname': record._catname,
                                '_mvalue': number_format(record._mvalue),
                                '_fvalue': number_format(record._fvalue),
                                '_receivedby': `${record.firstName ? record.firstName : ''} ${record.lastName ? record.lastName : '' }`,
                                '_attachment': `<a href="https://eaoug.org/${record._attachment}" class="text-primary">View</a>`,
                                '_status': `<span class="badge light ${record._status == 0 ? 'badge-success' : 'badge-danger'}"> ${record._status == 0 ? 'Active' : 'Released'} </span>`,
                                '_date_created': to_normal_date(record._date_created),
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
                                            <a class="dropdown-item"
                                                href="edit_collateral.php?id=${record._cid}"> <i class="fa fa-pencil"></i> Edit Collateral </a>
                                                <a class="dropdown-item"
                                                href="delete_collateral.php?id=${record._cid}"> <i class="fa fa-trash"></i> Trash Collateral </a>
                                                  <a href="export_report.php?exportFile=report_collateral_release&useFile=1" target="_blank" class="dropdown-item">
                            <i class="fas fa-file-pdf"></i> Collateral Release Form
                        </a>
                         <a class="dropdown-item"
                                                href="release_collateral.php?id=${record._cid}"> <i class="fa fa-trash"></i> Release Collateral </a>
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

                "aaData": data,

                "columns": [{
                        "data": "_cid"
                    }, {
                        "data": "_collateral"
                    },
                    {
                        "data": "loan"
                    }, {
                        "data": "_catname"
                    }, {
                        "data": "_mvalue"
                    }, {
                        "data": "_fvalue"
                    }, {
                        "data": "_receivedby"
                    }, {
                        "data": "_attachment"
                    }, {
                        "data": "_status"
                    }, {
                        "data": "_date_created",
                    }, {
                        "data": "actions",
                    }
                ]
            })

        }
    </script>

</body>

</html>