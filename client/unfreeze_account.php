<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->hasSubPermissions('unfreeze_savings')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->unfreezeaccount($_GET['id'],$_GET['amount']);
    if ($res) {
setSessionMessage(true,'Account Un-freezed Successfully!');
        header('location:freezed_accounts.php');
        exit;
    } else {
setSessionMessage(false,'Un-freeze failed! Try again');
        header('location:freezed_accounts.php');
        exit;
    }
}



?>
