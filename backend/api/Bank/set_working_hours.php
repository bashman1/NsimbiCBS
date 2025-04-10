<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
// include_once '../../config/handler.php';
include_once '../../models/WorkingHour.php';
include_once '../../models/Bank.php';
include_once '../../models/Permission.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"), true);

$database = new Database();
$db = $database->connect();

$ApiResponse = new ApiResponser();
try {
    $working_hour = new WorkingHour($db);
    $working_hour->branch_id = $data['branch_id'];
    $working_hour->working_hours = @$data['working_hours'] ?? [];
    $working_hour->working_hours_roles = @$data['working_hours_roles'] ?? [];

    $result = $working_hour->setBranchWorkingHours();

    echo $ApiResponse::SuccessMessage($result);
} catch (\Throwable $th) {
    // echo $ApiResponse::ErrorResponse("Error saving working hours");
    echo $ApiResponse::ErrorResponse($th->getMessage());
}
