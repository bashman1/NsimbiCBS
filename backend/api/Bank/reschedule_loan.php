<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/functions.php';
require_once '../../config/DbHandler.php';
include_once '../../models/Reschedule.php';
include_once '../../models/Bank.php';
include_once '../../models/AuditTrail.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true);

$handler = new DbHandler();
try {
    $ApiResponser = new ApiResponser();
    $database = new Database();
    $db = $database->connect();
    $reschedule = new Reschedule($handler);

    // $handler->database->beginTransaction();

    $reschedule->bank_class_instance = new Bank($db);
    $reschedule->audit_trail_instance = new AuditTrail($db);
    $data['reschedule_date'] = db_date_format($data['reschedule_date']);
    $reschedule->data = $data;
    $results = $reschedule->rescheduleLoan();
    // $results = $data;
    if ($results === true) {
        echo $ApiResponser::SuccessMessage();
        return;
    }
    echo $ApiResponser::ErrorResponse($results);
    // $handler->database->commit();
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
    // $handler->database->rollback();
}
