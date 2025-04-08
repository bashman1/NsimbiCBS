<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../models/User.php';
// require_once '../../models/DataImporter.php';
require_once '../../models/DataImporterClient.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
$records = json_decode($data['actual_data'], true);
$ApiResponser = new ApiResponser();

try {
    $importer = new DataImporterClient();
    $importer->create_transactions = @$data['create_transactions'];
    $importer->records = $records;
    $importer->batch_name = @$data['batch_name'];
    $importer->client_type = @$data['client_type'] ?? 'individual';
    $importer->auth_id = @$data['auth_id'];
    $importer->bank_id = @$data['bank_id'];
    $results = $importer->importClientsBatch();
    echo $ApiResponser::SuccessMessage($results);
    // echo $ApiResponser::SuccessMessage($records);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
