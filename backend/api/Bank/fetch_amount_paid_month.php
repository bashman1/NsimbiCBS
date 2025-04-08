<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$start = @$_GET['from_date'];
$end = @$_GET['to_date'];
$id = @$_GET['id'];

$principal = $item->getLoanPrincipalPaidPeriod($id, $start, $end);
$interest = $item->getLoanInterestPaidPeriod($id, $start, $end);
$has_collateral = $item->hasCollateral($id);
$total = $principal + $interest;

http_response_code(200);
echo json_encode([
    'princ_month' => number_format($principal ?? 0),
    'int_month' => number_format($interest ?? 0),
    'tot_month' => number_format($total ?? 0),
    'has_collateral' => $has_collateral,

]);
