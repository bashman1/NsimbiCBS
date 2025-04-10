<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
if ($_GET['id'] != '') {
    $item->id = $_GET['id'];
    $stmt = $item->getDebtorsDetails();
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
            "id" => $deb_id,
            "name" => $deb_name,
            "chart" => $cname,
            "chart_id" => $deb_chart_acc,
            "branch" => $bname,
            "date_created" => $datecreated,
            "receivable" => number_format($tot_receivable),
            "paid" => number_format($tot_paid),
            "oustanding" => number_format($tot_bal),



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
    $userArr['message'] = "No Debtor found !";
    http_response_code(200);
    echo json_encode($userArr);
}
