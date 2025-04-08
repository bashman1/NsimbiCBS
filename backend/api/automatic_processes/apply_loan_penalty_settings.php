<?php
require_once __DIR__ . '../../RequestHeaders.php';

try {

    $db_handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();
    $loan_instance = new Loan($db);

    if (IS_PRODUCTION_ENVIRONMENT) {
        $loans = $db_handler->database->fetchAll('SELECT * FROM `loan` WHERE `status` IN (?)', loan_active_statuses());
    } else {

        $loans = $db_handler->database->fetchAll('SELECT * FROM `loan` WHERE `status` IN (?) AND %and', loan_active_statuses(), [
            ['branchid = ?', 'b69be82c-3bda-465c-a58b-8c8fa0ba5e48'],
        ]);
    }

    foreach ($loans as $loan) {
        $loan_instance->ApplyLoanPenaltySettings($loan['loan_no']);
    }

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
