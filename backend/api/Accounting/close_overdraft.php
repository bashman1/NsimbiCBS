<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../models/Account.php';
require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
// $records = json_decode($data->actual_data, true);
$ApiResponser = new ApiResponser();

try {
    $db_handler = new DbHandler();
    $acc = $db_handler->fetch('Account', 'id', $data['acid']);
    $client = $db_handler->fetch('Client', 'userId', $data['client_id']);
    if (!@$client) {
        echo $ApiResponser::ErrorResponse("Client Not found");
        return;
    }


    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];
    $client_names = @$client_names ?? @$client['shared_name'];


    $data['amount'] = amount_to_integer($data['amount']);
    $data['int_due'] = amount_to_integer($data['int_due']);

    $amount = $data['amount'] + $data['int_due'];
    $data['account_balance'] = amount_to_integer($data['acc_balance']);

    $transaction_type =   'W';
    if ($amount <= $data['account_balance']) {

        $diff =  $data['account_balance'] - $amount;
        $diff2 =  $acc['balance'] + $data['amount'];
        /**
         * update account balance to amount
         */

        $db_handler->update('Client', ['acc_balance' => $diff,], 'userId', $client['userId']);
        $db_handler->update('Client', ['over_draft' => 0,], 'userId', $client['userId']);
        $db_handler->update('Account', ['balance' => $diff2,], 'id', $acc['id']);


        // $transaction_type = $is_deposit ? "D" : "W";
        $db_handler->insert('transactions', [
            'amount' => $data['amount'],
            'description' => 'Over-Draft Principal Payment:- ' . $data['oid'],
            '_authorizedby' => @$data['authby'],
            '_actionby' => @$data['authby'],
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'acid' => $acc['id'],
            'approvedby' => @$data['authby'],
            '_branch' => $client['branch'],
            't_type' => $transaction_type,
            'date_created' => date('Y-m-d'),
            'pay_method' => 'saving',
        ]);


        $db_handler->insert('transactions', [
            'amount' => $data['int_due'],
            'description' => 'Over-Draft Interest Payment:- ' . $data['oid'],
            '_authorizedby' => @$data['authby'],
            '_actionby' => @$data['authby'],
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'acid' => $data['income_id'],
            'approvedby' => @$data['authby'],
            '_branch' => $client['branch'],
            't_type' => 'I',
            'date_created' => date('Y-m-d'),
            'pay_method' => 'saving',
        ]);

        $db_handler->update('over_drafts', ['status' => 3,], 'odid', $data['oid']);


        echo $ApiResponser::SuccessMessage("Overdraft Succesfully Cleared");
        return;
    } else {
        echo $ApiResponser::ErrorResponse('Insufficient funds on account');
        return;
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
