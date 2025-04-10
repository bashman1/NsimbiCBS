<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Bank.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Bank($db);

    $stmt = $item->getAllBanks();
    $itemCount = $stmt->rowCount();

    if($itemCount > 0)
    {
        
         $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['count']=$itemCount;
    $count = 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $u = array(
                "id" => $count,
                "bid" => $id,
                "name" => $name,
            "tname" => is_null($trade_name)?'':$trade_name,
                "location" => $location,
                "recommender" => $recommender,
                "contact_person" => $contact_person_details,
                "onboardingdate" =>date('d-m-Y', strtotime($createdAt)),
                "lowestCurrencyValue" => $lowestCurrencyValue,
                "countryCode" => $countryCode,
            "status" => $bank_status==1? '<span class="badge light badge-primary">Active</span>': '<span class="badge light badge-danger">Deactivated</span>',
            "smsstatus" => $sms_sub_status == 1 ? '<span class="badge light badge-primary">Active</span>' : '<span class="badge light badge-danger">Deactivated</span>',
                "branches" => $item->getTotalBankBranches($id),
                "staffs" =>  $item->getTotalBankStaffs($id),
                "clients" => $item->getTotalBankClients($id),
                "actions" => ' <div class="d-flex">
              
                    <a href="edit_bank?name='.$name.'&location='.$location.'&contact='.$contact_person_details.'&refered='.$recommender.'&id='.$id. '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                             <a href="delete_bank?id=' . $id . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-trash"></i></a>
                    
                </div>',
               
            );
      


            array_push($userArr['data'], $u);
            $count++;
            // array_push($userArr['sub'], $u2);
        }
        http_response_code(200);
        echo json_encode($userArr);
    }else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['count']=0;
         $userArr['message']="No Banks found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
