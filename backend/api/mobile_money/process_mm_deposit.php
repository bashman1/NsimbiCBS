<?php
require_once __DIR__ . '../../RequestHeaders.php';
include('get_authorization_token.php');
try {

    $handler = new DbHandler();

    $mm = new MobileMoney($db);
    $data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;

    $data['amount'] = amount_to_integer(@$data['amount']);
    $data['reason'] = 'Mobile Money Deposit - ' . @$data['reason'];
    $data['acc_name'] = @$data['acc_name'];
    $data['acc_no'] = @$data['acc_no'];
    $data['phone'] = @$data['phone'];

    $member = @$handler->fetchAll("Client", " membership_no" , $data['acc_no'])[0];

    if (!@$member) {
        echo $ApiResponser::ErrorResponse("Invalid Account Number! Try again with the right Account Number.");
        return;
    }

    $account = @$handler->fetchAll("Account", " bank_account",44)[0];
    if (!@$account) {
        echo $ApiResponser::ErrorResponse("Something went wrong! Talk any of our Agents to have this resolved.");
        return;
    }

    $mmid = @$member->userId;
    $bal = @$member->acc_balance;
    $data['mid'] = @$mmid;
    $data['branchid'] = @$member->branchId;
    $data['t_type'] = @$data['t_type'];
    $data['pay_method'] = 'mobile_money';
    $data['charges'] = 0;
    $data['auth'] = 39;
    $data['acid'] = @$account->id;
    $data['channel'] = "AIRTELMMWEB";

    //  generate random tid
    $tid = "";

    for ($i = 0; $i < 10; $i++) {
        $tid .= mt_rand(1, 9);
    }

    $lastid = $handler->insert('transactions', [
        'amount' => (int)$data['amount'],
        'description' => $data['reason'],
        '_authorizedby' => $data['auth'],
        '_actionby' => $data['acc_name'],
        'acc_name' => $data['acc_name'],
        'mid' => (int)$data['mid'],
        'approvedby' => $data['auth'],
        '_branch' => $data['branchid'],
        'left_balance' => $data['charges'],
        't_type' => $data['t_type'],
        'acid' => $data['acid'],
        'pay_method' => $data['pay_method'],
        'bacid' => $data['acid'],
        'charges' => $data['charges'],
        '_status' => $data['charges'],
        'mm_tid' => $tid,
        'channel' => $data['channel'],
    ]);


    // send to airtel and take action for ussd push
    $str = $data["phone"];
    $mobile = ltrim($str, $str[0]);
    $res =   $mm->depositAirtelMM($mobile, (int)$data['amount'], $tid, $data['channel'], $token);

    if ($res['status']['success']) {

        // redirect user to success page
        header("Location: mm_check_trans.php?name=" . @$data["acc_name"] . "&amount=" . $data["amount"] . "&acc=" . $data["acc_no"] . "&reason=" . $data['reason'] . "&phone=" . $mobile . "&tid=" . $lastid."&bal=".$bal);
        exit;
      

    } else {
        echo $ApiResponser::ErrorResponse("Something went wrong! Try again.");
        return;
    }
    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
