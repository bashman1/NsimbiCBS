<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/User.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new User($db);

    $request = json_decode(file_get_contents("php://input"),true);

    $item->details = $request['data'];
    
    $results =$item->sharePurchase();

    // var_dump($results);
    // exit;
    
    if($results)
    {
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['message']="Shares purchased successfully !";
        http_response_code(200);
        echo json_encode($userArr);
    } else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="Share Purchase failed !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
