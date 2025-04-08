<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';


$data = json_decode(file_get_contents("php://input"));

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $bank = new Bank($db);
    $bank->bankId = $data->bankId;
    $bank->charges_membership_fee = $data->charges_membership_fee ??  false;
    $bank->membership_fee_chanel = $data->membership_fee_chanel ?? false;
    $bank->membership_fee_required = $data->membership_fee_required ?? false;

    $bank->setBankMembershipFeeSettings();

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
