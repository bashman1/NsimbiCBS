<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Loan.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Loan($db);
    $item->branchId = $_GET['branch'];
    $item->createdById = $_GET['bank'];
    if($_GET['branch']!=''){
        $stmt = $item->getAllBranchSMSKeys();
    }else if($_GET['bank']!=''){
        $stmt = $item->getAllBankSMSKeys();
    }else{
        $stmt = $item->getAllSystemSMSKeys();
    }


    $itemCount = $stmt->rowCount();

    if($itemCount > 0)
    {
        
         $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['count']=$itemCount;
   $count =0 ;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
       
            $u = array(
                "id" => ++$count,
                "name" => $msg_key,
            );
      


            array_push($userArr['data'], $u);
          
            // array_push($userArr['sub'], $u2);
        }
        http_response_code(200);
        echo json_encode($userArr);
    }else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="No keys found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
