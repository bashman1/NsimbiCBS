<?php
require_once __DIR__ . '../../RequestHeaders.php';

try {

    $handler = new DbHandler();

    $loan = new Loan($handler);
    $data = json_decode(file_get_contents("php://input"), true) ?? $_REQUEST;



    $loan_id = decrypt_data(@$data['loan_id']);
    if (!@$loan) {
        echo $ApiResponser::ErrorResponse("Loan missing from request");
        return;
    }

    $fees = '';

    if (@$data['fees']) {
        if (sizeof($data['fees']) > 0) {
            foreach ($data['fees'] as $selectedOption) {
                if ($selectedOption != 0) {
                    if ($fees != '') {
                        $fees = $fees . ',' . $selectedOption;
                    } else {
                        $fees = $selectedOption;
                    }
                }
            }
        }
    }


    $handler->update('loan', [
        'fees_to_charge' => $fees,
    ], 'loan_no', $loan_id);

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
