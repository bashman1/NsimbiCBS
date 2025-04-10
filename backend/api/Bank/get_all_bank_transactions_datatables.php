<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$bank = new Bank($db);
$bank->bankId = $_REQUEST['bankId'];
$bank->branchId = $_REQUEST['branch'];
$bank->id = $bank->bankId;
// if ($_GET['branch'] == '') {
//     $item->id = $_GET['bank'];
//     $stmt = $item->getAllBankTransactions();
// } else {

//     $item->id = $_GET['branch'];
//     $stmt = $item->getAllBranchTransactions();
// }

$bank->filter_per_page = @$_REQUEST['length'];
$bank->filter_page = @$_REQUEST['start'];

try {
    if ($bank->branchId) {
        $recordsTotal = count($bank->getAllBranchTransactions()->fetchAll(PDO::FETCH_ASSOC));
    } else {
        $recordsTotal = count($bank->getAllBankTransactions()->fetchAll(PDO::FETCH_ASSOC));
    }

    $records = $bank->getBankTransactionsDatatables();

    if ($bank->filter_search_string) {
        $recordsFiltered = count($records);
    } else {
        $recordsFiltered = $recordsTotal;
    }

    echo json_encode(['draw' => (int)@$_REQUEST['draw'], "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, "data" => $records, "input" => array("draw" => (int)@$_REQUEST['draw'], "length" => (int)@$_REQUEST['length'])]);
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
}

return;
