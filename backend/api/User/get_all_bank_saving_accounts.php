<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);


$item->bank = $_GET['bank'];
$item->branch = $_GET['branch'];



if($_GET['branch']!=''){
    $stmt = $item->getBranchSavingAccounts();
}else{
    $stmt =  $item->getBankSavingAccount();
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
            "id" => $id,
            "name" => $name,
            "ucode" => $ucode,
            "min" => number_format($min_balance),
            "opening" => number_format($opening_balance),
            "rate" => $rate.' / '.$rateper,
            "clients" => $item->getAccountClients($id),
            "clients_today" => $item->getAccountClientsToday($id),
            "actions" => '
                <div class="d-flex">
                <a href="edit_saving_account.php?id=' . $id . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-eye"></i></a>
                    <a href="delete_saving_account.php?id=' . $id . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                    
                </div>
                ',

        );


        array_push($userArr['data'], $u);
        // array_push($userArr['sub'], $u2);
        $count++;
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = '204';
    $userArr['message'] = "No Account found !";

    http_response_code(200);
    echo json_encode($userArr);
}
