<h3 class="mt-3 mb-2">Import Share Register</h3>
<form action="data_importer_shares_register" id="data_importer_shares_register" class="mt-1 data-importer-form" method="post" enctype="multipart/form-data">
    <div class="d-flex align-items-center justify-content-between">
        <a href="documents/data_importer_share_register.xlsx" class="btn btn-primary light btn-xs mb-1 p-2 flex-grow-4 bd-highlight align-self-start">
            <i class="fas fa-file-excel"></i> Download Excel Template
        </a>

        <div class="ps-2 flex-grow-3 bd-highlight align-self-start">
            <div class="input-group mb-2">
                <span class="input-group-text">Upload Template</span>
                <div class="form-file">
                    <input type="file" name="shares_register_data_file" class="form-file-input form-control" id="shares_register_upload">
                </div>
            </div>
        </div>

        <div class="align-self-start hide" id="shares_register_upload_data_actions">
            <div class="d-grid">
                <button type="submit" id="submit_btn" name="shares_register_data" class="btn btn-primary btn-sm btn-submit">
                    Import Data
                </button>

                <button type="button" class="btn btn-warning btn-sm mt-1 clear_data_importer_data">
                    Clear Imported Data
                </button>
            </div>
        </div>

    </div>

    <input type="hidden" name="actual_data" class="actual_data">

    <input type="hidden" name="accepted_files" class="accepted_files" value="data_importer_share_register">

</form>
<div class="table-responsive">
    <div id="shares_register_upload_data_section">
        <table class="table display" id="shares_register_upload_datatable"></table>
    </div>

</div>