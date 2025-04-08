<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['branch'];
$item->createdById = $_GET['bank'];
if ($_GET['branch'] != '') {
    $stmt = $item->getBranchInstitutions();
} else if ($_GET['bank'] != '') {
    $stmt = $item->getBankInstitutions();
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
        if ($school_pay == 0) {
            $btn = ' <a class="dropdown-item text-danger"
                        href="activate_school_pay.php?id=' . $uid . '">Subscribe to School Pay</a>';
        } else {
            $btn = ' <a class="dropdown-item text-danger"
                        href="deactivate_school_pay.php?id=' . $uid . '">UnSubscribe</a>';
        }
        $u = array(
            "id" => $uid,
            "acno" => $membership_no,
            "name" => $shared_name,
            "status" => $school_pay == 1 ? '<span class="badge badge-rounded badge-primary">Active</span>' : '<span class="badge badge-rounded badge-danger">Off</span>',
            "fees" => number_format($item->getTotalFeesCollected($uid)),
            "balance" => number_format($acc_balance),
            "contact" => $primaryCellPhone . ' / ' . $secondaryCellPhone . ' / ' . $otherCellPhone,
            "action" => '
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
                    <a class="dropdown-item"
                        href="view_fees_stmt.php?id=' . $uid . '">View Fees Statement</a>
                    ' . $btn . '
                </div>
            </div>
                ',
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
