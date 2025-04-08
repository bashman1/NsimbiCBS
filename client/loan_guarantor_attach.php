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
    $non_member = $_GET['non_member'];
    $other_name = $_GET['other_name'];
    $lid = $_GET['lid'];
    $uid = $_GET['uid'];
    $mid = $_GET['mid'];
    $is_client = $_GET['is_client'];

    $res = $response->addGuarantor($mid, $lid, $non_member, $is_client,$other_name);
    if ($res) {
        setSessionMessage(true, 'Guarantor Added Successfully!');
        header('location:loan_details_page.php?gsuccess&id=' . $lid . '#wizard_Payment');
        exit;
    } else {
        setSessionMessage(false, 'Process failed! Guarantor not added. Try again');
        header('location:add_guarantor.php?id=' . $lid);
        exit;
    }
}
