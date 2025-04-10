<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_GET['id'])) {
    $res = $response->declineBranchRequest($_GET['id']);
    if ($res) {
        setSessionMessage(true, 'Inter-Branch Cash Requisition Declined Successfully!');
        header('location:inter_branch_requests.php');
        exit;
    } else {
        setSessionMessage(false, 'Decline Process failed. Try again!');
        header('location:inter_branch_requests.php');
        exit;
    }
}



?>
