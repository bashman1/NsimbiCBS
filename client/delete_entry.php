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
    $res = $response->deleteShortfall($_GET['id']);
    if ($res) {

        header('location:staff_shortfalls.php');
        exit;
    } else {

        header('location:staff_shortfalls.php' );
        exit;
    }
}



?>
