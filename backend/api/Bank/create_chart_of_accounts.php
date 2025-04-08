<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../models/User.php';
require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $importer = new DataImporter($db);
    $importer->auth_id = @$data['auth_id'];
    $importer->data = [];
    $importer->data['account_id'] = @$data['account_id'];
    $importer->data['amount'] = @$data['amount'];
    $importer->data['record_date'] = @$data['record_date'];
    $importer->data['notes'] = @$data['notes'];

    $result = $importer->importChartOfAccounts();
    if ($result) {
        echo $ApiResponser::SuccessMessage();
    } else {
        echo $ApiResponser::ErrorResponse();
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
