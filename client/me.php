<?php
session_start();
$title = 'INTERNET BANKING';
require_once('includes/head_tag.php');
?>

<body class="vh-100" style="background-image: 'images/working-laptop.jpeg';">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-3">
                                        <a href="/"><img src="images/ucscucbs.png" alt="" style="width: 60%;
    margin: auto;
    padding: 20px 20px 0 20px;"></a>
                                    </div>
                                    <h4 class="text-center text-primary mb-4" style="color: #ec2a35 !important;">Internet Banking Login</h4>
                                    <h4 class="text-center mb-4">Sign in to your account</h4>
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
                                    <form method="POST" action="sacco_selection.php">
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Phone Number (Enter the Number you registered with your Institution) </strong></label>
                                            <input type="phone" class="form-control" name="phone" value="" required placeholder="Phone Number">
                                        </div>

                                        <div class="row d-flex justify-content-between mt-4 mb-2">


                                        </div>
                                        <div class="text-center">
                                            <button type="submit" name="submit" class="btn btn-primary btn-block" style="background-color: #ec2a35 !important; border-color: #ec2a35 !important;">Continue</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p>Have't Subscribed to Mobile Banking yet? <a class="text-primary" href="register_mobile_banking.php" style="color: #ec2a35 !important;">Register for Mobile Banking</a></p>
                                    </div>
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
    <!-- Required vendors -->
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

    <?php
    include('includes/bottom_scripts.php');
    ?>



</body>

</html>