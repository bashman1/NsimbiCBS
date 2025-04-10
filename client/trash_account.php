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
    $res = $response->deleteChartAccount($_GET['id'],$user[0]['branchId'],$user[0]['bankId'],$user[0]['userId']);
    if ($res['success']) {
        setSessionMessage(true,'Account Trashed Successfully!');
        header('Location: accounting_tab');
        exit;
    } else {
        setSessionMessage(false, $res['message']?? 'Account Balance is greater than 0 or Account has some Transactions against it! You can\'t delete such an account unless you transfer the Balance first.');
        header('Location: accounting_tab');
        exit;
    }

}
setSessionMessage(false, 'Select the account to trash first');
header('Location: accounting_tab');
exit;
