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
$item->lno = $data->id;
$stmt = $item->getLoanRepayments();
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
            "id" => $tid,
            "amount" => $t_type=='L' ? $amount + $loan_interest + $loan_penalty  : $amount,
            "principal" => $t_type == 'L' ? $amount??0 : 0,
            "penalty" => $loan_penalty??0,
            "interest" => $t_type == 'L' ? $loan_interest??0 : $amount,
            "ref" =>
            $t_type . '-ref-' . $pay_method . '-' . $tid . '-' . $_authorizedby,
            "date_created" => normal_date($date_created),




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
    $userArr['message'] = "No Repayments not found !";
    http_response_code(200);
    echo json_encode($userArr);
}
