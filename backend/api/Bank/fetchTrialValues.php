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
$bank = @$_GET['bankk'];
$branch = @$_GET['branch'];
$cr_dr = @$_GET['cr_dr'];
$acc_typ = @$_GET['acc_typ'];

$cr = 0;
$dr = 0;

$ass = 0;
$lia = 0;
$cap = 0;
$inc = 0;
$exp = 0;
$susp = 0;


$stmt = $item->getChartAccValue($id, $start, $end, $bank, $branch);

if ($cr_dr == 'dr') {
    $dr = $stmt;
} else {
    $cr = $stmt;
}


if ($acc_typ == 'ass') {
    $ass = $stmt;
} else if ($acc_typ == 'lia') {
    $lia = $stmt;
} else if ($acc_typ == 'cap') {
    $cap = $stmt;
} else if ($acc_typ == 'inc') {
    $inc = $stmt;
} else if ($acc_typ == 'exp') {
    $exp = $stmt;
} else {
    $susp = $stmt;
}

http_response_code(200);
echo json_encode([
    'details' => number_format($stmt ?? 0, 2, '.', ','),
    'cr' => ($cr ?? 0),
    'dr' => ($dr ?? 0),

    'ass' => ($ass ?? 0),
    'lia' => ($lia ?? 0),
    'cap' => ($cap ?? 0),
    'inc' => ($inc ?? 0),
    'exp' => ($exp ?? 0),
    'susp' => ($susp ?? 0)


]);
