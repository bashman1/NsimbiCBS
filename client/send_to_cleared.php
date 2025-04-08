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
    $res = $response->sendLoanToCleared($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Loan Cleared Successfully!');
        header('location:loans_search_general.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:loans_search_general.php');
        exit;
    }
}



?>
