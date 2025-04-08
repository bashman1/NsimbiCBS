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
$sql = $item->ac_transaction_check_status($data->tid);

$result = array();
if ($sql["Status"] == "OK") {
    $stcode = $sql['StatusCode'];


    $result = array(
        "Status"    => $stcode == "0" ? "0" : "2",
        "Tstatus"   => $stcode == "0" ? "SUCCEEDED" : ($stcode == "1" ? "PENDING" : "FAILED"),
        "StatusMessage"   => isset($sql['StatusMessage']) ? $sql['StatusMessage'] : "",
        "ErrorMessage"    => isset($sql['ErrorMessage']) ? $sql['ErrorMessage'] : "",
        "TransactionReference" => isset($sql['TransactionReference']) ? $sql['TransactionReference'] : "",
        "MNOTransactionReferenceId" => isset($sql['MNOTransactionReferenceId']) ? $sql['MNOTransactionReferenceId'] : "",
        "Amount" => isset($sql['Amount']) ? $sql['Amount'] : "",
        "CurrencyCode" => isset($sql['CurrencyCode']) ? $sql['CurrencyCode'] : "",
        "TransactionInitiationDate" => isset($sql['TransactionInitiationDate']) ? $sql['TransactionInitiationDate'] : "",
        "TransactionCompletionDate" => isset($sql['TransactionCompletionDate']) ? $sql['TransactionCompletionDate'] : "",
        "IssuedReceiptNumber" => isset($sql['IssuedReceiptNumber']) ? $sql['IssuedReceiptNumber'] : "",
        "Message"   => $stcode == "0"
            ? "Your Transaction has successfully worked on."
            : ($stcode == "1"
                ? "Your Transaction is in a pending state, please we shall get back to you after working on it."
                : "Sorry!! Your Transaction has failed, please try again later"
            ),
        "success" => $stcode == "0" ? true : false,
    );

    $stmt = $itemn->updateStatusMmFeesLog($data->tid, $stcode == "0" ? "SUCCEEDED" : ($stcode == "1" ? "PENDING" : "FAILED"));
} else {
    if ($sql['StatusCode'] == "--") {
        $result = array("Status"  => 1,  "Tstatus" => "FAILED", "Message" => $sql['StatusMessage']);
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
