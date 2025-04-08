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

$item->mid = $data->user_id;
$item->amount = $data->amount;
$item->description = $data->reason . ' ( ' . $data->method . ' ) for - ' . @$data->sno . ' - ' . $data->sname . ' - Class:' . $data->sclass . ' - Term:' . $data->sterm ;
$item->_actionby = $data->pname;
$item->_branch = $data->branch;
$item->_authorizedby = $data->auth;
$item->date_created = date('Y-m-d');
$item->pay_method = $data->method;
$item->cheque_no = @$data->sref;
$item->send_sms = @$data->sno;
$item->term = $data->sterm;

$item->student_name = $data->sname;
$item->student_class = $data->sclass;
$item->parent_phone = $data->pphone;
$item->parent_name = $data->pname;
$item->send_sms_parent = 1;
$item->send_sms_school = 0;
$item->cash_acc = 0;
$item->bacid = 0;
$item->external_ref = @$data->ext_ref;


try {
    $result = $item->createDepositFees();

    // insert into the audit trail table -- for creating a client

    // generate and organise audit trail info
    $audit_info  = array(
        "action" => $data->method . '  School Fees Payment',
        "log_desc" => 'Created New School Fees Payment for: - SNO: ' . $data->sno . ' ' . $data->user_id . ' of UGX: ' . number_format($data->amount ?? 0) . ': Record Date: ' . date('Y-m-d'),
        "uid" => $data->auth,
        "branch" => $data->branch,
        "bank" => NULL,
        "ip" => '',
        "status" => $result == true ? 'success' : 'failed',
    );

    // insert into audit trail
    $item2->insertAuditTrail($audit_info);


    // check deposit return 
    if ($result === true) {



        /* 
 --- check first for sms_consent on the parent or guardian
*/
        if ($data->send_sms) {

            // check for sms and send deposit sms
            // check for deposit sms subscription and sms balance , then send sms
            $smstype = $item2->getBankSMStypeStatus($data->branch, 'on_deposit_fees_parent');

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
                    if ($smstype['charge'] > 0 && !is_null($data->user_id) && $smstype['charged_to'] == 'client') {
                        $added_sms_charge = $smstype['charge'];
                    } else {
                        $added_sms_charge = 0;
                    }
                    $trxnDetails  = array(
                        "amount" => $data->amount,
                        "method" => $data->method,
                        "branch" => $data->branch,
                        "date" => date('Y-m-d'),
                        "id" => $data->user_id,
                        "charge" => $added_sms_charge,
                        "sno" => @$data->sno ?? '',
                        "sname" => $data->sname ?? '',
                        "pname" => $data->pname ?? '',
                        "pcontact" => $data->pphone ?? '',
                        "sclass" => $data->sclass ?? '',
                    );

                    $sms = $item2->decryptSMS($smstype['temp_body'], 'on_deposit_fees_parent', $trxnDetails);

                    // get client's primary phone number
                    $phone = $data->pphone;

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
                            if ($smstype['charge'] > 0 && !is_null($data->user_id) && $smstype['charged_to'] == 'client') {
                                // offset from client account (if charge >0 ) 
                                $item->chargeClientSMS($data->user_id, $smstype['charge']);

                                // get the chart account id , then create trxn in table transactions --- t_type = SMS
                                $acid =  $item->getBranchSMSChargesAcc($data->branch);

                                // engange the create sms trxn method
                                $item->createSMSChargeTrxn(
                                    $smstype['charge'],
                                    0,
                                    'SMS',
                                    'Deposit SMS Charge',
                                    $data->auth ?? 0,
                                    $phone[0],
                                    $phone[0],
                                    $phone[0],
                                    $data->user_id ?? 0,
                                    $data->auth ?? 0,
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
                                $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->user_id ?? 0, (int)$my_charge, 'sent', 1, $data->branch, 'Deposit Trxn SMS');
                            }
                        } else {
                            // get charge per sms
                            $my_charge = $smstype['charge'] / count($phone);
                            foreach ($phone as $value) {
                                // insert into sms_outbox for record purposes  with not sent status
                                $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->user_id ?? 0, (int)$my_charge, 'failed', 1, $data->branch, $res);
                            }
                        }
                    } else {
                        // no phone number found
                        /* no sms sending & charging */
                    }
                }
            }
        }

        // send to school if message consent is on 
        if (0) {

            // check for sms and send deposit sms
            // check for deposit sms subscription and sms balance , then send sms
            $smstype = $item2->getBankSMStypeStatus(
                $data->branch,
                'on_deposit_fees_school'
            );

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
                    if ($smstype['charge'] > 0 && !is_null($data->user_id) && $smstype['charged_to'] == 'client') {
                        $added_sms_charge = $smstype['charge'];
                    } else {
                        $added_sms_charge = 0;
                    }
                    $trxnDetails  = array(
                        "amount" => $data->amount,
                        "method" => $data->method,
                        "branch" => $data->branch,
                        "date" => date('Y-m-d'),
                        "id" => $data->user_id,
                        "charge" => $added_sms_charge,
                        "sno" => $data->sno ?? '',
                        "sname" => $data->sname ?? '',
                        "pname" => $data->pname ?? '',
                        "pcontact" => $data->pphone ?? '',
                        "sclass" => $data->sclass ?? '',
                    );

                    $sms = $item2->decryptSMS($smstype['temp_body'], 'on_deposit_fees_school', $trxnDetails);

                    // get client's primary phone number
                    $phone = $item->getClientPhone($data->user_id);

                    if ($phone) {
                        /* phone number array hold numbers , iterate & send to each number */

                        foreach ($phone as $value) {
                            // check if phone number has country code or not --use 256 by default
                            if (
                                $value[0] == "0" || $value[0] == 0 || $value[0] == "7"
                            ) {
                                if ($value[0] == "0" || $value[0] == 0) {
                                    $value = '256' . substr(
                                        $value,
                                        1
                                    );
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
                                $item->chargeBranchSMS(
                                    $sms_price,
                                    $data->branch
                                );
                            }
                            if ($smstype['charge'] > 0 && !is_null($data->user_id) && $smstype['charged_to'] == 'client') {
                                // offset from client account (if charge >0 ) 
                                $item->chargeClientSMS($data->user_id, $smstype['charge']);

                                // get the chart account id , then create trxn in table transactions --- t_type = SMS
                                $acid =  $item->getBranchSMSChargesAcc($data->branch);

                                // engange the create sms trxn method
                                $item->createSMSChargeTrxn(
                                    $smstype['charge'],
                                    0,
                                    'SMS',
                                    'Deposit SMS Charge',
                                    $data->auth ?? 0,
                                    $phone[0],
                                    $phone[0],
                                    $phone[0],
                                    $data->user_id ?? 0,
                                    $data->auth ?? 0,
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
                                $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->user_id ?? 0, (int)$my_charge, 'sent', 1, $data->branch, 'Deposit Trxn SMS');
                            }
                        } else {
                            // get charge per sms
                            $my_charge = $smstype['charge'] / count($phone);
                            foreach ($phone as $value) {
                                // insert into sms_outbox for record purposes  with not sent status
                                $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->user_id ?? 0, (int)$my_charge, 'failed', 1, $data->branch, $res);
                            }
                        }
                    } else {
                        // no phone number found
                        /* no sms sending & charging */
                    }
                }
            }
        }

        echo $ApiResponser::SuccessMessage("School Fees Payment created successfully !");
    } else {
        echo $ApiResponser::ErrorResponse($result);
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
