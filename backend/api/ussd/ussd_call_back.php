<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../models/Account.php';
// $data = json_decode(file_get_contents("php://input"), true);


$database = new Database();
$db = $database->connect();


$item = new Account($db);

$session_id = $_POST['sessionId'];
$service_code = $_POST['serviceCode'];
$phone_number = $_POST['phoneNumber'];
$text = $_POST['text'];


$uids = [];
$uids[0] = 0;

$response = '';
if ($text == '') {
    $response = 'CON Welcome to  UCSCU MOBILE WALLET: <br/>';
    $response .= '<br/>';
    $response .= 'Enter SACCO ID <br/>';
}

if (!str_contains($text, '*') && $text != '') {
    $sacco_details = $item->getSaccoDetails($text);
    if ($sacco_details) {
        $response = 'CON ' . $sacco_details[0]['trade_name'] . ': <br/>';
        $response .= '1) Deposit <br/>';
        $response .= '2) Withdraw <br/>';
        $response .= '3) Pay School Fees <br/>';
        $response .= '4) Check Balance <br/>';
        $response .= '5) Transfer <br/>';
        $response .= '6) Mini Statement <br/>';
        $response .= '7) Change PIN <br/>';
        $response .= '<br/>';
        $response .= '0. Back <br/>';
    } else {
        $response = 'END Invalid SACCO ID <br/>';
        $response .= '<br/>';
        $response .= 'Dial *284*62# and Re-Enter the correct SACCO ID <br/>';
        $response .= 'Thanks!<br/>';
    }
}

if (substr_count($text, "*") == 1 && (substr($text, -1) == '1' || substr($text, -1) == '2' || substr($text, -1) == '4' || substr($text, -1) == '6' || substr($text, -1) == '5')) {
    $response  = 'CON Enter your registered Phone Number: <br/>';
    $response .= '<br/>';
    $response .= '0.Back  00.Main Menu<br/>';
}

if (substr_count($text, "*") == 2 && (str_contains('1', explode('*', $text)[1]) || str_contains('2', explode('*', $text)[1]) || str_contains('4', explode('*', $text)[1]) || str_contains('6', explode('*', $text)[1]) || str_contains('5', explode('*', $text)[1]))) {
    $accs = $item->getPhoneDetailsUssd(explode('*', $text)[2], explode('*', $text)[0]);
    if (is_object($accs) && !empty($accs)) {
        $response  = 'CON Select an account to continue: <br/>';
        $acc_count = 1;
        while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $response  .= '' . $acc_count . '. ' . ('' . $membership_no . ' (' . $sname . ')') . ' <br/>';
            $acc_count++;
        }
    } else {
        $response = 'END No A/C found <br/>';
        $response .= '<br/>';
        $response .= 'Dial *284*62# and Re-Enter your registered Phone Number <br/>';
        $response .= 'Thanks!<br/>';
    }
}


if (substr_count($text, "*") == 3 && (str_contains('1', explode('*', $text)[1]) || str_contains('2', explode('*', $text)[1]) || str_contains('4', explode('*', $text)[1]) || str_contains('6', explode('*', $text)[1]) || str_contains('5', explode('*', $text)[1]))) {

    $exploded_text = explode('*', $text);

    $option_selected_before = $exploded_text[1];

    // check balance, withdraw funds --- verify MPIN first
    if ($option_selected_before == '4' || $option_selected_before == 4 || $option_selected_before == '2' || $option_selected_before == 2) {

        $response = '';
        $response  = 'CON Enter mPIN: <br/>';
    }

    // deposit funds to account
    if ($option_selected_before == '1' || $option_selected_before == 1) {
        $response = '';
        $response  = 'CON Enter amount to deposit: <br/>';
    }

    // withdraw funds to account
    // if ($option_selected_before == '2' || $option_selected_before == 2) {
    //     $response = '';
    //     $response  = 'CON Enter amount to withdraw: <br/>';
    // }

    // mini-statement of account --- last 5 transactions --- charges 2000 (sms) , email 5000
    if ($option_selected_before == '6' || $option_selected_before == 6) {
        $response = '';
        $response  = 'CON How would you like to receive your Mini-Statement (Last 5 trxns) : <br/>';
        $response .= '1. Email <br/>';
        $response .= '2. SMS <br/>';
    }
}

