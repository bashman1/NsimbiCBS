<?php
require_once __DIR__ . '../../RequestHeaders.php';

try {
    $date = date('Y-m-d');
    // $date = '2023-06-01';
    $db_handler = new DbHandler();


    $loans = $db_handler->database->fetchAll('SELECT * FROM share_register WHERE share_amount<1000000 AND branch_id=\'76de125b-788e-4ac6-a674-384c356b4341\'');

    foreach ($loans as $loan) {

        try {
            $share_amount = 0;
            $shares = 0;

            $share_amount = $loan['no_shares'] * 1800;
            $shares = ($share_amount / 30000);

            $new_share_amount = $loan['share_amount'] + $share_amount;
            $new_shares = $loan['no_shares'] + $shares;

            /**
             * update loan dues (principal and interest due)
             */
            $db_handler->update('share_register', [
                'share_amount' => $new_share_amount,
                'no_shares' => $new_shares,
            ], 'userid', $loan['userid']);


            // share purchase trxns

            $db_handler->insert('share_purchases', [
                'user_id' => $loan['userid'],
                'decription' => 'Share Dividends',
                'no_of_shares' => $shares,
                'current_share_value' => 30000,
                'amount' => $share_amount,
                'pay_method' => 'cash',
                'notes' => 'Auto Share Dividends',
                'record_date' => date('Y-m-d'),
                'added_by' => 49258,
                'branch_id' => $loan['branch_id'],
                'pay_method_acid' => 'ae659333-a861-4fcc-baeb-c7400d5f4e12',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
