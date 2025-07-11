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
    $stmt = $item->getAllBankOverDraftProducts();
} else {

    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchOverDraftProducts();
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

        $u = array(
            "id" => $odpid,
            "name" => $odpname,
            "max_amount" => $max_amount,
            "int_acc" => $item->getChartAccountDetails($affected_interest_acc),
            "pen_acc" => $item->getChartAccountDetails($affected_penalty_acc),
            "period_type" => $period_type,
            "max_period" => $max_period,
            "interest" => $interest_value . ' - ' . $interest_type . ' Per ' . $period_type,
            "penalty" => $penalty_value . ' - ' . $penalty_type . ' Per ' . $period_type,
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
                        href="edit_over_draft_product?id=' . $odpid . '">Edit</a>
                        <a class="dropdown-item"
                        href="trash_over_draft_product?id=' . $odpid . '">Trash</a>

                         <a class="dropdown-item"
                        href="view_all_product_over_drafts?id=' . $odpid . '">View Over-Drafts</a>
                   
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
    $userArr['message'] = "No Products found !";
    http_response_code(200);
    echo json_encode($userArr);
}
