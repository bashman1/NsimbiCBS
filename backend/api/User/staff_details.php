<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new User($db);

$data = json_decode(file_get_contents("php://input"));

$item->id = $data->id;

$stmt = $item->getStaffDetails();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        if ($is_supervisor) {
            if ($supervisor_level == 1) {
                $bankId = $supervisor_bankid;
            } else {
                $branchId = $supervisor_bankid;
            }
        }
        $u = array(
            "positionTitle" => $positionTitle,
            "roleId" => $roleId,
            "isadmin" => $is_admin,
            "issupervisor" => $is_supervisor,
            "supervisor_level" => $supervisor_level,
            "userId" =>  $item->id,
            "user_id" =>  $item->id,
            "bankId" =>  $bankId,
            "tname" => is_null($bankId) ? $item->getBranchBankTradeName($branchId) : $item->getBankTradeName($bankId),
            "bankName" => is_null($bankId) ? $item->getBranchName($branchId) : $item->getBankName($bankId),
            "blogo" => is_null($bankId) ? $item->getBranchLogo($branchId) : $item->getBankLogo($bankId),
            "bcontacts" => is_null($bankId) ? $item->getBranchContacts($branchId) : $item->getBankContacts($bankId),
            "bemail" => is_null($bankId) ? $item->getBranchEmail($branchId) : $item->getBankEmail($bankId),
            "blocation" => is_null($bankId) ? $item->getBranchLocation($branchId) : $item->getBankLocation($bankId),
            "branchName" => is_null($branchId) ? '' : $item->getBranchName($branchId),
            "bankName2" => is_null($bankId) ? '' : $item->getBankName($bankId),
            "branchId" => $branchId,
            "firstName" => $firstName,
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
            "notes" => $notes,
            "confirmed" => $confirmed,
            "spouseName" => $spouseName,
            "spouseCell" => $spouseCell,
            "createdAt" => $screatedat,
            "updatedAt" => $supdatedat,
            "deletedAt" => $sdeletedat,
            "status" => $status,
            "nin" => $nin,
            "spouseNin" => $spouseNin,
            "password" => $password,
            "kphone" => $spouseCell,
            "kaddress" => $kaddress,
            "krel" => $krelationship,


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
