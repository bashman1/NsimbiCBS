<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/DbHandler.php';
require_once '../../models/Loan.php';
require_once '../../models/Bank.php';
// require_once '../../models/DataImporter.php';
require_once '../../models/DataImporterLoanBatch.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
$records = json_decode($data['actual_data'], true);

$ApiResponser = new ApiResponser();

try {
    // $database = new Database();
    // $db = $database->connect();
    $handler = new DbHandler();

    $importer = new DataImporterLoanBatch($handler);
    $importer->records = $records;
    $importer->auth_id = @$data['auth_id'];
    $importer->bank_id = @$data['bank_id'];
    $importer->batch_name = @$data['batch_name'];
    $results = $importer->importLoans();

    // $results = $records;

    if ($results === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
