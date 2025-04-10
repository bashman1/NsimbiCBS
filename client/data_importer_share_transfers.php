<h3 class="mt-3 mb-2">Import Share Transfers</h3>
<form action="data_importer_share_transfers" id="data_importer_share_transfers" class="mt-1 data-importer-form" method="post" enctype="multipart/form-data">
    <div class="d-flex align-items-center justify-content-between">
        <a href="documents/data_importer_share_transfers.xlsx" class="btn btn-primary light btn-xs mb-1 p-2 flex-grow-4 bd-highlight align-self-start">
            <i class="fas fa-file-excel"></i> Download Excel Template
        </a>

        <div class="ps-2 flex-grow-3 bd-highlight align-self-start">
            <div class="input-group mb-2">
                <span class="input-group-text">Upload Template</span>
                <div class="form-file">
                    <input type="file" name="share_transfers_data_file" class="form-file-input form-control" id="share_transfers_upload">
                </div>
            </div>
        </div>

        <div class="align-self-start hide" id="share_transfers_upload_data_actions">
            <div class="d-grid">
                <button type="submit" id="submit_btn" name="share_transfers_data" class="btn btn-primary btn-sm btn-submit">
                    Import Data
                </button>

                <button type="button" class="btn btn-warning btn-sm mt-1 clear_data_importer_data">
                    Clear Imported Data
                </button>

            </div>
        </div>

    </div>

    <input type="hidden" name="actual_data" class="actual_data">
    <input type="hidden" name="accepted_files" class="accepted_files" value="data_importer_share_transfers">

</form>
<div class="table-responsive">
    <div id="share_transfers_upload_data_section">
    </div>
</div>