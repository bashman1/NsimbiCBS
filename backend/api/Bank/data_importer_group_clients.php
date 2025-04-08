<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../models/User.php';
require_once '../../models/DataImporterClient.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
$records = json_decode($data['actual_data'], true);
$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $importer = new DataImporterClient($db);
    $importer->create_transactions = @$data['create_transactions'];
    $importer->batch_name = @$data['batch_name'];
    $importer->client_type = @$data['client_type'] ?? 'group';
    $importer->records = $records;
    $importer->auth_id = @$data['auth_id'];
    $importer->bank_id = @$data['bank_id'];
    $results = $importer->importClientsBatch();

    if ($results === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
