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
    $res = $response->deleteGroupMember($_GET['id']);
    if ($res) {

        header('location:manage_group_members.php?id=' . $_POST['id'] . '&name=' . $_POST['name']);
        exit;
    } else {

        header('location:manage_group_members.php?id=' . $_POST['id'] . '&name=' . $_POST['name']);
        exit;
    }
}



?>
