<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
// $item->branchId = $_GET['id'];

    $stmt = $item->getAllLoanFees($_GET['id']);

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
            "fee_id" => $fee_id,
            "rateAmount" => $rateAmount,
            "name" => $name,
            "type" => $type,
            "paymentType" => $paymentType,
            
        );

        array_push($userArr['data'], $u);

    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Fees found !";
    http_response_code(200);
    echo json_encode($userArr);
}
