<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$data = json_decode(file_get_contents("php://input"));
// $item->lno = $data->id;
$stmt = $item->getLoanCollaterals($data->id);
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
            "_cid" => $_cid,
            "loanid" => $loanid,
            "_status" => $_status,
            "_collateral" => $_collateral,
            "_mvalue" => $_mvalue,
            "_date_created" => $_date_created,
            "_attachment" => $_attachment,
            "cat" => $cat,
            "_receivedby" => $firstName.' '.$lastName,
            "_releasedby" => $_releasedby,
            "_fvalue" => $_fvalue,
            "_location" => $_location,
            "_catname" => $_catname,

          




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
    $userArr['message'] = "No Guarantors found !";
    http_response_code(200);
    echo json_encode($userArr);
}
