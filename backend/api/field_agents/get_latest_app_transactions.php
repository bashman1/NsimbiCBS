<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/functions.php';
    include_once '../../models/FieldAgents.php';

    $database = new Database();
    $db = $database->connect();

    

    $item = new FieldAgents($db);


    // $data = json_decode(file_get_contents("php://input"));

    $item->account_id = $_GET['id'];
      $stmt = $item->getAgentTransactions();
    $itemCount = $stmt->rowCount();

    if($itemCount > 0)
    {
        
         $userArr = array();
        // $userArr["data"] = array();
        //  $userArr["success"] = true;
        //  $userArr['statusCode']="200";
        //  $userArr['count']=$itemCount;
  
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $acc = $item->getMemberProduct($mid);
            $byy = $item->getUserNames($_authorizedby);
            $u = array(
                "_did" => strval($tid),
                "_account_no" => $membership_no,
                "account_name" => 'TID: '.$tid.' - '.$firstName.' '.$lastName.'-'.$acc,
                "_authorisedby" => $byy,
                "_paidby_name" => @$_actionby??'',
                "_paidby_phone" => @$_actionbyphone??'',
                "_amount" => strval($amount),
                "_reason" => $description,
                "_status" => strval($_status),
                "acc_balance" => strval($acc_balance),
                "type" => $t_type,
                "_date_created" => normal_date($date_created),
                 "wallet" => 'Via Agent',
                
            );


            array_push($userArr, $u);
            // array_push($userArr['sub'], $u2);
        }
        http_response_code(200);
        echo json_encode($userArr);
    }
    else{
        $userArr = array();
        $userArr["data"] = array();
        // $userArr["sub"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="204";
         $userArr['message']="No Transactions found !";

        http_response_code(200);
        echo json_encode($userArr);
    }
