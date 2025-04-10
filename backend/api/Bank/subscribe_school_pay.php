<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';
include_once '../../models/Transaction.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);

$items = new Transaction($db);
$item2 = new User($db);

$item->branchId = $_GET['id'];




if ($item->subscribeSchoolPay()) {

    $c_data =  $item2->getClientDetails2($_GET['id']);


    // check for sms and send deposit sms
    // check for deposit sms subscription and sms balance , then send sms
    $smstype = $item2->getBankSMStypeStatus($c_data['branchId'], 'on_subscribe_school_pay');

    if ($smstype != 0 && $smstype['s_status'] == 1) {


        //  start on on_deposit sms sending process

        $sms_price = 0;
        $senderid = '';
        // check sacco branch sms balance first 
        $sms_bal = $items->checkBranchSMSBalance($c_data['branchId']);
        $prices = $items->checkBankSMSPrice($c_data['branchId']);

        // check for senderid used , and get sms price
        $senderid = $items->getBranchSenderid($c_data['branchId']);
        if ($senderid != '') {

            $sms_price = $prices['sms_sender_id_price'];
        } else {
            $sms_price = $prices['sms_price'];
        }
        if ($sms_bal > $sms_price || $sms_bal == $sms_price) {

            // fill temp_body tags with the right info
            if ($smstype['charge'] > 0 && !is_null($c_data['userId']) && $smstype['charged_to'] == 'client') {
                $added_sms_charge = $smstype['charge'];
            } else {
                $added_sms_charge = 0;
            }
            $trxnDetails  = array(
                "name" => $c_data['shared_name'],
                "id" => $c_data['userId'],
                "charge" => $added_sms_charge,
            );

            $sms = $item2->decryptSMS($smstype['temp_body'], 'on_subscribe_school_pay', $trxnDetails);

            // get client's primary phone number
            $phone = $items->getClientPhone($c_data['userId']);

            if ($phone) {
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
                    $res =  $items->SendSMS($senderid, $value, $sms);
                }

                $sms_price = $sms_price * count($phone);

                $smstype['charge'] = $smstype['charge'] * count($phone);

                // check if sms sent successfully or not
                // if success, then do the steps down , if false , then just insert into sms_outbox
                if ($res = 'OK') {

                    if ($sms_price > 0) {
                        // offset from sacco branch balance
                        $items->chargeBranchSMS($sms_price, $c_data['branchId']);
                    }
                    if ($smstype['charge'] > 0 && !is_null($c_data['userId']) && $smstype['charged_to'] == 'client') {
                        // offset from client account (if charge >0 ) 
                        $items->chargeClientSMS($c_data['userId'], $smstype['charge']);

                        // get the chart account id , then create trxn in table transactions --- t_type = SMS
                        $acid =  $items->getBranchSMSChargesAcc($c_data['branchId']);

                        // engange the create sms trxn method
                        $items->createSMSChargeTrxn(
                            $smstype['charge'],
                            0,
                            'SMS',
                            'School Pay Subscription SMS Charge',
                            0,
                            $phone[0],
                            $phone[0],
                            $phone[0],
                            $c_data['userId'] ?? 0,
                            0,
                            $c_data['branchId'],
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
                        $items->insertSMSOutBox($value, $sms, $senderid, (int)$c_data['userId'] ?? 0, (int)$my_charge, 'sent', 1, $c_data['branchId'], 'School Pay Subscription Trxn SMS');
                    }
                } else {
                    // get charge per sms
                    $my_charge = $smstype['charge'] / count($phone);
                    foreach ($phone as $value) {
                        // insert into sms_outbox for record purposes  with not sent status
                        $items->insertSMSOutBox($value, $sms, $senderid, (int)$c_data['userId'] ?? 0, (int)$my_charge, 'failed', 1, $c_data['branchId'], $res);
                    }
                }
            } else {
                // no phone number found
                /* no sms sending & charging */
            }
        }
    }

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";


    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Schools found !";
    http_response_code(200);
    echo json_encode($userArr);
}
