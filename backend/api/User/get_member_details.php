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

$stmt = $item->getClientDetails();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $uid_size = 0;
        if ($mult_uids) {
            $uid_array = explode($mult_uids, ',');
            $uid_size = count($uid_array)  + 1;
        }
        $clients_names = $client_type == 'individual' ? $firstName . ' ' . $lastName : $shared_name;
        $u = array(
            "branchId" => $branchId,
            "disability_cat" => $disability_cat??'',
            "disability_other" => $disability_other??'NIL',
            "disability_desc" => $disability_desc??'NIL',
            "disability_status" => $disability_status??'',
            "bankId" => $bankid,
            "balance" => $acc_balance + $loan_wallet,
            "mno" => $membership_no ?? '',
            "shares" => $no_shares ?? 0,
            "mpin" => $mpin ?? '',
            "sharesamount" => $share_amount ?? 0,
            "mtype" => is_null($membership_no) ? '<span class="badge badge-rounded badge-danger">Non-Member</span>' : '<span class="badge badge-rounded badge-primary">Member</span>',
            "actype" => $actype > 0 ? $item->getAccountType($actype) : '',
            "actype2" => $actype,
            "message" =>  $message_consent,
            "message_consent" =>  $message_consent,
            "branchName" => is_null($branchId) ? '' : $item->getBranchName($branchId),
            "userId" => $item->id,
            "cid" => $cid,
            "firstName" => $firstName,
            "lastName" => $lastName,
            "client_names" => $clients_names,
            "email" => $email,
            "gender" => $gender,
            "country" => $ucountry,
            "addressLine1" => $uaddress,
            "addressLine2" => $uaddress2,
            "village" => $village,
            "parish" => $parish,
            "subcounty" => $subcounty,
            "district" => $district,
            "primaryCellPhone" => $primaryCellPhone,
            "secondaryCellPhone" => $secondaryCellPhone,
            "otherCellPhone" => $otherCellPhone,
            "dateOfBirth" => is_null($dateOfBirth) ? '' : date('Y-m-d', strtotime($dateOfBirth)),
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
            "profilePhoto" =>$profilePhoto,
            "fingerprint" => $fingerprint,
            "fingerprint2" => $fingerprint_2??'',
            "fingerprint3" => $fingerprint_3??'',
            "sign" => $sign,
            "other_attachments" => $other_attachments,
            "krelationship" => $krelationship,
            "kaddress" => $kaddress,
            "bname" => $bname,
            "baddress" => $baddress,
            "baddress2" => $baddress2,
            "registrationNumber" => $registrationNumber,
            "bcity" => $bcity,
            "bcountry" => $bcountry,
            "registrationNumber" => $registrationNumber,
            "type" => @$type,
            "marital" => @$marital_status,
            "prof" => $profession,
            "shared_name" => $shared_name,
            "client_type" => $client_type,
            "old_membership_no" => $old_membership_no??'',
            "occupation_type_id" => $occupation_type_id,
            "occupation_type_name" => @$occupation_type_name,
            "business_type" => $business_type,
            "business_nature_description" => $business_nature_description,
            "registration_status" => $registration_status,
            "is_registered" => $registration_status,
            "business_type_other" => $business_type_other,
            "number_of_members" => $number_of_members,
            "sms_phone_numbers" => $sms_phone_numbers,
            "entered_by" => $entered_by,
            "income" => $expected_income??0,
            "region" => $region,
            "occupation_category" => $occupation_category,
            "occupation_sub_category" => $occupation_sub_category,
            "occupation_sector" => $occupation_sector,
            "accs" => is_null($mult_uids) ? 1 : $uid_size,
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
    $u = array(
        "branchId" => '',
        "bankId" => '',
        "mno" => '',
        "actype" => '',
        "message" => 0,
        "branchName" => '',
        "userId" => $item->id,
        "firstName" => '',
        "lastName" => '',
        "email" => '',
        "gender" => '',
        "country" => '',
        "addressLine1" => '',
        "addressLine2" => '',
        "village" => '',
        "parish" => '',
        "subcounty" => '',
        "district" => '',
        "primaryCellPhone" => '',
        "secondaryCellPhone" => '',
        "dateOfBirth" => '',
        "notes" => '',
        "confirmed" => '',
        "spouseName" => '',
        "spouseCell" => '',
        "createdAt" => '',
        "updatedAt" => '',
        "deletedAt" => '',
        "status" => '',
        "nin" => '',
        "spouseNin" => '',
        "profilePhoto" => '',
        "sign" => '',
        "other_attachments" => '',
        "krelationship" => '',
        "kaddress" => '',
        "bname" => '',
        "baddress" => '',
        "baddress2" => '',
        "bcity" => '',
        "bcountry" => '',
        "registrationNumber" => '',
        "type" => '',

    );


    array_push($userArr['data'], $u);
    http_response_code(200);
    echo json_encode($userArr);
}
