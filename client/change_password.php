<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

if (isset($_POST['submit'])) {
    include_once('includes/handler.php');
    include_once('includes/response.php');
    $handler = new Handler();
    $response = new Response();

    if ($handler->Encoding(md5($_POST['opassword'])) == $user[0]['password']) {

        $res = $response->updatePassword($_POST['password'], $user[0]['userId']);
        if ($res) {
            setSessionMessage(true, 'Password Updated Successfully!');
            header('location:profile.php');
            exit;
        } else {
            setSessionMessage(false, 'Password Update failed!');
            header('location:profile.php');
            exit;
        }
    } else {
        setSessionMessage(false, 'Current Password Entered is Invalid! Enter the correct Password.');
        header('location:profile.php');
        exit;
    }


    // header('location:all_banks.php');
}

?>