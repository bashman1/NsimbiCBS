<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/DbHandler.php';
include_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';
$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();

    $db_handler = new DbHandler();
    $importer = new DataImporter($db_handler);

    $importer->branch_id = $_REQUEST['branch'];
    $importer->bank_id = $_REQUEST['bank_id'];
    $importer->client_type = $_REQUEST['client_type'];

    $results = $importer->getClientsErrorLogs();

    echo $ApiResponser::SuccessResponse($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
