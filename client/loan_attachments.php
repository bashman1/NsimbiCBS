<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
require_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

$response = new Response();

if (isset($_GET['success'])) {
    $name = $_GET['name'];
    $other_name = $_GET['other_name'];
    $lid = $_GET['lid'];

    $res = $response->uploadAttachment($lid, $other_name, $name);
    if ($res) {

        setSessionMessage(true, 'Loan Attachment Added Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
    }
    header('location:loan_details_page.php?id=' . $lid);
    exit();
}
