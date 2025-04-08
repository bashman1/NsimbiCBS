<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$start = @$_GET['start'];
$end = @$_GET['end'];
$id = @$_GET['id'];


$stmt = $item->getClientRangeTransactionsBFEnd2($id, $start, $end);

http_response_code(200);
echo json_encode(['details' => number_format($stmt ?? 0, 2, '.', ',')]);
