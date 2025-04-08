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
$item->tname = $data->tname;
$item->auto_chart = $data->auto_chart;
$item->location = $data->location;
$item->contact_person_details = $data->contact;
$item->recommender = $data->refered;
$item->serialNumber = rand(pow(10, 3), pow(10, 4) - 1);
$item->countryCode = 'UG';
$item->lowestCurrencyValue = 100;



if ($item->registerBank()) {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "Bank created successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Bank not created !";
    http_response_code(200);
    echo json_encode($userArr);
}
