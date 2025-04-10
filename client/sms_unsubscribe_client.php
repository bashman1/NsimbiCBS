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

if (isset($_GET['t'])) {
    $res = $response->unsubscribe_client_sms($_GET['t']);
    if ($res) {
setSessionMessage(true,'SMS Consent Turned Off Successfully!');
        header('location:sms_tab');
        exit;
    } else {
setSessionMessage(false,'Something went wrong! Try again.');
        header('location:sms_tab');
        exit;
    }
}



?>
