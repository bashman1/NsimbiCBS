<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Transaction.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Transaction($db);
$item2 = new User($db);

$data = json_decode(file_get_contents("php://input"));


$item->mid = $data->clientid;
$item->amount = $data->charge;
$item->description = $data->sms_text;
$item->_actionby = $data->sms_phone;
$item->_branch = $data->branch;
$item->_authorizedby = $data->user;
$item->send_sms = $data->send_to;
$item->cash_acc = $data->sid;

$sms_price = 0;
$senderid = '';
// check sacco branch sms balance first 
$sms_bal = $item->checkBranchSMSBalance($item->_branch);
$prices = $item->checkBankSMSPrice($item->_branch);

// check for senderid used , and get sms price
if ($item->cash_acc > 0) {
    $sms_price = $prices['sms_sender_id_price'];
    if ($item->cash_acc == 0) {
        $senderid = '';
    } else {
        $senderid = $item->checkBankSenderid($item->cash_acc);
    }
} else {
    $sms_price = $prices['sms_price'];
}
if ($sms_bal > $sms_price) {

    if ($item->send_sms == 'sub') {
        $item->_actionby = $item->getClientPhone($item->mid, '');
    }

    // check if phone number has country code or not --use 256 by default
    if ($item->_actionby[0] == "0" || $item->_actionby[0] == 0 || $item->_actionby[0] == "7") {
        if ($item->_actionby[0] == "0" || $item->_actionby[0] == 0) {
            $item->_actionby = '256' . substr($item->_actionby, 1);
        } else {
            $item->_actionby = '256' . $item->_actionby;
        }
    } else {
        $item->_actionby =
            $item->_actionby[0];
    }
    // send sms
    $res =  $item->SendSMS($senderid, $item->_actionby, $data->sms_text);



    // check if sms sent successfully or not
    // if success, then do the steps down , if false , then just insert into sms_outbox
    if ($res = 'OK') {

        if ($sms_price > 0) {
            // offset from sacco branch balance
            $item->chargeBranchSMS($sms_price, $item->_branch);
        }
        if ($item->amount > 0 && !is_null($item->mid)) {
            // offset from client account (if charge >0 ) 
            $item->chargeClientSMS($item->mid, $item->amount);

            // get the chart account id , then create trxn in table transactions --- t_type = SMS
            $acid =  $item->getBranchSMSChargesAcc($item->_branch);

            $item->createSMSChargeTrxn(
                $item->amount,
                0,
                'SMS',
                'Sms Charge',
                $item->_authorizedby ?? 0,
                $item->_actionby,
                $item->_actionby,
                $item->_actionby,
                $item->mid ?? 0,
                $item->_authorizedby ?? 0,
                $item->_branch,
                1,
                $acid,
                'saving',
                1
            );
        }
        // insert into sms_outbox for record purposes  with status sent
        $item->insertSMSOutBox($item->_actionby, $data->sms_text, $senderid, (int)$item->mid ?? 0, (int)$item->amount, 'sent', 0, $item->_branch, '');

        // create audit trail log
        $audit_info  = array(
            "action" => 'Sent Single SMS to ' . $item->_actionby,
            "log_desc" => 'SMS Body: ' . $data->sms_text,
            "uid" => $item->_authorizedby ?? 0,
            "branch" => $item->_branch,
            "bank" => NULL,
            "ip" => '',
            "status" => 'success',
        );

        // insert into audit trail
        $item2->insertAuditTrail($audit_info);

        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = true;
        $userArr['statusCode'] = "200";
        $userArr['message'] = "SMS sent successfully !";
        http_response_code(200);
        echo json_encode($userArr);
    } else {
        // insert into sms_outbox for record purposes  with not sent status
        $item->insertSMSOutBox($item->_actionby, $data->sms_text, $senderid, (int)$item->mid ?? 0, (int)$item->amount, 'failed', 0, $item->_branch, $res);

        // create audit trail log
        $audit_info  = array(
            "action" => 'Sent Single SMS to ' . $item->_actionby,
            "log_desc" => 'SMS Body: ' . $data->sms_text,
            "uid" => $item->_authorizedby ?? 0,
            "branch" => $item->_branch,
            "bank" => NULL,
            "ip" => '',
            "status" => 'success',
        );

        // insert into audit trail
        $item2->insertAuditTrail($audit_info);


        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = false;
        $userArr['statusCode'] = "400";
        $userArr['message'] = "SMS generated but not sent ! Check SMS Outbox to resend the SMS again";
        http_response_code(200);
        echo json_encode($userArr);
    }
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "SMS not sent !";
    http_response_code(200);
    echo json_encode($userArr);
}
