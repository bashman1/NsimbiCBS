<?php
$import_file_name = 'data_importer_client_institutions';
?>
<div class="d-flex align-items-center bd-highlight mt-2">
    <h3 class="mb-2 mt-2">Import Institutions</h3>
    <a href="documents/<?= $import_file_name ?>.xlsx" class="btn btn-primary light btn-xs mb-1 p-2 bd-highlight ms-4">
        <i class="fas fa-file-excel"></i> Download Institutions Excel Template
    </a>
</div>
<hr>

<form action="data_importer_institution_clients" id="data_importer_institution_clients" class="mt-1 data-importer-form" method="post" enctype="multipart/form-data">
    <!-- <div class="d-flex align-items-center justify-content-between">
        <a href="documents/<?= $import_file_name ?>.xlsx" class="btn btn-primary light btn-xs mb-1 p-2 flex-grow-4 bd-highlight align-self-start">
            <i class="fas fa-file-excel"></i> Download Excel Template
        </a>

        <div class="ps-2 flex-grow-3 bd-highlight align-self-start">
            <div class="input-group mb-2">
                <span class="input-group-text">Upload Template</span>
                <div class="form-file">
                    <input type="file" name="institution_clients_data_file" class="form-file-input form-control" id="institution_clients_upload">
                </div>
            </div>
        </div>

        <div class="align-self-start hide" id="institution_clients_upload_data_actions">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="create_transactions_institutions" name="create_transactions" value="1">
                <label for="create_transactions_institutions" class="form-check-label text-danger fw-bold">
                    Create transactions for Account Balances
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" id="submit_btn" name="institution_clients_data" class="btn btn-primary btn-sm btn-submit mt-4">
                    Import Data
                </button>

                <button type="button" class="btn btn-warning btn-sm mt-1 clear_data_importer_data">
                    Clear Imported Data
                </button>
            </div>
        </div>

    </div> -->


    <div class="row">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" name="batch_name" required placeholder=" ">
                <label> Enter Batch Name </label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="input-group mb-2">
                <span class="input-group-text">Upload Template</span>
                <div class="form-file">
                    <input type="file" name="institution_clients_data_file" class="form-file-input form-control" id="institution_clients_upload">
                </div>
            </div>
        </div>

        <div class="col hide" id="institution_clients_upload_data_actions">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="create_transactions_institutions" name="create_transactions" value="1">
                <label for="create_transactions_institutions" class="form-check-label text-danger fw-bold">
                    Create transactions for Account Balances
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" id="submit_btn" name="institution_clients_data" class="btn btn-primary btn-sm btn-submit mt-4">
                    Import Data
                </button>

                <button type="button" class="btn btn-warning btn-sm mt-1 clear_data_importer_data">
                    Clear Imported Data
                </button>
            </div>
        </div>
    </div>

    <input type="hidden" name="actual_data" class="actual_data">

    <input type="hidden" name="accepted_files" class="accepted_files" value="<?= $import_file_name ?>">

</form>
<div class="table-responsive">
    <div id="institution_clients_upload_data_section">
    </div>
</div>




<div class="row">
    <div class="col-12">
        <div class="card p-0">
            <div class="card-header">
                <h4 class="mt-0 header-title">
                    Durrent Batches
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table display dataTable" id="instututions_batches_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Naration</th>
                                <th>Import Date</th>
                                <th>Imported By</th>
                                <th>Number of Institutions</th>
                                <th> Exported To Main </th>
                                <th>Pending</th>
                                <th>Failed</th>
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