<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/MobileApp.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new MobileApp($db);


$item->mid = isset($_GET['id']) ? $_GET['id'] : die();


$stmt = $item->getAllCustomerTransAppLatest();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "_did" => strval($tid),
            "_account_no" => $membership_no,
            "account_name" => $acc_name,
            "_authorisedby" => $firstName . " " . $lastName,
            "_paidby_name" => $_actionby,
            "_paidby_phone" => '',
            "_amount" => strval($amount),
            "_reason" => @$description . ' by '.@$_actionby,
            "_status" => strval($_status),
            "branch_name" => $name,
            "acc_balance" => strval($acc_balance),
            "pending" => '0',
            "_date_created" => $date_created2,
            "address" => $addressLine1,
            "type" => is_null($mm_tid) ? $t_type : 'MM',
            "mdate" => $mdate,
            "role" => $entry_via_role == 'cashier' ? '1' : '2',

        );


        array_push($userArr, $u);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "204";
    $userArr['message'] = "No Deposits found !";

    http_response_code(200);
    echo json_encode($userArr);
}
