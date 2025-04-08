<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Transaction.php';

include_once '../../models/User.php';

require_once '../ApiResponser.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();
$ApiResponser = new ApiResponser();

$item = new Transaction($db);

$item2 = new User($db);

$data = json_decode(file_get_contents("php://input"));

$item->mid = $data->client;
$item->amount = $data->amount;
$item->description = $data->reason . ' ( ' . $data->pay_method . ' )';
$item->_actionby = $data->deposited;
$item->_branch = $data->branch;
$item->_authorizedby = $data->user;
$item->date_created = $data->date;
$item->pay_method = $data->pay_method;
$item->bacid = $data->bank_acc;
$item->cheque_no = $data->cheque_no;
$item->cash_acc = $data->cash_acc;
$item->make_charges = $data->make_charges;
$item->is_verified = $data->is_verified ?? 0;

if ($data->send_sms) {
    $item->send_sms  = true;
    // send withdraw sms 
} else {
    $item->send_sms = false;
}
try {
    $result = $item->createWithdraw();
    // insert into the audit trail table -- for creating a client

    // generate and organise audit trail info
    $audit_info  = array(
        "action" => $data->pay_method . '  Withdraw',
        "log_desc" => 'Created New Withdraw for: - ' . $data->client . ' of UGX: ' . number_format($data->amount ?? 0) . ': Record Date: ' . $data->date,
        "uid" => $data->user,
        "branch" => $data->branch,
        "bank" => NULL,
        "ip" => '',
        "status" => $result > 0 ? 'success' : 'failed',
    );

    // insert into audit trail
    $item2->insertAuditTrail($audit_info);


    // check deposit return 
    // if ($result === true) {
    $sms_consent = $item->getClientSMSConsent($data->client);
    if ($sms_consent) {
        // check for sms and send deposit sms
        // check for deposit sms subscription and sms balance , then send sms
        $smstype = $item2->getBankSMStypeStatus($data->branch, 'on_withdraw');

        if ($smstype != 0 && $smstype['s_status'] == 1) {


            //  start on on_deposit sms sending process

            $sms_price = 0;
            $senderid = '';
            // check sacco branch sms balance first 
            $sms_bal = $item->checkBranchSMSBalance($data->branch);
            $prices = $item->checkBankSMSPrice($data->branch);

            // check for senderid used , and get sms price
            $senderid = $item->getBranchSenderid($data->branch);
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
                    "amount" => $data->amount,
                    "method" => $data->pay_method,
                    "branch" => $data->branch,
                    "date" => $data->date,
                    "id" => $data->client,
                    "charge" => $added_sms_charge,
                );

                $sms = $item2->decryptSMS($smstype['temp_body'], 'on_withdraw', $trxnDetails);

                // get client's primary phone number
                $phone = $item->getClientPhone($data->client, $data->phone);

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
                        $res =  $item->SendSMS($senderid, $value, $sms);
                    }

                    $sms_price = $sms_price * count($phone);

                    $smstype['charge'] = $smstype['charge'] * count($phone);

                    // check if sms sent successfully or not
                    // if success, then do the steps down , if false , then just insert into sms_outbox
                    if ($res = 'OK') {

                        if ($sms_price > 0) {
                            // offset from sacco branch balance
                            $item->chargeBranchSMS($sms_price, $data->branch);
                        }
                        if ($smstype['charge'] > 0 && !is_null($data->client) && $smstype['charged_to'] == 'client') {
                            // offset from client account (if charge >0 ) 
                            $item->chargeClientSMS($data->client, $smstype['charge']);

                            // get the chart account id , then create trxn in table transactions --- t_type = SMS
                            $acid =  $item->getBranchSMSChargesAcc($data->branch);

                            // engange the create sms trxn method
                            $item->createSMSChargeTrxn(
                                $smstype['charge'],
                                0,
                                'SMS',
                                'Withdraw SMS Charge',
                                $data->user ?? 0,
                                $phone[0],
                                $phone[0],
                                $phone[0],
                                $data->client ?? 0,
                                $data->user ?? 0,
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
                            $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->client ?? 0, (int)$my_charge, 'sent', 1, $data->branch, 'Withdraw Trxn SMS');
                        }
                    } else {
                        // get charge per sms
                        $my_charge = $smstype['charge'] / count($phone);
                        foreach ($phone as $value) {
                            // insert into sms_outbox for record purposes  with not sent status
                            $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->client ?? 0, (int)$my_charge, 'failed', 1, $data->branch, $res);
                        }
                    }
                } else {
                    // no phone number found
                    /* no sms sending & charging */
                }
            }
        }
    }
    echo $ApiResponser::SuccessMessage($result);
    // } else {
    //     echo $ApiResponser::ErrorResponse($result);
    // }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
