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


if (@$data->bank || @$data->branch) {
    $stmt = $item->getActiveLoansReportData2(@$data->bank, @$data->branch, @$data->lpid, @$data->officer, @$data->st, @$data->end);
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract($row);
        // $u = array(
        //     "loans" => $item->getActiveLoansReportData(@$data->bank, @$data->branch)
        // );
        array_push($userArr['data'], $row);

        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No institution selected !";
    http_response_code(200);
    echo json_encode($userArr);
}
