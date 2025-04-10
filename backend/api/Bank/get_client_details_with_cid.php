<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);



$stmt =  $item->getClientDetailswithCID($_GET['id']);


$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $u = array(
        "image" => $profilePhoto,
        "sign" => $sign,
        "address" => $addressLine1.' , '. $addressLine2.' , '. $village.' , '. $parish.' , '. $subcounty.' , '. $district,
        "otherattach" => $other_attachments,
        "fingerprint" => $fingerprint,
        "accno" => $membership_no == 0 ? '' : $membership_no,
        "actype" => $membership_no > 0 ? 'Member' : 'Non-Member',
        "userId" => $userId,
        "branchId" => $branchId,
        "branchName" => is_null($branchId) ? '' : $item->getBranchName($branchId),

        "name" => $firstName . ' ' . $lastName . $shared_name,
        "gender" => $gender,
        "shares" => $no_shares ?? 0,
        "sms_consent" => $message_consent ?? 0,
        "shareamount" => $share_amount ?? 0,
        "acc_balance" => $acc_balance,
        "loan_wallet" => $loan_wallet,
        "accbalance" => $loan_wallet + $acc_balance,
        "freezed" => $freezed_amount,
        "over_draft" => $over_draft ?? 0,
        "contact" => $primaryCellPhone . ' / ' . $secondaryCellPhone,
        "status" => $status,
        "openingdate" => normal_date($ccreatedat),


    );


    array_push($userArr['data'], $u);
    // array_push($userArr['sub'], $u2);
    // $count++;
}
http_response_code(200);
echo json_encode($userArr);
