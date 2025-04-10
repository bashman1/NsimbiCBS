<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../models/Fee.php';
require_once '../ApiResponser.php';


// $data = json_decode(file_get_contents("php://input"));
$data = $_REQUEST;

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $fee = new Fee($db);
    $fee->bankId = $data['bankId'];
    $fee->branchId = $data['branchId'];
    $results = $fee->bankId ? $fee->getAllAccountOpeningFees() : [];
    echo $ApiResponser::SuccessResponse($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
