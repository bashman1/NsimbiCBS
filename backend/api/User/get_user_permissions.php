<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/User.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new User($db);

    $data = json_decode(file_get_contents("php://input"));

    // $item->id = $data->id;
    
    try {
        // echo json_encode($item->getUserPermissions());
        //code...
    } catch (\Throwable $th) {
        //throw $th;
    }

    return;