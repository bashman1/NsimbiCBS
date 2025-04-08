<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $data = $_GET;
    $data['record_id'] = decrypt_data($_GET['id']);

    $res = $response->import_batch_record_to_main_database($data);
    // var_dump($res);
    // exit;
    if (@$res['success']) {
        setSessionMessage();
    } else {
        setSessionMessage(false, $res['message']);
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
