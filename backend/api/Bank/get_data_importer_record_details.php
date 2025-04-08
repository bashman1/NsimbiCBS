<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/DbHandler.php';
include_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';


$data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();

    $db_handler = new DbHandler();
    $importer = new DataImporter($db_handler);
    $importer->request = $data;
    $results = $importer->getSingleRecord();
    echo $ApiResponser::SuccessResponse($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
