<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/Account.php';
require_once '../ApiResponser.php';

$ApiResponse = new ApiResponser();

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$accounts = new Account($db);

$accounts->account_id = $_GET['id'];
$accounts->branch_id = $_GET['branch'];
$accounts->bank_id = $_GET['bank'];
$accounts->user_id = $_GET['user'];


// try {
    $details = $accounts->delete_sub_account();
    

    if ($details === true) {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = true;
        $userArr['message'] = "Account Deleted Successfully!";
        http_response_code(200);
        echo json_encode($userArr);
    } else {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = false;
        $userArr['message'] = $details;
        http_response_code(200);
        echo json_encode($userArr);
    }
// } catch (\Throwable $th) {
//     echo json_encode($th->getMessage());
//     //throw $th;
// }
// return;
