<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$transaction = new Loan($db);
$transaction->branchId = $_REQUEST['branch'];
$transaction->bankId = $_REQUEST['bank'];
$transaction->createdById = $_REQUEST['bank'];

$transaction->filter_branch_id = @$_REQUEST['branch'];
$transaction->filter_approved_by_id = @$_REQUEST['auth'];
$transaction->filter_transaction_start_date = @$_REQUEST['start_date'];
$transaction->filter_transaction_end_date = @$_REQUEST['end_date'];

$transaction->filter_sub_account_id = @$_REQUEST['acid'];

$transaction->filter_search_string = @$_REQUEST['search']['value'] ? trim(@$_REQUEST['search']['value']) : "";
$transaction->filter_per_page = @$_REQUEST['length'];
$transaction->filter_page = @$_REQUEST['start'];

try {
    if ($transaction->branchId) {

        $recordsTotal = count($transaction->getAllBranchJournalEntries()->fetchAll(PDO::FETCH_ASSOC));
    } else {

        $recordsTotal = count($transaction->getAllBankJournalEntries()->fetchAll(PDO::FETCH_ASSOC));
    }

    $records = $transaction->getBankJournalEntriesDatatables();

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
