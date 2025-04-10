<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../models/ChartOfAccountsModel.php';

    $database = new Database();
    $db = $database->connect();

    $item = new ChartOfAccountsModel($db);

    $data = json_decode(file_get_contents("php://input"));
$item->bankid = $data->bank;
$stmt = $item->get_general_ledger_accounts();
    
   
        $userArr = array();
        $userArr["data"] =array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['message']="Subaccounts printed successfully !";

         
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "account_code" => $account_code,
            "account_name" => $account_name,
            "account_descri" =>$account_descr,
            "status" => $status,
            "actype" => $actype,
           

        );



        array_push($userArr['data'], $u);

        // array_push($userArr['sub'], $u2);
    }
        http_response_code(200);
        echo json_encode($userArr);
   
?>