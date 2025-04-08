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
    $res = $response->subscribe_client_sms($_GET['t']);
    if ($res) {
        setSessionMessage(true, 'SMS Consent Turned On Successfully!');
        header('location:sms_tab');
    } else {
        setSessionMessage(false, 'Process failed! Try again');
        header('location:sms_tab');
    }
}



?>
