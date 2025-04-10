<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../models/User.php';
require_once '../../models/DataImporterClient.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
$ApiResponser = new ApiResponser();

try {
    $importer = new DataImporterClient();
    $importer->request = $data;
    $importer->auth_id = @$data['auth_id'];
    $importer->bank_id = @$data['bank_id'];
    $results = $importer->adjustImportedAccBal(@$data['client_id'], @$data['orig_bal'], @$data['new_bal']);
    echo $ApiResponser::SuccessMessage($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
