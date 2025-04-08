<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/DbHandler.php';
require_once '../../config/functions.php';
require_once '../../models/Loan.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';


try {
    $ApiResponser = new ApiResponser();

    $handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();

    $loan = new Loan($handler);
    $data = json_decode(file_get_contents("php://input"), true);

   
    $loan->data_array = @$data;

    $loan->bank_object = new Bank($db);

    $result = $loan->writeOffLoan();

    if ($result === true) {
        echo $ApiResponser::SuccessMessage();
        // return;
    } else {

        echo $ApiResponser::ErrorResponse($result);
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
