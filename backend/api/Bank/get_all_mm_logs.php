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
    $stmt = $item->getBranchMMLogs();
} else if ($_GET['bank'] != '') {
    $stmt = $item->getBankMMLogs();
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
            "id" => $log_id,
            "phone" => $log_phone ?? '',
            "amount" => number_format($log_trxn_amount ?? 0),
            "acno" => $log_acc_no ?? '',
            "acname" => $log_acc_name ?? '',
            "uid" => $log_uid ?? '',
            "refno" => $log_ext_ref_no ?? '',
            "descr" => $log_description ?? '',
            "message" => $log_message ?? '',
            "branch" => is_null($log_branch_id) ? '' : $item->getBranchName($log_branch_id),
            "date" => normal_date(@$log_date_created),
            "status" => @$log_status == 'SUCCEEDED' ? '<span class="badge badge-rounded badge-primary">Successful</span>' : '<span class="badge badge-rounded badge-danger">' . @$log_status . '</span>',


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
    $userArr['message'] = "No Logs found !";
    http_response_code(200);
    echo json_encode($userArr);
}
