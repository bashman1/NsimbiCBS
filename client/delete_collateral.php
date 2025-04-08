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
    $res = $response->deleteLoanCollateral($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Collateral Deleted Successfully!');
        header('location:collateral_register.php?dsuccess');
        exit;
    } else {
        setSessionMessage(false, 'Collateral not deleted Successfully. Try again!');
        header('location:collateral_register.php?derror');
        exit;
    }
}



?>
