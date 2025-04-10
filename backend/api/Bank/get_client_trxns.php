<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$transaction = new Loan($db);
$start_date = '';
$end_date = '';
if ($_GET['range'] == 'today') {
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d');
}

if ($_GET['range'] == 'week') {
    $start_date = date('Y-m-d', strtotime('-7 days'));
    $end_date = date('Y-m-d');
}

if ($_GET['range'] == 'month') {
    $start_date = date('Y-m-d', strtotime('-30 days'));
    $end_date = date('Y-m-d');
}
$stmt = $transaction->getClientPortalTrxns($_GET['uid'], $start_date, $end_date);


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
    $debit = 0;
    $credit = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        if (
            $t_type == "W" or $t_type == "LE" or $t_type == "C" or $t_type == "CW" or $t_type == "CS" or $t_type == "SMS" or
            $t_type == "LP" or
            $t_type == "RC" or   $t_type == "I" or $t_type == 'R' or $t_type == 'L'
        ) {
            $debit = '-';
            $credit = number_format($amount);
        }

        if ($t_type == "D" or $t_type == "A" or $t_type == "LC" or   $t_type == "E") {
            $debit = number_format($amount);
            $credit = '-';
        }


        $u = array(
            "descrip" => $description,
            "pmethod" => $pay_method,
            "status" => $_status  == 1 ? '<span class="text-success fs-16 font-w500 text-end d-block">successful</span>' : '<span class="text-danger fs-16 font-w500 text-end d-block">pending</span>',
            "tid" => $tid,
            "trxn_date" => $date_created,
            "dr_amount" => $debit,
            "cr_amount" => $credit


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
    $userArr['message'] = "No trxns found !";
    http_response_code(200);
    echo json_encode($userArr);
}
