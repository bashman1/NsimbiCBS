<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->subscribe_client_sms($_GET['id']);
    if ($res) {
setSessionMessage(true,'Client Subscribed to SMS Banking Successfully!');
        header('location:sms_tab');
        exit;
    } else {
setSessionMessage(false,'Somthing went wrong! Client not Subscribed to SMS Banking');
        header('location:sms_tab?dserror');
        exit;
    }
}



?>
