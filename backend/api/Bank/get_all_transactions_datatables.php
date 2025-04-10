<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$transaction = new Loan($db);
$transaction->branchId = $_REQUEST['branch'];
$transaction->bankId = $_REQUEST['bankId'];
$transaction->createdById = $_REQUEST['bankId'];
$transaction->transaction_type = $_REQUEST['transaction_type'];

$transaction->filter_branch_id = @$_REQUEST['branchId'];
$transaction->filter_deposit_method = @$_REQUEST['deposit_method'];
$transaction->filter_transaction_method = @$_REQUEST['transaction_method'];
$transaction->filter_withdraw_method = @$_REQUEST['withdraw_method'];
$transaction->filter_approved_by_id = @$_REQUEST['approved_by_id'];
$transaction->filter_transaction_start_date = @$_REQUEST['start_date'];
$transaction->filter_transaction_end_date = @$_REQUEST['end_date'];
$transaction->filter_loan_amount = @$_REQUEST['loan_amount'];

$transaction->filter_sub_account_id = @$_REQUEST['sub_account_id'];

$transaction->filter_search_string = @$_REQUEST['search']['value'] ? trim(@$_REQUEST['search']['value']) : "";
$transaction->filter_per_page = @$_REQUEST['length'];
$transaction->filter_page = @$_REQUEST['start'];

try {
    if ($transaction->branchId) {
        if ($transaction->transaction_type == "D") {
            $recordsTotal = count($transaction->getAllBranchDeposits()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($transaction->transaction_type == "W") {
            $recordsTotal = count($transaction->getAllBranchWithdraws()->fetchAll(PDO::FETCH_ASSOC));
        } else {
            $recordsTotal = count($transaction->getAllBranchTransactions());
        }
    } else {
        if ($transaction->transaction_type == "D") {
            $recordsTotal = count($transaction->getAllBankDeposits()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($transaction->transaction_type == "W") {
            $recordsTotal = count($transaction->getAllBankWithdraws()->fetchAll(PDO::FETCH_ASSOC));
        } else {
            $recordsTotal = count($transaction->getAllBankTransactions());
        }
    }

    $records = $transaction->getBankTransactionsDatatables();

    if ($transaction->filter_search_string) {
        $recordsFiltered = count($records);
    } else {
        $recordsFiltered = $recordsTotal;
    }

    echo json_encode(['draw' => (int)@$_REQUEST['draw'], "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, "data" => $records, "input" => array("draw" => (int)@$_REQUEST['draw'], "length" => (int)@$_REQUEST['length'])]);
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
}
return;
