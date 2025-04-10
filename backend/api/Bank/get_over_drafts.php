<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
if ($_GET['branch'] == '') {
    $item->id = $_GET['bank'];
    $stmt = $item->getAllBankOverDrafts();
} else {

    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchOverDrafts();
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
        $btn = '';
        $st = '';



        if ($ostatus == 0) {
            $btn = '<a class="dropdown-item delete-record"
                        href="approve_over_draft.php?id=' . $odid . '">Approve Over-Draft</a> <a class="dropdown-item delete-record"
                        href="decline_over_draft.php?id=' . $odid . '">Decline Over-Draft</a> <a class="dropdown-item delete-record"
                        href="trash_over_draft.php?id=' . $odid . '">Trash Over-Draft</a>';

            $st = '<span class="badge badge-rounded badge-danger">Pending</span>';
        }

        if ($ostatus == 1) {
            $st = '<span class="badge badge-rounded badge-success">Active</span>';
            $btn = '<a class="dropdown-item"  href="pay_over_draft.php?id=' . $odid  . '">Pay Over-Draft</a>
            ';
        }
        if ($ostatus == 2) {
            // declined
            $st = '<span class="badge badge-rounded badge-danger">Declined</span>';
        }
        if ($ostatus == 3) {
            // cleared
            $st = '<span class="badge badge-rounded badge-success">Cleared</span>';
        }
        $u = array(
            "id" => $odid,
            "client_name" => $firstName . ' ' . $lastName . ' ' . $shared_name,
            "acno" => $membership_no,
            "amount" => number_format($amount),
            "period" => number_format($duration) . ' ( ' . strtoupper($duration_type) . ' )',
            "princ_balance" => number_format($over_draft),
            "interest" => number_format($interest_accumulated),
            "interest_bal" => number_format($interest_accumulated - $interest_paid),
            "arrears_days" => $arrears_days,
            "penalty_total" => number_format($penalty_accumulated),
            "penalty_balance" => number_format($penalty_accumulated - $penalty_paid),
            "appln_date" => normal_date_short($trxn_date),
            "approval_date" =>  normal_date_short($approval_date),
            "maturity_date" =>  normal_date_short(date('Y-m-d', strtotime($approval_date . ' + ' . $duration . ' days'))),
            "branch" => $name,
            "product" => 'Daily',
            "status" => $st,
            "actions" => '
                <div class="dropdown custom-dropdown mb-0">
                <div class="btn sharp btn-primary tp-btn"
                    data-bs-toggle="dropdown">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                        height="18px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none"
                            fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <circle fill="#000000" cx="12" cy="5" r="2">
                            </circle>
                            <circle fill="#000000" cx="12" cy="12" r="2">
                            </circle>
                            <circle fill="#000000" cx="12" cy="19" r="2">
                            </circle>
                        </g>
                    </svg>
                </div>
                <div class="dropdown-menu dropdown-menu-end">
                ' . $btn . '
                    
        
                </div>
            </div>
                ',

        );


        array_push($userArr['data'], $u);
        // $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Over-Drafts found !";
    http_response_code(200);
    echo json_encode($userArr);
}