// deposit , withdraw
if (substr_count($text, "*") == 4 && (str_contains('1', explode('*', $text)[1]) || str_contains('2', explode('*', $text)[1]) || str_contains('4', explode('*', $text)[1]))) {

    $exploded_text = explode('*', $text);

    $option_selected_before = $exploded_text[1];

    // deposit
    if ($option_selected_before == '1' || $option_selected_before == 1) {
        $response = '';
        $response  = 'CON Enter Mobile Number: <br/>';
    }

    // required resources for withdraw & check balance at this step
    $position = (int)$exploded_text[3];
    $accs = $item->getPhoneDetailsUssd(explode('*', $text)[2], explode('*', $text)[0]);
    $acc_count = 1;
    while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $uids[$acc_count] = $uid;
        $acc_count++;
    }

    $member_no = $uids[$position];




    //  check balance
    if ($option_selected_before == '4' || $option_selected_before == 4) {
        // verify mPIN entered first
        $verify_status = false;
        $entered_mpin = (int)$exploded_text[4];
        $mpin_true = $item->verifyUssdMpin($member_no);

        if ($mpin_true != '') {
            if ($mpin_true == $entered_mpin) {
                $verify_status = true;
            }
        }

        if ($verify_status) {
            $details = $item->getAccountBalance($member_no);
            $response = '';
            $response  = 'END <br/>Balance: UGX ' . number_format($details ?? 0) . '.<br/>Thanks for Saving with Us!';
        } else {

            $response = '';
            $response  = 'END <br/>Invalid mPIN: ' . $entered_mpin . '.<br/>Please enter correct PIN registered with this account!';
        }
    }

    // withdraw
    if ($option_selected_before == '2' || $option_selected_before == 2) {

        // verify mPIN entered first
        $verify_status = false;
        $entered_mpin = (int)$exploded_text[4];
        $mpin_true = $item->verifyUssdMpin($member_no);

        if ($mpin_true != '') {
            if ($mpin_true == $entered_mpin) {
                $verify_status = true;
            }
        }

        if ($verify_status) {

            $response = '';
            $response  = 'CON Enter amount to withdraw: <br/>';
        } else {
            $response = '';
            $response  = 'END <br/>Invalid mPIN: ' . $entered_mpin . '.<br/>Please enter correct PIN registered with this account!';
        }
    }
}

// deposit final --- initiate ussd push
if (substr_count($text, "*") == 5 && (str_contains('1', explode('*', $text)[1]))) {
    $exploded_text = explode('*', $text);
    $phone_to_use = $exploded_text[5];
    $phone_to_use = ltrim($phone_to_use, '0');
    $phone_to_use = '256' . $phone_to_use;

    $amount_to_deposit = $exploded_text[4];
    $amount_to_deposit = (int)$amount_to_deposit;

    $position = (int)$exploded_text[3];
    $accs = $item->getPhoneDetailsUssd(explode('*', $text)[2], explode('*', $text)[0]);
    $acc_count = 1;
    while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $uids[$acc_count] = $uid;
        $acc_count++;
    }

    $member_no = $uids[$position];

    $response = '';
    $response  = 'END Enter PIN to Confirm payment of ' . number_format($amount_to_deposit) . ' on ' . $phone_to_use . '  <br/>We shall get back to you shortly if payment was successful!';

    // send mm to yo request
    $endpoint = "https://app.ucscucbs.net/backend/api/mobile_app/ussd_momo_deposit_initiate.php";
    $url = $endpoint;
    $data = array(
        'phone'      => $phone_to_use,
        'amount'      => $amount_to_deposit,
        'reason'      => ' Deposit Via USSD (' . $member_no . ') ',
        'my_phone'      => explode('*', $text)[2],
        'counter'      => explode('*', $text)[3],
        'sid'      => explode('*', $text)[0],

    );

    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode($data),
            'header' =>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
        )
    );

    $context  = stream_context_create($options);
    $responsen = file_get_contents($url, false, $context);
    $data = json_decode($responsen, true);
    if ($data['success']) {
        // wait for 25 secs
        // sleep(5);

        // send mm to yo verify
        // $endpoint = "https://app.ucscucbs.net/backend/api/mobile_app/app_momo_deposit_verify.php";
        // $url = $endpoint;
        // $data = array(
        //     'tid'      => $data['TreffID'],
        // );

        // $options = array(
        //     'http' => array(
        //         'method'  => 'POST',
        //         'content' => json_encode($data),
        //         'header' =>  "Content-Type: application/json\r\n" .
        //             "Accept: application/json\r\n"
        //     )
        // );

        // $context  = stream_context_create($options);
        // $responsen = file_get_contents($url, false, $context);
        // $data = json_decode($responsen, true);

        // send sms 
    }
}

