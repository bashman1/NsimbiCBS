<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);



$stmt =  $item->getTrxnDetails($_GET['id']);


$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $u = array(
        "tid" => $tid,
        "acid" => $pay_method == 'cash' ? $cash_acc : $bacid,
        "said" => $acid,
        "method" => $pay_method,
        "amount" => $amount,
        "trn_date" =>  date('Y-m-d', strtotime($date_created)),
        "notes" => $description,
        "branch" => $_branch,
        "deposited_by" => $acc_name,


    );


    array_push($userArr['data'], $u);
    // array_push($userArr['sub'], $u2);
    // $count++;
}
http_response_code(200);
echo json_encode($userArr);
