<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankAdmin()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->activateBankAccount($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Bank A/C Activated Successfully!');
        header('location:trxn_accounts');
        exit;
    } else {
        setSessionMessage(false, 'Bank A/C not activated!');
        header('location:trxn_accounts');
        exit;
    }
}



?>
