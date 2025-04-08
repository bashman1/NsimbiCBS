<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Loan.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Loan($db);
$item->branchId = $_GET['bank'];
    $stmt = $item->getAllBankFees();
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
                "id" => $id,
                "name" => $name,
                "ftype" => $type,
                "rate" => $rateAmount,
                "ptype" => $paymentType=='UP_FRONT'?'UP FRONT':'ON DISBURSEMENT',
                
                "status" => $status==1?'<span class="badge light badge-primary">ACTIVE</span>':'<span class="badge light badge-danger">DEACTIVATED</span>',
               
                
                "actions" => ' <div class="d-flex">
              
                    <a href="edit_fee.php?id='.$id.'" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                    
                </div>',
               
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
         $userArr['message']="No Fees found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
