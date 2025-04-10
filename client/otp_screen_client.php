<?php
$feedback = '';
if (isset($_POST['submit'])) {

    require_once('includes/response.php');

    $response = new Response();

    $cid = $_POST['cid'];
    $mpin = $_POST['v0'] . $_POST['v1'] . $_POST['v2'] . $_POST['v3'];

    $res = $response->verifyClient($cid, $mpin);

    if ($res != '') {
        if ($res[0]['md'] == 1) {
            $feedback = 'You\'re using a default mPIN! Set an mPIN of your choice to Continue.';
            $_SESSION['success'] = $feedback;
            header('location: set_mpin.php?cid=' . $res[0]['cid'] . '&uid=' . $res[0]['uid']);
            exit;
        } else {
            header('location: client_portal_index.php?cid=' . $res[0]['cid'] . '&uid=' . $res[0]['uid']);
            exit;
        }
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
            Enter your mPIN below to continue <br />

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

        <form method="POST">
            <input type="hidden" name="cid" value="<?= $_GET['cid'] ?>" />
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
            If you don't recall your mPIN !! <br /><strong> Visit any of our Branches to have your mPIN reset</strong>
        </small>

    </div>

    <script src="./js/otp_script.js"></script>
</body>

</html>