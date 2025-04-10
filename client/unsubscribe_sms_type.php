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
    $res = $response->unsubscribe_to_sms_type($_GET['t']);
    if ($res) {
        setSessionMessage(true, 'Unsubscribed Successfully from this SMS Type!');
        header('location:sms_types');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:sms_types');
        exit;
    }
}



?>
