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
    $fee->fee_id = $data->fee_id;
    $result = $fee->fee_id ? $fee->getAccountOpeningFee() : null;
    echo $ApiResponser::SuccessResponse($result);

    // $result = $fee->getBranchSavingsAccountFee('b69be82c-3bda-465c-a58b-8c8fa0ba5e48',7);
    // echo $ApiResponser::SuccessResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
