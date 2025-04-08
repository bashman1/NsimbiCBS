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
    $res = $response->deleteAccountOpeningFee($_GET['id']);
    if ($res['success']) {
        setSessionMessage();
        header('location:fees_tab');
    } else {
        setSessionMessage(false, $res['message']);
        header('location:fees_tab');
    }
    exit;
}
