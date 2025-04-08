<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$data = json_decode(file_get_contents("php://input"));
$item->createdAt = $data->id;

$stmt = $item->undoLoanDisbursement();



    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = $stmt;
    http_response_code(200);
    echo json_encode($userArr);

