<?php
require_once __DIR__ . '../../RequestHeaders.php';

try {
    $now = date('Y-m-d');
    $today_date = $now;
    $db_handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();

    $bank_instance = new Bank($db);


    $loans = $db_handler->database->fetchAll('SELECT * FROM `loan` WHERE `branchid`=\'\' AND (principal_due>0 OR interest_due>0) AND  `status` IN (?) ', loan_active_statuses());


    $schedules = [];
    foreach ($loans as $loan) {
        // try {
        $loan_id = $loan['loan_no'];
        $client = $db_handler->fetch('Client', 'userId', $loan['account_id']);

        if (!@$client) continue;

        // echo $ApiResponser::SuccessMessage($loan);
        // return;

        // $client_user = $db_handler->fetch('User', 'id', @$client['userId']);

        // $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];
        // $client_names = @$client_names ?? @$client['shared_name'];

        /**
         * if client is member we then use their account balance to collect loan
         */
        if ($client['membership_no'] > 0) {
            $is_member = true;
            $balance_field = 'acc_balance';
        }

        /**
         * if client is non member we the use their loan wallet to collect loan
         */
        else {
            $is_member = false;
            $balance_field = 'loan_wallet';
        }

        $wallet_balance = $client[$balance_field];
        $actual_balance = $client[$balance_field];


        $total_collected = 0;
        $interest_collected = 0;
        $principal_collected = 0;
        $penalty_amount = 0;

        /**
         * get last fully paid loan installation
         */


        // echo $ApiResponser::SuccessMessage($loan);
        // return;

        if ($wallet_balance > 0) {

            if ($wallet_balance <= (int)$loan['interest_due']) {
                $interest_collected = $wallet_balance;
            } else if ($wallet_balance > (int)$loan['interest_due']) {
                $interest_collected = (int)$loan['interest_due'];
                $balance = $wallet_balance - $interest_collected;
                /**
                 * if amount can clear principal
                 */
                if ($balance <= $loan['principal_due']) {
                    $principal_collected = $balance;
                } else {
                    $principal_collected = $loan['principal_due'];
                }
            }

            $total_collected = $interest_collected + $principal_collected;

            $transaction_principal_balance = max($loan['principal_due'] - $principal_collected, 0);
            $transaction_interest_balance = max($loan['interest_due'] - $interest_collected, 0);
            $total_transaction_balance = $transaction_principal_balance + $transaction_interest_balance;

            // collect the payment from savings
            // make loan repayment
            $endpoint = "https://app.ucscucbs.net/backend/api/Bank/create_loan_repay.php";
            $url = $endpoint;
            $data = array(
                'lno'      => $loan['loan_no'],
                'amount'      => $total_collected ?? 0,
                'date_of_next_pay'      =>  date('Y-m-d H:i:s'),
                'collection_date'      => date('Y-m-d H:i:s'),
                'balance'      => max(($loan['current_balance'] ?? 0) - ($total_collected ?? 0), 0),
                'interest'      => 0,
                'clear_loan'      => 0,
                'uid'      => $loan['account_id'],
                'notes'      => 'Auto-Deductions - Loan Repayment',
                'pay_method'      => 'saving',
                'bank_acc'      => '',
                'cash_acc'      => '',
                'cheque_no'      => 0,
                'send_sms'      => 0,
                'auth_id'      => $loan['loan_officer'],
                'clear_penalty'      => 0,
                'penalty_amount'      => 0,
                'branch'      => $loan['branchid'],

            );

            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'content' => json_encode($data),
                    'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
                )
            );

            $context  = stream_context_create($options);
            $responsen = file_get_contents($url, false, $context);
            $data = json_decode($responsen, true);



            /**
             * update loan dues
             * TODO uncomment below section
             */
            $db_handler->update('loan', [
                'principal_arrears' => $transaction_principal_balance,
                'interest_arrears' => $transaction_interest_balance,
            ], 'loan_no', $loan_id);



            $arrears_collection_date = @$loan['arrearsbegindate'] ? db_date_format(@$loan['arrearsbegindate']) : null;
            $new_arrears_collection_date = $arrears_collection_date;
            if ($transaction_principal_balance <= 0 && $transaction_interest_balance <= 0) {
                $new_arrears_collection_date = null;
            } else {
                if (!@$loan['arrearsbegindate']) {
                    $new_arrears_collection_date = $loan['next_due_date'] ?? $now;
                }
            }

            if ($arrears_collection_date != $new_arrears_collection_date) {
                $db_handler->update('loan', [
                    'arrearsbegindate' => $new_arrears_collection_date,
                    'status' => 4,
                ], 'loan_no', $loan_id);
            }
        }



        /**
         * check if loan product has penalty
         */
        if (@$loan['charge_penalty'] && $total_transaction_balance > 0) {

            $loan['penalty_interest_rate'] = (float)$loan['penalty_interest_rate'];
            $loan['penalty_fixed_amount'] = (float)$loan['penalty_fixed_amount'];
            /**
             * if loan has a grace period
             */
            $has_grace_periods = false;
            $grace_period_is_covered = false;
            $is_principal_penalty = @$loan['penalty_based_on'] == 'p' ? true : false;
            $is_interest_penalty = @$loan['penalty_based_on'] == 'i' ? true : false;
            $is_principal_interest_penalty = ($is_principal_penalty == false && $is_interest_penalty == false) ? true : false;
            // $is_principal_interest_penalty = @$loan['penalty_based_on'] == 'both' ? true : false;
            // if () {
            // $is_principal_interest_penalty = true;
            // }

            $create_penalty = false;
            if ((int)@$loan['num_grace_periods'] > 0) {
                $has_grace_periods = true;

                if (@$loan['penalty_grace_type'] != 'pay_none') {
                    $grace_period_is_covered = (int)$loan['num_grace_periods_covered'] >= (int)@$loan['num_grace_periods'] ? true : false;

                    if (!@$grace_period_is_covered) {
                        $db_handler->update('loan', [
                            'num_grace_periods_covered' => (int)$loan['num_grace_periods_covered'] + 1,
                        ], 'loan_no', $loan_id);
                    }

                    // if grace period is covered, apply loan penalty
                    if (@$grace_period_is_covered) {
                        $create_penalty = true;

                        if ((int)$loan['penalty_fixed_amount'] > 0) {
                            $penalty_amount = (int)$loan['penalty_fixed_amount'];
                        } else {

                            if (@$loan['penalty_grace_type'] == 'pay_i') {
                                $penalty_amount = ($loan['penalty_interest_rate'] / 100) * $transaction_interest_balance;
                            } else  if (@$loan['penalty_grace_type'] == 'pay_p') {
                                $penalty_amount = ($loan['penalty_interest_rate'] / 100) * $transaction_principal_balance;
                            } else {
                                $create_penalty = false;
                                // $penalty_amount = ($loan['penalty_interest_rate'] / 100) * $total_transaction_balance;
                            }
                        }
                    }
                }
            } else {
                $create_penalty = true;
                if ($is_principal_penalty) {
                    $penalty_amount = ($loan['penalty_interest_rate'] / 100) * $transaction_principal_balance;
                } else if ($is_interest_penalty) {
                    $penalty_amount = ($loan['penalty_interest_rate'] / 100) * $transaction_interest_balance;
                } else {
                    $penalty_amount = ($loan['penalty_interest_rate'] / 100) * $total_transaction_balance;
                }
            }

            /**
             * create penalty
             */
            if ($create_penalty) {
                if (@$loan['penalty_fixed_amount'] > 0 || @$loan['penalty_interest_rate'] > 0) {
                 

                    $db_handler->update('loan', [
                        'penalty_balance' => (int)@$loan['penalty_balance'] + $penalty_amount
                    ], 'loan_no', $loan_id);
                }
            }
        }
    }

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
