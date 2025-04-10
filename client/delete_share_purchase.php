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
    $res = $response->deleteSharePurchase($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Share Purchase Deleted Successfully!');
        header('location:share_purchase_trxns.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:share_purchase_trxns.php');
        exit;
    }
}



?>
