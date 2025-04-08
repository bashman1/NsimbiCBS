<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/functions.php';
include_once '../../models/FieldAgents.php';
include_once '../../models/Transaction.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();



$item = new FieldAgents($db);
$item3 = new Transaction($db);
$item2 = new User($db);

$data = json_decode(file_get_contents("php://input"));

$item->account_id = $data->client;
$item->details = 'Deposit Via Agent' . ' ' . ($data->comment ?? '');
$item->acc_name = $data->acc_name;
$item->branch_id = $data->branch;
$item->user_id = $data->authorized;
$item->cash_acc = $data->cash_acc ?? '';
$item->actby = $data->depositor_name;
$item->actbyphone = $data->depositor_phone;
$item->pay_method = $data->loan < 0 ? 0 : $data->loan;
$item->amount = $data->amount < 0 ? 0 : $data->amount;





$stmt = $item->createDepositAgent();

$sms_consent = $item3->getClientSMSConsent($data->client);
if ($sms_consent) {
    // check for sms and send deposit sms
    // check for deposit sms subscription and sms balance , then send sms
    $smstype = $item2->getBankSMStypeStatus($data->branch, 'on_deposit_agent');

    if ($smstype != 0 && $smstype['s_status'] == 1) {


        //  start on on_deposit sms sending process

        $sms_price = 0;
        $senderid = '';
        // check sacco branch sms balance first 
        $sms_bal = $item3->checkBranchSMSBalance($data->branch);
        $prices = $item3->checkBankSMSPrice($data->branch);

        // check for senderid used , and get sms price
        $senderid = $item3->getBranchSenderid($data->branch);
        if ($senderid != '') {

            $sms_price = $prices['sms_sender_id_price'];
        } else {
            $sms_price = $prices['sms_price'];
        }
        if ($sms_bal > $sms_price || $sms_bal == $sms_price) {

            // fill temp_body tags with the right info
            if ($smstype['charge'] > 0 && !is_null($data->client) && $smstype['charged_to'] == 'client') {
                $added_sms_charge = $smstype['charge'];
            } else {
                $added_sms_charge = 0;
            }
            $trxnDetails  = array(
                "amount" => $data->amount ?? 0,
                "wallet" => $data->loan ?? 0,
                "method" => 'cash',
                "branch" => $data->branch,
                "date" => now(),
                "id" => $data->client,
                "charge" => $added_sms_charge,
            );

            $sms = $item2->decryptSMS($smstype['temp_body'], 'on_deposit_agent', $trxnDetails);

            // get client's primary phone number
            $phone = $item3->getClientPhone($data->client, $data->depositor_phone);

            if ($phone && !is_null($phone)) {
                /* phone number array hold numbers , iterate & send to each number */

                foreach ($phone as $value) {
                    // check if phone number has country code or not --use 256 by default
                    if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
                        if ($value[0] == "0" || $value[0] == 0) {
                            $value = '256' . substr($value, 1);
                        } else {
                            $value = '256' . $value;
                        }
                    }
                    // send sms
                    $res =  $item3->SendSMS($senderid, $value, $sms);
                }

                $sms_price = $sms_price * count($phone);

                $smstype['charge'] = $smstype['charge'] * count($phone);

                // check if sms sent successfully or not
                // if success, then do the steps down , if false , then just insert into sms_outbox
                if ($res = 'OK') {

                    if ($sms_price > 0) {
                        // offset from sacco branch balance
                        $item3->chargeBranchSMS($sms_price, $data->branch);
                    }
                    if ($smstype['charge'] > 0 && !is_null($data->client) && $smstype['charged_to'] == 'client') {
                        // offset from client account (if charge >0 ) 
                        $item3->chargeClientSMS($data->client, $smstype['charge']);

                        // get the chart account id , then create trxn in table transactions --- t_type = SMS
                        $acid =  $item3->getBranchSMSChargesAcc($data->branch);

                        // engange the create sms trxn method
                        $item3->createSMSChargeTrxn(
                            $smstype['charge'],
                            0,
                            'SMS',
                            'Deposit SMS Charge',
                            $data->authorized ?? 0,
                            $phone[0],
                            $phone[0],
                            $phone[0],
                            $data->client ?? 0,
                            $data->authorized ?? 0,
                            $data->branch,
                            1,
                            $acid,
                            'saving',
                            1
                        );
                    }
                    // get charge per sms
                    $my_charge = $smstype['charge'] / count($phone);
                    foreach ($phone as $value) {

                        // insert into sms_outbox for record purposes  with status sent
                        $item3->insertSMSOutBox($value, $sms, $senderid, (int)$data->client ?? 0, (int)$my_charge, 'sent', 1, $data->branch, 'Deposit Trxn SMS');
                    }
                } else {
                    // get charge per sms
                    $my_charge = $smstype['charge'] / count($phone);
                    foreach ($phone as $value) {
                        // insert into sms_outbox for record purposes  with not sent status
                        $item3->insertSMSOutBox($value, $sms, $senderid, (int)$data->client ?? 0, (int)$my_charge, 'failed', 1, $data->branch, $res);
                    }
                }
            } else {
                // no phone number found
                /* no sms sending & charging */
            }
        }
    }
}



$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["success"] = true;

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
