<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new User($db);

$data = json_decode(file_get_contents("php://input"));

$item->id = $data->staff;
$item->createdAt = $data->start;
$item->updatedAt = $data->end;

$stmt = $item->getSafeRangeTransactions();


$cash_acc_details = $item->getAccountName($data->staff);
$bf = $item->getAccountBf2($data->staff, $data->start);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $meth = $pay_method ?? 'cash';
        $u = array(
            "_did" => $tid,
            "bf" => $bf,
            "ref" => strtolower($t_type . '-ref-' . $meth . '-' . $tid . '-' . $_authorizedby),
            "cr_acc" => is_null($cr_acid) ? '' : $item->getAccountName($cr_acid),
            "dr_acc" => is_null($dr_acid) ? '' : $item->getAccountName($dr_acid),

            "_amount" => $amount,

            "_reason" => $description,
            "_status" => $_status,
            "branch_name" => $name,

            "_date_created" => $date_created,

            "type" => $t_type,
            "cr_acid" => @$cr_acid??'',
            "dr_acid" => @$dr_acid??'',
            "cr_dr" => $cr_dr,
            "mdate" => $date_created,
        );


        array_push($userArr['data'], $u);
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = $item->password;
    $userArr['message'] = "No Transactions!";

    http_response_code(200);
    echo json_encode($userArr);
}
