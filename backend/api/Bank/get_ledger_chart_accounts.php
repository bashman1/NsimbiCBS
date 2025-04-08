<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
if ($_GET['branch'] == '') {
    $item->id = $_GET['bank'];
    $stmt = $item->getAllBankAccountsLedger($_GET['acc']);
} else {

    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchAccountsLedger($_GET['acc']);
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
            "id" => $aid,
            "name" => $aname,
            "balance" => $balance,
            "type" => $type,
            "description" => $description,
            "branch" => $bname


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
    $userArr['message'] = "No Accounts found !";
    http_response_code(200);
    echo json_encode($userArr);
}
