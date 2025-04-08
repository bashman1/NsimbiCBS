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
$item->createdAt = $data->start;
$item->updatedAt = $data->end;
$item->deletedAt = $data->filtered;

$filtered_shares = 0;
$bf_shares = $item->getClientSharesBF();

$value = $item->getInstitutionShareValue();

if ($item->deletedAt) {
    $filtered_shares = $item->getClientSharesFiltered();
}


$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;

$u = array(
    "shares" => $bf_shares ?? 0,
    "amount" => $bf_shares * $value,
    "filtered_shares" => $filtered_shares,
    "filtered_amount" => $filtered_shares * $value,
);

array_push($userArr['data'], $u);

http_response_code(200);
echo json_encode($userArr);
