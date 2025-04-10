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
    $res = $response->reverseClosedLoan($_GET['id'], $_SESSION['user']['bankId'], $_SESSION['user']['branchId'], $_SESSION['user']['userId']);
    if ($res) {
        $_SESSION['success_message'] = 'Loan Re-activated Successfully!';
        header('location:loan_details_page.php?id='.$_GET['id']);
    } else {
        $_SESSION['error_message'] = 'Something went wrong! Try again';
        header('location:loan_details_page.php?id='.$_GET['id']);
    }
}



?>
