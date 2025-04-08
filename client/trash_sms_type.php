<?php
include('../backend/config/session.php');


if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_GET['t'])) {
    $res = $response->deleteSMSType($_GET['t']);
    if ($res) {
setSessionMessage(true,'SMS Type Deleted Successfully!');
        header('location:sms_types');
        exit;
    } else {
setSessionMessage(false,'Something went wrong! Try again');
        header('location:sms_types');
        exit;
    }
}