// withdraw -- confirm number to receive
if (substr_count($text, "*") == 5 && (str_contains('2', explode('*', $text)[1]))) {
    $exploded_text = explode('*', $text);

    // check whether there's enough account balance to withdraw this amount
    $position = (int)$exploded_text[3];
    $accs = $item->getPhoneDetailsUssd(explode('*', $text)[2], explode('*', $text)[0]);
    $acc_count = 1;
    while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $uids[$acc_count] = $uid;
        $acc_count++;
    }

    $member_no = $uids[$position];

    $verify_status = false;
    $entered_amount = (int)$exploded_text[5];
    $acc_bal = $item->getAccountBalance($member_no);

    if ($acc_bal) {
        if ($acc_bal > $entered_amount) {
            $verify_status = true;
        }
    }

    if ($verify_status && ($entered_amount > 0)) {
        $response = '';
        $response  = 'CON Confirm Mobile Number to receive ' . number_format($entered_amount) . ': <br/>';
    } else {
        $response = '';
        $response  = 'END Insufficient funds on your account. Your Current Balance is: ' . number_format($acc_bal) . ': <br/>';
    }
}

// withdraw final -- initiate disbursement request to yo
if (substr_count($text, "*") == 6 && (str_contains('2', explode('*', $text)[1]))) {
    $exploded_text = explode('*', $text);
    $phone_to_use = $exploded_text[6];
    $phone_to_use = ltrim($phone_to_use, '0');
    $phone_to_use = '256' . $phone_to_use;

    $amount_to_withdraw = $exploded_text[5];
    $amount_to_withdraw = (int)$amount_to_withdraw;

    $position = (int)$exploded_text[3];
    $accs = $item->getPhoneDetailsUssd(explode('*', $text)[2], explode('*', $text)[0]);
    $acc_count = 1;
    while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $uids[$acc_count] = $uid;
        $acc_count++;
    }

    $member_no = $uids[$position];

    $response = '';
    $response  = 'END Check ' . $phone_to_use . ' for the disbursement of ' . number_format($amount_to_withdraw) . '  <br/>We shall get back to you shortly if payment was successful!';

    // send mm withdraw to yo request
    $endpoint = "https://app.ucscucbs.net/backend/api/mobile_app/ussd_momo_withdraw_initiate.php";
    $url = $endpoint;
    $data = array(
        'phone'      => $phone_to_use,
        'amount'      => $amount_to_withdraw,
        'reason'      => ' Withdraw Via USSD (' . $member_no . ') ',
        'my_phone'      => explode('*', $text)[2],
        'counter'      => explode('*', $text)[3],
        'sid'      => explode('*', $text)[0],

    );

    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode($data),
            'header' =>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
        )
    );

    $context  = stream_context_create($options);
    $responsen = file_get_contents($url, false, $context);
    $data = json_decode($responsen, true);
    if ($data['success']) {
        // confirm disbursement & subtract from account(create withdraw trxn)
    }
}


