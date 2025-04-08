<?php
require_once __DIR__ . '../../RequestHeaders.php';

try {

    $handler = new DbHandler();

    $loan = new Loan($handler);
    $data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;


    // $amount = @$data['savings'];
    $amount = @$data['sector'];
    $loan_id = decrypt_data(@$data['loan_id']);
    // if (!@$loan) {
    //     echo $ApiResponser::ErrorResponse("Loan missing from request");
    //     return;
    // }

    $handler->update('loan', [
        'enconomic_sector' => $amount,
    ], 'loan_no', $loan_id);

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
