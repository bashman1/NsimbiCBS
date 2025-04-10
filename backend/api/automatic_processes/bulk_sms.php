<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../models/Transaction.php';
include_once '../../models/User.php';

try {
    $date = date('Y-m-d');
    $database = new Database();
    $db = $database->connect();

    $item = new Transaction($db);
    $item2 = new User($db);
    $db_handler = new DbHandler();

    $scheduled_taks = $db_handler->database->fetchAll('SELECT * FROM `scheduled_sms` WHERE `s_status` IN (0) ORDER BY s_id ASC LIMIT 1 ');
    $row = $scheduled_taks[0];

    // foreach ($scheduled_taks as $row) {



    //  start on on_deposit sms sending process

    $sms_price = 0;
    $senderid = '';
    // check sacco branch sms balance first 
    $sms_bal = $item->checkBranchSMSBalance($row['branch_id']);
    $prices = $item->checkBankSMSPrice($row['branch_id']);



    // check for senderid used , and get sms price
    $senderid = $item->getBranchSenderid($row['branch_id']);
    if ($senderid != '') {

        $sms_price = $prices['sms_sender_id_price'];
    } else {
        $sms_price = $prices['sms_price'];
    }
    $total_sms_cost = $sms_price;
    $recs = $item->getBranchClientsContacts($row['branch_id']);
    if ($row['s_type'] == 'all') {
        $total_sms_cost = ($recs->rowCount()) * $sms_price;
    }

    if ($sms_bal > $total_sms_cost || $sms_bal == $total_sms_cost) {
        // $client = $recs->fetch(PDO::FETCH_ASSOC);
        foreach ($recs as $client) {

            // fill temp_body tags with the right info
            if ($row['sms_charge'] > 0 && !is_null($client['uid'])) {
                $added_sms_charge = $row['sms_charge'];
            } else {
                $added_sms_charge = 0;
            }
            $trxnDetails  = array(
                "branch" => $row['branch_id'],
                "date" => $row['s_date'],
                "id" => $client['uid'],
                "charge" => $added_sms_charge,
            );

            $sms = $item2->decryptSMS($row['s_body'], 'general_sms', $trxnDetails);

            // get client's primary phone number
            $phone = $item->getClientPhone($client['uid'],'');

            if (!empty($phone)) {
                /* phone number array hold numbers , iterate & send to each number */

                // foreach ($phone as $value) {
                    $value = $phone[0];
                    // check if phone number has country code or not --use 256 by default
                    if (!empty($value)) {
                        if ($value[0] == "0" || $value[0] == 0 || $value[0] == "7") {
                            if ($value[0] == "0" || $value[0] == 0) {
                                $value = '256' . substr($value, 1);
                            } else {
                                $value = '256' . $value;
                            }
                        }
                    }
                    // var_dump($value);
                    // send sms
                    $res =  $item->SendSMS($senderid, $value, $sms);
                    $item->updateClientSMSStatus($client['uid']);
                // }

                $sms_price = $sms_price * count($phone);

                $row['sms_charge'] = $row['sms_charge'] * count($phone);

                // check if sms sent successfully or not
                // if success, then do the steps down , if false , then just insert into sms_outbox
                if ($res = 'OK') {

                    if ($sms_price > 0) {
                        // offset from sacco branch balance
                        $item->chargeBranchSMS($sms_price, $row['branch_id']);
                    }
                    if ($row['sms_charge'] > 0 && !is_null($client['uid'])) {
                        // offset from client account (if charge >0 ) 
                        $item->chargeClientSMS($client['uid'], $row['sms_charge']);

                        // get the chart account id , then create trxn in table transactions --- t_type = SMS
                        $acid =  $item->getBranchSMSChargesAcc($row['branch_id']);

                        // engange the create sms trxn method
                        $item->createSMSChargeTrxn(
                            $row['sms_charge'],
                            0,
                            'SMS',
                            'General Bulk SMS Charge',
                            $row['scheduled_by'] ?? 0,
                            $phone[0],
                            $phone[0],
                            $phone[0],
                            $client['uid'] ?? 0,
                            $row['scheduled_by'] ?? 0,
                            $row['branch_id'],
                            1,
                            $acid,
                            'saving',
                            1
                        );
                    }
                    // get charge per sms
                    $my_charge = $row['sms_charge'] / count($phone);
                    foreach ($phone as $value) {

                        // insert into sms_outbox for record purposes  with status sent
                        $item->insertSMSOutBox($value, $sms, $senderid, (int)$client['uid'] ?? 0, (int)$my_charge, 'sent', 1, $row['branch_id'], 'General Communication SMS');
                    }
                } else {
                    // get charge per sms
                    $my_charge = $row['sms_charge'] / count($phone);
                    foreach ($phone as $value) {
                        // insert into sms_outbox for record purposes  with not sent status
                        $item->insertSMSOutBox($value, $sms, $senderid, (int)$client['uid'] ?? 0, (int)$my_charge, 'failed', 1, $row['branch_id'], $res);
                    }
                }
            } else {
                // no phone number found
                /* no sms sending & charging */
            }
        }
    }

    // }

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
