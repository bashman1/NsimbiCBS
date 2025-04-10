<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

// general fees ---membership renewal
// $stmt = $item->MembershipRenewal('2024-12-30', 'faaaf847-1e1d-455b-b3d6-995efba6874c',89, 'e6ccfbf0-320e-402b-96b9-a948e6070a86',10000,10165);
// e6ccfbf0-320e-402b-96b9-a948e6070a86  member moyo cid
// 3ea73fef-47ae-4636-9acc-e469b427c0b3  member bongi cid
// faaaf847-1e1d-455b-b3d6-995efba6874c ---obongi --id
// 8fb667c6-b92e-4286-91b0-d31df32e5174 ---moyo id
// interest on savings
$stmt = $item->InterestOnSavings();

// $stmt = $item->sortNow($_GET['lno']);
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
    $userArr['message'] = "No ACTYPE found !";
    http_response_code(200);
    echo json_encode($userArr);
}
