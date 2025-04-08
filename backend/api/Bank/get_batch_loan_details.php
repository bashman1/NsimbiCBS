<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../config/database.php';
require_once '../../models/Loan.php';
require_once '../../models/Bank.php';
require_once '../../models/DataImporter.php';
require_once '../../models/DataImporterLoanBatch.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
// $records = json_decode($data['actual_data'], true);

$ApiResponser = new ApiResponser();

try {
    // $handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();
    $importer = new DataImporterLoanBatch($db);
    $importer->loan_id = @$data['loan_id'];
    $batch = $importer->getBatchLoanDetails();
    echo $ApiResponser::SuccessResponse($batch);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