// // if option is deposit , withdraw, check balance, mini statement, then ask for member details
// if ($text != '' && (str_contains($text, '1') || str_contains($text, '2') || str_contains($text, '4') || str_contains($text, '6')) && substr_count($text, "*") == 0) {
//     $option_selected_before = $text;
//     $response = '';
//     $response  = 'CON Enter your registered Phone Number: <br/>';
// }
// $member_no = 0;
// $primary_no = '';
// $uids = [];
// $uids[0] = 0;
// $option_selected_before = '';
// $option_selected = '';

// $amount_to_deposit = 0;
// $amount_to_withdraw = 0;
// // school pay, zakah, donate
// if ($text != '' && (str_contains($text, '3') || str_contains($text, '7') || str_contains($text, '8')) && substr_count($text, "*") == 0) {
//     $response = '';
//     $response  = 'CON Select your Prefered Mode of Payment: <br/>';
//     $response .= '1. Mobile Money <br/>';
//     $response .= '2. Use your Savings Balance <br/>';
// }
// // school pay, zakah, donate options step 2
// if (substr_count($text, "*") == 1 && (str_contains('378', explode('*', $text)[0]))) {

//     // if mode is mm
//     if (explode('*', $text)[1] == 1 || explode('*', $text)[1] == '1') {
//         // school pay
//         if (explode('*', $text)[0] == 3 || explode('*', $text)[0] == '3') {
//             $response = '';
//             $response  = 'CON Select Institution to Continue: <br/>';
//             $accs = $accounts->getSchoolPayInsts();
//             $acc_count = 1;
//             while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
//                 extract($row);

//                 $response  .= '' . $acc_count . '. ' . ($firstName ? 'A/C: ' . $membership_no . ', Name: ' . $firstName . ' ' . $lastName : 'A/C: ' . $membership_no . ', Name: ' . $shared_name) . ' <br/>';
//                 $acc_count++;
//             }
//         }
//     } else if (explode('*', $text)[1] == 1 || explode('*', $text)[1] == '1') {
//         // savings balance
//         $response = '';
//         $response  = 'CON Enter your registered Phone Number: <br/>';
//     }
// }

// // school pay, zakah, donate, options step3
// if (substr_count($text, "*") == 2 && (str_contains('378', explode('*', $text)[0]))) {
//     $primary_no = explode('*', $text)[2];
//     $response = '';
//     $response  = 'CON Select an account to continue: <br/>';
//     $accs = $accounts->getPhoneDetails($primary_no);
//     $acc_count = 1;
//     while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
//         extract($row);

//         $response  .= '' . $acc_count . '. ' . ($firstName ? 'A/C: ' . $membership_no . ', Name: ' . $firstName . ' ' . $lastName : 'A/C: ' . $membership_no . ', Name: ' . $shared_name) . ' <br/>';
//         $acc_count++;
//     }
// }

// // school pay, zakah, donate, options step4
// if (substr_count($text, "*") == 3 && (str_contains('378', explode('*', $text)[0]))) {
//     $exploded_text = explode('*', $text);
//     $position = (int)$exploded_text[3];

//     $primary_no = $exploded_text[2];
//     $accs = $accounts->getPhoneDetails($primary_no);
//     $acc_count = 1;
//     while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
//         extract($row);
//         $uids[$acc_count] = $uid;
//         $acc_count++;
//     }

//     $member_no = $uids[$position];
//     $option_selected_before = $exploded_text[0];

//     // pay school fees
//     if ($option_selected_before == '3' || $option_selected_before == 3) {
//         $response = '';
//         $response  = 'CON Select Institution to Continue: <br/>';
//         $accs = $accounts->getSchoolPayInsts();
//         $acc_count = 1;
//         while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
//             extract($row);

//             $response  .= '' . $acc_count . '. ' . ($firstName ? 'A/C: ' . $membership_no . ', Name: ' . $firstName . ' ' . $lastName : 'A/C: ' . $membership_no . ', Name: ' . $shared_name) . ' <br/>';
//             $acc_count++;
//         }
//     }

