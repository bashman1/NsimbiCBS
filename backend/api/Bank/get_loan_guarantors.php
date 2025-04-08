<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$data = json_decode(file_get_contents("php://input"));
// $item->lno = $data->id;
$stmt = $item->getLoanGuarantors($data->id);
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
            "gid" => $gid,
            "_mid" => $_mid,
            "_loanid" => $_loanid,
            "_gstatus" => $_gstatus,
            "init" => $guarantor_initials,
            "attachment" => $attachment ?? '',
            "_date_created" => $_date_created,
            "name" => is_null($firstName) ? $description : $firstName . ' ' . $lastName,
            "membership_no" => is_null($membership_no) ? 'Non-Member' : $membership_no,






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
