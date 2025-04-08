<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$item->id = $_GET['id'];
$stmt = $item->getAllDebtorReceivables();


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $st = '';
        if ($pay_status == 0) {
            $st = '<span class="badge badge-rounded badge-danger">Pending</span>';
        } else if ($pay_status == 1) {
            $st = '<span class="badge badge-rounded badge-warning">Partially Paid</span>';
        } else {
            $st = '<span class="badge badge-rounded badge-primary">Cleared</span>';
        }
        $u = array(
            "id" => $p_id,
            "amount" => number_format($p_amount),
            "description" => $p_descri,
            "comments" => $p_comments,
            "rdate" => normal_date_short($pay_trxn_date),
            "mdate" => normal_date_short($maturity_date),
            "branch" => $bname,
            "pmeth" => $p_pay_method,
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
                    <a class="dropdown-item"
                        href="pay_receivable.php?id=' . $item->id . '&rid='.$p_id.'">Make Payment</a>
                      
                          
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
    $userArr['message'] = "No Creditors found !";
    http_response_code(200);
    echo json_encode($userArr);
}
