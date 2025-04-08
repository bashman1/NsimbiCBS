<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = "LOGIN";
if (isset($_SESSION['user']) && $_SESSION['user'] !== "") {
    header('location: index.php');
    exit();
}
// Set the timezone to Kampala (East Africa Time)
date_default_timezone_set('Africa/Kampala');
require_once('includes/head_tag.php');

// Get the current hour
$currentHour = date('H');
// Determine the salutation based on the current hour
if ($currentHour >= 5 && $currentHour < 12) {
    $salutation = "Good Morning! ";
} elseif ($currentHour >= 12 && $currentHour < 18) {
    $salutation = "Good Afternoon! ";
} elseif ($currentHour >= 18 && $currentHour < 21) {
    $salutation = "Good Evening! ";
} else {
    $salutation = "Hi, ";
}

?>

<style>
    .box-1 {
        position: relative;
        /* Make sure child elements are positioned relative to this container */
        width: 50%;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        /* Prevent background from overflowing */
        color: #fff;
        text-align: center;
    }

    .background-blur {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('./images/saccos.jpg');
        /* background-size: cover; */
        background-position: center;
        background-size: contain;
        /* Ensures the entire image fits within the available space */
        background-repeat: no-repeat;
        /* Prevents the image from repeating */

        /* opacity: 0.5; */

    }

    .authincation {
        width: 100%;
        height: 100vh;
    }

    /* .authincation-content { */
    /* border-radius: 20px; */
    /* box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3); */

    /* } */

    .intro_message {
        text-align: center;
        opacity: 0.99;
    }

    .intro_message h2 {
        font-size: 50px;
        font-weight: 700;
    }

    .intro_message p span {
        color: #ec2a35;
    }

    .intro_message p {
        font-size: 20px;
        font-weight: 700;
        color: black;
    }

    .chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
    }

    .chatbot-toggle {
        padding: 10px 15px;
        background: #fff;
        color: #25D366;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 10px;
        animation: bounce 2s infinite;
    }

    .nsimbi-logo {
        font-size: 80px;
        line-height: 10px;
        font-weight: bold;
        color: #ec2a35;
        font-family: 'Nunito Sans', sans-serif;
    }

    /* .form-control {
        border: none !important;
        border-bottom: 1px solid #000 !important;
        border-radius: 0 !important;
    } */

    .form-control {
        border: none !important;
        border-bottom: 1px solid #bfbfbf !important;
        border-radius: 0 !important;
        transition: border-bottom 0.3s ease-in-out;

        padding: 0 !important;
        height: 2rem;
    }

    .form-control:focus {
        border-bottom: 1px solid #0d0d0d !important;
        /* Change to black when focused */
        outline: none !important;
        /* Remove default browser outline */
    }


    .middle-form {
        width: 100%;
        max-width: 400px;
        /* Adjust width for responsiveness */
        margin: 0 auto;
        /* padding: 20px; */
        /* background: #fff; */
        /* border-radius: 10px; */
        /* box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); */
    }

    /* .svg-icon {
        width: 100px;
        height: 100px;
        background: red;
        -webkit-mask: url('./images/Logo.svg') no-repeat center;
        mask: url('./images/Logo.svg') no-repeat center;
    } */



    .svg-icon {
        width: 150px;
        height: 150px;
        background: #EC4646;
        -webkit-mask: url('./images/Logo.svg') no-repeat center;
        mask: url('./images/Logo.svg') no-repeat center;
        position: absolute;
        /* Enables positioning */
        top: 0;
        /* Moves to the top */
        left: 100px;
        /* Moves to the left */
        /* margin-bottom: 200px !important; */
    }


    .salutation {
        /* font-size: 22px;
  line-height: 28px; */
        text-align: center;
        text-rendering: optimizelegibility;
        color: rgb(14, 14, 14);
        font-weight: 600;
        font-family: Clarkson, "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 22px;
        line-height: 28px;
        font-weight: bold;
        margin-bottom: 100px;
    }

    /* Default button styles */
    #submitButton {
        background-color: #0d0d0d;
        border-color: #0d0d0d;
        border-radius: 0;
        padding: 10px;
        color: white;
        cursor: not-allowed;
        /* Shows disabled cursor */
        transition: background-color 0.3s ease;
    }

    #submitButton {
        position: relative;
        overflow: hidden;
    }

    #submitButton::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background-color: rgba(26, 26, 26, 0.1);
        color: #fff;
        transition: left 0.5s ease-in-out;
    }

    #submitButton:not(:disabled):hover::before {
        left: 0;
    }


    /* When enabled, change cursor */
    #submitButton:not(:disabled) {
        cursor: pointer;
    }

    /* Hover effect when enabled */
    #submitButton:not(:disabled):hover {
        background-color: rgba(26, 26, 26, 0.1);
        border-color: rgba(26, 26, 26, 0.1);
        color: #fff;
    }







    .alert-danger {
        border-radius: 0;
    }

    .chatbot-toggle:hover {
        background-color: #25D366;
        color: #fff;
    }

    @keyframes bounce {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-20px);
        }

        60% {
            transform: translateY(-10px);
        }
    }

    @media only screen and (max-width: 767px) {
        .svg-icon {
            position: relative !important;
            left: 37%;
            margin-bottom: 0;
            width: 100px;
            height: 100px;
        }
    }

    @media only screen and (max-width: 480px) {
        .svg-icon {
            left: 30%;
            position: relative !important;
            margin-bottom: 0;
            width: 100px;
            height: 100px;
        }
    }

    @media (min-width: 768px) {
        .login-form {
            flex: 0 0 auto;
            width: 100%;
        }



    }

    @media (max-width: 1108px) {
        .box-1 {
            width: 0%;
        }

        .authincation {
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            /* background-image: url('./images/2.png'); */
            /* background-size: cover; */
            background-position: center;
            background-size: contain;
            /* Ensures the entire image fits within the available space */
            background-repeat: no-repeat;
            /* Prevents the image from repeating */
        }

        .animation__wobble {
            -webkit-animation: wobble 1.5s;
            animation: wobble 1.5s;
        }

        .text-info {
            color: #227093 !important;
        }


    }
