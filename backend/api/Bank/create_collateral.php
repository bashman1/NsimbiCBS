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
$item->lno = $data->cat;
$item->createdById = $data->lid;
$item->updatedById = $data->name;
$item->requestedAmount = $data->location;
$item->applicationDate = $data->mv;
$item->disbursedAmount = $data->fv;
$item->interestRate = $data->link;
$item->penaltyInterestRate = $data->rby;
$stmt = $item->createCollateral();

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
    $userArr['message'] = "Collateral not created !";
    http_response_code(200);
    echo json_encode($userArr);
}
