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

$item->branch = $data->branch;


$stmt = $item->getBranchSMSWalletDetails();
$itemCount = $stmt->rowCount();



if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "sms_purchase_count" => $sms_purchase_count,
            "sms_used_count" => $sms_used_count,
            "sms_amount_loaded" => $sms_amount_loaded,
            "sms_amount_spent" => $sms_amount_spent,
            "sms_balance" => $sms_balance,
        );



        array_push($userArr['data'], $u);

        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No accounts  found !";
    http_response_code(200);
    echo json_encode($userArr);
}