//     // pay zakah
//     if ($option_selected_before == '7' || $option_selected_before == 7) {
//         $response = '';
//         $response  = 'CON Enter Amount: <br/>';
//     }

//     // donate
//     if ($option_selected_before == '8' || $option_selected_before == 8) {
//         $response = '';
//         $response  = 'CON Enter Amount: <br/>';
//     }
// }

// if (substr_count($text, "*") == 1 && (str_contains('1246', explode('*', $text)[0]))) {
//     $option_selected = $text;
//     $primary_no = explode('*', $text)[1];
//     $response = '';
//     $response  = 'CON Select an account to continue: <br/>';
//     $accs = $accounts->getPhoneDetails($primary_no);
//     $acc_count = 1;
//     while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
//         extract($row);
//         $response  .= '' . $acc_count . '. ' . ($firstName ? 'A/C: ' . $membership_no . ', Name: ' . $firstName . ' ' . $lastName : 'A/C: ' . $membership_no . ', Name: ' . $shared_name) . ' <br/>';
//         $acc_count++;
//     }
// }

// if (substr_count($text, "*") == 2 && (str_contains('1246', explode('*', $text)[0]))) {
//     $exploded_text = explode('*', $text);
//     $position = (int)$exploded_text[2];

//     $primary_no = $exploded_text[1];
//     $accs = $accounts->getPhoneDetails($primary_no);
//     $acc_count = 1;
//     while ($row = $accs->fetch(PDO::FETCH_ASSOC)) {
//         extract($row);
//         $uids[$acc_count] = $uid;
//         $acc_count++;
//     }

//     $member_no = $uids[$position];
//     $option_selected_before = $exploded_text[0];

//     // check balance
//     if ($option_selected_before == '4' || $option_selected_before == 4) {
//         $details = $accounts->getAccountBalance($member_no);
//         $response = '';
//         $response  = 'END <br/>Balance: UGX ' . number_format($details ?? 0) . '.<br/>Thanks for Saving with Us!';
//     }
//     // deposit funds to account
//     if ($option_selected_before == '1' || $option_selected_before == 1) {
//         $response = '';
//         $response  = 'CON Enter amount to deposit: <br/>';
//     }

//     // withdraw funds to account
//     if ($option_selected_before == '2' || $option_selected_before == 2) {
//         $response = '';
//         $response  = 'CON Enter amount to withdraw: <br/>';
//     }

//     // mini-statement of account --- last 5 transactions --- charges 2000 (sms) , email 5000
//     if ($option_selected_before == '6' || $option_selected_before == 6) {
//         $response = '';
//         $response  = 'CON How would you like to receive this Mini-Statement (Last 5 trxns) : <br/>';
//         $response .= '1. Email <br/>';
//         $response .= '2. SMS <br/>';
//     }
// }

// if (substr_count($text, "*") == 3 && (str_contains('1246', explode('*', $text)[0]))) {
//     $exploded_text = explode('*', $text);
//     $option_selected_before = $exploded_text[0];
//     if ($option_selected_before == '1' || $option_selected_before == 1) {
//         $response = '';
//         $response  = 'CON Enter Mobile Money number to use (07xxxxxxxx): <br/>';
//     }

//     if ($option_selected_before == '2' || $option_selected_before == 2) {
//         $response = '';
//         $response  = 'CON Enter Mobile Money number to use (07xxxxxxxx): <br/>';
//     }

//     if ($option_selected_before == '6' || $option_selected_before == 6) {
//         if ($exploded_text[3] == '1' || $exploded_text[3] == 1) {
//             // email request
//             $response = '';
//             $response  = 'CON Enter active Email ID to receive statement: <br/>';
//         } else if ($exploded_text[3] == '2' || $exploded_text[3] == 2) {
//             // confirm phone number
//             $response = '';
//             $response  = 'CON Enter Phone number to receive SMS (07xxxxxxxx): <br/>';
//         }
//     }
// }

