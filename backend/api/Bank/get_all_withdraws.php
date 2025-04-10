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
    if($_GET['bank']==''){
        $stmt = $item->getAllBranchWithdraws();
    }else{
        $stmt = $item->getAllBankWithdraws();
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
                "id" => $tid,
                "name" => $firstName.' '.$lastName,
                "acno" => $membership_no,
                "print" => ' <button class="btn btn-success btn-sm edit btn-flat"> <a target="_blank" href="receipt.php?id=' . $tid . '&type=W" style="color:#fff !important;">Print</a></button>',
                "amount" => number_format($amount),
                "balance" => number_format($left_balance),
                "auth" =>$item->getStaffDetails($_authorizedby),
                "description" => $description,
                "actionby" => $_actionby,
                "status" => $_status==0?'<span class="badge light badge-danger">Pending</span>':'<span class="badge light badge-primary">Successful</span>',
              
                "branch" => $item->getBranchDetails($_branch),
                
                "dateCreated" =>date('d-m-Y', strtotime($date_created)),
                
                "actions" => ' <div class="d-flex">
              
                    <a href="edit_withdraw.php?id='.$tid.'" class="btn btn-primary shadow btn-xs sharp me-1"><i
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
         $userArr['message']="No Loans found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
