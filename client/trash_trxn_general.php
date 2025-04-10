<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['type'])) {
    if ($_GET['type'] == 'D') {
        header('Location: trash_deposit.php?id=' . $_GET['id']);
        exit;
    } else  if ($_GET['type'] == 'W') {
        header('Location: trash_withdraw.php?id=' . $_GET['id']);
        exit;
    } else  if ($_GET['type'] == 'L') {
        header('Location: trash_loan_repayment.php?tid=' . $_GET['id'].'&id=' . $_GET['lid']);
        exit;
    } else if ($_GET['type'] == 'I' || $_GET['type'] == 'E' || $_GET['type'] == 'ASS' || $_GET['type'] == 'LIA' || $_GET['type'] == 'AJE') {
        header('Location: trash_trxn.php?id=' . $_GET['id']);
        exit;
    } else {
        setSessionMessage(false, 'Process failed! Try again to delete transaction');
    }
} else {
    setSessionMessage(false, 'Process failed! Try again to delete transaction');
}
