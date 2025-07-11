<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->lno = $_GET['cid'];
$stmt = $item->getClientSaccoDetails();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $tname = $item->getBankTradeName($bankId);
        $u = array(
            "name" => $firstName ? 'A/C: ' . $membership_no . ', Name: ' . $firstName . ' ' . $lastName . ',  Branch: ' . $name.' - '.$tname : 'A/C: ' . $membership_no . ', Name: ' . $shared_name . ',  Branch: ' . $name
            . ' - ' . $tname,

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
    $userArr['message'] = "No Institutions found !" . $item->lno;
    http_response_code(200);
    echo json_encode($userArr);
}
