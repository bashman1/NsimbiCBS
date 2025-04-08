<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
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
    $res = $response->convertClient($_GET['id'],$_GET['to']);
    if ($res) {
        setSessionMessage(true, 'Client Converted Successfully!');
    } else {
        setSessionMessage(false, 'Client not Converted! Try again');
    }

    header('location:convert_clients');
    exit;
}



?>
