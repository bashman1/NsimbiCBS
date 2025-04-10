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

$res = $response->openHoliday($user[0]['bankId'], $_GET['id']);
if ($res) {
    setSessionMessage(true, 'Bank Open Day Set Successfully!');
    header('location:bank_settings.php');
    exit;
} else {
    setSessionMessage(false, 'Open Day not set! Something went wrong');
    header('location:bank_settings.php');
    exit;
}
