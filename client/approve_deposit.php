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
    $res = $response->approveTrxn($_GET['id'],$user[0]['userId']);

    // var_dump($res);
    // exit;

    if ($res['success']) {
        setSessionMessage(true,'Transaction Approved Successfully! ');
    } else {
        setSessionMessage(false,'Process failed! Try again to approve transaction');
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
