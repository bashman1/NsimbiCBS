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
    $res = $response->subscribe_to_sms_type($_GET['t']);
    if ($res) {

        header('location:sms_types.php?success');
    } else {

        header('location:sms_types.php?error');
    }
}



?>
