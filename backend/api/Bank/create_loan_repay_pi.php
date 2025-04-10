<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/DbHandler.php';
require_once '../../config/functions.php';
include_once '../../models/Transaction.php';
include_once '../../models/User.php';
require_once '../../models/Loan.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';


try {
    $ApiResponser = new ApiResponser();



    $handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();



    $loan = new Bank($db);
    $item = new Transaction($db);
    $item2 = new User($db);
    $loan->loan_object = new Loan($handler);
    $data = json_decode(file_get_contents("php://input"));

    $loan->loan_id = @$data->lno;
    $loan->createdAt = date('Y-m-d H:i:s');
    $loan->principal = amount_to_integer(@$data->principal ?? 0);
    $loan->interest = amount_to_integer(@$data->interest ?? 0);
    $loan->date_of_next_pay = @$data->date_of_next_pay ?? date('Y-m-d H:i:s');
    $loan->collection_date = db_date_format(@$data->collection_date);
    $loan->serialNumber = @$data->balance;
    // $loan->interest = @$data->interest;
    // $loan->identificationNumber = @$data->clear_loan ?? 0;
    $loan->deletedAt = @$data->uid;
    $loan->description = $data->notes == '' ? "Loan Repayment - Loans Department (M)-" . @$data->lno . ' ( ' . @$data->pay_method . ' )' :
        "Loan Repayment -" . @$data->notes . ' ( ' . @$data->pay_method . ' )';
    $loan->left_balance = "L";

    $loan->pay_method = @$data->pay_method;
    $loan->bank_acc = @$data->bank_acc;
    $loan->cash_acc = @$data->cash_acc;
    $loan->cheque_no = @$data->cheque_no;
    $loan->send_sms = @$data->send_sms;
    $loan->auth_id = @$data->auth_id;
    $loan->clear_penalty = @$data->clear_penalty;
    $loan->penalty_amount = amount_to_integer(@$data->penalty_amount ?? 0);

    // echo $data;
    $result = $loan->createManualLoanRepayPI();
    // $result = [];

    if ($result === true) {


        /* 
TO DO --- check first for sms_consent 
*/
        $sms_consent = $item->getClientSMSConsent($data->uid);
        if ($sms_consent) {
            // check for sms and send deposit sms
            // check for deposit sms subscription and sms balance , then send sms
            $smstype = $item2->getBankSMStypeStatus($data->branch, 'loan_repay');

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
                    if ($smstype['charge'] > 0 && !is_null($data->uid) && $smstype['charged_to'] == 'client') {
                        $added_sms_charge = $smstype['charge'];
                    } else {
                        $added_sms_charge = 0;
                    }
                    $trxnDetails  = array(
                        "amount" => (amount_to_integer(@$data->principal ?? 0) + amount_to_integer(@$data->interest ?? 0)),
                        "method" => $data->pay_method,
                        "branch" => $data->branch,
                        "date" => db_date_format(@$data->collection_date),
                        "id" => $data->uid,
                        "lid" => $data->lno,
                        "charge" => $added_sms_charge,
                    );

                    $sms = $item2->decryptSMS($smstype['temp_body'], 'loan_repay', $trxnDetails);

                    // get client's primary phone number
                    $phone = $item->getClientPhone($data->uid,'');

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
                            if ($smstype['charge'] > 0 && !is_null($data->uid) && $smstype['charged_to'] == 'client') {
                                // offset from client account (if charge >0 ) 
                                $item->chargeClientSMS($data->uid, $smstype['charge']);

                                // get the chart account id , then create trxn in table transactions --- t_type = SMS
                                $acid =  $item->getBranchSMSChargesAcc($data->branch);

                                // engange the create sms trxn method
                                $item->createSMSChargeTrxn(
                                    $smstype['charge'],
                                    0,
                                    'SMS',
                                    'Loan Repayment SMS Charge',
                                    $data->auth_id ?? 0,
                                    $phone[0],
                                    $phone[0],
                                    $phone[0],
                                    $data->uid ?? 0,
                                    $data->auth_id ?? 0,
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
                                $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->uid ?? 0, (int)$my_charge, 'sent', 1, $data->branch, 'Loan Repayment Trxn SMS');
                            }
                        } else {
                            // get charge per sms
                            $my_charge = $smstype['charge'] / count($phone);
                            foreach ($phone as $value) {
                                // insert into sms_outbox for record purposes  with not sent status
                                $item->insertSMSOutBox($value, $sms, $senderid, (int)$data->uid ?? 0, (int)$my_charge, 'failed', 1, $data->branch, $res);
                            }
                        }
                    } else {
                        // no phone number found
                        /* no sms sending & charging */
                    }
                }
            }
        }





        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
