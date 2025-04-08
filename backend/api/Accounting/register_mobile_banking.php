<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
// $records = json_decode($data->actual_data, true);
$ApiResponser = new ApiResponser();

try {

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Bank($db);

    $item->id = $data['phone'];
    $item->bank_acc =  $data['client_id'];

    $item->setClientDefaultMpin2();



    echo $ApiResponser::SuccessMessage("Mobile Banking Registration Succesful!");
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
