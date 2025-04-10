<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

if ($_GET['bank'] != '') {
    $item->id = $_GET['bank'];
    $stmt = $item->getAllBranches();
} else {
    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchBranches();
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
            "id" => $id,
            "name" => $name,
            "bcode" => $bcode,
            "location" => $location,
            "openingdate" => date('d-m-Y', strtotime($createdAt)),
        );


        array_push($userArr['data'], $u);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Branches found !";
    http_response_code(200);
    echo json_encode($userArr);
}
