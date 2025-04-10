<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/functions.php';
include_once '../../models/FieldAgents.php';

$database = new Database();
$db = $database->connect();



$item = new FieldAgents($db);


// $data = json_decode(file_get_contents("php://input"));

$item->account_id = $_GET['id'];
$stmt = $item->getAgentTransactions();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    //  $userArr['count']=$itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $acc = $item->getMemberProduct($mid);
        $byy = $item->getUserNames($_authorizedby);
        $u = array(
            "_did" => strval($tid),
            "_account_no" => $membership_no,
            "account_name" => $membership_no . ' : ' . $firstName . ' ' . $lastName . '-' . $acc,
            "_authorisedby" => $byy,
            "_amount" => number_format($amount),
            "loan" => number_format(
                $agent_loan_amount ?? 0
            ),
            "total_amount" => number_format($amount + ($agent_loan_amount ?? 0)),
            "_reason" => $description,
            "_status" => $_status == 0 ? '<span class="badge light badge-danger">Pending</span>' : '<span class="badge light badge-primary">Successful</span>',
            "acc_balance" => strval($acc_balance),
            "type" => $t_type,
            "_date_created" => normal_date($date_created),
            "wallet" => 'Via Agent',
            "pay_method" => strtoupper($pay_method),
            "actions" => '
                 <div class="d-flex">
              
                 <a href="approve_deposit.php?id=' . $tid . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                         class="fas fa-check-circle"></i></a>
                         <a href="trash_deposit.php?id=' . $tid . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                         class="fas fa-trash"></i></a>
                        
                 
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
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "204";
    $userArr['message'] = "No Transactions found !";

    http_response_code(200);
    echo json_encode($userArr);
}