</style>

<body class="vh-100" style="display: flex;">
    <!-- <div class="box-1">
        <div class="background-blur"></div>
        <div class="intro_message">
            <p><i class="fas fa-check-circle animation__wobble"></i> Sound <span>SACCOs</span> for a Sustainable <span>UCSCU</span></p>
        </div>
    </div> -->

    <!-- </div> -->
    <div class="authincation">
        <div class="container">
            <!-- <div class="svg-icon"></div> -->
            <div class="row justify-content-center h-100 align-items-center">
            
                <div class="col-md-6 login-form">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                        <div class="svg-icon"></div>
                            <div class="col-xl-12">
                                <h4 class="text-center mb-4 salutation"><?= $salutation ?>Sign in to continue</h4>
                                <div class="auth-form">
                                    <div class="text-center mb-3">

                                        <!-- <span class="nsimbi-logo">NSIMBI</span> -->
                                        <!-- <a href="/"><img src="images/ucscucbs.png" alt="" style="width: 50%; margin: auto; padding: 20px 20px 0 20px;"></a> -->
                                    </div>
                                    <!-- <h4 class="text-center mb-4 salutation"><?= $salutation ?>Sign in to continue</h4> -->

                                    <form class="middle-form" method="POST" action="verify.php" id="loginForm">
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
                                        <div class="mb-3">
                                            <label class="mb-1 underline" ><strong>EMAIL ADDRESS</strong></label>
                                            <input type="email" class="form-control" name="email" value="" required placeholder="Email">
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>PASSWORD</strong></label>
                                            <div style="display: flex; align-items: center;">
                                                <input type="password" class="form-control" value="" name="password" autocomplete="current-password" required id="id_password" style="display:inline-block; margin-right:10px;" placeholder="Password">
                                                <i class="fas fa-eye" id="togglePassword" style="margin-left: -45px !important; cursor: pointer !important; display:inline-block;"></i>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Optional. Two Factor Authentication <i class="fas fa-question-circle text-info" data-toggle="tooltip" data-placement="top" title="Enter 2FA if your account has Two Factor Authentication enabled."></i></strong></label>
                                            <input type="text" class="form-control" name="fa" value="" placeholder="Two Factor Authentication Token">
                                        </div>
                                        <div class="row d-flex justify-content-between mt-4 mb-2">

                                            <div class="mb-3">
                                                <a href="forgot_password.php" style="color: #ec2a35;">Forgot Password?</a>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" name="submit" class="btn btn-primary btn-block" id="submitButton" disabled>LOG IN</button>
                                        </div>

                                        <div class="row d-flex justify-content-between mt-4 mb-2">

                                            <div class="mb-3">
                                                <a href="#register" style="color: rgb(148, 164, 196);">Don't have an account? <span style="color: #ec2a35 !important;">Contact NSIMBI Team Now</span></a>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="chatbot-container">
                                        <button class="chatbot-toggle" onclick="handleSendMessage()">
                                            <img src="./images/whatsapp icon.png" width="25" height="25" style="border-radius: 40px; vertical-align: middle;">
                                            <!-- WhatsApp Us! -->
                                        </button>
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
        const handleSendMessage = () => {
            const phoneNumber = "+256701601305";
            const message = "Hello NSIMBI System Admin!";
            const url = `https://api.whatsapp.com/send?phone=${phoneNumber}&text=${encodeURIComponent(message)}`;
            window.open(url, "_blank");
        };

        // 

        // const form = document.getElementById("loginForm");
        // const submitButton = document.getElementById("submitButton");

        // form.addEventListener("input", function () {
        //     submitButton.disabled = !form.checkValidity();
        // });
        // 

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("loginForm");
            const submitButton = document.getElementById("submitButton");

            form.addEventListener("input", function() {
                submitButton.disabled = !form.checkValidity();
            });

        });

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