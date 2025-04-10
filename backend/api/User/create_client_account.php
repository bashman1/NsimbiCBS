<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/User.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';


$request = json_decode(file_get_contents("php://input"), true);
// echo $request->id;
// exit;

// echo $request['data']['uid'];
// return;
$ApiResponser = new ApiResponser();

// echo "fine";
// return;

try {
    $data = $request['data'];
    $database = new Database();
    $db = $database->connect();
    /*  */
    $client_object = new User($db);
    $client_object->id = @$data['uid'];
    $client_object->account_id = @$data['account_id'];
    $client_object->serialNumber = 100;
    $client_object->mno = 0;
    $client_object->message_consent = 0;

    $client = $client_object->createClientAccount();
    // echo json_encode($client);
    // return;

    /**
     * process to generate client account number
     */
    // get account number length && filler character in the account number of the bank
    $getAccValues = $client_object->getBankAccLength($client['branchId']);


    // separate the return merge separated by / , i.e acc-length and filler character
    $myArray = explode('/', $getAccValues);
    $accLength = (int)$myArray[0];
    $paddValue = $myArray[1];


    $accCode = $client_object->getAccountCode($client['actype']);
    $branchCode = $client_object->getBranchCode($client['branchId']);
    $codelength = strlen($accCode['ucode']);
    $uselength = $accLength - $codelength;



    // generate the account number now
    $take = $client['userId'];
    $rett = $client['id'];
    $padd = sprintf('%' . $paddValue . '' . $uselength . 'd', $take);

    $acc_use_no = $branchCode . '-' . $accCode['ucode'] . '-' . $take;

    $client_object->setClientAccountNumber($acc_use_no, $rett);

    echo $ApiResponser::SuccessMessage();
} catch (\Throwable $th) {
    // echo $ApiResponser::ErrorResponse($th);
    echo $th->getMessage();
}
return;
