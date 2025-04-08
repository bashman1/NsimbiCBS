<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
// $data = json_decode(file_get_contents("php://input"));
$item->branchId = $_GET['tid'];

$stmt = $item->getTransactionDetails();


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $title = 'Trxn Details:- ';
        if ($t_type == 'D') {
            $title1 =  '  Deposit';
        }
        if ($t_type == 'W') {
            $title1 =   '  Withdraw';
        }
        if ($t_type == 'I' || $t_type == 'R') {
            $title1 = '  Income Entry';
        }
        if ($t_type == 'L') {
            $title1 =   '  Loan Repayment';
        }
        if ($t_type == 'A') {
            $title1 =   '  Loan Disbursement';
        }
        if ($t_type == 'E') {
            $title1 =   '  Expense Entry';
        }
        if ($t_type == 'LIA') {
            $title1 =   '  Liability Entry';
        }
        if ($t_type == 'CAP') {
            $title1 =   '  Capital Entry';
        }
        if ($t_type == 'AJE') {
            $title1 =   '  Advanced Journal Entry';
        }
        if ($t_type == 'ASS') {
            $title1 =   '  Asset Entry';
        }
        if ($t_type == 'BTB' || $t_type == 'BTS' || $t_type == 'STB' || $t_type == 'TTT' || $t_type == 'TTS' || $t_type == 'STT') {
            $title1 =   '  Cash Transfer';
        }
        $verify_label = '';
        if ($verify == 0) {
            $verify_label = '<span class="badge light badge-danger">No</span>';
        } else {
            $verify_label = '<span class="badge light badge-primary">Yes</span>';
        }

        $reversal_label = '';
        if ($is_reversal == 0) {
            $reversal_label = '<span class="badge light badge-danger">No</span>';
        } else {
            $reversal_label = '<span class="badge light badge-primary">Yes</span>';
        }

        $u = array(
            "id" => $tid,
            "lid" => $loan_id ?? 0,
            "title" => $title . $title1,
            "type" => $t_type,
            "meth" => $pay_method,
            "name" => $firstName . ' ' . $lastName,
            "acno" => $membership_no,
            "verify" => $verify_label,
            "reversal" => $reversal_label,
            "amount" => number_format($amount),
            "interest" => number_format($loan_interest ?? 0),
            "penalty" => number_format($loan_penalty ?? 0),
            "charges" => number_format($charges),
            "auth" => $item->getStaffDetails($_authorizedby),
            "actype" => $item->getUserActype($mid),
            "acid" => $acid ? $item->getAccountDetails($acid) : '',
            "cr_acid" => $cr_acid && is_valid_uuid($cr_acid) ? $item->getAccountDetails($cr_acid) : '',
            "dr_acid" => $dr_acid && is_valid_uuid($dr_acid) ? $item->getAccountDetails($dr_acid) : '',
            "cash_acc" => $cash_acc && is_valid_uuid($cash_acc) ? $item->getAccountDetails($cash_acc) : '',
            "authid" => $_authorizedby,
            "description" => $title1 . ': ' . $description,
            "actionby" => $_actionby,
            "mid" => $mid,
            "old_ref" => $trxn_ref ?? '---',
            "ref" =>
            $t_type . '-ref-' . $pay_method . '-' . $tid . '-' . $_authorizedby,

            "branch" => $item->getBranchDetails($_branch),

            "d" => date('d-m-Y', strtotime($date_created)),



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
    $userArr['message'] = "No Transaction found !";
    http_response_code(200);
    echo json_encode($userArr);
}
