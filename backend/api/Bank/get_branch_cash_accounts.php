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
    $stmt = $item->getAllBankStaffCashAccounts();
} else {
    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchStaffCashAccounts();
}

$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "id" => $sidd,
            "cid" => $item->getCashAccountCid($sidd),
            "staff" => $item->getStaffDetails($userid),
            "acname" => $acc_name,
            "branch" => $name,
            "balance" => $item->getCashAccountBalance($sidd),
            // "is_main_account" => $item->checkIsMainAcc($sidd),
            "userid" => $userid,
            "status" => $status == 1 ? '<span class="badge badge-rounded badge-primary">Active</span>' : '<span class="badge badge-rounded badge-danger">Deactivated</span>',
            "currency" => $currency,
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
                    <a class="dropdown-item"
                        href="edit_cash_account?id=' . $sidd . '">Edit Cash Account</a>
                    <a class="dropdown-item text-danger"
                        href="deactivate_cash_account?id=' . $sidd . '">Deactivate Cash Account</a>
                </div>
            </div>
                ',


        );



        array_push($userArr['data'], $u);
        $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No A/Cs found !";
    http_response_code(200);
    echo json_encode($userArr);
}
