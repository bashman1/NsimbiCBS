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
   
    $item->name = $data->descri;
    $item->location = $data->amount;
    $item->contact_person_details = $data->exp_acc;
    $item->recommender = $data->date_of_p;
    $item->id = $data->bank_acc;
    $item->createdAt = $data->cash_acc;
    $item->gracetype = $data->account_id;
    $item->penaltybased = $data->cheque;
    $item->pay_method = $data->pay_method;
    $item->cheque_no = $data->comment;
    $item->send_sms = $data->branchId;
    $item->bank_acc = $data->bankId;
    $item->cash_acc = $data->userId;
    
    if($item->createExpense())
    {
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['message']="Expense created successfully !";
        http_response_code(200);
        echo json_encode($userArr);
    } else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="Expense not created !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
?>