<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);

if ($_GET['branch'] != '') {
    $item->branchId = $_GET['branch'];
    $stmt = $item->getBranchStaffExcess();
} else {
    $item->branchId = $_GET['bank'];
    $stmt = $item->getBankStaffExcess();
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
        if ($status == 2) {
            $btn = '<span class="badge badge-rounded badge-primary">Cleared</span>';
        } else if ($status == 1) {
            $btn = '<span class="badge badge-rounded badge-danger">pending</span>';
        } else if ($status == 4) {
            $btn = '<span class="badge badge-rounded badge-warning">partially Cleared</span>';
        }
        $u = array(
            "id" => $staff_id,
            "notes" => $narration,
            "status" => $btn,
            "date" => $date_created,
            "amount" => number_format($amount),
            "amount_paid" => number_format($amount_paid ?? 0),
            "name" => $item->getStaffNames($staff_id),
            "cash" => $item->getAccountDetails($affected_cash_acid),
            "journal" => $item->getAccountDetails($affected_acid),
            "branch" => $item->getBranchName($_branch),

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
                                            <a class="dropdown-item text-primary"
                                                href="clear_excess.php?id=' . $ss_id . '"> <i class="fa fa-edit"></i>Clear Shortfall </a>
                                                <a class="dropdown-item text-danger"
                                                href="delete_excess.php?id=' . $ss_id . '"> <i class="fa fa-trash"></i>Delete Entry </a>

                                               
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
    $userArr['message'] = "No Excess found !";
    http_response_code(200);
    echo json_encode($userArr);
}
