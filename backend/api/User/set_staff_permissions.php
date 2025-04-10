<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../models/Permission.php';
include_once '../../models/User.php';
require_once '../ApiResponser.php';


$database = new Database();
$db = $database->connect();

$ApiResponse = new ApiResponser();


$data = json_decode(file_get_contents("php://input"));

try {

    $permission = new Permission($db);
    $permission->staff_id = $data->staff_id;
    $permission->permissions = $data->permissions ?? [];
    $permission->child_permissions = $data->child_permissions ?? [];

    $user = new User($db);
    $user->id = $permission->staff_id;
    $permission->staff_details = $user->getStaffDetails()->fetch(PDO::FETCH_ASSOC);
    $permission->role_uuid = $permission->staff_details['roleId'];
    $result = $permission->saveStaffPermissions();

    echo $ApiResponse::SuccessMessage();
    // echo $ApiResponse::SuccessResponse($data->staff_id);
} catch (\Throwable $th) {
    // echo $ApiResponse::ErrorResponse("Error creating permissions");
    echo $ApiResponse::ErrorResponse($th->getMessage());
}
