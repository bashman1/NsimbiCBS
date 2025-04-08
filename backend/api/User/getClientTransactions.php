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

$item->by_tid = $data->by_tid;
$item->by_date = $data->by_date;

$stmt = $item->getClientRangeTransactions();
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
            "_did" => $tid,
            "_auth" => $_authorizedby,
            "ref" =>
            $t_type . '-ref-' . $pay_method . '-' . $tid . '-' . $_authorizedby,
            "_account_no" => $membership_no,
            "account_name" => $c_name,
            "_authorisedby" => $firstName . " " . $lastName,
            "_paidby_name" => $_actionby,
            "_paidby_phone" => '',
            "_amount" => $amount,
            "loan_interest" => $loan_interest,
            "outstanding_amount_total" => $outstanding_amount_total,
            "outstanding_interest_total" => $outstanding_interest_total,
            "_reason" => $is_reversal ? ('Reversal - TID.' . $reversal_tid . ': ' . $description . ' by ' . $_actionby) : ($description . ' by ' . $_actionby),
            "_status" => $_status,
            "branch_name" => $name,
            "acc_balance" => $acc_balance,
            "loanswallet" => $loan_wallet,
            "_date_created" => $date_created,
            "cheque_no" => $cheque_no,
            "address" => '',
            "type" => $t_type,
            "entry_chanel" => $tchanel ?? '',
            "pay_method" => $pay_method ?? 'cash',
            "mdate" => $date_created,
            "freezed_amount" => $freezed_amount,
            "over_draft" => $over_draft ?? 0,
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
        "_did" => '',
        "_account_no" => '',
        "account_name" => '',
        "_authorisedby" => '',
        "_paidby_name" => '',
        "_paidby_phone" => '',
        "_amount" => '',
        "loan_interest" => '',
        "outstanding_amount_total" => '',
        "outstanding_interest_total" => '',
        "_reason" => '',
        "_status" => '',
        "branch_name" => '',
        "acc_balance" => '',
        "loanswallet" => '',
        "_date_created" => '',
        "address" => '',
        "type" => '',
        "mdate" => '',
        "freezed_amount" => '',

    );


    array_push($userArr['data'], $u);
    http_response_code(200);
    echo json_encode($userArr);
}
