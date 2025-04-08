<?php
require_once __DIR__ . '../../RequestHeaders.php';

try {

    $handler = new DbHandler();

    $loan = new Loan($handler);
    $data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;


    // $amount = @$data['savings'];
    $amount = str_replace(",", "", @$data['savings']);
    $loan_id = decrypt_data(@$data['loan_id']);
    // if (!@$loan) {
    //     echo $ApiResponser::ErrorResponse("Loan missing from request");
    //     return;
    // }

    $handler->update('loan', [
        'forced_saving' => $amount,
    ], 'loan_no', $loan_id);

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
