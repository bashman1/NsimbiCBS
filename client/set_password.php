<?php include('../backend/config/session.php');
$title = "SET PASSWORD";
?>
<?php

if (isset($_SESSION['user']) && $_SESSION['user'] !== "") {
    header('location: index');
    // exit();
}
include_once('includes/response.php');
$response = new Response();

if(isset($_POST['submit'])){

$password = $_POST['password'];
$uid = $_POST['id'];

$res = $response->setPassword($password, $uid);
    if ($res) {
        $_SESSION['success'] = 'Password set Successfully! Login to Continue';
        // setSessionMessage(true,'Password set Successfully! Login to Continue');
        header('location:login?success');
        exit;
    } else {
        $_SESSION['error'] = 'Password Reset failed';
        // setSessionMessage(false,'Password Reset failed');
        header('location:set_password?error');
        exit;
    }

}

require_once('includes/head_tag.php');

?>


<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-3">
                                        <a href="/"><img src="images/logo-dark-2.png" alt=""></a>
                                    </div>
                                    <h4 class="text-center mb-4">Set Password for your account</h4>
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
                                    <form method="POST" >
                                        <input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
                                        
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Enter your Desired Password</strong></label>
                                            <input type="password" class="form-control" value="" name="password"
                                                autocomplete="current-password" required id="id_password"
                                                style="display:inline-block;margin-right:10px;" >
                                            <i class="fas fa-eye" id="togglePassword"
                                                style="margin-left: -30px !important; cursor: pointer !important; display:inline-block;"></i>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" name="submit" class="btn btn-primary btn-block">Submit</button>
                                        </div>
                                    </form>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <?php include('includes/bottom_scripts.php'); ?>

    <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#id_password');

    togglePassword.addEventListener('click', function(e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });
    </script>
</body>

</html>