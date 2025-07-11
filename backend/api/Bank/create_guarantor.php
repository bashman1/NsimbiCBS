<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$data = json_decode(file_get_contents("php://input"));
$item->lno = $data->mid;
$item->createdById = $data->lid;
$item->updatedById = $data->non_member;
$item->denialReason = $data->is_client;
$item->interestRate = $data->attach;
$stmt = $item->createGuarantor();

if ($stmt) {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";

    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Guarantor not created !";
    http_response_code(200);
    echo json_encode($userArr);
}
