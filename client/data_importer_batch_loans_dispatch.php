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
    $res = $response->approveLoanBatch($_GET['id']);
    if ($res) {
        setSessionMessage();
    } else {
        setSessionMessage(false, $res['message']);
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
