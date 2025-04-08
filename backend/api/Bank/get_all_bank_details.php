<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$stmt = $item->getAllBankDetails($_GET['id']);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "id" => $id,
            "fin_month" => $fin_year_month,
            "fin_day" => $fin_year_day,
            "name" => $name,
            "location" => $location,
            "trade_name" => $trade_name,
            "email" => $bankmail,
            "logo" => $logo,
            "contacts" => $bankcontacts,
            "charges_membership_fee" => $charges_membership_fee,
            "membership_fee_chanel" => $membership_fee_chanel,
            "membership_fee_required" => $membership_fee_required,
        );

        array_push($userArr['data'], $u);
        $count++;
    }

    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Bank Info found !";
    http_response_code(200);
    echo json_encode($userArr);
}
