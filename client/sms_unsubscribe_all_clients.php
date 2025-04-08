<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->unsubscribe_all_client_sms($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'All Branch Clients Successfully Unsubscribed from SMS Banking!');
        header('location:sms_tab');
        exit;
    } else {
        setSessionMessage(false, 'Somthing went wrong! Try again.');
        header('location:sms_tab');
        exit;
    }
}



?>
