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
    $client = $db_handler->fetch('Client', 'userId', $data['client_id']);
    if (!@$client) {
        echo $ApiResponser::ErrorResponse("Client Not found");
        return;
    }


    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];
    $client_names = @$client_names ?? @$client['shared_name'];

    $create_transaction = false;
    $rec_tid = 0;
    $reason = @$data['reason'] ?? '';


    $data['amount'] = amount_to_integer($data['amount']);
    $data['account_balance'] = amount_to_integer($data['acc_balance']);

    $transaction_type =   $data['amount'] > $data['account_balance'] ? 'D' : 'W';
    if ($data['amount'] >= $data['account_balance']) {
        $amount = abs($data['amount'] - $data['account_balance']);
        // $is_deposit = true;
        // $transaction_type  = 'D';
    } else {
        $amount = abs($data['account_balance'] - $data['amount']);
        // $is_deposit = false;
        // $transaction_type = 'W';
    }

    /**
     * update account balance to amount
     */
    if ($client['membership_no'] > 0) {
        $db_handler->update('Client', ['acc_balance' => $data['amount'],], 'userId', $client['userId']);
    } else {
        $db_handler->update('Client', ['loan_wallet' => $data['amount'],], 'userId', $client['userId']);
    }
    if ($amount > 0) {
        // $transaction_type = $is_deposit ? "D" : "W";
        $create_transaction = true;
        $rec_tid =  $db_handler->insert('transactions', [
            'amount' => $amount,
            'description' => 'Account Balance reconciliation as of ' . normal_date(date('Y-m-d')) . ' - ' . @$data['reason'],
            '_authorizedby' => @$data['auth_id'],
            '_actionby' => @$data['auth_id'],
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'approvedby' => @$data['auth_id'],
            '_branch' => $client['branchId'],
            't_type' => $transaction_type,
            'date_created' => @$data['rd'],
            'pay_method' => 'cash',
        ]);
    }


    $db_handler->insert('acc_reconciliations', [
        'rec_by' => @$data['auth_id'],
        'rec_option' => 'other_update_balance',
        'rec_uid' => $client['userId'],
        'rec_client_name' => $client_names,
        'rec_client_branch' => $client['branchId'],
        'rec_acc_bal' => $data['account_balance'],
        'rec_other_amount' => $data['amount'],
        'rec_closing_bal' => $data['account_balance'],
        'rec_freezed_bal' => @$client['freezed_amount'],
        'rec_created_trxn' => $create_transaction,
        'rec_trxn_tid' => $rec_tid,
        'rec_date_entered' => $data['rd'],
        'rec_reason' => $reason
    ]);
    echo $ApiResponser::SuccessMessage("Account Balance Succesfully Reconciled");

    // if ($results === true) {
    //     return;
    // }
    // echo $ApiResponser::ErrorResponse($results);

} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
