<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;
$ApiResponser = new ApiResponser();

try {
    $handler = new DbHandler();
    $importer = new DataImporter($handler);
    $importer->record_id = @$data['record_id'];
    $importer->importer_type = @$data['importer_type'];
    $importer->deleteBatchRecord();
    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
