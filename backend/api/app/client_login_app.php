<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/MobileApp.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new MobileApp($db);


$item->phone_number = isset($_GET['phone']) ? $_GET['phone'] : die();
$item->app_mpin = isset($_GET['mpin']) ? (int)$_GET['mpin'] : die();
$stmt = $item->loginClientApp();



if ($stmt != '') {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "You've logged in successfully !";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $st = 0;
        if ($status == 'PENDING') {
            $st = 0;
        }
        if ($status == 'ACTIVE') {
            $st = 1;
        }
        if ($status == 'INACTIVE') {
            $st = 2;
        }

        $u = array(
            "names" => '' . ' ' . @$firstName . ' ' . @$lastName . @$shared_name,
            "fname" => @$firstName,
            "id" => strval($userId),
            "acc" => @$name,
            "branch_id" => @$branchId,
            "bank_name" => @$item->getBankName2($branchId,'branch'),
            "balance" => number_format((float)$acc_balance, 2, '.', ''),
            "status" => strval($st),
            "phone" => @$primaryCellPhone,
            "mno" => @$membership_no,
            "shares" => number_format((float)$item->getClientShares($userId), 2, '.', ''),
            "share_amount" => number_format((float)$item->getClientShareAmount($userId), 2, '.', ''),

        );

        array_push($userArr['data'], $u);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "401";
    $userArr['message'] = "Invalid Credentials !";

    http_response_code(200);
    echo json_encode($userArr);
}
