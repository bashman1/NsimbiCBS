<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('data_importer')) {
    return $permissions->isNotPermitted(true);
}

$responser = new Response();
$_REQUEST['id'] = decrypt_data($_REQUEST['id']);
$response = $responser->get_data_importer_batch_records($_REQUEST);

$records = @$response['data']['records'] ?? [];
$batch = @$response['data']['batch'] ?? [];

// var_dump($response);
// exit;

$section = @$_GET['section'];
$type = @$_GET['type'];
$status = @$_GET['status'];
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
                                    <?= ucwords($type) ?> Data Importer Records
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
                                                <?= normal_date(@$batch['created_at']['date']) ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Imported By</span>
                                            <h6 class="mt-0">
                                                <?= @$batch['imported_by'] ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">No. Records</span>
                                            <h6 class="mt-0">
                                                <?= number_format(@$batch['number_of_records']) ?>
                                            </h6>
                                        </div>
                                        <div class="col-lg-2">
                                            <span class="text-muted">Exported To Main</span>
                                            <h6 class="mt-0"><?= number_format(@$batch['num_imported']) ?></h6>
                                        </div>

                                        <div class="col-lg-2">
                                            <span class="text-muted">Failed</span>
                                            <h6 class="mt-0"><?= number_format(@$batch['num_failed']) ?></h6>
                                        </div>

                                        <div class="col-lg-2">
                                            <span class="text-muted">Pending</span>
                                            <h6 class="mt-0"><?= number_format(@$batch['number_of_records'] - @$batch['num_imported']) ?></h6>
                                        </div>
                                    </div>

                                </div><br />

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <h3 class="mt-4">
                                                <?= ucwords(to_plural(@$section)) ?> <?= @$status == 'failed' ? 'Error Logs' : '' ?>
                                            </h3>
                                            <div class="table-responsive">
                                                <?php if ($type == 'clients') { ?>
                                                    <table class="table display dataTable table-data-table" style="min-width: 845px">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Membership Number</th>

                                                                <?php if (@$section == 'individual') { ?>
                                                                    <th>First Name</th>
                                                                    <th>Last Name</th>
                                                                <?php } else if ($section == 'institution') { ?>
                                                                    <th>Institution Name</th>
                                                                <?php } else if ($section == 'group') { ?>
                                                                    <th>Group Name</th>
                                                                <?php } ?>
                                                                <th>Branch Code</th>
                                                                <th>Email</th>
                                                                <th>AccountBalance</th>
                                                                <th>AccountTypeID</th>
                                                                <th>SavingsOfficerID</th>
                                                                <th>Status</th>
                                                                <th>Error</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($records as $record) { ?>
                                                                <tr>
                                                                    <td> <?= @$record['id'] ?>. </td>
                                                                    <td> <?= @$record['old_membership_no'] ?> </td>
                                                                    <?php if (@$section == 'individual') { ?>
                                                                        <td> <?= @$record['first_name'] ?> </td>
                                                                        <td> <?= @$record['last_name'] ?> </td>
                                                                    <?php } else { ?>
                                                                        <td> <?= @$record['shared_name'] ?> </td>
                                                                    <?php } ?>
                                                                    <td> <?= @$record['branch_code'] ?> </td>
                                                                    <td> <?= @$record['email'] ?> </td>
                                                                    <td> <?= number_format(@$record['account_balance']) ?> </td>
                                                                    <td> <?= @$record['account_type_id'] ?> </td>
                                                                    <td> <?= @$record['savings_officer_id'] ?> </td>
                                                                    <td>
                                                                        <span class="badge badge-rounded light badge-<?= @$record['is_imported'] ? 'success' : 'danger' ?>"> <?= @$record['is_imported'] ? 'IMPORTED' : 'PENDING' ?> </span>
                                                                    </td>
                                                                    <td class="text-danger"> <?= @$record['error'] ?> </td>
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
                                                                                <!-- <a class="dropdown-item" href="client_error_log?id=${record.id}"> <i class="fa fa-eye"></i> View/Update Client </a> -->

                                                                                <a class="dropdown-item" href="update_importer_balance.php?id=<?= encrypt_data(@$record['id']) ?>"> <i class="fa fa-edit"></i> Update A/C Balance </a>

                                                                                <?php if (!@$record['is_imported']) { ?>

                                                                                    <a class="dropdown-item" href="import_data_importer_edit_client?id=<?= encrypt_data(@$record['id']) ?>"> <i class="fa fa-edit"></i> Edit Client </a>

                                                                                    <?php if (@!$record['error']) { ?>
                                                                                        <a class="dropdown-item confirm-action" data-href="import_data_importer_record_db?id=<?= encrypt_data(@$record['id']) ?>&type=client"> <i class="fa fa-share"></i> Export to Main database </a>
                                                                                    <?php } ?>

                                                                                    <a class="dropdown-item text-danger confirm-action" data-href="delete_data_importer_record?id=<?= encrypt_data($record['id']) ?>&type=client"> <i class="fa fa-trash"></i> Delete Record </a>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                <?php } ?>
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

</html>