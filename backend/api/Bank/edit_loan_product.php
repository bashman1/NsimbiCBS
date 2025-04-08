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

    $item->name = $data->name;
    $item->id = $data->lpid;
    $item->bank = $data->intrate;
    $item->branch = $data->freq;
    $item->description =$data->interestMethod;
    $item->createdAt =$data->penalty;
    $item->updatedAt =$data->fee;
    $item->deletedAt =$data->prate;
    $item->countryCode =$data->pfamount;
    $item->serialNumber =$data->gracedays;
    $item->identificationNumber =$data->maxdays;
    $item->pv = $data->bank;
    $item->auto_repay = $data->$auto_repay;
   $item->auto_penalty = $data->$auto_penalty;
   $item->round_off = $data->$round_off;
   $item->gracetype = $data->$gracetype;
   $item->penaltybased = $data->$penaltybased;
    
   
    
    if($item->editLoanProduct())
    {
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['message']="Loan Product Updated successfully ! Note that Pre-Existing Loans won't be affected by these changes.";
        http_response_code(200);
        echo json_encode($userArr);
    } else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="Loan Product not Updated !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
