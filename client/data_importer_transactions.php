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
$title = 'TRXNS IMPORTER';
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

                                <a href="export_report.php?exportFile=export_data_importer_resources&useFile=1" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Data Importer Resources
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">

                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a href="#transactions" data-bs-toggle="tab" class="nav-link  active">Transactions</a>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div id="transactions" class="tab-pane fade active show">
                                                    <h3 class="mt-3 mb-2">Import Transactions</h3>
                                                    <form action="data_importer_transactions" id="data_importer_transactions" class="mt-1 data-importer-form" method="post" enctype="multipart/form-data">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <a href="documents/data_importer_transactions.xlsx" class="btn btn-primary light btn-xs mb-1 p-2 flex-grow-4 bd-highlight align-self-start">
                                                                <i class="fas fa-file-excel"></i> Download Excel Template
                                                            </a>

                                                            <div class="ps-2 flex-grow-3 bd-highlight align-self-start">
                                                                <div class="input-group mb-2">
                                                                    <span class="input-group-text">Upload Template</span>
                                                                    <div class="form-file">
                                                                        <input type="file" name="transactions_data_file" class="form-file-input form-control" id="transactions_upload">
                                                                    </div>
                                                                </div>

                                                                <div class="text-danger">
                                                                    Note: You can only import Deposits, Withdraws and Transaction charges
                                                                </div>

                                                            </div>

                                                            <div class="align-self-start hide" id="transactions_upload_data_actions">
                                                                <div class="d-grid">
                                                                    <button type="submit" id="submit_btn" name="transactions_data" class="btn btn-primary btn-sm btn-submit">
                                                                        Import Data
                                                                    </button>

                                                                    <button type="button" class="btn btn-warning btn-sm mt-1 clear_data_importer_data">
                                                                        Clear Imported Data
                                                                    </button>

                                                                </div>
                                                            </div>

                                                        </div>

                                                        <input type="hidden" name="actual_data" class="actual_data">

                                                        <input type="hidden" name="accepted_files" class="accepted_files" value="data_importer_transactions">

                                                    </form>
                                                    <div class="table-responsive">
                                                        <div id="transactions_upload_data_section">
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


            </div>
            <?php include('includes/footer.php'); ?>


        </div>
        <?php include('includes/bottom_scripts.php'); ?>
</body>

</html>