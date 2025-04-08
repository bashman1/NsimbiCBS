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
    $res = $response->deleteLoan($_GET['id']);
    if ($res) {
setSessionMessage(true,'Loan Deleted Successfully!');
        header('location:loan_applications.php');
        exit;
    } else {
setSessionMessage(false,'Something went wrong! Try again');
        header('location:loan_details_page.php?id=' . $_GET['id'] . '');
        exit;
    }
}



?>
