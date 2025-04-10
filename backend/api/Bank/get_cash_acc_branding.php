<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new User($db);

$data = json_decode(file_get_contents("php://input"));

if ($data->staff) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = 0;

    $u = array(
        "staff_names" => $item->getStaffNames($data->staff),
        "staff_role" => $item->getStaffRoleName($data->staff),
        "cash_acc_name" => $item->getCashAccName($data->staff),
        "cash_acc_balance" => $item->getCashAccBalance($data->staff),
        "staff_branch" => $item->getCashAccBranch($data->staff),
    );


    array_push($userArr['data'], $u);

    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = 204;
    $userArr['message'] = "No staff selected!";

    http_response_code(200);
    echo json_encode($userArr);
}
