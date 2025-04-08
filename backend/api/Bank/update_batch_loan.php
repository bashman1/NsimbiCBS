<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../config/functions.php';
// require_once '../../config/database.php';
require_once '../../models/DataImporter.php';
require_once '../../models/DataImporterLoanBatch.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
// $records = json_decode($data['actual_data'], true);

$ApiResponser = new ApiResponser();

try {
    $handler = new DbHandler();
    $importer = new DataImporterLoanBatch($handler);
    $importer->data = @$data;
    $importer->data['disbursement_date'] = db_date_format(@$data['disbursement_date'], false);
    $importer->data['next_due_date'] = db_date_format(@$data['next_due_date'], false);
    $importer->data['loan_amount'] = amount_to_integer(@$data['loan_amount']);
    $importer->data['principal_balance'] = amount_to_integer(@$data['principal_balance']);
    $importer->data['amount_paid'] = amount_to_integer(@$data['amount_paid']);
    $importer->data['interest_balance'] = amount_to_integer(@$data['interest_balance']);
    $importer->data['principal_arrears'] = amount_to_integer(@$data['principal_arrears']);
    $importer->data['interest_arrears'] = amount_to_integer(@$data['interest_arrears']);
    $importer->data['interest_rate'] = amount_to_integer(@$data['interest_rate']);

    $importer->updateBatchLoan();
    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
