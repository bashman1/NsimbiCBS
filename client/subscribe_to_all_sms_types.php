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
    $res = $response->subscribe_to_sms_types($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Subscribed to All SMS Types Successfully!');
        header('location:sms_types.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again.');
        header('location:sms_types.php');
        exit;
    }
}



?>
