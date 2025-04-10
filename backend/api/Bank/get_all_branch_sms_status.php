<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$item->id = $_GET['bank'];
if ($item->id == '') {
    $stmt = $item->getAllSystemBranchesSMSWallets();
} else {
    $stmt = $item->getAllBankBranchesSMSWallets();
}

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
            "id" => $count,
            "bname" => $item->getBankName($bankId),
            "name" => $name,
            "balance" => number_format($sms_balance??0),
            "income" => number_format($item->getBranchSMSIncome($id)),
            

        );



        array_push($userArr['data'], $u);
        $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Branch found !";
    http_response_code(200);
    echo json_encode($userArr);
}
