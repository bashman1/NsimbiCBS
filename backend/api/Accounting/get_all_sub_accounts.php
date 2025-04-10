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


try {
    $details = $accounts->get_all_sub_accounts();
    $itemCount = $details->rowCount();

    if ($itemCount > 0) {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = true;
        $userArr['statusCode'] = "200";
        $userArr['count'] = $itemCount;

        while ($row = $details->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $u = array(
                "id" => $id,
                "name" => $name,
                "balance" => $balance,
                "type" => $type,
                "bid" => $branchId,
                "description" => $description,
                "issys" => $isSystemGenerated,


            );

            array_push($userArr['data'], $u);

            // array_push($userArr['sub'], $u2);
        }

        http_response_code(200);
        echo json_encode($userArr);
    } else {
        // echo json_encode('Accounts not found');
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = false;
        $userArr['statusCode'] = "200";
        $userArr['count'] = 0;

       

        http_response_code(200);
        echo json_encode($userArr);
    }
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
    //throw $th;
}
return;
