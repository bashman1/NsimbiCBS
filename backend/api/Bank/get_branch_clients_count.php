<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

$staffs = $item->getTotalBranchClientsToday($_GET['id']);
$clients = $item->getTotalBranchClients($_GET['id']);

$u = array(
    "count" => number_format($clients) . ' - ' . number_format($staffs),

);



array_push($userArr['data'], $u);

http_response_code(200);
echo json_encode($userArr);
