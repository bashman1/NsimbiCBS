<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../config/database.php';
require_once '../../models/FieldAgents.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
// $records = json_decode($data['actual_data'], true);

$ApiResponser = new ApiResponser();

try {
    $handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();

    $importers = new FieldAgents($handler);


    $importers->auth_id = @$data['tid'];
    $importers->uid = @$data['uid'];
    $results = $importers->approveTrxn();
    echo $ApiResponser::SuccessMessage($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
