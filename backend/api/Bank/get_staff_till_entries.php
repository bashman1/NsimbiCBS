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

$stmt = $item->getStaffRangeTransactions();

$cash_acc_details = $item->getCashAccountDetails($data->staff);
$bf = $item->getAccountBf($data->staff, $data->start);
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
            "is_reversal" => $is_reversal??0,
            "ref" => strtolower($t_type . '-ref-' . $meth . '-' . $tid . '-' . $_authorizedby),
            "_account_no" => $membership_no,
            "mid" => $mid ?? '',
            "entry_channel" => $entry_channel ?? '',
            "acc_name" => $acc_name ?? '',
            "_actionby" => $_actionby ?? '',
            "cash_acc_details" => $cash_acc_details,
            "account_name" => $acc_name,
            "_authorisedby" => $firstName . " " . $lastName,
            "_paidby_name" => $_actionby,
            "_paidby_phone" => '',
            "_amount" => $amount,
            "loan_interest" => $loan_interest,
            "outstanding_amount_total" => $outstanding_amount_total,
            "outstanding_interest_total" => $outstanding_interest_total,
            "_reason" => $description,
            "_status" => $_status,
            "branch_name" => $name,
            "acc_balance" => $acc_balance,
            "loanswallet" => $loan_wallet,
            "_date_created" => $date_created,
            "address" => '',
            "type" => $t_type,
            "cr_acid" => $cr_acid??'',
            "dr_acid" => $dr_acid??'',
            "cr_dr" => $cr_dr,
            "mdate" => $date_created,
            "freezed_amount" => $freezed_amount,
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
