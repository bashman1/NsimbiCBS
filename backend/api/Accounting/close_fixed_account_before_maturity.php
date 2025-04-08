<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../models/Account.php';
require_once '../../models/User.php';
require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';
include_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
// $records = json_decode($data->actual_data, true);
$ApiResponser = new ApiResponser();


$database = new Database();
$db = $database->connect();


$item = new User($db);

try {
    $db_handler = new DbHandler();
    $client = $db_handler->fetch('Client', 'userId', $data['client_id']);
    if (!@$client) {
        echo $ApiResponser::ErrorResponse("Client Not found");
        return;
    }


    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];
    $client_names = @$client_names ?? @$client['shared_name'];


    $data['fd_amount'] = amount_to_integer($data['fd_amount']);
    $data['int_take'] = amount_to_integer($data['int_given']);
    $data['wht_take'] = amount_to_integer($data['wht_charged']);

    $transaction_type =     'D';
    $disburse_amount =
        $data['fd_amount'] + $data['int_take'] - $data['wht_take'];
    $int_amount = $data['int_take'] - $data['wht_take'];

    /**
     * update account balance to amount
     */
    if ($client['membership_no'] > 0) {
        $new_cust_balance = $client['acc_balance'] + $disburse_amount;
        $db_handler->update('Client', ['acc_balance' => $new_cust_balance,], 'userId', $client['userId']);
    } else {
        $new_cust_balance = $client['loan_wallet'] + $disburse_amount;
        $db_handler->update('Client', ['loan_wallet' => $new_cust_balance,], 'userId', $client['userId']);
    }
    if ($disburse_amount > 0) {
        $db_handler->insert('transactions', [
            'amount' =>
            $data['fd_amount'],
            'description' => 'Fixed Deposit A/C Closure  - Principal',
            '_authorizedby' => @$data['auth_id'],
            '_actionby' => @$data['auth_id'],
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'approvedby' => @$data['auth_id'],
            '_branch' => $client['branchId'],
            't_type' => $transaction_type,
            'date_created' => date('Y-m-d'),
            'pay_method' => 'saving',
        ]);

        $db_handler->insert('transactions', [
            'amount' => $int_amount,
            'description' => 'Fixed Deposit A/C  - Interest Disbursed',
            '_authorizedby' => @$data['auth_id'],
            '_actionby' => @$data['auth_id'],
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'approvedby' => @$data['auth_id'],
            '_branch' => $client['branchId'],
            't_type' => $transaction_type,
            'date_created' => date('Y-m-d'),
            'pay_method' => 'saving',
        ]);

        $status = 1;

        $db_handler->update('fixed_deposits', ['fd_status' => $status, 'fd_int_paid' => $data['int_take'], 'wht_paid' => $data['wht_take'],], 'fd_id', $data['fd_id']);

        // update fd interest chart a/c to add the disbursed interest
        $item->updateInterestChartAcc($data['int_take'], $data['wht_take'], $data['fd_branch']);
    }
    echo $ApiResponser::SuccessMessage("Fixed Account Closed Succesfully!");

    // if ($results === true) {
    //     return;
    // }
    // echo $ApiResponser::ErrorResponse($results);

} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
