<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../models/Permission.php';
require_once '../ApiResponser.php';

$database = new Database();
$db = $database->connect();

$ApiResponse = new ApiResponser();

$data = json_decode(file_get_contents("php://input"));

try {

    $permission = new Permission($db);
    $permission->role_uuid = $data->role_uuid;
    $permission->name = $data->name;
    $permission->bank_id = $data->bank_id;
    $permission->branch_id = $data->branch_id ?? $data->branch;
    $permission->description = $data->description;
    $permission->permissions = $data->permissions ?? [];
    $permission->child_permissions = $data->child_permissions ?? [];

    // echo $permission;
    // exit;

    $permission->saveRole();

    echo $ApiResponse::SuccessMessage();
} catch (\Throwable $th) {
    // echo $ApiResponse::ErrorResponse("Error creating permissions");
    echo $ApiResponse::ErrorResponse($th->getMessage());
}
