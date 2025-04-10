<?php
session_start();
$feedback = '';
require_once('includes/response.php');

$response = new Response();

if (isset($_POST['submit'])) {
    $mpin = $_POST['v0'] . $_POST['v1'] . $_POST['v2'] . $_POST['v3'];

    $res = $response->verifyClient3($_POST['cid'], $_POST['uid'], $mpin);

    if ($res) {
        $feedback = 'Success! Your mPIN has been successfully set. Login to Continue.';
        $_SESSION['success'] = $feedback;
        header('location: me.php');
        exit;
    } else {
        $feedback = 'Something went wrong! Re-Enter your mPIN to continue.';
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
    <title>NSIMBI CBS | mPIN SET</title>
</head>

<body>

    <div class="container">
        <h2>Set mPIN for Your Account</h2>
        <p>
            Enter your desired 4-digit mPIN to continue <br />

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
            <input type="hidden" name="cid" value="<?= $_GET['cid'] ?>" />
            <input type="hidden" name="uid" value="<?= $_GET['uid'] ?>" />
            <div class="code-container">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v0">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v1">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v2">
                <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v3">
                <!-- <input type="number" class="code" placeholder="0" min="0" max="9" maxlength="1" required name="v4"> -->
            </div>

            <div>
                <button type="submit" class="btn btn-primary" name="submit" style="background-color:#ec2a35 !important; color: white; border-radius: 1.25rem !important;">Set</button>
            </div>
        </form>
        <small class="info">
            Thanks for subscribing to Internet Banking ! <br /><strong> Visit or Call any of our Branches incase of any other Questions</strong>
        </small>

    </div>

    <script src="./js/otp_script.js"></script>
</body>

</html>