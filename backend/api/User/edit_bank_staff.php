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

    $item->id = $data->id;
    $item->userId = $data->uid;
    $item->gender = $data->gender;
    $item->branchId = $data->branch;
    $item->roleId = $data->role;
    $item->profilePhoto = $data->passport;
    $item->firstName = $data->fname;
    $item->lastName = $data->lname;
    $item->addressLine1 = $data->address;
    $item->addressLine2 = $data->address2;
    $item->country = $data->country;
    $item->district = $data->district;
    $item->subcounty = $data->subcounty;
    $item->parish = $data->parish;
    $item->village = $data->village;
    $item->primaryCellPhone = $data->phone;
    $item->secondaryCellPhone = $data->other_phone;
    $item->email = $data->email;
    $item->nin = $data->nin;
    $item->dateOfBirth = $data->dob;
    $item->bname = $data->title;
    $item->spouseName = $data->kname;
    $item->spouseCell = $data->kphone;
    $item->spouseNin = $data->knin;
    $item->krelationship = $data->relationship;
    $item->kaddress = $data->kphysicaladdress;
    $item->confirmed = true;
    $item->status = 'ACTIVE';
    $item->serialNumber = 100;
    $item->identificationNumber = '100';


    $uid = $item->editBankStaff();
    if($uid>0)
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