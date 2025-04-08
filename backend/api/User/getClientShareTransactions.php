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

$item->id = $data->id;
$item->createdAt = $data->start;
$item->updatedAt = $data->end;

$stmt = $item->getClientRangeShareTransactions();
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
            "tid" => $id,
            "ref" => 'sh-ref-' . $pay_method . '-' . $id . '-' . $added_by,
            "shares" => $no_of_shares,
            "amount" => $amount,
            "notes" => $notes,
            "share_value" => $current_share_value,
            "date_created" => $record_date,
            "date_added" => $date_added,
            "entry_chanel" => $entry_chanel,
            "_account_no" => $memb_no,
            "account_name" => $c_name,
            "tot_shares" => $tot_sh,
            "tot_amount" => $tot_amount,
            "branch_name" => $bname,
            "address" => '',

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
    $u = array(
        "tid" => '',
        "ref" => '',
        "shares" => '',
        "amount" => '',
        "notes" => '',
        "share_value" => '',
        "date_created" => '',
        "date_added" => '',
        "entry_chanel" => '',
        "_account_no" => '',
        "account_name" => '',
        "tot_shares" => '',
        "tot_amount" => '',
        "branch_name" => '',
        "address" => '',

    );


    array_push($userArr['data'], $u);
    http_response_code(200);
    echo json_encode($userArr);
}
