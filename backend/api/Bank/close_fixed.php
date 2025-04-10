<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/FieldAgents.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new FieldAgents($db);
$item->auth_id = $_GET['id'];

$stmt = $item->undoFixedClosure($_GET['id']);


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
    $userArr['message'] = "No Trxn found !";
    http_response_code(200);
    echo json_encode($userArr);
}
