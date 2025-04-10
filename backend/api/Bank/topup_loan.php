<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/functions.php';
require_once '../../config/DbHandler.php';
require_once '../../models/Reschedule.php';
require_once '../../models/Bank.php';
require_once '../../models/AuditTrail.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true);
$ApiResponser = new ApiResponser();

try {
    $database = new Database();
    $db = $database->connect();

    $handler = new DbHandler();

    $reschedule = new Reschedule($handler);
    $data['application_date'] = db_date_format(@$data['application_date']);
    $data['loan_amount'] = amount_to_integer(@$data['loan_amount']);
    $reschedule->data = $data;
    $reschedule->bank_class_instance = new Bank($db);
    $reschedule->audit_trail = new AuditTrail($db);

    $result = $reschedule->applyLoanTopUp();
    // $result = $data;

    if ($result === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }

    echo $ApiResponser::ErrorResponse($result);
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
