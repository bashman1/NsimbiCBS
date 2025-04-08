<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
// if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_audit_trail') || !$permissions->IsSuperAdmin()) {
// return $permissions->isNotPermitted(true);
// }

include_once('includes/response.php');
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);
?>

<?php require_once('includes/head_tag.php'); ?>

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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" id="district" name="branchId">
                                                <option value="0"> All</option>
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

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="text-label form-label">Staff*</label>

                                        <select class="me-sm-2 default-select form-control wide" id="village" name="staff_id">
                                            <option value="0">All </option>
                                            <?php
                                            if ($staff !== '') {
                                                foreach ($staff as $row) {
                                                    if ($row['id'] == $_REQUEST['staff_id']) {
                                                        echo '
                                                         <option value="' . $row['id'] . '" selected>
                                                        ' . $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] . '
                                                    </option>
                                                        
                                                        ';
                                                    } else {
                                            ?>

                                                        <option value="<?= $row['id'] ?>">
                                                            <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                        </option>
                                                <?php }
                                                }
                                            } else { ?>
                                                <option readonly>No Staff Added yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="<?= @$_REQUEST['start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">End Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?>" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch Entries</button>
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
                            Audit Trail
                        </h4>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Export as</button>
                            <div class="dropdown-menu" style="margin: 0px;">

                                <a class="dropdown-item" onclick="exportToExcel('audit_trail','audit_trail_export_excel')">EXCEL</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="audit_trail" class="display fixed-layout">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Staff</th>
                                        <th>Branch</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Action Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- </div>



                </div> -->
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
    <?php include('includes/bottom_scripts.php'); ?>

    <script type="text/javascript">
        $(document).ready(function() {
            bindtoDatatable();
        });

        function bindtoDatatable() {

            var table = $('#audit_trail').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,
                layout: {
                    topStart: {
                        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    }
                },
                ajax: {
                    url: `<?= BACKEND_BASE_LOCALLY_URL ?>/User/get_audit_trail_datatables.php?bankId=<?= $_SESSION['session_user']['bankId'] ?>&branch=<?= $_SESSION['session_user']['branchId'] ?>&branchId=<?= @$_REQUEST['branchId'] ?>&staff_id=<?= @$_REQUEST['staff_id'] ?>&start_date=<?= @$_REQUEST['start_date'] ?>&end_date=<?= @$_REQUEST['end_date'] ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];

                        for (let record of data) {
                            var log_type = '';
                            if (record.type) {
                                log_type = record.type.replace(/_/g, ' ');
                            }
                            datatable_data.push({
                                'id': record.id,
                                'type': log_type,
                                'staff_names': record.staff_names,
                                'branch': record.branch_name || "-",
                                'log_message': record.log_message,
                                'amount': record.amount || 0,
                                'date_created': to_normal_date(record.date_added),
                            });
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
                    "data": "id",
                    "width": "20px",
                }, {
                    "data": "staff_names",
                    "width": "140px",
                }, {
                    "data": "branch",
                    "width": "140px",
                }, {
                    "data": "type",
                    "width": "100px"
                }, {
                    "data": "log_message",
                    "width": "300px"
                }, {
                    "data": "amount",
                    "width": "300px"
                }, {
                    "data": "date_created",
                    "width": "140px",
                }]
            })
        }
    </script>


</body>

</html>