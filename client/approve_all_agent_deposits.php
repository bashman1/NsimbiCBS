<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
// require_once('./middleware/PermissionMiddleware.php');

?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->approveAllAgentDeposits($_GET['id'], $_GET['user'], $_GET['amount']);
    if ($res) {
        setSessionMessage(true, 'All Transactions Approved Successfully!');
        header('location:agent_list.php');
        // exit;
    } else {
        setSessionMessage(false, 'Transaction Approval failed!');

        header('location:agent_list.php');
        // exit;
    }
}



?>
