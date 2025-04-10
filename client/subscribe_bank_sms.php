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

if (isset($_GET['t'])) {
    $res = $response->subscribe_bank_sms($_GET['t'],$_GET['st'],$_GET['type']);
    if ($res) {

        header('location:general_sms_settings.php?success');
    } else {

        header('location:general_sms_settings.php?error');
    }
}



?>
