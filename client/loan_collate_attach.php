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
    $uid = $_GET['uid'];
    $fv = $_GET['fv'];
    $mv = $_GET['mv'];
    $catid = $_GET['catid'];
    $location = $_GET['location'];

    $res = $response->addCollateral($catid, $lid, $name, $location, $mv, $fv, $other_name, $uid);
    if ($res) {
        setSessionMessage(true, 'Collateral Added Successfully!');
        header('location:loan_details_page.php?csuccess&id=' . $lid . '#wizard_Details');
        exit();
    } else {
        setSessionMessage(false, 'Collateral not Added! Try again');
        header('location:add_collateral.php?error&id=' . $lid);
        exit();
    }
}
