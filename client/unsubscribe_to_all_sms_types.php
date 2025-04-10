<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->unsubscribe_to_sms_types($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Unscubscribed from all SMS Types Successfully!');
        header('location:sms_types.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:sms_types.php');
        exit;
    }

    exit();
}



?>
