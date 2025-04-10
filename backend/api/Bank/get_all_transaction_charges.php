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
        $stmt = $item->getAllBankTransactionsCharges();
    }else{
      
    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchTransactionsCharges();
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
                "id" => $c_id,
                "date" => date('Y-m-d H:i:s',strtotime($date_created)),
                "cname" => strtoupper($cname),
                "mode" => strtoupper($charge_mode),
                "cappln" => strtoupper($c_application),
                "status" =>$c_status==1?'<span class="text-primary">ACTIVE</span>': '<span class="text-danger">DEACTIVATED</span>',
                "charge" => $charge,
                "c_type" => strtoupper($c_type),
                "min" => number_format($min_amount),
                "max" => number_format($max_amount),
                "actions"=>''
               
               
            );
      


            array_push($userArr['data'], $u);
            // $count++;
            // array_push($userArr['sub'], $u2);
        }
        http_response_code(200);
        echo json_encode($userArr);
    }else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="No Transactions found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
