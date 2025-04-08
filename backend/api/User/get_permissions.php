<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
// include_once '../../config/handler.php';
include_once '../../models/Permission.php';
require_once '../ApiResponser.php';

$database = new Database();
$db = $database->connect();

// $handler = new Handler();

$ApiResponse = new ApiResponser();
try {
    $permission = new Permission($db);
    $permissions = $permission->getMainPermissions();

    $child_permission = new Permission($db);
    $permissions = array_map(function ($record) use ($child_permission) {
        $record['child_permissions'] = $child_permission->getSubPermissions($record['id']);
        return $record;
    }, $permissions);

    echo $ApiResponse::SuccessResponse($permissions);
} catch (\Throwable $th) {
    echo $ApiResponse::ErrorResponse("Error getting permissions");
    // echo $ApiResponse::ErrorResponse($th->getMessage());
}
