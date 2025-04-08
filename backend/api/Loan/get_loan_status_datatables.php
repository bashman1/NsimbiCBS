<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$loan = new Loan($db);
$loan->branchId = $_GET['branch'];
$loan->bankId = $_GET['bankId'];
$loan->createdById = $_GET['bankId'];
$loan->status = $_REQUEST['loan_status'] ?? 'active';


/**
 * general filters
 */
$loan->filter_branch_id = @$_REQUEST['branchId'];
// $loan->filter_loan_status = @$_REQUEST['loan_status'];
$loan->filter_loan_product_id = @$_REQUEST['loan_product_id'];
$loan->filter_disbursement_start_date = @$_REQUEST['disbursement_start_date'];
$loan->filter_disbursement_end_date = @$_REQUEST['disbursement_end_date'];

$loan->filter_closing_start_date = @$_REQUEST['closing_start_date'];
$loan->filter_closing_end_date = @$_REQUEST['closing_end_date'];

$loan->filter_transaction_start_date = @$_REQUEST['trxn_start_date'];
$loan->filter_transaction_end_date = @$_REQUEST['trxn_end_date'];
$loan->filter_loan_status = @$_REQUEST['loan_status'];

/**
 * datatables filters
 */
$loan->filter_search_string = @$_REQUEST['search']['value'] ? trim(@$_REQUEST['search']['value']) : "";
$loan->filter_per_page = @$_REQUEST['length'];
$loan->filter_page = @$_REQUEST['start'];

$loan->with_payment_totals = true;

// var_dump($loan->filter_next_due_date);
// exit;

try {

    /**
     * Handles transactions at branch level
     */
    if ($loan->bankId) {
        $recordsTotal = count($loan->getAllBankLoansActive()->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Handles transactions at branch level
     */
    else {
        $recordsTotal = count($loan->getAllBranchLoansActive()->fetchAll(PDO::FETCH_ASSOC));
    }

    $records = $loan->getLoansDatatables();

    if ($loan->filter_search_string) {
        $recordsFiltered = count($records);
    } else {
        $recordsFiltered = $recordsTotal;
    }

    echo json_encode(['draw' => (int)@$_REQUEST['draw'], "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, "data" => $records, "input" => array("draw" => (int)@$_REQUEST['draw'], "length" => (int)@$_REQUEST['length'])]);
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
    //throw $th;
}
return;
