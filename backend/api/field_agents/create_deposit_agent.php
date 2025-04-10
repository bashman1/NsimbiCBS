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

$item->account_id = $_GET['mid'];
$item->details = 'Deposit Via Agent';
$item->acc_name = $_GET['acc_name'];
$item->branch_id = $_GET['branch'];
$item->user_id = $_GET['authorized'];
$item->actby = $_GET['pay_name'];
$item->actbyphone = $_GET['pay_phone'];
// $item->pay_method = $_GET['wallet'] < 0 ? 0 : $_GET['wallet'];
$item->pay_method = 0;
$item->amount = $_GET['amount'] < 0 ? 0 : $_GET['amount'];

// if($_GET['wallet']=='Savings Wallet'){
//     $item->interest = 'S';
// }else{
//     $item->interest = 'L'; 
// }

//  if($_GET['ttype']=='Membership'){
//     $item->t_type = 'R';
// }else{
//     $item->t_type = 'D'; 
// }



$stmt = $item->createDepositAgent();

$sms_consent = $item3->getClientSMSConsent($_GET['mid']);
if ($sms_consent) {
    // check for sms and send deposit sms
    // check for deposit sms subscription and sms balance , then send sms
    $smstype = $item2->getBankSMStypeStatus($_GET['branch'], 'on_deposit_agent');

    if ($smstype != 0 && $smstype['s_status'] == 1) {


        //  start on on_deposit sms sending process

        $sms_price = 0;
        $senderid = '';
        // check sacco branch sms balance first 
        $sms_bal = $item3->checkBranchSMSBalance($_GET['branch']);
        $prices = $item3->checkBankSMSPrice($_GET['branch']);

        // check for senderid used , and get sms price
        $senderid = $item3->getBranchSenderid($_GET['branch']);
        if ($senderid != '') {

            $sms_price = $prices['sms_sender_id_price'];
        } else {
            $sms_price = $prices['sms_price'];
        }
        if ($sms_bal > $sms_price || $sms_bal == $sms_price) {

            // fill temp_body tags with the right info
            if ($smstype['charge'] > 0 && !is_null($_GET['mid']) && $smstype['charged_to'] == 'client') {
                $added_sms_charge = $smstype['charge'];
            } else {
                $added_sms_charge = 0;
            }
            $trxnDetails  = array(
                "amount" => $_GET['amount'] ?? 0,
                "wallet" => $_GET['wallet'] ?? 0,
                "method" => 'cash',
                "branch" => $_GET['branch'],
                "date" => now(),
                "id" => $_GET['mid'],
                "charge" => $added_sms_charge,
            );

            $sms = $item2->decryptSMS($smstype['temp_body'], 'on_deposit_agent', $trxnDetails);

            // get client's primary phone number
            $phone = $item3->getClientPhone($_GET['mid'], $_GET['pay_phone']);

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
                        $item3->chargeBranchSMS($sms_price, $_GET['branch']);
                    }
                    if ($smstype['charge'] > 0 && !is_null($_GET['mid']) && $smstype['charged_to'] == 'client') {
                        // offset from client account (if charge >0 ) 
                        $item3->chargeClientSMS($_GET['mid'], $smstype['charge']);

                        // get the chart account id , then create trxn in table transactions --- t_type = SMS
                        $acid =  $item3->getBranchSMSChargesAcc($_GET['branch']);

                        // engange the create sms trxn method
                        $item3->createSMSChargeTrxn(
                            $smstype['charge'],
                            0,
                            'SMS',
                            'Deposit SMS Charge',
                            $_GET['authorized'] ?? 0,
                            $phone[0],
                            $phone[0],
                            $phone[0],
                            $_GET['mid'] ?? 0,
                            $_GET['authorized'] ?? 0,
                            $_GET['branch'],
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
                        $item3->insertSMSOutBox($value, $sms, $senderid, (int)$_GET['mid'] ?? 0, (int)$my_charge, 'sent', 1, $_GET['branch'], 'Deposit Trxn SMS');
                    }
                } else {
                    // get charge per sms
                    $my_charge = $smstype['charge'] / count($phone);
                    foreach ($phone as $value) {
                        // insert into sms_outbox for record purposes  with not sent status
                        $item3->insertSMSOutBox($value, $sms, $senderid, (int)$_GET['mid'] ?? 0, (int)$my_charge, 'failed', 1, $_GET['branch'], $res);
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
    // $userArr["data"] = array();
    //  $userArr["success"] = true;
    //  $userArr['statusCode']="200";
    //  $userArr['count']=$itemCount;
    //   $userArr['message']="Yes";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        // $wcc = $wc=='S'?'Savings Wallet':'Loans Wallet';
        $byy = $item->getUserNames($_authorizedby);
        $u = array(
            "_did" =>
            $t_type . '-ref-' . $pay_method . '-' . $tid . '-' . $_authorizedby,
            "_account_no" => $membership_no,
            "account_name" => strtoupper($acc_name),
            "_authorisedby" => $byy,
            "_paidby_name" => @$_actionby ?? '',
            "_paidby_phone" => @$_actionbyphone ?? '',
            "_amount" => number_format($amount + ($agent_loan_amount ?? 0)),
            "_reason" => $description ?? '',
            "_status" => $_status,
            "acc_balance" => $acc_balance,
            "pending" => $amount + ($agent_loan_amount ?? 0),
            "_date_created" => normal_date($date_created),
            "wallet" => strtoupper($pay_method),
            "message" => 'Yes',

        );


        array_push($userArr, $u);
        // array_push($userArr['sub'], $u2);
    }
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
