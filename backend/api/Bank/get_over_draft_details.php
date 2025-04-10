<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$item->id = $_GET['id'];
$stmt = $item->getOverDraftDetails();


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
            "odid" => $odid,
            "client_name" => $firstName . ' ' . $lastName . ' ' . $shared_name,
            "acno" => $membership_no,
            "acc_balance" => $acc_balance,
            "authby" => $authby,
            "daily_rate" => $daily_rate,
            "branch" => $branch,
            "acc_id_affected" => $acc_id_affected,
            "income_acid" => $income_acid,
            "userId" => $userId,
            "userId" => $userId,
            "amount" => $amount,
            "period" => number_format($duration) . ' ( ' . strtoupper($duration_type) . ' )',
            "princ_balance" => number_format($over_draft),
            "interest" => number_format($interest_accumulated),
            "interest_bal" => number_format($interest_accumulated - $interest_paid),
            "arrears_days" => $arrears_days,
            "penalty_total" => number_format($penalty_accumulated),
            "penalty_balance" => number_format($penalty_accumulated - $penalty_paid),
            "appln_date" => normal_date_short($trxn_date),
            "approval_date" =>  $approval_date,
            "duration" =>  $duration,


            "product" => 'Daily',
            "status" => $ostatus,


        );


        array_push($userArr['data'], $u);
        // $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Over-Drafts found !";
    http_response_code(200);
    echo json_encode($userArr);
}
