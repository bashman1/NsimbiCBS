<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$stmt = $item->getAccBalVal2($_GET['lpid'], $_GET['bid'], $_GET['acid'], $_GET['type']);


$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['bal'] = $stmt ?? 0;
http_response_code(200);
echo json_encode($userArr);
