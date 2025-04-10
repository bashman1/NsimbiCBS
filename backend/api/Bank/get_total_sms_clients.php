<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$data = json_decode(file_get_contents("php://input"));

$item->bank = $data->bank;
$item->branch = $data->branch;


$stmt = $item->bank=='' ? $item->getTotalBranchSMSClients2() : $item->getTotalBankSMSClients2();

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
  
  
        $u = array(
            "total" => $stmt,
        );


        array_push($userArr['data'], $u);

    
    http_response_code(200);
    echo json_encode($userArr);

