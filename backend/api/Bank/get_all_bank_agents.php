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
    $item->id = $_GET['id'];
    $stmt = $item->getAllBankAgents();
} else {

    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchAgents();
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
        $members = $item->getAgentActiveMembersToday($suserid) ?? 0;
        $membersC = $item->getAgentMembershipCommision($suserid) ?? 0;
        $deposits = $item->getAgentTotalDeposits($suserid);
        $loan_repays = $item->getAgentTotalLoanRepays($suserid);
        $deposits_count = $item->getAgentCountDeposits($suserid);

        $total_amount = $deposits + $loan_repays;

        $allowance = 200 * $deposits_count;
        $commision = 0.075 * $membersC;

        $nn = $firstName . ' ' . $lastName . ' - ' . $email . ' : ' . $primaryCellPhone;

        $u = array(
            "count" => $count,
            "id" => $idx,
            "name" => $nn,
            "branch" => is_null($branchId) ? '' : $item->getBranchName2($branchId),
            "customers_served" => '<a class="text-danger" href="agent_deposits.php?id=' . $suserid . '&n=' . $nn . '&amount=' . $deposits . '">' . number_format($deposits_count) . '</a>',
            "deposits" => '<a class="text-danger" href="agent_deposits.php?id=' . $suserid . '&n=' . $nn . '&amount=' . $total_amount . '">' . number_format($deposits) . '</a>',
            "loan" => '<a class="text-danger" href="agent_deposits.php?id=' . $suserid . '&n=' . $nn . '&amount=' . $total_amount . '">' . number_format($loan_repays) . '</a>',
            "members" => '<a class="text-primary" href="agent_new_members.php?id=' . $suserid . '&n=' . $nn . '">' . number_format($members) . '</a>',
            "allowance" => '<a class="text-danger" href="agent_deposits.php?id=' . $suserid . '&n=' . $nn . '&amount=' . $total_amount . '">' . number_format($allowance) . '</a>',
            "commision" => '<a class="text-primary" href="agent_new_members.php?id=' . $suserid . '&n=' . $nn . '">' . number_format($commision) . '</a>',
            "total_pay" => number_format($allowance + $commision),
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
                        href="agent_new_members.php?id=' . $suserid . '&n=' . $nn . '">View New Members</a>
                        <a class="dropdown-item"
                        href="agent_deposits.php?id=' . $suserid . '&n=' . $nn . '&amount=' . $total_amount . '">View Deposits</a>
                       
                  
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
    $userArr['message'] = "No Agents found !";
    http_response_code(200);
    echo json_encode($userArr);
}
