<?php
include('../backend/config/session.php');
?>
<?php


include_once('includes/response.php');
$response = new Response();

if (isset($_GET['t'])) {
    $res = $response->subscribe_bank_to_sms($_GET['t'], $_GET['st']);
    if ($res) {
        if($_GET['st']==0){
            setSessionMessage(true, 'Institution Un-Subscribed from SMS Banking Successfully');
        }else{
            setSessionMessage(true, 'Institution Subscribed to SMS Banking Successfully & offered free 100 SMS divided amongst it\'s Current Branches');
        }

        header('location:sms_manage.php');
        exit;
    } else {
setSessionMessage(false, 'Somthing went wrong! Try again ');
        header('location:sms_manage.php');
        exit;
    }
}



?>
