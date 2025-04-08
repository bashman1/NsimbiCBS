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
    $res = $response->closeFixed($_GET['id']);

    // var_dump($res);
    // exit;

    if ($res) {
        setSessionMessage(true, 'Fixed A/C Closure Reversed Successfully! Ensure to trash the disbursement transactions of both Interest & Principal off the savings A/C');
    } else {
        setSessionMessage(false, 'Process failed! Try again to reverse Fixed A/C Closure transaction');
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
