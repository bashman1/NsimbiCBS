<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Transaction.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Transaction($db);

    $data = json_decode(file_get_contents("php://input"));

    $item->mid = $data->client;
    $item->amount = $data->amount;
    $item->description = $data->reason;
    $item->_actionby = $data->deposited;
    $item->_branch = $data->branch;
    $item->_authorizedby = $data->user;
    
   
    
    if($item->createDeposit())
    {
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['message']="Deposit created successfully !";
        http_response_code(200);
        echo json_encode($userArr);
    } else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="Deposit not created !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
