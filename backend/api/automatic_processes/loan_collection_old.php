<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../config/database.php';
include_once '../../models/Bank.php';
require_once '../../config/functions.php';
require_once '../ApiResponser.php';

try {
    $now = date('Y-m-d');
    $db_handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();

    $bank_instance = new Bank($db);

    $active_statuses = [2, 3, 4];
    /**
     * TODO uncomment actual loans query
     */
    //? $loans = $db_handler->database->fetchAll('SELECT * FROM `loan` WHERE `status` IN (?) AND %and', $active_statuses, [['auto_pay = ?', 1]]);


    /**
     * TODO remove below query in production mode
     */
    $and_where = [
        // ['DATE(date_of_next_pay) > ?', $now],
        ['branchid = ?', 'b69be82c-3bda-465c-a58b-8c8fa0ba5e48'],
    ];
    $loans = $db_handler->database->fetchAll('SELECT * FROM `loan` WHERE `status` IN (?) AND %and', $active_statuses, $and_where);

    // echo $ApiResponser::SuccessMessage($loans);
    // return;
    // $loan_ids = array_column($loans, 'loan_no');

    // $where = [
    //     ['date_of_payment <= ?', $now],
    //     ['status <= ?', 'active'],
    // ];

    // $schedule_records = $db_handler->database->fetchAll('SELECT * FROM `loan_schedule` WHERE `loan_no` IN (?) %and ORDER BY %by', $loan_ids, $where, ['date_of_payment' => true]);



    $schedules = [];
    foreach ($loans as $loan) {
        try {
            $loan_id = $loan['loan_no'];
            $client = $db_handler->fetch('Client', 'userId', $loan['account_id']);

            if (@$client) {

                $client_user = $this->conn->fetch('User', 'id', $client['userId']);
                $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];
                $client_names = @$client_names ?? @$client['shared_name'];

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

                $amount_balance = $client[$balance_field];
                $actual_balance = $client[$balance_field];



                $total_paid = 0;
                $interest_paid = 0;
                $principal_paid = 0;

                /**
                 * get last fully paid loan installation
                 */


                if ($amount_balance > 0) {
                    // $actual_schedule = $db_handler->database->fetch('SELECT * FROM `loan_schedule` WHERE %and ORDER BY %by', [
                    //     ['DATE(date_of_payment) = ?', $now],
                    //     ['status = ?', 'active'],
                    //     ['loan_id = ?', $loan_id],
                    // ], ['date_of_payment' => false]);

                    // $current_schedule = $db_handler->database->fetch('SELECT * FROM `loan_schedule` WHERE %and ORDER BY %by', [
                    //     ['status = ?', 'active'],
                    //     ['loan_id = ?', $loan_id],
                    // ], ['date_of_payment' => false]);


                    $principal_paid_current = (int)$current_schedule['principal_paid'];
                    $interest_paid_current = (int)$current_schedule['interest_paid'];

                    $current_schedule['outstanding_interest'] = max((int)$current_schedule['interest'] - $interest_paid_current);
                    $current_schedule['outstanding_principal'] = max((int)$current_schedule['principal'] - $principal_paid_current);

                    if ($amount_balance <= (int)$current_schedule['outstanding_interest']) {
                        $interest_paid = $amount_balance;
                    } else if ($amount_balance > (int)$current_schedule['outstanding_interest']) {
                        $interest_paid = (int)$current_schedule['outstanding_interest'];

                        $balance = $amount_balance - $interest_paid;

                        /**
                         * if amount can clear principal
                         */
                        if ($balance <= $current_schedule['outstanding_principal']) {
                            $principal_paid = $balance;
                        } else {
                            $principal_paid = $current_schedule['outstanding_principal'];
                        }
                    }
                }

                $total_paid = $interest_paid + $principal_paid;
                $amount_balance -= $total_paid;
                $amount_balance = max($amount_balance, 0);

                $transaction_principal_balance = $current_schedule['principal'] - ($principal_paid + $current_schedule['principal_paid']);
                $transaction_interest_balance = $current_schedule['interest'] - ($interest_paid + $current_schedule['interest_paid']);

                $active_status = 'active';
                $paid_status = 'paid';

                $schedule_status = $active_status;
                if ($transaction_principal_balance == 0 && $transaction_interest_balance == 0) {
                    $schedule_status = $paid_status;
                }

                /**
                 * create arrears if any
                 */

                if ($actual_schedule && $actual_schedule['schedule_id'] != $current_schedule['schedule_id']) {
                    $loan['principal_arrears'] = (int)$loan['principal_arrears'] + $actual_schedule['principal'];
                    $loan['interest_arrears'] = (int)$loan['interest_arrears'] + $actual_schedule['interest'];
                }

                if ($transaction_principal_balance > 0) {
                    $db_handler->update('loan', [
                        'principal_arrears' => (int)$loan['principal_arrears'] + $transaction_principal_balance,
                    ], 'loan_no', $loan_id);
                }

                if ($transaction_interest_balance > 0) {
                    $db_handler->update('loan', [
                        'interest_arrears' => (int)$loan['interest_arrears'] + $transaction_interest_balance,
                    ], 'loan_no', $loan_id);
                }


                /**
                 * if any amount has been paid
                 */
                if ($total_paid > 0) {
                    /**
                     * update schedule
                     */
                    $db_handler->update('loan_schedule', [
                        'principal_paid' => (int)$current_schedule['principal_paid'] + $principal_paid,
                        'interest_paid' => (int)$current_schedule['interest_paid'] + $interest_paid,
                        'outstanding_principal' => $transaction_principal_balance,
                        'outstanding_interest' => $transaction_interest_balance,
                    ], 'schedule_id', $current_schedule['schedule_id']);


                    /** 
                     * update member balance
                     */
                    $member_account_balance = max($actual_balance - $amount_balance, 0);
                    $db_handler->update('Client', [
                        $balance_field => @$member_account_balance,
                    ], 'userId', $client['userId']);


                    /**
                     * create transaction
                     */
                    $db_handler->insert('transactions', [
                        'amount' => $principal_paid,
                        'description' => 'Loan collection',
                        '_authorizedby' => 'automatically',
                        '_actionby' => 'system',
                        'acc_name' => $client_names,
                        'mid' => $client['userId'],
                        'approvedby' => 'system',
                        '_branch' => $client['branchId'],
                        't_type' => 'L',
                        'date_created' => date('Y-m-d'),
                        'loan_id' => $loan_id,
                        'outstanding_amount' => max((int)$loan['principal_balance'] - $principal_paid, 0),
                        'outstanding_amount_total' => max((int)$loan['principal_balance'] - $principal_paid, 0),
                        'loan_interest' => $interest_paid,
                        'outstanding_interest' => max((int)$loan['interest_balance'] - $interest_paid, 0),
                        'outstanding_interest_total' => max((int)$loan['interest_balance'] - $interest_paid, 0),
                        'pay_method' => 'cash',
                        'loan_penalty' => $loan['penalty_balance'],
                    ]);

                    /**
                     * upload last date of payment
                     */
                    $db_handler->update('loan', [
                        'last_date_of_pay' => date('Y-m-d H:i:s'),
                    ], 'loan_no', $loan_id);
                }

                /**
                 * update loan balances
                 */

                /** 
                 * check if loan is fully paid
                 */

                // find any active loan schedule
                $pending = $db_handler->database->fetch('SELECT * FROM `loan_schedule` WHERE %and ', [['loan_id = ? ', $loan_id, 'status = ? ', 'active']]);

                $clear_loan = @!$pending && $loan['penalty_balance'] <= 0 ? true : false;

                // $total_arrears = 
                // if(@$loan['interest_arrears'] > 0 || @$loan['principal_arrears'])

                $bank_instance->updateTotalLoanAmount($loan_id, $clear_loan);


                /**
                 * update schedule iteratively as per amount paid
                 */




                /**
                 * penalty charges
                 */

                 // check if loan product has penalty [penalty]

                 /**
                  * apply grace period
                  */

                 /**
                  * if loan has penalty 
                  * check whether its fixed or rate based
                  * if is fixed, increment loan penalty by loan product penalty fixed amount
                  * if is rate based, compute amount by based on field of loan product
                  */


                  /**
                   * add auto pay for penalties
                   */


                // $principal_components = $bank_instance->getLoanPrincipal($lloan_id);
                // $interest_components = $bank_instance->getLoanInterest($loan_id);
            }
        } catch (\Throwable $th) {
            continue;
        }
    }

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
