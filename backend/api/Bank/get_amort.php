<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../models/amortization.php';

// $database = new Database();
// $db = $database->connect();

$data = json_decode(file_get_contents("php://input"));
//   $lam,$rate,$dur,$cycle,$sr

// term_years represents number of years
/**
 * terms represents number of months
 * 
 */

$cycle = @$data->cycle;
$terms = null;

// if($cycle =){

// }

$data = array(
    'loan_amount'     => $data->lam,
    'term_years'     => 1,
    'interest'         => $data->rate,
    'terms'         => $data->dur / 30
);

$amortization = new Amortization($data);

http_response_code(200);
echo $amortization;

    // $item = new Member($db);

    // $data = json_decode(file_get_contents("php://input"));

    // $item->m_id = $data->mid;
    //   $stmt = $item->getMemberDetails();
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
    //           "membership_id" => $m_id,
    //             "account_no" => $membership_no,
    //             "fname" => $fname,
    //             "lname" => $lname,
    //             "mname" => $mname,
    //             "title" => $title,
    //             "names" => $title.' '.$fname.' '.$lname.' '.$mname,
    //             "account_type" => $acc_name,
    //             "physical_address" => $physical_address,
    //             "mobile" => $mobile_number,
    //             "phone" => $phone_number,
    //             "email" => $email,
    //             "sex" => $sex,
    //             "place_of_birth" => $place_of_birth,
    //             "age" =>$dob,
    //             "id_type" => $identification_type,
    //             "nin" => $nin_no,
    //               "nin2" => $ag_nin,
    //               "ag" => $ag_names,
    //             "occupation" => $occupation,
    //             "status" => $status,
    //             "acc_balance" => $acc_balance,
    //             "date_created" => $md,
    //             "id_photo" => $id_photo,
    //             "photo" => $photo,
    //             "sign" => $signature,
    //              "fid" => $fid,
    //               "signatories" => $signatories,
    //               "message" => $message_consent,
    //               "otp" => $mpin,
    //               "acc_code" => $acc_code,
                
    //         );


    //         array_push($userArr['data'], $u);
    //         // array_push($userArr['sub'], $u2);
    //     }
        // http_response_code(200);
        // echo json_encode($userArr);
    // }
    // else{
    //     $userArr = array();
    //     $userArr["data"] = array();
    //     // $userArr["sub"] = array();
    //      $userArr["success"] = false;
    //      $userArr['statusCode']="204";
    //      $userArr['message']="No Data found !";

    //     http_response_code(200);
    //     echo json_encode($userArr);
    // }
