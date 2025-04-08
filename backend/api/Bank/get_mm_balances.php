<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);

$item->bank = $_GET['bank'];
$item->branch = $_GET['branch'];
$item->user = $_GET['user'];



$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

$deposits = $item->bank == '' ? 0 : $item->getBankTotalMMDeposits();
$withdraws = $item->bank == '' ? 0 : $item->getBankTotalMMWithdraws();

if ($item->bank == 'cfc390f4-f816-44c0-9191-fcd759ef83d7') {
    // $deposits = $deposits - 13662225;
    $deposits = 73740000;
}
if ($item->bank == 'f5c30c7f-a28d-4b2e-a44a-2d354f2aaff1') {
    // $deposits = $deposits - 13662225;
    $deposits = 4247841;
    
}
$u = array(

    "ac_bal" => number_format(max(($deposits - $withdraws), 0)),
    "cr" => number_format($deposits),
    "dr" => number_format($withdraws),

);


array_push($userArr['data'], $u);


http_response_code(200);
echo json_encode($userArr);
