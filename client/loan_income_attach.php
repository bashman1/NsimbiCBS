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
    $returns = $_GET['returns'];
    $details = $_GET['details'];

    $res = $response->addIncomeSource($lid, $name, $returns, $details, $other_name);
    if ($res) {

        setSessionMessage(true, 'Loan Income Source Added Successfully!');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
    }
    header('location:loan_details_page.php?id=' . $lid);
    exit();
}
