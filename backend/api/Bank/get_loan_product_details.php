<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Loan.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Loan($db);
    $data = json_decode(file_get_contents("php://input"));
    $item->branchId = $data->pid;
   
        $stmt = $item->getLoanProductDetails();
   

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
                "id" => $type_id,
                "name" => $type_name,
                "interestrate" => $interestrate,
                 "has_penalty" => $penalty,
                "fees" =>$item->getLoanProductFees($type_id),
                "numberofgraceperioddays" => $numberofgraceperioddays,
                "penaltyinterestrate" => $penaltyinterestrate,
                "penaltyfixedamount" => $penaltyfixedamount,
                "bankId" => $bankId,
                "frequency" => $frequency,
                "interestmethod" => $interestmethod,
                "maxnumberofpenaltydays" => $maxnumberofpenaltydays,
                "penalty_based_on" => $penalty_based_on,
                "gracetype" => $gracetype,
                "auto_repay" => $auto_repay,
                "auto_penalty" => $auto_penalty,
                "round_off" => $round_off,
                
               
               
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
         $userArr['message']="No Product found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
