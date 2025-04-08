<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
// include_once '../../config/handler.php';
include_once '../../models/Permission.php';
require_once '../ApiResponser.php';

$data = json_decode(file_get_contents("php://input"));

$database = new Database();
$db = $database->connect();

// $handler = new Handler();

$ApiResponse = new ApiResponser();
try {
    $permission = new Permission($db);
    $permission->role_uuid = $data->role_id;
    $role = $permission->getRole();
    $permission->role_id = $role['role_id'];
    $role['permissions'] = $permission->getRolePermissions();
    $role['child_permissions'] = $permission->getRoleChildPermissions();

    echo $ApiResponse::SuccessResponse($role);
} catch (\Throwable $th) {
    // echo $ApiResponse::ErrorResponse("Error getting role");
    echo $ApiResponse::ErrorResponse($th->getMessage());
}
