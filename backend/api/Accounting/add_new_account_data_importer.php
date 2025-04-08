<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/DbHandler.php';
require_once '../../models/Account.php';
require_once '../../models/DataImporter.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input")) ?? $_REQUEST;
// $records = json_decode($data['actual_data'], true);
$ApiResponser = new ApiResponser();

try {
    $db_handler = new DbHandler();
    $account = new Account($db_handler);
    $account->request = $data;
    $results = $account->create_new_account_data_importer();

    if ($results === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($results);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
return;
