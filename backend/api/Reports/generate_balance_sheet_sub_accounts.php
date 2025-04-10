<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/SystemGlAccount.php';
require_once '../../models/Account.php';
require_once '../../models/Transaction.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"));
try {
    $response = [];
    $database = new Database();
    $db = $database->connect();
    $ApiResponser = new ApiResponser();
    $system_account = new SystemGlAccount($db);

    /**
     * Handle assets
     */

    $assets_account = new Account($db);
    $assets_account->bank_id = $data->bank_id;
    $assets_account->branch_id = $data->branch ? $data->branch : $data->branchId;
    $assets_account->with_assets_totals = true;
    $assets_account->type = "ASSETS";
    $assets_account->transaction_start_date = $data->transaction_start_date;
    $assets_account->transaction_end_date = $data->transaction_end_date;
    $response['assets'] = $assets_account->getAccounts2();

    /**
     * Handle liabilities
     */
    $liability_account = new Account($db);
    $liability_account->bank_id = $data->bank_id;
    $liability_account->branch_id = $data->branch ? $data->branch : $data->branchId;
    $liability_account->with_liabilities_totals = true;
    $liability_account->type = "LIABILITIES";
    $liability_account->transaction_start_date = $data->transaction_start_date;
    $liability_account->transaction_end_date = $data->transaction_end_date;
    $response['liabilities'] = $liability_account->getAccounts2();


    /**
     * Handle capital
     */
    $capital_account = new Account($db);
    $capital_account->bank_id = $data->bank_id;
    $capital_account->branch_id = $data->branch ? $data->branch : $data->branchId;
    $capital_account->with_capital_totals = true;
    $capital_account->type = "CAPITAL";
    $capital_account->transaction_start_date = $data->transaction_start_date;
    $capital_account->transaction_end_date = $data->transaction_end_date;
    $response['capital'] = $capital_account->getAccounts2();

    echo $ApiResponser::SuccessResponse($response);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
