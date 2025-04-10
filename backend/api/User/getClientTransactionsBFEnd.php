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

$stmt = $item->getClientRangeTransactionsBFEnd();


$userArr = array();
$userArr["data"] = $stmt;
$userArr["success"] = true;
http_response_code(200);
echo json_encode($userArr);
