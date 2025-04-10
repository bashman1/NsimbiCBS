<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();
$data = json_decode(file_get_contents("php://input"));

$item = new Bank($db);
if ($data->branch == '') {
    $item->id = $data->bank;
    $stmt = $item->getAllBankStaffs();
} else {

    $item->id = $data->branch;
    $stmt = $item->getAllBranchStaffs2();
}

$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "count" => $count,
            "id" => $suserid,
            "email" => $email,
            "phone" => $primaryCellPhone,
            "name" => $firstName . ' ' . $lastName,
            "branch" => is_null($branchId) ? '' : $item->getBranchName($branchId),
            "position" => $positionTitle,
            "status" => '<span class="badge badge-rounded badge-primary">Active</span>',


        );



        array_push($userArr['data'], $u);
        $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Staff found !";
    http_response_code(200);
    echo json_encode($userArr);
}
