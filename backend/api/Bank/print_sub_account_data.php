<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../models/ChartOfAccountsModel.php';

    $database = new Database();
    $db = $database->connect();

    $item = new ChartOfAccountsModel($db);

    $data = json_decode(file_get_contents("php://input"));

$stmt = $item->print_sub_account_data($data->major,[]);
    
   
        $userArr = array();
        $userArr["data"] = $stmt;
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['message']="Subaccounts printed successfully !";
        http_response_code(200);
        echo json_encode($userArr);
   
?>