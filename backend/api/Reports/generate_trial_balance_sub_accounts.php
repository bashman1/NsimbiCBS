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

    $account = new Account($db);
    $sub_accounts = $account->getSubAccounts();


    /**
     * Handle income
     */

    $income_account = new Account($db);
    $income_account->bank_id = $data->bankk;
    $income_account->branch_id = $data->bid;
    $income_account->with_income_totals = $data->with_income_totals ?? true;
    $income_account->is_trial_balance = $data->is_trial_balance;
    $income_account->type = "INCOMES";
    $income_account->transaction_start_date = $data->transaction_start_date;
    $income_account->transaction_end_date = $data->transaction_end_date;
    $response['income'] = $income_account->getAccounts2();
    $i = 0;
    for ($i = 0; $i < count($response['income']); $i++) {
        $response['income'][$i]['sub_accounts'] = [];
        $k = 0;
        foreach ($sub_accounts as $sub_account) {
            if ($response['income'][$i]['account_id'] == $sub_account['main_account_id']) {
                array_push($response['income'][$i]['sub_accounts'], $sub_account);
                array_splice($sub_account, $k, 1);
            }
            $k++;
        }
    }


    /**
     * Handle expenditure
     */
    $expenditure_account = new Account($db);
    $expenditure_account->bank_id = $data->bankk;
    $expenditure_account->branch_id = $data->bid;
    $expenditure_account->with_expenditure_totals = $data->with_expenditure_totals ?? true;
    $expenditure_account->is_trial_balance = $data->is_trial_balance;
    $expenditure_account->type = "EXPENSES";
    $expenditure_account->transaction_start_date = $data->transaction_start_date;
    $expenditure_account->transaction_end_date = $data->transaction_end_date;
    $response['expenses'] = $expenditure_account->getAccounts2();
    $i = 0;
    for ($i = 0; $i < count($response['expenses']); $i++) {
        $response['expenses'][$i]['sub_accounts'] = [];
        $k = 0;
        foreach ($sub_accounts as $sub_account) {
            if ($response['expenses'][$i]['account_id'] == $sub_account['main_account_id']) {
                array_push($response['expenses'][$i]['sub_accounts'], $sub_account);
                array_splice($sub_account, $k, 1);
            }
            $k++;
        }
    }

    /**
     * Handle assets
     */

    $assets_account = new Account($db);
    $assets_account->bank_id = $data->bankk;
    $assets_account->branch_id = $data->bid;
    $assets_account->with_assets_totals = $data->with_assets_totals ?? true;
    $assets_account->is_trial_balance = $data->is_trial_balance;
    $assets_account->type = "ASSETS";
    $assets_account->transaction_start_date = $data->transaction_start_date;
    $assets_account->transaction_end_date = $data->transaction_end_date;
    $response['assets'] = $assets_account->getAccounts2();

    $i = 0;
    for ($i = 0; $i < count($response['assets']); $i++) {
        $response['assets'][$i]['sub_accounts'] = [];
        $k = 0;
        foreach ($sub_accounts as $sub_account) {
            if ($response['assets'][$i]['account_id'] == $sub_account['main_account_id']) {
                array_push($response['assets'][$i]['sub_accounts'], $sub_account);
                array_splice($sub_account, $k, 1);
            }
            $k++;
        }
    }

    /**
     * Handle liabilities
     */
    $liability_account = new Account($db);
    $liability_account->bank_id = $data->bankk;
    $liability_account->branch_id = $data->bid;
    $liability_account->with_liabilities_totals = $data->with_liabilities_totals ?? true;
    $liability_account->is_trial_balance = $data->is_trial_balance;
    $liability_account->type = "LIABILITIES";
    $liability_account->transaction_start_date = $data->transaction_start_date;
    $liability_account->transaction_end_date = $data->transaction_end_date;
    $response['liabilities'] = $liability_account->getAccounts2();
    $i = 0;
    for ($i = 0; $i < count($response['liabilities']); $i++) {
        $response['liabilities'][$i]['sub_accounts'] = [];
        $k = 0;
        foreach ($sub_accounts as $sub_account) {
            if ($response['liabilities'][$i]['account_id'] == $sub_account['main_account_id']) {
                array_push($response['liabilities'][$i]['sub_accounts'], $sub_account);
                array_splice($sub_account, $k, 1);
            }
            $k++;
        }
    }


    /**
     * Handle capital
     */
    $capital_account = new Account($db);
    $capital_account->bank_id = $data->bankk;
    $capital_account->branch_id = $data->bid;
    $capital_account->with_capital_totals = $data->with_capital_totals ?? true;
    $capital_account->is_trial_balance = $data->is_trial_balance;
    $capital_account->type = "CAPITAL";
    $capital_account->transaction_start_date = $data->transaction_start_date;
    $capital_account->transaction_end_date = $data->transaction_end_date;
    $response['capital'] = $capital_account->getAccounts2();
    $i = 0;
    for ($i = 0; $i < count($response['capital']); $i++) {
        $response['capital'][$i]['sub_accounts'] = [];
        $k = 0;
        foreach ($sub_accounts as $sub_account) {
            if ($response['capital'][$i]['account_id'] == $sub_account['main_account_id']) {
                array_push($response['capital'][$i]['sub_accounts'], $sub_account);
                array_splice($sub_account, $k, 1);
            }
            $k++;
        }
    }

    /**
     * Handle suspense & error accounts
     */
    $suspense_account = new Account($db);
    $suspense_account->bank_id = $data->bankk;
    $suspense_account->branch_id = $data->bid;
    $suspense_account->with_suspense_totals = $data->with_suspense_totals ?? true;
    $suspense_account->is_trial_balance = $data->is_trial_balance;
    $suspense_account->type = "SUSPENSES";
    $suspense_account->transaction_start_date = $data->transaction_start_date;
    $suspense_account->transaction_end_date = $data->transaction_end_date;
    $response['suspenses'] = $suspense_account->getAccounts2();
    $i = 0;
    for ($i = 0; $i < count($response['suspenses']); $i++) {
        $response['suspenses'][$i]['sub_accounts'] = [];
        $k = 0;
        foreach ($sub_accounts as $sub_account) {
            if ($response['suspenses'][$i]['account_id'] == $sub_account['main_account_id']) {
                array_push($response['suspenses'][$i]['sub_accounts'], $sub_account);
                array_splice($sub_account, $k, 1);
            }
            $k++;
        }
    }

    echo $ApiResponser::SuccessResponse($response);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
