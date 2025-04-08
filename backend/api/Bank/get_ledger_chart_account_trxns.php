<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

    $stmt = $item->getAllChartAccountLedgerTrxns($_GET['acc']);


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
            "tid" => $tid,
            "descr" => $description,
            "amount" => $amount,
            "type" => $t_type,
            "pay_method" => $pay_method,
            "cr_acid" => $cr_acid??'',
            "dr_acid" => $dr_acid??'',
            "acid" => $acid??'',
            "bacid" => $bacid??'',
            "date" => $date_created,
            "auth" => $item->getStaffDetails($_authorizedby),
            "branch" => $name,


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
    $userArr['message'] = "No trxns found !";
    http_response_code(200);
    echo json_encode($userArr);
}
