<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$stmt = $item->getAllBanks();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "id" => $count,
            "bid" => $id,
            "badmin" => $item->getBankAdminId($id),
            "name" => $name,
            "email" => $bankmail,
            "years" => '-',
            "membership" => 'UGX -',
            "maintenance" => 'UGX -',
            "min_bal" => 'UGX -',
            "share_value" => 'UGX -',
            "min_shares" => '-',
            "int_range" => '-',
            "max_loan" => 'UGX -',
            "contacts" => $bankcontacts,
            "logo" => 'https://app.ucscucbs.net/client/'.$logo ?? 'https://app.ucscucbs.net/client/images/no_logo.png',
            "tname" => is_null($trade_name) ? '' : $trade_name,
            "location" => $location,
            "recommender" => $recommender,
            "contact_person" => $contact_person_details,
            "onboardingdate" => date('d-m-Y', strtotime($createdAt)),
            "lowestCurrencyValue" => $lowestCurrencyValue,
            "countryCode" => $countryCode,
            "branches" => $item->getTotalBankBranches($id),
            "clients" => $item->getTotalBankClients($id),
           

        );



        array_push($userArr['data'], $u);
        $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['count'] = 0;
    $userArr['message'] = "No Banks found !";
    http_response_code(200);
    echo json_encode($userArr);
}
