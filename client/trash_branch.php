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
    $res = $response->deleteBranch($_GET['id'],$_SESSION['user']['bankId'],$_SESSION['user']['branchId'],$_SESSION['user']['userId']);
    if ($res) {
$_SESSION['success_message'] = 'Branch Trashed Successfully!';
        header('location:all_branches.php');
    } else {
$_SESSION['error_message'] = 'Something went wrong! Try Again to Trash this Branch';
        header('location:all_branches.php');
    }
}



?>
