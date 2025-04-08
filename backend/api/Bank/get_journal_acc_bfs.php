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

$item->id = $data->start;
$item->createdAt = $data->end;
$item->updatedAt = $data->lpid;
$item->firstName = $data->branch;
$item->lastName = $data->acid;
$item->deletedAt = $data->bank;


$bf = $item->getJournalAccBfs();



$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";
$userArr['count'] = 1;



$u = array(
    "bf" => $bf,

);


array_push($userArr['data'], $u);
// array_push($userArr['sub'], $u2);


http_response_code(200);
echo json_encode($userArr);
