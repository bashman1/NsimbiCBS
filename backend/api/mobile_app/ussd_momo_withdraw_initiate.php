<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

include_once '../../models/YoApi.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$itemn = new Loan($db);


$item = new YoAPI('100009519113', 'oQgV-3S6p-LyY7-uwSO-VLql-s0q9-XokJ-peWJ');
$data = json_decode(file_get_contents("php://input"));


$item->set_nonblocking("TRUE");
$ext_ref = rand(pow(10, 12), pow(10, 13) - 1);
$item->set_external_reference($ext_ref);
// $sql = $item->ac_withdraw_funds($data->phone, $data->amount, $data->reason);

$result = array();
if ($sql["Status"] == "OK") {
    $stcode = $sql['StatusCode'];

    $mes = $stcode == "0"
        ? "Your Transaction has been successfully Initiated."
        : ($stcode == "1"
            ? "Your Transaction is in a pending state, please we shall get back to you after working on it."
            : "Sorry!! Your Transaction has failed, please try again later"
        );

    $result = array(
        "Status"    => $stcode == "0" ? "0" : "2",
        "Tstatus"   => $stcode == "0" ? "SUCCEEDED" : ($stcode == "1" ? "PENDING" : "FAILED"),
        "TreffID"   => isset($sql['TransactionReference']) ? $sql['TransactionReference'] : "",
        "MomoID"    => isset($sql['MNOTransactionReferenceId']) ? $sql['MNOTransactionReferenceId'] : "",
        "ReceiptID" => isset($sql['IssuedReceiptNumber']) ? $sql['IssuedReceiptNumber'] : "",
        "Message"   => $mes,
        "success" => true,
    );

    $stmt = $itemn->InsertMmTrxnLogUssd($data->my_phone, (int)$data->counter, $data->sid, isset($sql['TransactionReference']) ? $sql['TransactionReference'] : "", $data->reason, $stcode == "0" ? "SUCCEEDED" : ($stcode == "1" ? "PENDING" : "FAILED"), $data->amount, $mes, $ext_ref, $data->phone,'WITHDRAW');
} else {
    if ($sql['StatusCode'] == "--") {
        $result = array("Status"  => 1,  "Tstatus" => "FAILED", "Message" => $sql['StatusMessage'], "success" => false);
    } else {
        $result = array(
            "Status"  => 1,
            "Tstatus"   => isset($sql['TransactionStatus']) ? $sql['TransactionStatus'] : "FAILED",
            "Message" => isset($sql['StatusMessage']) ? $sql['StatusMessage'] : "",
            "success" => false,
        );
    }
}

http_response_code(200);
echo json_encode($result);
