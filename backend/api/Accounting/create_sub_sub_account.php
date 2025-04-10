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

$request = json_decode(file_get_contents("php://input"), true);

$accounts->details = $request['data'];


try {
    $stmt = $accounts->createSubAccount();

    if ($stmt) {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = true;
        $userArr['statusCode'] = "200";
        $userArr['count'] = $itemCount;


        http_response_code(200);
        echo json_encode($userArr);
    } else {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = false;
        $userArr['statusCode'] = "400";
        $userArr['message'] = $stmt;

        http_response_code(200);
        echo json_encode($userArr);
    }
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
    //throw $th;
}
return;
