<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
// require_once '../../config/DbHandler.php';
require_once '../../config/functions.php';
require_once '../../models/Loan.php';
require_once '../ApiResponser.php';


$ApiResponser = new ApiResponser();
try {

    $database = new Database();
    // $handler = new DbHandler();
    $db = $database->connect();
    // $db = $database->connect();


    $loan = new Loan($db);
    $data = json_decode(file_get_contents("php://input"), true);

    $data['amount'] = amount_to_integer(@$data['amount']);
    $data['description'] = @$data['comment'];
    $loan->data_array = @$data;

    $result = $loan->processBranchRequest();

    if ($result === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