// if (substr_count($text, "*") == 4 && (str_contains('1246', explode('*', $text)[0]))) {
//     $exploded_text = explode('*', $text);
//     $option_selected_before = $exploded_text[0];

//     if ($option_selected_before == '1' || $option_selected_before == 1) {

//         $phone_to_use = $exploded_text[4];
//         $phone_to_use = ltrim($phone_to_use, '0');
//         $phone_to_use = '256' . $phone_to_use;

//         $amount_to_deposit = $exploded_text[3];
//         $amount_to_deposit = (int)$amount_to_deposit;

//         $response = '';
//         $response  = 'END Kindly Confirm payment on ' . $phone_to_use . '  <br/>We shall get back to you shortly if payment was successful!';

//         // send mm to yo request
//         $endpoint = "https://app.ucscucbs.net/backend/api/mobile_app/app_momo_deposit_initiate.php";
//         $url = $endpoint;
//         $data = array(
//             'phone'      => $phone_to_use,
//             'amount'      => $amount_to_deposit,
//             'reason'      => 'USSD Deposit ',
//         );

//         $options = array(
//             'http' => array(
//                 'method'  => 'POST',
//                 'content' => json_encode($data),
//                 'header' =>  "Content-Type: application/json\r\n" .
//                     "Accept: application/json\r\n"
//             )
//         );

//         $context  = stream_context_create($options);
//         $responsen = file_get_contents($url, false, $context);
//         $data = json_decode($responsen, true);
//         if ($data['success']) {
//             // send mm to yo verify
//             $endpoint = "https://app.ucscucbs.net/backend/api/mobile_app/app_momo_deposit_verify.php";
//             $url = $endpoint;
//             $data = array(
//                 'tid'      => $data['TreffID'],
//             );

//             $options = array(
//                 'http' => array(
//                     'method'  => 'POST',
//                     'content' => json_encode($data),
//                     'header' =>  "Content-Type: application/json\r\n" .
//                         "Accept: application/json\r\n"
//                 )
//             );

//             $context  = stream_context_create($options);
//             $responsen = file_get_contents($url, false, $context);
//             $data = json_decode($responsen, true);
//         }

//         // send success deposit message to client
//         // }else{
//         // send failed message to client
//         // }

//     }
//     if ($option_selected_before == '2' || $option_selected_before == 2) {
//         $phone_to_use = $exploded_text[4];
//         $phone_to_use = ltrim($phone_to_use, '0');
//         $phone_to_use = '256' . $phone_to_use;

//         $amount_to_deposit = $exploded_text[3];
//         $amount_to_deposit = (int)$amount_to_deposit;

//         $response = '';
//         $response  = 'END UGX ' . number_format($amount_to_deposit) . ' will be deposited to your mobile money account on ' . $phone_to_use . ' , shortly.  <br/>Thanks for saving with Us!';
//     }

//     if ($option_selected_before == '6' || $option_selected_before == 6) {
//         if ($exploded_text[3] == 1 || $exploded_text[3] == '1') {
//             // get email address
//             $email = $exploded_text[4];
//             // check if balance is enough to charge then send email with last 5 trxns
//             $response = '';
//             $response  = 'END Your Mini-Statement will be shared with you via Email on ' . $email . '.<br/>Thanks for Saving with Us!';
//         } else if ($exploded_text[3] == 2 || $exploded_text[3] == '2') {
//             // get phone number
//             $phone = $exploded_text[4];
//             // check balance is enough then send sms with last 5 trxns
//             $response = '';
//             $response  = 'END Your Mini-Statement will be shared with you via text message on ' . $phone . '.<br/>Thanks for Saving with Us!';
//         }
//     }
// }
// if ($text == '7') {
//     $response  = 'END Your Mini-Statement will be shared with you via text message & via email on your registered Phone number & Email ID.<br/>Thanks for Saving with Us!';
// }
// header('Content-type; text/plain');
http_response_code(200);
echo $response;
