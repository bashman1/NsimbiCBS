<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Bank.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Bank($db);
    $stmt = $item->closeDay($_GET['id'], $_GET['day']);
   
    
         $userArr = array();
        $userArr["data"] = $stmt;
         $userArr["success"] = true;
         $userArr['statusCode']="200";

        http_response_code(200);
        echo json_encode($userArr);
 
