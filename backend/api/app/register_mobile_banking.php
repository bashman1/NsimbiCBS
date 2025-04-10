<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/MobileApp.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new MobileApp($db);


$item->phone_number = isset($_GET['phone']) ? $_GET['phone'] : die();
$item->macno = isset($_GET['acno']) ? $_GET['acno'] : die();

$stmt = $item->registerMobileBanking();

if ($stmt != '') {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['message'] = "You've registered for Mobile Banking successfully !";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "names" => '' . ' ' . @$firstName . ' ' . @$lastName . @$shared_name,
            "phone" => @$primaryCellPhone,
            "acno" => @$membership_no,

        );

        array_push($userArr['data'], $u);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "401";
    $userArr['message'] = "Registration failed !";

    http_response_code(200);
    echo json_encode($userArr);
}
