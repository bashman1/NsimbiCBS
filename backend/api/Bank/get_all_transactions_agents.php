<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$transaction = new Loan($db);
$transaction->filter_approved_by_id = @$_REQUEST['approved_by_id'];

try {
   
            $recordsTotal = count($transaction->getAllAgentDeposits()->fetchAll(PDO::FETCH_ASSOC));
        
    

    $records = $transaction->getAllAgentDeposits();

    echo json_encode(['draw' => (int)@$_REQUEST['draw'], "recordsTotal" => $recordsTotal, "recordsFiltered" => $records, "data" => $records, "input" => array("draw" => (int)@$_REQUEST['draw'], "length" => (int)@$_REQUEST['length'])]);
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
}
return;
