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
    $res = $response->reverseTrxn($_GET['id']);

    // var_dump($res);
    // exit;

    if ($res) {
        setSessionMessage(true, 'Transaction Reversed Successfully! ');
    } else {
        setSessionMessage(false, 'Process failed! Try again to reverse transaction');
    }

    header('Location: '. $_SERVER['HTTP_REFERER']);
    exit;
}
