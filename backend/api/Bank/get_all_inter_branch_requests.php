<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$transaction = new Loan($db);


if ($_GET['bank'] != '') {
    $transaction->bankId = $_GET['bank'];
    $stmt = $transaction->getAllBankBranchRequests();
} else {
    $transaction->branchId = $_GET['branch'];
    $stmt = $transaction->getAllBranchRequests();
}

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
            "id" => $req_id,
            "amount" => $req_amount,
            "fname" => $transaction->getBranchName($from_branch),
            "tname" => $transaction->getBranchName($to_branch),
            "pmode" => $req_pay_mode,
            "date"=> $date_created,
            "status"=> $req_status,


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
    $userArr['message'] = "No Requisitions found !";
    http_response_code(200);
    echo json_encode($userArr);
}
