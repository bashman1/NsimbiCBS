<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['branch'];
$item->createdById = $_GET['bank'];
if ($_GET['branch'] != '') {
    $stmt = $item->getAllBranchShareTransferTrxns();
} else if ($_GET['bank'] != '') {
    $stmt = $item->getAllBankShareTransferTrxns();
}


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $u = array(
            "id" => $tr_id,
            "from" => $item->getMemberNames($from_uid),
            "to" => $item->getMemberNames($to_uid),
            "shares" => number_format($no_shares),
            "shareamount" => number_format($current_share_value* $no_shares),
            "sharevalue" => number_format($current_share_value),
            "auth" => $item->getUserNames($added_by),
            "notes" => $description??'',
            "dateCreated" => date('d-m-Y', strtotime($record_date)),
        );



        array_push($userArr['data'], $u);

        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No trxns found !";
    http_response_code(200);
    echo json_encode($userArr);
}
