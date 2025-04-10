<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new User($db);


$item->id = $_GET['uid'];

$stmt = $item->getPortalClientDetails();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "acbalance" => $acc_balance ?? 0,
            "loan_wallet" => $loan_wallet ?? 0,
            "freezed" => $freezed_amount ?? 0,
            "fees" => $school_pay ?? 0,
            "shares" => $item->getShareDetails($item->id),
            "share_amount" => $item->getShareAmountDetails($item->id),
            "acno" => $membership_no ?? '',
            "userId" =>  $item->id,
            "bankId" => $bankId,
            "tname" =>  $item->getBankTradeName($bid),
            "bankName" =>  $item->getBankName($bid),
            "blogo" =>  $item->getBankLogo($bid),
            "bcontacts" =>  $item->getBankContacts($bid),
            "bemail" => $item->getBankEmail($bid),
            "blocation" =>  $item->getBankLocation($bid),
            "branchName" =>  $item->getBranchName($branc),
            "branchId" => $branc,
            "firstName" => $firstName,
            "shared_name" => $shared_name,
            "lastName" => $lastName,
            "photo" => $profilePhoto,
            "email" => $email,
            "gender" => $gender,
            "country" => $country,
            "addressLine1" => $addressLine1,
            "addressLine2" => $addressLine2,
            "village" => $village,
            "parish" => $parish,
            "subcounty" => $subcounty,
            "district" => $district,
            "primaryCellPhone" => $primaryCellPhone,
            "secondaryCellPhone" => $secondaryCellPhone,
            "dateOfBirth" => $dateOfBirth,
            "client_type" => $client_type,
            "status" => $status,
            "nin" => $nin,


        );


        array_push($userArr['data'], $u);
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = $item->password;
    $userArr['message'] = "User doesn't exist !";

    http_response_code(200);
    echo json_encode($userArr);
}
