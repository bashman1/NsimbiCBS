<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
// include_once '../../config/handler.php';
include_once '../../models/WorkingHour.php';
include_once '../../models/Bank.php';
include_once '../../models/Permission.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"));

$database = new Database();
$db = $database->connect();

// $handler = new Handler();

$ApiResponse = new ApiResponser();
try {
    $working_hour = new WorkingHour($db);
    $bank = new Bank($db);
    $permission = new Permission($db);

    $branch = $bank->getBranchDetails($data->branch_id);
    $working_hour->branch_id = $data->branch_id;
    $branch['working_hours'] = $working_hour->getBranchWorkingHours();
    $branch['roles'] = $permission->getBranchRoles($working_hour->branch_id);

    echo $ApiResponse::SuccessResponse($branch);
} catch (\Throwable $th) {
    echo $ApiResponse::ErrorResponse("Error getting role");
    // echo $ApiResponse::ErrorResponse($th->getMessage());
}
