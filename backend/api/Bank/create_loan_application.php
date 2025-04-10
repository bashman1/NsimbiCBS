<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';
include_once '../../models/Transaction.php';

include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$item3 = new Transaction($db);

$item4 = new User($db);

$data = json_decode(file_get_contents("php://input"));

$item->name = $data->client;
$item->bank = $data->product;
$item->branch = $data->disbursedate;
$item->description = $data->startdate;
$item->createdAt = $data->amount;
$item->updatedAt = $data->duration;
$item->deletedAt = $data->notes;
$item->countryCode = $data->bank;
$item->serialNumber = $data->branch;
$item->pv = $data->user;
$item->sv = $data->freq;

$stmt = $item->createLoanApplication();

if ($stmt > 0) {


    // check for account opening sms subscription and sms balance , then send sms
    $smstype = $item4->getBankSMStypeStatus($data->branch, 'loan_apply');

    if ($smstype != 0 && $smstype['s_status'] == 1) {


        //  start on account_opening sms sending process

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

            $loan_data = $item->getLoanClientDetails($stmt);

            $clientDetails  = array(
                "fname" => $loan_data['firstName']
                    . $loan_data['shared_name'],
                "lname" => $loan_data['lastName'],
                "branch" => $data->branch,
                "othername" => '',
                "phone" => $loan_data['primaryCellPhone'],
                "acno" => $loan_data['membership_no'],
                "lpname" => $loan_data['type_name'],
                "amount" => $loan_data['requestedamount'],
            );

            $sms = $item4->decryptSMS($smstype['temp_body'], 'loan_apply', $clientDetails);

            // check if client is individual or group , institution

            if ($loan_data['client_type'] == 'individual') {
                // check if phone number has country code or not --use 256 by default
                if ($loan_data['primaryCellPhone'][0] == "0" || $loan_data['primaryCellPhone'][0] == 0 || $loan_data['primaryCellPhone'][0] == "7") {
                    if ($loan_data['primaryCellPhone'][0] == "0" || $loan_data['primaryCellPhone'][0] == 0) {
                        $loan_data['primaryCellPhone'] = '256' . substr($loan_data['primaryCellPhone'], 1);
                    } else {
                        $loan_data['primaryCellPhone'] = '256' .  $loan_data['primaryCellPhone'];
                    }
                }
                // send sms
                $res =  $item3->SendSMS($senderid, $loan_data['primaryCellPhone'], $sms);
            } else {
                if ($loan_data['sms_phone_numbers'] && count($loan_data['sms_phone_numbers']) >0) {
                    foreach ($loan_data['sms_phone_numbers'] as $value) {
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

                    $sms_price = $sms_price * count($loan_data['sms_phone_numbers']);
                    $smstype['charge'] = (int)$smstype['charge'] * count($loan_data['sms_phone_numbers']);
                }
            }


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
                        'Loan Application SMS Charge',
                        $data->uid ?? 0,
                        $data->client,
                        $data->client,
                        $data->client,
                        $data->client ?? 0,
                        $data->client ?? 0,
                        $data->branch,
                        1,
                        $acid,
                        'saving',
                        1
                    );
                }

                if ($loan_data['sms_phone_numbers'] && $loan_data['client_type'] != 'individual') {
                    foreach ($loan_data['sms_phone_numbers'] as $value) {
                        $my_charge =    $smstype['charge'] / count($loan_data['sms_phone_numbers']);
                        // insert into sms_outbox for record purposes  with status sent
                        $item3->insertSMSOutBox($value, $sms, $senderid, (int)$data->client ?? 0, (int)$my_charge, 'sent', 1, $data->branch, 'Loan Application SMS');
                    }
                } else {
                    $item3->insertSMSOutBox($loan_data['primaryCellPhone'], $sms, $senderid, (int)$data->client ?? 0, (int)$smstype['charge'], 'sent', 1, $data->branch, 'Loan Application SMS');
                }
            } else {


                if ($loan_data['sms_phone_numbers'] && $loan_data['client_type'] != 'individual') {
                    foreach ($loan_data['sms_phone_numbers'] as $value) {
                        $my_charge =    $smstype['charge'] / count($loan_data['sms_phone_numbers']);
                        // insert into sms_outbox for record purposes  with not sent status
                        $item3->insertSMSOutBox($value, $sms, $senderid, (int)$data->client ?? 0, (int)$my_charge, 'failed', 1, $data->branch, $res);
                    }
                } else {
                    // insert into sms_outbox for record purposes  with not sent status
                    $item3->insertSMSOutBox($loan_data['primaryCellPhone'], $sms, $senderid, (int)$data->client ?? 0, (int)$smstype['charge'], 'failed', 1, $data->branch, $res);
                }
            }
        }
    }



    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['id'] = $stmt;
    $userArr['message'] = "Loan Application created successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Loan Application not created !";
    http_response_code(200);
    echo json_encode($userArr);
}
