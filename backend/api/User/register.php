<?php
    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");
    // header("Access-Control-Allow-Methods: POST");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // include_once '../../config/database.php';
    // include_once '../../config/handler.php';
    // include_once '../../models/User.php';

    // $database = new Database();
    // $db = $database->connect();

    // $handler = new Handler();

    // $item = new User($db);

    // $data = json_decode(file_get_contents("php://input"));

    // $item->email = $data->email;
    // $item->password = $handler->Encoding(md5($data->pass));
    
    // $stmt = $item->registerUser();
    // $itemCount = $stmt->rowCount();

    // if($itemCount > 0)
    // {
        
    //      $userArr = array();
    //     $userArr["data"] = array();
    //      $userArr["success"] = true;
    //      $userArr['statusCode']="200";
    //      $userArr['count']=$itemCount;
  
    //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    //         extract($row);
    //         $u = array(
    //             "positionTitle" => $positionTitle,
    //             "roleId" => $roleId,
    //             "bankId" => $bankId,
    //             "branchId" => $branchId,
    //             "firstName" => $firstName,
    //             "lastName" => $lastName,
    //             "email" => $email,
    //             "gender" => $gender,
    //             "country" => $country,
               
    //         );


    //         array_push($userArr['data'], $u);
    //         // array_push($userArr['sub'], $u2);
    //     }
    //     http_response_code(200);
    //     echo json_encode($userArr);
    // }
    // else{
    //     $userArr = array();
    //     $userArr["data"] = array();
    //     // $userArr["sub"] = array();
    //      $userArr["success"] = false;
    //      $userArr['statusCode']="204";
    //      $userArr['message']="User doesn't exist !";

    //     http_response_code(200);
    //     echo json_encode($userArr);
    // }

    
    
 


    
?>