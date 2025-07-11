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
        $stmt = $item->getAllBankTransactions();
    }else{
      
    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchTransactions();
    }

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
                "count" => $count,
                "id" => $tid,
                "date" => date('Y-m-d H:i:s',strtotime($date_created)),
                "description" => strtoupper($tdescription),
                "amount" => '<span class="text-danger">'.number_format($amount).'</span>',
                "account" => strtoupper($aname),
                "vendor" => strtoupper($acc_name),
                "type" => $t_type=='D'? '<span class="badge light badge-primary">DEBIT</span>':'<span class="badge light badge-danger">CREDIT</span>',
                "auth" => strtoupper($firstName.' '.$lastName),
                "branch" => strtoupper($bname)
               
               
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
         $userArr['message']="No Transactions found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
