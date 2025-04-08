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
    $res = $response->rectifyPrincipal($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Loan Principal Cleared & Loan Marked as Closed Successfully!');
        header('location:active_loans.php');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:active_loans.php');
        exit;
    }
}



?>
