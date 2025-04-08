<?php
include('../backend/config/session.php');
$title = 'ADD LOAN PRODUCT';
require_once('includes/head_tag.php');
include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $prate = 0;
    $pfamount = 0;
    $gracedays = 0;
    $maxdays = 0;
    $gracetype = 'pay_none';
    $penaltybased = 'both';

    $round_off = 0;
    $auto_repay = 0;
    $auto_penalty = 0;
    if ($_POST['auto_repay'] == 'true') {
        $auto_repay = 1;
    }

    if ($_POST['auto_penalty'] == 'true') {
        $auto_penalty = 1;
    }

    if ($_POST['round_off'] == 'true') {
        $round_off = 0;
    }

    $enable_penalty = false;

    if ($_POST['enable_penalty'] == 1) {
        $enable_penalty = true;
        $prate = $_POST['prate'];
        $pfamount = $_POST['pfamount'];
        $gracedays = $_POST['gracedays'];
        $maxdays = $_POST['maxdays'];
        $gracetype = $_POST['gracetype'];
        $penaltybased = $_POST['penaltybased'];
    } else {
        $feeAmount = @$_POST['famount'];
    }


    $_POST['penalty'] = $enable_penalty;
    $_POST['bank'] = $user[0]['bankId'];
    $_POST['prate'] = $prate;
    $_POST['pfamount'] = $pfamount;
    $_POST['gracedays'] = $gracedays;
    $_POST['maxdays'] = $maxdays;
    $_POST['auto_repay'] = $auto_repay;
    $_POST['auto_penalty'] = $auto_penalty;
    $_POST['round_off'] = $round_off;
    $_POST['gracetype'] = $gracetype;
    $_POST['penaltybased'] = $penaltybased;

    $res = $response->createLoanProduct($_POST);
    if ($res) {
        setSessionMessage(true, 'Loan Product Created Successfully!');
        header('location:loan_settings.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to create the loan product');
        header('location:add_loan_product.php');
    }
    // exit;
}

?>


<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php include('includes/preloader.php'); ?>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        include('includes/nav_bar.php');
        include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Create New Loan Product
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <?php require_once __DIR__ . '/loan_product_form.php' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--**********************************
            Content body end
        ***********************************-->


            <!--**********************************
            Footer start
        ***********************************-->
            <?php include('includes/footer.php'); ?>
            <!--**********************************
            Footer end
        ***********************************-->

            <!--**********************************
           Support ticket button start
        ***********************************-->

            <!--**********************************
           Support ticket button end
        ***********************************-->


        </div>
        <!--**********************************
        Main wrapper end
    ***********************************-->

        <!--**********************************
        Scripts
    ***********************************-->
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>
        <script>
            $('body').on('change', '.select-frequency', function(event) {
                $('.frequency-singular').html('');
                $('.frequency-plural').html('');

                if ($(this).val()) {
                    let frequency_singular = $(this).children('option:selected').data('frequency-singular');
                    let frequency_plural = $(this).children('option:selected').data('frequency-plural');
                    $('.frequency-singular').html(frequency_singular);
                    $('.frequency-plural').html(frequency_plural);
                }
            });

            $('#inlineFormCustomSelect2').change(function() {

                if ($(this).find('option:selected').val() == 'DAILY') {
                    $('#dtype').html(' DAY');
                    $('#dtype2').html(' DAY');
                    $('#dtype3').html(' DAYS');
                    $('#dtype4').html(' DAYS');
                } else if ($(this).find('option:selected').val() == 'WEEKLY') {
                    $('#dtype').html(' WEEK');
                    $('#dtype2').html(' WEEK');
                    $('#dtype3').html(' WEEKS');
                    $('#dtype4').html(' WEEKS');

                } else if ($(this).find('option:selected').val() == 'MONTHLY') {
                    $('#dtype').html(' MONTH');
                    $('#dtype2').html(' MONTH');
                    $('#dtype3').html(' MONTHS');
                    $('#dtype4').html(' MONTHS');

                } else if ($(this).find('option:selected').val() == 'YEARLY') {
                    $('#dtype').html(' YEAR');
                    $('#dtype2').html(' YEAR');
                    $('#dtype3').html(' YEARS');
                    $('#dtype4').html(' YEARS');
                } else {
                    $('#dtype').html(' DAY');
                    $('#dtype2').html(' DAY');
                    $('#dtype3').html(' DAYS');
                    $('#dtype4').html(' DAYS');
                }

            });
        </script>
        <script>
            function setUp() {
                var x = document.getElementById("headd");
                var y = document.getElementById("headf");
                y.style.display = "none";

                x.style.display = "none";
            }


            function setDown() {
                var x = document.getElementById("headd");
                var y = document.getElementById("headf");
                x.style.display = "block";

                y.style.display = "block";
            }

            function setExist() {
                var x = document.getElementById("pc");
                var x1 = document.getElementById("pcn");
                var x2 = document.getElementById("pcm");
                var y = document.getElementById("pc1");
                var w = document.getElementById("pc2");
                var z = document.getElementById("pc3");
                x.style.display = "block";
                x1.style.display = "block";
                x2.style.display = "block";
                y.style.display = "none";
                w.style.display = "none";
                z.style.display = "none";
            }


            function setCreate() {
                var x = document.getElementById("pc");
                var x1 = document.getElementById("pcn");
                var x2 = document.getElementById("pcm");
                var y = document.getElementById("pc1");
                var w = document.getElementById("pc2");
                var z = document.getElementById("pc3");

                x.style.display = "none";
                x1.style.display = "none";
                x2.style.display = "none";
                y.style.display = "block";
                w.style.display = "block";
                z.style.display = "block";
            }
        </script>



</body>

</html>