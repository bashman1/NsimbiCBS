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

if (isset($_GET['id'])) {
    $res = $response->approveBatchLoan($_GET['id']);

    // var_dump($res);
    // exit;

    if ($res['success']) {
        setSessionMessage();
    } else {
        setSessionMessage(false, $res['message']);
    }
// RedirectReferrer();
    header('Location: data_importer_batch_loans.php?id='.$_GET['batch']);
    // exit;
}
