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

    if (!in_array($data['reconcile'], ['account_balance', 'closing_balance', 'other'])) {
        echo $ApiResponser::ErrorResponse("Please select right reconciliation method");
        return;
    }

    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];
    $client_names = @$client_names ?? @$client['shared_name'];

    $reason = @$data['reason'] ?? '';
    $rec_tid = 0;

    $is_account_balance = false;
    $is_closing_balance = false;
    $is_other = false;
    $is_deposit = false;
    $is_withdrawal = false;
    $create_transaction = true;
    $diff = 0;

    $data['amount'] = amount_to_integer(@$data['amount'] ?? 0);
    $data['account_balance'] = amount_to_integer(@$data['account_balance'] ?? 0);
    $data['closing_balance'] = amount_to_integer(@$data['closing_balance'] ?? 0);
    $data['freezed'] = amount_to_integer(@$data['freezed'] ?? 0);

    if ($data['reconcile'] == 'other') {
        $amount = @$data['amount'];
        $is_other = true;
        $create_transaction = true;
    } else {
        if ($data['reconcile'] == 'account_balance') {
            $is_account_balance = true;
            $create_transaction = true;
            $diff = abs($data['account_balance'] - @$data['closing_balance']);
            if ($data['account_balance'] > @$data['closing_balance']) {
                $is_deposit = true;
                $is_withdrawal = false;
            } else {
                $is_deposit = false;
                $is_withdrawal = true;
            }
        } else {
            $is_closing_balance = true;
            $is_withdrawal = true;
            $create_transaction = false;
        }
        $diff = abs(@$data['account_balance'] - @$data['closing_balance']);
    }

    if ($is_other) {
        /**
         * update account balance to amount
         */
        if ($client['membership_no'] > 0) {
            $db_handler->update('Client', ['acc_balance' => $amount,], 'userId', $client['userId']);
        } else {
            $db_handler->update('Client', ['loan_wallet' => $amount,], 'userId', $client['userId']);
        }

        if ($amount > $data['closing_balance']) {
            $is_deposit = true;
            $diff = abs(@$data['amount'] - @$data['closing_balance']);
        } else if ($amount < @$data['closing_balance']) {
            $is_withdrawal = true;
            $diff = abs(@$data['amount'] - @$data['closing_balance']);
        } else {
            $create_transaction = false;
        }
    }

    if ($is_closing_balance) {
        $db_handler->update('Client', ['acc_balance' => abs(@$data['closing_balance'] - @$data['freezed'])], 'userId', $client['userId']);
    }

    if (@$create_transaction) {
        $transaction_type = $is_deposit ? "D" : "W";
        $rec_tid = $db_handler->insert('transactions', [
            'amount' => $diff,
            'description' => 'Account Statement reconciliation:- ' . $reason,
            '_authorizedby' => @$data['auth_id'],
            '_actionby' => @$data['auth_id'],
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'approvedby' => @$data['auth_id'],
            '_branch' => $client['branchId'],
            't_type' => $transaction_type,
            'date_created' => $data['rd'],
            'pay_method' => 'cash',
        ]);
    }

    $db_handler->insert('acc_reconciliations', [
        'rec_by' => @$data['auth_id'],
        'rec_option' => $data['reconcile'],
        'rec_uid' => $client['userId'],
        'rec_client_name' => $client_names,
        'rec_client_branch' => $client['branchId'],
        'rec_acc_bal' => $data['account_balance'],
        'rec_other_amount' => $data['amount'],
        'rec_closing_bal' => $data['closing_balance'],
        'rec_freezed_bal' => @$data['freezed'],
        'rec_created_trxn' => $create_transaction,
        'rec_trxn_tid' => $rec_tid,
        'rec_date_entered' => $data['rd'],
        'rec_reason' => $reason
    ]);


    echo $ApiResponser::SuccessMessage("Account Statement Succesfully Reconciled");

    // if ($results === true) {
    //     return;
    // }
    // echo $ApiResponser::ErrorResponse($results);

} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
