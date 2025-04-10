<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$stmt = $item->getAllSchoolPaySchoolsSpecificSACCO($_REQUEST['bank']);

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
            "id" => $uid,
            "branch" => $branchId,
            "membership" => $membership_no,
            "sacco" => $item->getBankName2($branchId, 'branch'),
            "name" => $shared_name,
            "location" => @$addressLine1 . ', ' . @$addressLine2 . ', ' . @$village . ', ' . @$parish . ', ' . @$subcounty . ', ' . @$district,
            "email" => $email,
            "contact" => $primaryCellPhone . ' / ' . $secondaryCellPhone . ' / ' . $otherCellPhone,

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
    $userArr['message'] = "No Schools found !";
    http_response_code(200);
    echo json_encode($userArr);
}
