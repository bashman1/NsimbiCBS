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

$item->id = $data->id;

$stmt = $item->getShareAccDetails();
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
            
            "_account_no" => $membership_no,
            "account_name" => $client_name,
            "tot_shares" => $no_shares,
            "tot_amount" => $share_amount,
            "branch_name" => $bname,
            "address" => '',

        );


        array_push($userArr['data'], $u);
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = $item->password;
    $userArr['message'] = "No Transactions!";
    $u = array(
        
        "_account_no" => '',
        "account_name" => '',
        "tot_shares" => '',
        "tot_amount" => '',
        "branch_name" => '',
        "address" => '',

    );


    array_push($userArr['data'], $u);
    http_response_code(200);
    echo json_encode($userArr);
}
