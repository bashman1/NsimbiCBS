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
    $data = ['batch_id' => decrypt_data($_GET['id']), 'importer_type' => $_GET['type']];
    $res = $response->deleteDataImporterBatch($data);

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
