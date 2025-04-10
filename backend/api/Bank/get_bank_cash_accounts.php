<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Bank.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Bank($db);
    if($_GET['branch']==''){
        $item->id = $_GET['bank'];
        $stmt = $item->getAllBankCashAccounts();
    }else{
      
    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchCashAccounts();
    }

    $itemCount = $stmt->rowCount();

    if($itemCount > 0)
    {
        
         $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['count']=$itemCount;
   
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $u = array(
                "acid" => $sidd,
            "cid" => $item->getCashAccountCid($sidd),

            "uid" => $userid,
                "acname" => $acc_name,
                "branch" => $name,
                "currency" => $currency

               
               
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
         $userArr['message']="No Accounts found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
