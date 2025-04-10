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
    $item->createdAt = $data->id;
    $item->updatedAt = $data->rate;
    $item->deletedAt = $data->notes;
    $item->name = $data->duration;
    $item->location = $data->amount;
    $item->serialNumber = $data->uid;
    $item->loan_id = $data->freq;
$item->approval_date = $data->ddate;
   
        $stmt = $item->approveLoan();
   

    if($stmt)
    {
        
         $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
        http_response_code(200);
        echo json_encode($userArr);
    }else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="No loan found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
