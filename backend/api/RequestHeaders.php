<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/ApiResponser.php';
require_once __DIR__ . '../../config/functions.php';
require_once __DIR__ . '../../config/database.php';
require_once __DIR__ . '../../config/DbHandler.php';
require_once __DIR__ . '../../config/constants.php';
require_once __DIR__ . '../../models/Loan.php';
require_once __DIR__ . '../../models/Bank.php';
require_once __DIR__ . '../../models/MobileMoney.php';

$ApiResponser = new ApiResponser();
// echo $ApiResponser::SuccessMessage("All is good");
// exit;
// return [];