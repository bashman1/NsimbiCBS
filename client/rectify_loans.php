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

if (isset($_GET['id'])) {
    $res = $response->rectifyLoan($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Loan Rectified Successfully!');
        header('location:active_loans');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Try again');
        header('location:active_loans');
        exit;
    }
}



?>
