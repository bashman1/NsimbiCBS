<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$loan = new Loan($db);
$loan->bankId = $_REQUEST['bankId'];
$loan->branchId = $_REQUEST['branch'];

$loan->filter_branch_id = @$_REQUEST['branchId'];
$loan->filter_loan_status = @$_REQUEST['loan_status'];
$loan->filter_collateral_type_id = @$_REQUEST['collateral_type_id'];
$loan->filter_received_start_date = @$_REQUEST['received_start_date'];
$loan->filter_received_end_date = @$_REQUEST['received_end_date'];


/**
 * datatables filters
 */
$loan->filter_search_string = @$_REQUEST['search']['value'] ? trim(@$_REQUEST['search']['value']) : "";
$loan->filter_per_page = @$_REQUEST['length'];
$loan->filter_page = @$_REQUEST['start'];

try {

    if ($loan->branchId) {
        $recordsTotal = count($loan->getBranchCollaterals($_REQUEST['branch'])->fetchAll(PDO::FETCH_ASSOC));
    } else {
        $recordsTotal = count($loan->getBankCollaterals($_REQUEST['bankId'])->fetchAll(PDO::FETCH_ASSOC));
    }

    $records = $loan->getBankCollateralsDatatables();

    if ($loan->filter_search_string) {
        $recordsFiltered = count($records);
    } else {
        $recordsFiltered = $recordsTotal;
    }

    echo json_encode(['draw' => (int)@$_REQUEST['draw'], "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, "data" => $records, "input" => array("draw" => (int)@$_REQUEST['draw'], "length" => (int)@$_REQUEST['length'])]);
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
}
return;