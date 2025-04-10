<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/User.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';


$request = json_decode(file_get_contents("php://input"));

$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();

    $client_object = new User($db);
    $client_object->id = @$request->id;
    $client_object->actype = @$request->account_id;
    $client_object->mno = @$request->membership_no;

    $client_details = $client_object->getClientDetails()->fetch(PDO::FETCH_ASSOC);
    $client_accounts = $client_object->getClientAccounts();

    $bank_object = new Bank($db);
    // var_dump($client_details);

    // exit;
    $bank_object->branch = $client_details['branchId'];
    $client_bank_accounts = $bank_object->getBranchSavingAccounts()->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        'client_accounts' => $client_accounts,
        'accounts' => $client_bank_accounts,
        'client' => $client_details,
    ];
    echo $ApiResponser::SuccessResponse($data);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th);
    //throw $th;
}
return;
