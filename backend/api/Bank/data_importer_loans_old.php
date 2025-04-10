<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../models/Loan.php';
require_once '../../models/Bank.php';
require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
$records = json_decode($data['actual_data'], true);
$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $importer = new DataImporter($db);
    $importer->records = $records;
    $importer->auth_id = @$data['auth_id'];
    $importer->bank_id = @$data['bank_id'];
    $importer->batch_name = @$data['batch_name'];
    $importer->importLoans();

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
