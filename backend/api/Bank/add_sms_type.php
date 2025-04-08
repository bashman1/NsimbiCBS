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

$item->name = $data->name;
$item->id = $data->body;
$item->bank = $data->charge;
$item->branch = $data->charge_to;
$item->description = $data->charge_on;
$item->location = $data->uid;
$item->bank_acc = $data->bid;



if ($item->addSMSType()) {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "SMS Type Created successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "SMS Type not Created !";
    http_response_code(200);
    echo json_encode($userArr);
}
