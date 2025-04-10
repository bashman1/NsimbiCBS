<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);

$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

$fd_id = $_GET['id'];
// start calculation

$amount = str_replace(",", "", $_GET['amount']);
$interest_rate = $_GET['interest'];
$period = $_GET['period'];
$period_type = $_GET['period_type'];
$frequency = $_GET['frequency'];

$interest = 0;
$freq_days = 1;
$total_period_interest = 0;
$inst_interest = 0;
$no_times = 0;


if ($period_type == 'y') {
    $period_no_days = $period * 360;
    $daily_rate  = $interest_rate / 36000;
    $daily_interest = $amount * $daily_rate;
    $total_period_interest = round($daily_interest * $period_no_days);

    if ($frequency == 12) {
        $freq_days = 30;
    } else  if ($frequency == 4) {
        $freq_days = 90;
    } else  if ($frequency == 2) {
        $freq_days = 180;
    } else  if ($frequency == 1) {
        $freq_days = 360;
    }

    $no_times = round($period_no_days / $freq_days);

    $inst_interest = round($total_period_interest / $no_times);
} else if ($period_type == 'm') {
    $period_no_days = $period * 30;
    $daily_rate  = $interest_rate / 36000;
    $daily_interest = $amount * $daily_rate;
    $total_period_interest = round($daily_interest * $period_no_days);

    if ($frequency == 12) {
        $freq_days = 30;
    } else  if ($frequency == 4) {
        $freq_days = 90;
    } else  if ($frequency == 2) {
        $freq_days = 180;
    } else  if ($frequency == 1) {
        $freq_days = 360;
    }

    $no_times = round($period_no_days / $freq_days);

    $inst_interest = round($total_period_interest / $no_times);
} else if ($period_type == 'd') {
    $period_no_days = $period;
    $daily_rate  = $interest_rate / 36000;
    $daily_interest = $amount * $daily_rate;
    $total_period_interest = round($daily_interest * $period_no_days);

    if ($frequency == 12) {
        $freq_days = 30;
    } else  if ($frequency == 4) {
        $freq_days = 90;
    } else  if ($frequency == 2) {
        $freq_days = 180;
    } else  if ($frequency == 1) {
        $freq_days = 360;
    }

    $no_times = round($period_no_days / $freq_days);

    $inst_interest = round($total_period_interest / $no_times);
}


// end calculation

$u = array(
    "id" => $fd_id,
    "amount" => $amount,
    "interest" => $total_period_interest,
    "inst_interest" => $inst_interest,
    "no_times" => $no_times,
    

);



array_push($userArr['data'], $u);


http_response_code(200);
echo json_encode($userArr);
