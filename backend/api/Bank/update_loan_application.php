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

    $item->name = $data->client;
    $item->bank = $data->product;
    $item->branch = $data->disbursedate;
    $item->description =$data->startdate;
    $item->createdAt =$data->amount;
    $item->updatedAt =$data->duration;
    $item->deletedAt =$data->notes;
    $item->countryCode =$data->bank;
    $item->serialNumber =$data->branch;
    $item->pv = $data->user;
    $item->identificationNumber = $data->lno;
    
   
    
    if($item->updateLoanApplication())
    {
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['message']="Loan Application Updated successfully !";
        http_response_code(200);
        echo json_encode($userArr);
    } else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="Loan Application not Updated !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
?>