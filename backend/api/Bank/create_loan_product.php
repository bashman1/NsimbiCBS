<?php
require_once __DIR__ . '../../RequestHeaders.php';

$database = new Database();
$db = $database->connect();

$item = new Bank($db);

$data = json_decode(file_get_contents("php://input"));

$item->name = $data->name;
$item->bank = $data->intrate;
$item->branch = $data->freq;
$item->description = $data->interestMethod;
$item->charge_penalty = $data->penalty;
$item->updatedAt = $data->fee;
$item->deletedAt = $data->prate;
$item->countryCode = $data->pfamount;
$item->serialNumber = $data->gracedays;
$item->identificationNumber = $data->maxdays;
$item->pv = $data->bank;
$item->auto_repay = $data->auto_repay;
$item->auto_penalty = $data->auto_penalty;
$item->round_off = $data->round_off;
$item->gracetype = $data->gracetype;
$item->penaltybased = $data->penaltybased;


$item->createdAt = $data->penalty_id;
$item->acid = $data->interest_id;
$item->pid = $data->parent_id;

$item->account_id = $data->account_id;
$item->int_id = $data->int_id;
$item->p_id = $data->p_id;
$item->check_st = $data->pform;


if ($item->createLoanProduct()) {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "Loan Product created successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Loan Product not created !";
    http_response_code(200);
    echo json_encode($userArr);
}
