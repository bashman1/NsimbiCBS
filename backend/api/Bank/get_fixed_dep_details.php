<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['id'];
$stmt = $item->getFixedDepDetails();

$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $dtype = '';
        $dtype1 = '';
        if ($duration_type == 'y') {
            $dtype = 'Years';
        } else if ($duration_type == 'm') {
            $dtype = 'Months';
        } else if ($duration_type == 'd') {
            $dtype = 'Days';
        }


        if ($compound_freq == 'y') {
            $dtype1 = 'Annually';
        } else if ($compound_freq == 'm') {
            $dtype1 = 'Monthly';
        } else if ($compound_freq == 'q') {
            $dtype1 = 'Quarterly';
        } else if ($compound_freq == 'h') {
            $dtype1 = 'Half Yearly';
        }

        $u = array(
            "id" => $fd_id,
            "wht" => $wht,
            "user_id" => $user_id,
            "fd_branch" => $fd_branch,
            "int_rate" => $int_rate,
            "fd_notes" => $fd_notes,
            "auto_pay" => $auto_pay,
            "auto_close" => $auto_close,
            "ptype" => $duration_type,
            "freqtype" => $compound_freq,
            "client" => $item->getLoanClientDetails($user_id),
            "int_due" => $fd_int_due??0,
            "tot_int" => 0,
            "wht_due" => $wht_due??0,
            "wht_paid" => $wht_paid??0,
            "int_paid" => $fd_int_paid??0,
            "fd_schedule" => '',
            "last_transaction" => $item->getLastUserTransaction($user_id),
            "amount" => $fd_amount ?? 0,
            "period" => number_format($fd_duration ?? 0) . ' ' . $dtype,
            "per" => $fd_duration ?? 0,
            "freq" => $dtype1,
            "fd_st" => $fd_status,
            "status" => $fd_status == 0 ? '<span class="badge badge-rounded badge-danger">in progress</span>' : '<span class="badge badge-rounded badge-success">Closed</span>',
            "open_date" => $fd_date,
            "close_date" => $fd_maturity_date,
            "closure_date" => $updated_at,

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
    $userArr['message'] = "No trxns found !";
    http_response_code(200);
    echo json_encode($userArr);
}
