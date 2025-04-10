<?php
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
                                        <a href="/">Reset your Password</a>
                                    </div>
                                    <h4 class="text-center mb-4">Enter your email address, and we will send you a link to reset your password.</h4>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label><strong>Email</strong></label>
                                            <input type="email" required class="form-control" value="" name="email">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Reset</button>
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
    <!-- Required vendors -->
    <?php
    include('includes/bottom_scripts.php');
    ?>
</body>

</html>