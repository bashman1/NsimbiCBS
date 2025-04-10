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

if (isset($_GET['type'])) {
    if ($_GET['type'] == 'D') {
        header('Location: edit_deposit?id=' . $_GET['tid'] . '&t=' . $_GET['t']);
        exit;
    } else  if ($_GET['type'] == 'W') {
        header('Location: edit_withdraw?id=' . $_GET['tid'] . '&t=' . $_GET['t']);
        exit;
    } else {
        setSessionMessage(false, 'Process failed! Try again to Edit transaction');
    }
} else {
    setSessionMessage(false, 'Process failed! Try again to Edit transaction');
}
