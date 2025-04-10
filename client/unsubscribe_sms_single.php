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
    $res = $response->unsubscribe_client_sms($_GET['id']);
    if ($res) {

        header('location:sms_tab?ssuccess');
    } else {

        header('location:sms_tab?serror');
    }
}



?>
