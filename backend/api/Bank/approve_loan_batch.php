<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../config/database.php';
require_once '../../models/DataImporter.php';
require_once '../../models/DataImporterLoanBatch.php';
require_once '../../models/AuditTrail.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
// $records = json_decode($data['actual_data'], true);

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();

    $handler = new DbHandler();
    $importer = new DataImporterLoanBatch($handler);

    $importer->bank_object = new Bank($db);
    $importer->audit_trail = new AuditTrail($db);

    if (!@$data['batch_id']) {
        echo $ApiResponser::ErrorResponse("Batch Id is missing from request");
        return;
    }

    if (!@$data['auth_id']) {
        echo $ApiResponser::ErrorResponse("Session user is missing from request");
        return;
    }

    $importer->batch_id = @$data['batch_id'];
    $importer->auth_id = @$data['auth_id'];
    $result = $importer->approveLoanBatch();

    if ($result === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
