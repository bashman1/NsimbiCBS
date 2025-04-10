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
    $res = $response->deleteLoanGuarantor($_GET['id']);
    if ($res) {

        header('location:loan_details_page.php?dgsuccess&id='.$_GET['lno'].'#wizard_Payment');
        // exit;
    } else {

        header('location:loan_details_page.php?dgerror&id=' . $_GET['lno'] . '#wizard_Payment');
        // exit;
    }
}



?>
