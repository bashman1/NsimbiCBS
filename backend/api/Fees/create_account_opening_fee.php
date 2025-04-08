<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../models/Fee.php';
require_once '../ApiResponser.php';


$data = json_decode(file_get_contents("php://input"));

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();
    $fee = new Fee($db);
    $fee->bankId = $data->bankId;
    $fee->fee_id = $data->fee_id;
    $fee->fee_name = $data->fee_name;
    $fee->chart_account_id = $data->account_id;
    $fee->saving_accounts = $data->saving_accounts ?? [];
    $fee->applies_to = $data->applies_to;
    $fee->amount = $data->amount;
    $fee->passbook = $data->passbook;
    $fee->shares = $data->shares;
    $fee->pass_acid = $data->pass_acid;
    if ($fee->applies_to == 'all_clients') {
        $fee->saving_accounts = [];
    }

    // $fee->upsertFee();
    // echo $ApiResponser::SuccessMessage();

    if ($fee->upsertFee()) {
        echo $ApiResponser::SuccessMessage();
    } else {
        echo $ApiResponser::ErrorResponse();
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
