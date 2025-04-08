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
    $res = $response->resendEmail($_GET['id'], $_GET['email']);
    if ($res) {
        setSessionMessage(true, 'Set Password Email sent Successfully!');
        header('location:all_bank_staff.php');
        exit;
    } else {
        setSessionMessage(false, 'Email not Sent');
        header('location:all_bank_staff.php');
        exit;
    }
}



?>
