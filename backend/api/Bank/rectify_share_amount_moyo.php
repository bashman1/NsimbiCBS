<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/DbHandler.php';
require_once '../../config/functions.php';
include_once '../../models/Transaction.php';
include_once '../../models/User.php';
require_once '../../models/Loan.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';


try {
    $ApiResponser = new ApiResponser();

    $handler = new DbHandler();
    $database = new Database();
    $db = $database->connect();

    $loan = new Bank($db);

    $data = json_decode(file_get_contents("php://input"));


    $loan->rectifyShareAmount(amount_to_integer(@$data->difference), @$data->notes, db_date_format(@$data->collection_date), @$data->uid, @$data->auth_id, @$data->branch_id,@$data->main_acc,@$data->shares);

    echo $ApiResponser::SuccessMessage();
    return;
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
