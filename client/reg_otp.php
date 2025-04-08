<?php
session_start();
$feedback = '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$acc_no = isset($_POST['acc_no']) ? $_POST['acc_no'] : '';
$agree = isset($_POST['agree']) ? $_POST['agree'] : 0;
$cid = isset($_POST['cid']) ? $_POST['cid'] : 0;
require_once('includes/response.php');

$response = new Response();
if (isset($_POST['reg'])) {

    $res = $response->setClientDefaultMpin($phone, $acc_no);

    if (!$res) {
        $feedback = 'Invalid Information provided or your account is already registered to Mobile Banking! Enter the right details to continue or visit any of our Branches to be helped.';
        $_SESSION['error'] = $feedback;
        header('location: register_mobile_banking?phone=' . $phone . '&acc_no=' . $acc_no);
        exit;
    }
}
if (isset($_POST['submit'])) {
    $mpin = $_POST['v0'] . $_POST['v1'] . $_POST['v2'] . $_POST['v3'];

    $res = $response->verifyClient2($mpin, $phone, $acc_no);

    if ($res != '') {
        $feedback = 'Success! Set an mPIN of your choice to Continue.';
        $_SESSION['success'] = $feedback;
        header('location: set_mpin?cid=' . $res[0]['cid'] . '&uid=' . $res[0]['uid']);
        exit;
    } else {
        $feedback = 'Invalid Credentials! Enter the right mPIN to continue.';
        $_SESSION['error'] = $feedback;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./css/otp_style.css" />
    <link rel="shortcut icon" type="image/png" href="images/logo-dark-2.png" />
    <title>NSIMBI CBS | OTP VERIFICATION</title>
</head>

<body>

    <div class="container">
        <h2>Verify Your Account</h2>
        <p>
            Enter the temporary mPIN sent to your phone(<?= $phone; ?>) to continue <br />

        </p>

        <?php
        if ($feedback != "") {
            echo '
              <div class="alert alert-danger" style="color: red !important;">
              <a href="#" class="close text-danger" data-dismiss="alert"></a>
              ' . $feedback . '
              </div>
              ';
            $feedback = '';
        }


        ?>
        <?php
        if (isset($_SESSION['success']) && $_SESSION['success'] !== "") {
            echo '
              <div class="alert alert-success">
              <a href="#" class="close" data-dismiss="alert"></a>
              ' . $_SESSION['success'] . '
              </div>
              ';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error']) && $_SESSION['error'] !== "") {
            echo '
                <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert"></a>
                ' . $_SESSION['error'] . '
                </div>
                ';
        }
        unset($_SESSION['error']);

        ?>

        <form method="POST">
            <input type="hidden" name="cid" value="<?= $cid ?>" />
            <input type="hidden" name="phone" value="<?= $phone ?>" />
            <input type="hidden" name="acc_no" value="<?= $acc_no ?>" />
            <input type="hidden" name="agree" value="<?= $agree ?>" />
            <div class="code-container">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v0">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v1">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v2">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v3">
                <!-- <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v4"> -->
            </div>

            <div>
                <button type="submit" class="btn btn-primary" name="submit" style="background-color:#ec2a35 !important; color: white; border-radius: 1.25rem !important;">Verify</button>
            </div>
        </form>
        <small class="info">
            If you didn't receive the SMS with the temporary mPIN ! <br /><strong> Visit any of our Branches to have your mPIN set</strong>
        </small>

    </div>

    <script src="./js/otp_script.js"></script>
</body>

</html>