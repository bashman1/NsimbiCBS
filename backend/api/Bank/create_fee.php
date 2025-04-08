<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$data = json_decode(file_get_contents("php://input"));

$item->name = $data->name;
$item->bank = $data->ftype;
$item->branch = $data->ptype;
$item->description = $data->feeamount;
$item->pv = $data->bank;

$item->createdAt = $data->pform;
$item->deletedAt = $data->acid;
$item->updatedAt = $data->pid;



if ($item->createFee()) {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "Fee created successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Fee not created !";
    http_response_code(200);
    echo json_encode($userArr);
}
