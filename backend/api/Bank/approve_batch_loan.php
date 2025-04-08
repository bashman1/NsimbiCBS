<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../config/database.php';
require_once '../../models/Loan.php';
require_once '../../models/AuditTrail.php';
require_once '../../models/Bank.php';
require_once '../../models/DataImporter.php';
require_once '../../models/DataImporterLoanBatch.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
// $records = json_decode($data['actual_data'], true);

$ApiResponser = new ApiResponser();

try {
    $handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();

    $importer = new DataImporterLoanBatch($handler);

    $importer->bank_object = new Bank($db);
    $importer->audit_trail = new AuditTrail($db);

    $importer->loan_id = @$data['loan_id'];
    $importer->auth_id = @$data['auth_id'];
    $results = $importer->approveBatchLoan($data['loan_id']);
    echo $ApiResponser::SuccessMessage($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
