<?php
require_once('includes/response.php');
    $title = 'ACCOUNT SELECTION';
    require_once('includes/head_tag.php');
$response = new Response();
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
                                        <a href="/"><img src="images/ucscucbs.png" alt="" style="width: 60%;
    margin: auto;
    padding: 20px 20px 0 20px;"></a>
                                    </div>

                                    <?php
                                    $inst = $response->getMySaccos($_POST['phone']);
                                    if ($inst != '') {
                                    ?>

                                        <h4 class="text-center text-primary mb-4" style="color: #ec2a35 !important;">Welcome Back, Below are all the Accounts associated with this Phone Number<?= ' ( ' . $inst[0]['phone'] . ')' ?></h4>
                                        <h4 class="text-center mb-4">Click on one of the accounts to Continue: </h4>

                                        <?php
                                        $count = 1;
                                        foreach ($inst as $c) {
                                            $sacco = $response->getClientSaccoDetails($c['cid']);
                                            if ($sacco) {
                                                echo '
                                            <a href="otp_screen_client.php?cid=' . $c['cid'] . '"> <div class="accordion accordion-danger-solid" id="accordion-two">
                                        <div class="accordion-item">
                                        <div class="accordion-header rounded-lg collapsed" id="accord-' . $count++ . 'One" data-bs-toggle="collapse" data-bs-target="#collapse' . $count++ . 'One" aria-controls="collapse' . $count++ . 'One" aria-expanded="false" role="button">
                                        <span class="accordion-header-text">' . $sacco[0]['name'] . '</span>
                                        <span class="accordion-header-indicator"></span>
                                        </div>
                                        <div id="collapse' . $count++ . 'One" class="accordion__body collapse" aria-labelledby="accord-' . $count++ . 'One" data-bs-parent="#accordion-two" style="">
                                        <div class="accordion-body-text">
                                        ' .   $sacco[0]['name'] . '
                                        </div>
                                        </div>
                                        </div>

                                        </div></a>
                                            
                                            ';
                                            }
                                        }

                                        ?>

                                    <?php  } else { ?>
                                        <h4 class="text-center text-primary mb-4">Sorry, <?= $_POST['phone'] ?></h4>
                                        <h4 class="text-center mb-4">No account associated to this number!</h4>
                                        <div class="text-center">
                                            <a href="me.php" class="btn btn-primary btn-block" style="background-color: #ec2a35 !important; border-color: #ec2a35 !important;">Back</button>
                                        </div>
                                    <?php } ?>
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