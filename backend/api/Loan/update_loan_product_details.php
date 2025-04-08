<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';
try {

    $handler = new DbHandler();

    $database = new Database();
    $db = $database->connect();
    $loan = new Loan($db);
    $data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;

    $data['amount'] = amount_to_integer(@$data['penalty_fixed_amount']);
    $data['description'] = @$data['comment'];
    // $loan->data_array = @$data;

    $loan_id = decrypt_data(@$data['loan_id']);
    $loan_details = $loan->getLoanDetails($loan_id);
    if (!$loan_details) {
        echo $ApiResponser::ErrorResponse("Loan missing from request");
        return;
    }


    if ($data['penalty_type'] == 'fixed_amount') {
        $data['penalty_interest_rate'] = 0;
        $data['penalty_fixed_amount'] = amount_to_integer($data['penalty_fixed_amount']);
        $data['penalty_based_on'] = null;
    } else {
        $data['penalty_fixed_amount'] = 0;
        $data['penalty_interest_rate'] = amount_to_integer($data['penalty_interest_rate']);
    }

    $handler->update('loan', [
        'auto_pay' => (int)$data['auto_pay'],
        'auto_repay_penalty' => (int)$data['auto_repay_penalty'],
        'num_grace_periods' => (int)$data['num_grace_periods'],
        'penalty_based_on' => $data['penalty_based_on'],
        'penalty_fixed_amount' => (float)$data['penalty_fixed_amount'],
        'penalty_grace_type' => $data['penalty_grace_type'],
        'penalty_interest_rate' => (float)$data['penalty_interest_rate'],
        'penalty_max_days' => (int)$data['penalty_max_days'],
        'grace_period_type' => (int)$data['grace_applies'],
    ], 'loan_no', $loan_id);

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
