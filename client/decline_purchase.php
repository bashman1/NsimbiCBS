<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->declinepurchaseSMS($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'SMS Purchase Requisition Declined Successfully!');
        if (isset($_GET['p'])) {
            header('location:sms_tab.php');
        } else {
            header('location:sms_manage.php');
        }

        // exit;
    } else {
        setSessionMessage(false, 'SMS Purchase Decline failed. Try again!');
        if (isset($_GET['p'])) {
            header('location:sms_tab.php');
        } else {
            header('location:sms_manage.php');
        }
        // exit;
    }
}



?>
