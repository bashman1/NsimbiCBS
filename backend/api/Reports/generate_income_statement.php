<?php
require_once __DIR__.'../../RequestHeaders.php';

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
     * Handle income
     */

    $income_account = new Account($db);
    $income_account->bank_id = $data->bank_id;
    $income_account->branch_id = $data->branch ? $data->branch : $data->branchId;
    $income_account->with_income_totals = true;
    $income_account->type = "INCOMES";
    $income_account->transaction_start_date = $data->transaction_start_date;
    $income_account->transaction_end_date = $data->transaction_end_date;
    $response['income'] = $income_account->getAccounts();

    /**
     * Handle expenditure
     */
    $expenditure_account = new Account($db);
    $expenditure_account->bank_id = $data->bank_id;
    $expenditure_account->branch_id = $data->branch ? $data->branch : $data->branchId;
    $expenditure_account->with_expenditure_totals = true;
    $expenditure_account->type = "EXPENSES";
    $expenditure_account->transaction_start_date = $data->transaction_start_date;
    $expenditure_account->transaction_end_date = $data->transaction_end_date;
    $response['expenses'] = $expenditure_account->getAccounts();

    echo $ApiResponser::SuccessResponse($response);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
