<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$data = json_decode(file_get_contents("php://input"));
// $item->lno = $data->id;
$stmt = $item->getLoanSchedule($data->id);
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
            "schedule_id" => $schedule_id,
            "amount" => $amount,
            "interest" => $interest,
            "principal" => $principal,
            "balance" => $balance,
            "interest_paid" => $interest_paid,
            "principal_paid" => $principal_paid,
            "outstanding_principal" => $outstanding_principal,
            "outstanding_interest" => $outstanding_interest,
            "interest_waivered" => $interest_waivered,
            "date_of_payment" => $date_of_payment,
            "edited_date" => is_null($edited_date) ? $date_of_payment : $edited_date,
            "status" => $status ?? '',
            "pay_time_status" => $performance_status ?? '',
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
    $userArr['message'] = "No schedule found !";
    http_response_code(200);
    echo json_encode($userArr);
}
