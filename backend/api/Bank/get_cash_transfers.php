<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);

if ($_GET['branch'] != '') {
    $item->branchId = $_GET['branch'];
    $stmt = $item->getBranchTransfers();
} else {
    $item->branchId = $_GET['bank'];
    $stmt = $item->getBankTransfers();
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
            "tid" => $tid,
            "sender" => $item->getMemberNames($acid),
            "receiver" => $item->getMemberNames($mid),
            "amount" => number_format($amount),
            "auth" => $item->getUserNames($_authorizedby),
            "date" => normal_date($date_created),
            "branch" => $item->getBranchName($_branch),

            "notes" => $description,


            "actions" => ' <div class="d-flex">
              
                   
                    
                </div>',

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
    $userArr['message'] = "No Fees found !";
    http_response_code(200);
    echo json_encode($userArr);
}
