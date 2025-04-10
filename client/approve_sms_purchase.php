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

if (isset($_GET['id'])) {
    $res = $response->approvepurchaseSMS($_GET['id']);
    if ($res) {
setSessionMessage(true,'SMS Purchase Requisition Approved Successfully!');
        header('location:sms_manage.php');
        exit;
    } else {
setSessionMessage(false,'Approval failed. Something went wrong!');
        header('location:sms_purchase_form.php');
        exit;
    }
}



?>
