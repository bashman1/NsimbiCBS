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

    $item->bankId = $data->bank;
    $item->gender = $data->gender;
    // $item->branchId = $data->branch;
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


    $uid = $item->createBankAdmin();
    if($uid>0)
    {
      $url = 'http://ucscucbs-282d986e7f0f.herokuapp.com/client/set_password.php?id='.$uid;
      $mail_subject ='Sign up with UCSCU CBS';
  
  $item->sendEmail($item->email,$mail_subject,
        "<style>p{font-size: 14px;}</style><img src='http://ucscucbs-282d986e7f0f.herokuapp.com/client/images/ucscucbs.png' style='margin: auto;'/><p>Hello,</p><p>You've just had an account created for you.</p><p>You must now complete signing up by setting up a password for your account using link below: <br/><br/><a href='".$url. "'>Set Password</a><br/></p><p>If this email was sent by mistake, please ignore this email and contact the administrator at your institution. If you need further support with siging up, please contact our team at <a href='mailto:support@ucscucbs.com'>support@ucscucbs.com</a>.</p><p>Thank you for using UCSCUCBS!</p><p>-The UCSCUCBS Team</p>"
  );
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
         $userArr['message']="Bank Admin not updated !";

        http_response_code(200);
        echo json_encode($userArr);
    }

    
    
 


    
?>