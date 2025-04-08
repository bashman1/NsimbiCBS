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

$item->mid = $data->client;
$item->amount = $data->amount;
$item->description = $data->reason . ' ( ' . $data->pay_method . ' )';
$item->_actionby = $data->deposited;
$item->_branch = $data->branch;
$item->_authorizedby = $data->user;
$item->date_created = $data->date;
$item->pay_method = $data->pay_method;
$item->bacid = $data->bank_acc;
$item->cheque_no = $data->cheque_no;
$item->cash_acc = $data->cash_acc;
$item->send_sms = $data->tid;
$item->make_charges = $data->orig_amount;
$item->said = $data->aid;
$item->orig_acid = $data->orig_acid;


try {
    $result = $item->editWithdraw();

    // insert into the audit trail table -- for creating a client

    // generate and organise audit trail info
    $audit_info  = array(
        "action" => $data->pay_method . '  Withdraw Update',
        "log_desc" => 'Updated Withdraw TID: ' . $data->tid . ' for: - ' . $data->client . ' of UGX: ' . number_format($data->amount ?? 0) . ': Record Date: ' . $data->date,
        "uid" => $data->user,
        "branch" => $data->branch,
        "bank" => NULL,
        "ip" => '',
        "status" => $result == true ? 'success' : 'failed',
    );

    // insert into audit trail
    $item2->insertAuditTrail($audit_info);


    // check deposit return 
    if ($result === true) {





        echo $ApiResponser::SuccessMessage("Withdraw Updated successfully !");
    } else {
        echo $ApiResponser::ErrorResponse($result);
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
