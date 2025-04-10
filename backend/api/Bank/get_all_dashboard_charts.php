<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
// Get filter parameters

$item->bank = $_GET['bank'];
$item->branch = $_GET['branch'];
$item->user = $_GET['user'];
$item->deletedAt = $_GET['period'];
$item->createdAt = $_GET['start_date'];
$item->updatedAt = $_GET['end_date'];



$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

// $deposits = $item->user == '' ? 0 : $item->getUserTotalDeposits();
$genderData = $item->getChartGenderData($item->createdAt, $item->updatedAt, $item->branch, $item->bank, $item->user, $item->deletedAt);

$ageData = $item->getChartAgeData($item->createdAt, $item->updatedAt, $item->branch, $item->bank, $item->user, $item->deletedAt);

$occupationData = $item->getChartOccupationData($item->createdAt, $item->updatedAt, $item->branch, $item->bank, $item->user, $item->deletedAt);

// $educationData = $item->getCharteducationData($item->createdAt, $item->updatedAt, $item->branch, $item->bank, $item->user, $item->deletedAt);

$incomeExpenseData = $item->getChartIncomeExpenseData($item->createdAt, $item->updatedAt, $item->branch, $item->bank, $item->user, $item->deletedAt);

$membership_branchData = $item->getMembershipStatisticsBranchData($item->createdAt, $item->updatedAt, $item->branch, $item->bank, $item->user, $item->deletedAt);

$membership_productData = $item->getMembershipStatisticsProductData($item->createdAt, $item->updatedAt, $item->branch, $item->bank, $item->user, $item->deletedAt);


http_response_code(200);
// Combine results
echo json_encode([
    'gender' => $genderData,
    'age' => $ageData,
    'occupation' => $occupationData,
    'education' => $incomeExpenseData,
    'membership_branch' => $membership_branchData,
    'membership_product' => $membership_productData
]);
