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
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->approveOverDraft($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Over-Draft Application Approved Successfully!');
        header('location:all_over_drafts.php');
        exit;
    } else {
        setSessionMessage(false, 'Process failed . Try again!');
        header('location:all_over_drafts.php');
        exit;
    }
}



?>
