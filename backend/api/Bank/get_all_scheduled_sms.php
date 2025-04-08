<?php
require_once __DIR__ . '../../RequestHeaders.php';

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
    $stmt = $item->getAllBranchSMSTasks();
} else if ($_GET['bank'] != '') {
    $stmt = $item->getAllBankSMSTasks();
} else {
    $stmt = $item->getAllSystemSMSTasks();
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
        $stt = '';
        if ($s_status == 0) {
            $stt = '<span class="badge light badge-danger">Pending</span>';
        }
        if ($s_status == 1) {
            $stt = '<span class="badge light badge-primary">Finished</span>';
        }
        if ($s_status == 3) {
            $stt = '<span class="badge light badge-danger">Cancelled</span>';
        }
        $u = array(
            "id" => $s_id,
            "key" => $s_key,
            "senderid" => $sender_id,
            "charge" => number_format($sms_charge),
            "body" => $s_body,
            "bank" => $branch_id ? $item->getBankName($branch_id, 'branch') : $item->getBankName($bank_id, 'bank'),
            "audience" => $item->getsmstaskaudience($s_id),
            "status" => $stt,
            "sdate" => date('d-m-Y', strtotime($s_date)),



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
    $userArr['message'] = "No Tasks found !";
    http_response_code(200);
    echo json_encode($userArr);
}
