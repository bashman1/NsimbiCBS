<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/User.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new User($db);

    $data = json_decode(file_get_contents("php://input"));

    $item->id = $data->id;
    $item->password = $handler->Encoding(md5($data->password));
    
  

    if($item->updatePassword())
    {
        
         $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
  
       
        http_response_code(200);
        echo json_encode($userArr);
    }
    else{
        $userArr = array();
        $userArr["data"] = array();
        // $userArr["sub"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']= '204';
         $userArr['message']="Password Change failed !";

        http_response_code(200);
        echo json_encode($userArr);
    }

    
    
 


    
?>