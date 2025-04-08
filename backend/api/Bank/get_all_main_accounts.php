<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/SystemGlAccount.php';
require_once '../../models/Account.php';
require_once '../../models/Bank.php';
require_once '../../models/Transaction.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true);
try {

    $response = [];
    $database = new Database();
    $db = $database->connect();
    $ApiResponser = new ApiResponser();
    // $system_account = new SystemGlAccount($db);
    $account = new Account($db);
    $account->bank_id = @$data['bank_id'];
    $account->branch_id = @$data['branch'];

    $bank = new Bank($db);
    $bank->id = @$data['bank_id'];
    $bank->branch = @$data['branch'];
    // $bank->branch = @$data['branch'];

    $main_accounts = $account->getMainAccounts();

    $accounts = [];

    if ($bank->id) {
        $accounts = $bank->getAllBankChartAccounts2()->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $accounts = $bank->getAllBranchChartAccounts2()->fetchAll(PDO::FETCH_ASSOC);
    }
    $sub_accounts = $account->getSubAccounts();

    for ($i = 0; $i < count($main_accounts); $i++) {
        $main_accounts[$i]['accounts'] = [];
        $j = 0;

        /**
         * get accounts
         */
        foreach ($accounts as $account) {
            if ($main_accounts[$i]['use_name'] == $account['type']) {
                $account['sub_accounts'] = [];

                /**
                 * get sub accounts
                 */
                // $k = 0;
                // foreach ($sub_accounts as $sub_account) {
                //     if ($account['aid'] == $sub_account['main_account_id']) {
                //         array_push($account['sub_accounts'], $sub_account);
                //         array_splice($sub_account, $k, 1);
                //     }
                //     $k++;
                // }
                array_push($main_accounts[$i]['accounts'], $account);
                array_splice($accounts, $j, 1);
            }
            $j++;
        }
    }


    echo $ApiResponser::SuccessResponse($main_accounts);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
