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
$item->createdAt = $data->id;
$item->updatedAt = $data->freeze;
$item->deletedAt = $data->ddate;
$item->name = $data->sdate;
$item->location = $data->mode;
$item->serialNumber = $data->uid;
$item->amount = $data->amount;
$item->identificationNumber = $data->auth;
$item->contact_person_details = $data->lpid;
$item->auto_repay = $data->auto_pay ?? 0;
$item->cash_acc = $data->cash_acc ?? 0;
$item->bank_acc = $data->bacc ?? 0;
$item->cheque_no = $data->cheque ?? 0;

$stmt = $item->disburseLoan();


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
    $userArr['message'] = "No loan found !";
    http_response_code(200);
    echo json_encode($userArr);
}
