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
$item->name = $data->id;

$item->bank = $data->bank;
$item->branch = $data->branch;
$item->auth_id = $data->uid;

if ($item->deleteBranch()) {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "Branch Deleted successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Branch not Deleted !";
    http_response_code(200);
    echo json_encode($userArr);
}
