<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../models/Fee.php';
require_once '../ApiResponser.php';


$data = json_decode(file_get_contents("php://input"));

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $fee = new Fee($db);
    $fee->bankId = $data->bankId;
    $fee->fee_id = $data->fee_id;
    if ($fee->delete()) {
        echo $ApiResponser::SuccessMessage();
    } else {
        echo $ApiResponser::ErrorResponse("Deletion failed");
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
