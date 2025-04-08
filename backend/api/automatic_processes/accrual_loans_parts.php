<?php
require_once __DIR__ . '../../RequestHeaders.php';

// $start_lno = '';
// $last_lno = '';
try {
    $date = date('Y-m-d');
    // $date = '2023-06-01';
    $db_handler = new DbHandler();

    $data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;

    $loans = $db_handler->database->fetchAll('SELECT * FROM `loan` WHERE `branchid` =\'' . $data['branch'] . '\' AND `status` IN (?)', loan_active_statuses());

    // $start_lno = $loans[0]['loan_no'];
    // $last_lno = $loans[169]['loan_no'];
    // } else {
    //     $and_where = [
    //         // ['DATE(date_of_next_pay) > ?', $date],
    //         ['branchid = ?', 'b69be82c-3bda-465c-a58b-8c8fa0ba5e48'],
    //     ];
    //     $loans = $db_handler->database->fetchAll('SELECT * FROM `loan` WHERE `status` IN (?) AND %and', loan_active_statuses(), $and_where);
    // }

    // echo $ApiResponser::SuccessMessage($loans);
    // return;

    $schedules = [];
    foreach ($loans as $loan) {

        //TODO remove this conetent
        // if ($loan['loan_no'] != 317) continue;

        $db_handler->update('loan', [
            'principal_due' => 0,
            'interest_due' => 0,
            'principal_arrears' => 0,
            'interest_arrears' => 0
        ], 'loan_no', $loan['loan_no']);

        try {
            $schedule = null;
            /**
             * get all loan schedules prior date selected
             */
            $schedules = $db_handler->database->fetchAll('SELECT * FROM `loan_schedule` WHERE %and ORDER BY %by', [
                ['DATE(date_of_payment) <= ?', $date],
                ['status = ?', 'active'],
                ['loan_id = ?', $loan['loan_no']],
            ], ['date_of_payment' => false]);

            if (count($schedules) > 0) {
                $total_principal_paid = array_sum(array_column($schedules, 'principal_paid'));
                $total_interest_paid = array_sum(array_column($schedules, 'interest_paid'));

                $total_principal_due = array_sum(array_column($schedules, 'principal'));
                $total_interest_due = array_sum(array_column($schedules, 'interest'));

                // $num_schedules = count($schedules);


                // echo $ApiResponser::SuccessMessage($schedules);
                // return;


                /**
                 * get last fully paid loan installation
                 */
                $last_payment = $db_handler->database->fetch('SELECT * FROM `loan_schedule` WHERE %and ORDER BY %by', [
                    ['status = ?', 'paid'],
                    ['loan_id = ?', $loan['loan_no']],
                ], ['date_of_payment' => false]);
                // $last_payment = $db_handler->fetch('loan_schedule', ['loan_id' => $loan['loan_no'], 'status' => 'paid']);
                if (@$last_payment) {
                    $date_of_last_pay = db_date_format(@$last_payment['date_of_payment']);
                }



                /**
                 * pluck first record from scheule as current first payment
                 */
                $first_schedule = @$schedules[0];
                // echo $ApiResponser::SuccessMessage(db_date_format($first_schedule['date_of_payment']));
                // return;
                if (@$first_schedule['principal_paid'] > 0 || @$first_schedule['interest_paid'] > 0) $date_of_last_pay = db_date_format(@$first_schedule['date_of_payment']);



                /**
                 * pluck last record from scheule as current last payment
                 */
                $last_schedule = @$schedules[count($schedules) - 1];
                $next_due_date = db_date_format(@$last_schedule['date_of_payment']);

                // if (@$last_schedule) {
                //     $where = [
                //         ['status = ?', 'active'],
                //         ['loan_id = ?', $loan['loan_no']],
                //         ['schedule_id > ?', $last_schedule['schedule_id']],
                //     ];

                //     /**
                //      * if we have next payment record, then set it as the next due date
                //      */
                //     $next_payment = $db_handler->database->fetch('SELECT * FROM `loan_schedule` WHERE %and ORDER BY %by', $where, ['date_of_payment' => true]);

                //     if (@$next_payment) $next_due_date = db_date_format(@$next_payment['date_of_payment']);
                // }

                /**
                 * update loan dues (principal and interest due)
                 */
                $db_handler->update('loan', [
                    'principal_due' => max((float)$total_principal_due - (float)$total_principal_paid, 0),
                    'interest_due' => max((float)$total_interest_due - (float)$total_interest_paid, 0),
                    'date_of_last_pay' => @$date_of_last_pay,
                    'next_due_date' => @$next_due_date,
                    'status' => max((float)$total_principal_due - (float)$total_principal_paid, 0) > 0 ? 3 : 2,
                ], 'loan_no', $loan['loan_no']);
            }

            // echo $ApiResponser::SuccessMessage(db_date_format($first_schedule['date_of_payment']));
            // return;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
