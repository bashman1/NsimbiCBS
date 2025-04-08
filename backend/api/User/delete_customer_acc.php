<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new User($db);

$request = json_decode(file_get_contents("php://input"));

$item->details = $request->id;



if ($item->deleteCustomerAccount()) {
    $client_name = $item->getClientNames($request->id);
    $staff = $item->getStaffAccNames($request->user);

    $audit_info  = array(
        "action" => 'Account Deletion  - User ID: ' . @$item->details,
        "log_desc" => 'Deleted Account for: - ' . @$client_name . '  by Staff: ' . @$staff,
        "uid" => @$item->details,
        "branch" => @$request->branch,
        "bank" => @$request->bank,
        "ip" => '',
        "status" =>  'success',
    );

    // insert into audit trail
    $item->insertAuditTrail($audit_info);

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "Client Deleted successfully !";
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "Client not Deleted !";
    http_response_code(200);
    echo json_encode($userArr);
}
