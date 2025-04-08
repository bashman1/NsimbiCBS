<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/User.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new User($db);

    $data = json_decode(file_get_contents("php://input"));

    $item->email = $data->email;
    $item->firstName = $data->fname;
    $item->lastName = $data->lname;
    $item->gender = $data->gender;
    $item->addressLine1 = $data->address1;
    $item->addressLine2 = $data->address2;
    $item->district = $data->district;
    $item->subcounty = $data->subcounty;
    $item->parish = $data->parish;
    $item->village = $data->village;
    $item->primaryCellPhone = $data->primaryCell;
    $item->secondaryCellPhone = $data->secondaryCell;
    $item->dateOfBirth = $data->dob;
    $item->nin = $data->nin;
    $item->spouseName = $data->spousename;
    $item->spouseNin = $data->spousenin;
    $item->id = $data->id;
    $item->spouseCell = $data->spousePhone;
    $item->country = $data->country;
    

    if($item->updateStaff())
    {
         $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
  
        
        http_response_code(200);
        echo json_encode($userArr);
    }
    else{
        $userArr = array();
        $userArr["data"] = array();
        // $userArr["sub"] = array();
         $userArr["success"] = false;
        $userArr['statusCode'] = "204";
         $userArr['message']="Staff not updated !";

        http_response_code(200);
        echo json_encode($userArr);
    }

    
    
 


    
?>