<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$item->account_id = $_GET['acid'];
$item->createdAt = $_GET['start_date'];
$item->deletedAt = $_GET['end_date'];
if ($_GET['branch'] == '') {
    $item->id = $_GET['bank'];
    $stmt = $item->getAllBankTransactionsIncomes();
} else {

    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchTransactionsIncomes();
}

$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $meth = $pay_method ?? 'cash';
        $ref = strtolower($t_type . '-ref-' . $meth . '-' . $tid . '-' . $_authorizedby);
        $u = array(
            "count" => $count,
            "id" => $tid,
            "date" => normal_date_short($date_created),
            "description" => $tdescription,
            "pay_method" => $meth,
            "actual_amount" => $amount ?? 0,
            "dr" =>  0,
            "ref_no" => '<span class="no_print clickable_ref_no" ref-no="' . $ref . '" tid="'.$tid.'">' . $ref . '</span>',
            "cr" => '<span class="text-danger">' . number_format($amount) . '</span>',
            "account" => is_null($cr_acid) ? $item->getChartAccountName($acid) : $item->getChartAccountName($cr_acid),
            "auth" => strtoupper($firstName . ' ' . $lastName),
            "branch" => strtoupper($bname),
            // "actions" => '<a href="trxn_details?id=' . $tid . '" class="text-primary light btn-xs mb-1"><i class="fa fa-eye"></i></a>'
            "actions" => ' '
        );


        array_push($userArr['data'], $u);
        $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Transactions found !";
    http_response_code(200);
    echo json_encode($userArr);
}
