<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
require_once '../../config/functions.php';
include_once '../../models/Bank.php';
require_once '../ApiResponser.php';


try {
    $ApiResponser = new ApiResponser();

    $database = new Database();
    $db = $database->connect();


    $loan = new Bank($db);
    $data = json_decode(file_get_contents("php://input"));

    $loan->loan_id = @$data->auth_by;
    $loan->createdAt = @$data->branchId;
    $loan->amount = @$data->fees;
    $loan->affected_acid = @$data->acid;
    $loan->date_of_next_pay = @$data->actype;
    $loan->collection_date = db_date_format(@$data->date);
  
    $loan->identificationNumber = @$data->send_sms ?? 0;
    $loan->serialNumber = @$data->name ?? '';


    // echo $data;
    $result = $loan->createGeneralFeesInitiation();
    // $result = [];

    if ($result === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
