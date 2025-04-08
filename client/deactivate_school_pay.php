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
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->unsubscribeSchoolPay($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Institution Un-Subscribed from School Pay Successfully!');
        header('location:fees_subscriptions.php');
        exit;
    } else {
        setSessionMessage(false, 'Un-Subscription failed! Try again');
        header('location:fees_subscriptions.php');
        exit;
    }
}



?>
