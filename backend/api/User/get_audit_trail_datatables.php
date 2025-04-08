<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/AuditTrail.php';
require_once '../ApiResponser.php';

$ApiResponse = new ApiResponser();

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$audit_trail = new AuditTrail($db);

$audit_trail->filter_search_string = @$_REQUEST['search']['value'] ? trim(@$_REQUEST['search']['value']) : "";
$audit_trail->filter_branch_id = @$_REQUEST['branchId'] ?? @$_GET['branch'];
$audit_trail->filter_actype = @$_REQUEST['actype'];
$audit_trail->filter_start_date  = @$_REQUEST['start_date'];
$audit_trail->filter_end_date  = @$_REQUEST['end_date'];
$audit_trail->filter_staff_id  = @$_REQUEST['staff_id'];

$audit_trail->filter_per_page = @$_REQUEST['length'];
$audit_trail->filter_page = @$_REQUEST['start'];

$audit_trail->is_datatables = true;

// var_dump($audit_trail->filter_start_date);
// var_dump($audit_trail->filter_end_date);
// exit;

$audit_trail->bankId = @$_GET['bankId'];
// $audit_trail->filter_branch_id = @$_GET['branch'] ?? @$_GET['branchId'];

try {
    $records = $audit_trail->get();
    if ($audit_trail->filter_branch_id) {
        $recordsTotal =  count($audit_trail->getBranchAuditTrail());
    } else if($audit_trail->bankId){
        $recordsTotal =  count($audit_trail->getBankAuditTrail());
    }else{
        $recordsTotal =  count($audit_trail->getSystemAuditTrail());
    }

    if ($audit_trail->filter_search_string) {
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
