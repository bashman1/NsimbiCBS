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
    $importer->request = $data;
    $results = $importer->importSingleRecord();

    if ($results === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }

    echo $ApiResponser::ErrorResponse($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
