<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';

$ApiResponse = new ApiResponser();

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$client = new Bank($db);

$client->filter_search_string = @$_REQUEST['search']['value'] ? trim(@$_REQUEST['search']['value']) : "";
$client->filter_branch_id = @$_REQUEST['branchId'];
$client->filter_client_type = @$_REQUEST['client_type'];
$client->filter_gender  = @$_REQUEST['gender'];
$client->filter_actype = @$_REQUEST['actype'];
$client->filter_start_date  = @$_REQUEST['start_date'];
$client->filter_end_date  = @$_REQUEST['end_date'];

$client->filter_per_page = @$_REQUEST['length'];
$client->filter_page = @$_REQUEST['start'];
$client->client_type_section = @$_REQUEST['client_type_section'];

// var_dump($client->filter_start_date);
// var_dump($client->filter_end_date);
// exit;

$client->bank = @$_GET['bank'];
$client->branch = @$_GET['branch'];
$client->with_active_status = false;

try {
    $records = $client->getBankClientsDatatable()->fetchAll(PDO::FETCH_ASSOC);
    if ($client->bank) {
        $recordsTotal =  count($client->getBankClients2()->fetchAll(PDO::FETCH_ASSOC));
    } else {
        $recordsTotal =  count($client->getBranchClients2()->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($client->filter_search_string) {
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
