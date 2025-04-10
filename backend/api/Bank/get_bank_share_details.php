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
    $stmt = $item->getAllBankShareDetails($_GET['bank']);
} else {
    $stmt = $item->getAllBranchShareDetails($_GET['branch']);
}

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
            "share_holders" => $countern,
            "shares" => $counterr,
            "shareamount" => $counteramount,
            "sharesdividends" => $countershares,
            "savings" => $countersavings,
            "non_shares" => 0,


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
    $userArr['message'] = "No Value found !";
    http_response_code(200);
    echo json_encode($userArr);
}
