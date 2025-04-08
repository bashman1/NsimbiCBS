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

$res = $response->closeHoliday($user[0]['bankId'], $_GET['id']);

if ($res) {
    setSessionMessage(true, 'Closed Day Marked Successfully!');
    header('location:bank_settings.php');
    // exit;
} else {
    setSessionMessage(false, 'Closed Day not set! Try again');
    header('location:bank_settings.php');
    // exit;
}
