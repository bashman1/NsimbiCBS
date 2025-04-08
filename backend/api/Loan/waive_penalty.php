<?php
require_once __DIR__.'../../RequestHeaders.php';

// require_once '../../config/database.php';
require_once '../../config/DbHandler.php';
require_once '../../config/functions.php';
require_once '../../models/Loan.php';
require_once '../ApiResponser.php';


try {
    $ApiResponser = new ApiResponser();

    // $database = new Database();
    $handler = new DbHandler();
    // $db = $database->connect();
    // $db = $database->connect();


    $loan = new Loan($handler);
    $data = json_decode(file_get_contents("php://input"), true);

    $data['amount'] = amount_to_integer(@$data['amount']);
    $data['date_of_waiver'] = db_date_format(@$data['date_of_waiver']);
    $data['description'] = @$data['comment'];
    $loan->data_array = @$data;

    $result = $loan->waivePenalty();

    if ($result === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
