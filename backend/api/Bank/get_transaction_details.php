<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$data = json_decode(file_get_contents("php://input"));
$item->branchId = $data->tid;

$stmt = $item->getTransactionDetails();


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
            "id" => $tid,
            "meth" => $pay_method,
            "name" => $firstName . ' ' . $lastName.''.$shared_name,
            "acno" => $membership_no,
            "amount" => number_format($amount),
            "auth" => $item->getStaffDetails($_authorizedby),
            "authid" => $_authorizedby,
            "description" => 'Deposit: ' . $description,
            "actionby" => $_actionby,
            "mid" => $mid,

            "bank" => $item->getBankDetails($_branch),

            "d" => normal_date_short($date_created),
            "sname" => $sname ?? '',
            "sno" => $sno ?? '',
            "sclass" => $sclass ?? '',
            "sterm" => $sterm ?? '',




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
    $userArr['message'] = "No Transaction found !";
    http_response_code(200);
    echo json_encode($userArr);
}
