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

$item->bank = $data->bid;
$item->name = $data->st;
$item->location = 'main';



if ($item->updateBankSMSStatusbyCBSAdmin()) {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "Bank SMS Activated successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Bank SMS not activated !";
    http_response_code(200);
    echo json_encode($userArr);
}
