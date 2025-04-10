<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->unsubscribe_all_bank_client_sms($_GET['id']);
    if ($res) {
setSessionMessage(true,'All Clients Un-Subscribed from SMS Banking!');
        header('location:sms_tab.php');
        exit;
    } else {
setSessionMessage(false,'Something went wrong! Try again to Un-subscribe all clients from SMS Bnaking.');
        header('location:sms_tab.php');
        exit;
    }
}



?>
