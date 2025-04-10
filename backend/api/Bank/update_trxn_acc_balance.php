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

    $loan->loan_id = @$data->id;
    $loan->deletedAt = @$data->acid;
    $loan->updatedAt = @$data->main_acc;
    $loan->name = @$data->user;
    $loan->location = @$data->notes;
    $loan->amount = amount_to_integer(@$data->bal);
    $loan->createdAt = amount_to_integer(@$data->orig_bal);



    // echo $data;
    $result = $loan->updateTrxnAccBalance();
    // $result = [];

    if ($result === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
