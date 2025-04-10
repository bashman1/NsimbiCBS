<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$data = json_decode(file_get_contents("php://input"));

$item->bank = $data->bank;


$stmt =  $item->getBankSMSWalletDetails();
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
            "no_sender_id_cost" => $sms_price,
            "sender_id_cost" => $sms_sender_id_price,
            "sender_id" => $item->getBankSenderIds($item->bank),
            "status" => $sms_sub_status==0? '<a class="ajax_delete" href="subscribe_bank_sms?t=' . $item->bank . '&st=1&type=main"> <i class="fa fa-toggle-off" style="color:red;font-size: 20px;"></i> </a>': '<a class="ajax_delete" href="subscribe_bank_sms?t=' . $item->bank . '&st=0&type=main"> <i class="fa fa-toggle-on" style="color:#0ad40a;font-size: 20px;"></i> </a>',
            "m_status" => $manual_sms_status == 0 ? '<a class="ajax_delete" href="subscribe_bank_sms?t=' . $item->bank . '&st=1&type=manual"> <i class="fa fa-toggle-off" style="color:red;font-size: 20px;"></i> </a>' : '<a class="ajax_delete" href="subscribe_bank_sms?t=' . $item->bank . '&st=0&type=manual"> <i class="fa fa-toggle-on" style="color:#0ad40a;font-size: 20px;"></i> </a>',
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
    $userArr['message'] = "No accounts  found !";
    http_response_code(200);
    echo json_encode($userArr);
}
