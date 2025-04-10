<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$stmt = $item->getMemberLoans($_REQUEST['user']);



$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $bt = '';
        $mark = 'grey';
        if ($status == 0) {
            $bt = 'REVIEW';
        } else if ($status == 1) {
            $bt = 'DISBURSEMENT';
        } else if ($status == 2) {
            $bt = 'ON TIME';
        } else if ($status == 3) {
            $bt = 'DUE';
            $mark = 'red';
        } else if ($status == 4) {
            $bt = 'OVERDUE';
            $mark = 'red';
        } else if ($status == 5) {
            $bt = 'CLEARED';
        } else if ($status == 6) {
            $bt = 'DENIED';
            $mark = 'red';
        }



        $u = array(
            "mid" => $account_id,
            "loan_no" => $loan_no,
            "status" => $status,
            "status_mark" => $mark,
            "status_name" => $bt,
            "amount" => number_format($approvedamount ?? 0),
            "current_balance" => number_format($current_balance ?? 0),
            "duration" => $approved_loan_duration,
            "freq" => $repay_cycle_id,

        );
        array_push($userArr, $u);

        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    http_response_code(200);
    echo json_encode($userArr);
}
