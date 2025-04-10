<?php
include_once '../../models/User.php';

$item = new User($db);
$url = 'http://ucscucbs-282d986e7f0f.herokuapp.com/client/set_password.php?id=' . $_GET['id'];
$mail_subject = 'Sign up with UCSCU CBS';

$item->sendEmail(
    $_GET['email'],
    $mail_subject,
    "<style>p{font-size: 14px;}</style><img src='http://ucscucbs-282d986e7f0f.herokuapp.com/client/images/ucscucbs.png' style='margin: auto;'/><p>Hello,</p><p>You've just had an account created for you.</p><p>You must now complete signing up by setting up a password for your account using link below: <br/><br/><a href='" . $url . "'>Set Password</a><br/></p><p>If this email was sent by mistake, please ignore this email and contact the administrator at your institution. If you need further support with siging up, please contact our team at <a href='mailto:support@ucscucbs.com'>support@ucscucbs.com</a>.</p><p>Thank you for using UCSCUCBS!</p><p>-The UCSCUCBS Team</p>"
);
$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";


http_response_code(200);
echo json_encode($userArr);