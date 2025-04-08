<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$data = json_decode(file_get_contents("php://input"));
$item->lno = $data->id;
$stmt = $item->getLoanDetails();
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
            "id" => $item->lno,
            "client" => $item->getLoanClientDetails($account_id),
            "staff" => $item->getLoanStaffDetails($loan_officer),
            "business" => $item->getLoanBusinessDetails($account_id),
            "loan" => $item->getSingleLoanDetails($loan_no),
            "penalty" => $item->getLoanPenaltyPaid($loan_no),
            "branch" => $item->getBranchName($branchid),
            "product" => $item->getProductDetails($loan_type),
            // "lp_name" => $item->getLoanProductName($loanproductid),
            // "lp_name" => $item->getLoanProductName($loanproductid),
            // "guarantor" => $item->getLoanGuarantors($loan_no),

            // "schedule" =>$item->getLoanSchedule($loan_no) ,




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
    $userArr['message'] = "Loan not found !";
    http_response_code(200);
    echo json_encode($userArr);
}
