<?php
include('../backend/config/session.php');
$title = 'ADD OVER-DRAFT PRODUCT';
require_once('includes/head_tag.php');
include_once('includes/response.php');
include_once('includes/functions.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $res = $response->createOverDraftProduct($_POST);
    if ($res) {
        setSessionMessage(true, 'Over-Draft Product Created Successfully!');
        header('location:over_draft_products.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to create the loan product');
        header('location:add_over_draft_product.php');
    }
    exit;
}
$sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
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
                                    Create New Over-Draft Product
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <?php require_once __DIR__ . '/over_draft_product_form.php' ?>
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
            $('#cash_trans').change(function() {

                if ($(this).find('option:selected').val() == 'DAILY') {
                    $('#dtype').html(' DAY');
                    $('#dtypes').html(' DAYS');
                } else if ($(this).find('option:selected').val() == 'WEEKLY') {
                    $('#dtype').html(' WEEK');
                    $('#dtypes').html(' WEEKS');

                } else if ($(this).find('option:selected').val() == 'MONTHLY') {
                    $('#dtype').html(' MONTH');
                    $('#dtypes').html(' MONTHS');

                } else if ($(this).find('option:selected').val() == 'YEARLY') {
                    $('#dtype').html(' YEAR');
                    $('#dtypes').html(' YEARS');
                } else {
                    $('#dtype').html(' DAY');
                    $('#dtypes').html(' DAYS');
                }


            });



            $('#penalty_type').change(function() {

                if ($(this).find('option:selected').val() == 'percent') {
                    $('#ptype').html(' Rate');
                } else if ($(this).find('option:selected').val() == 'flat') {
                    $('#ptype').html(' Amount');

                } else {
                    $('#ptype').html(' Rate');
                }


            });


            $('#charge_type').change(function() {

                if ($(this).find('option:selected').val() == 'percent') {
                    $('#itype').html(' Rate');
                } else if ($(this).find('option:selected').val() == 'flat') {
                    $('#itype').html(' Amount');

                } else {
                    $('#itype').html(' Rate');
                }


            });
        </script>




</body>

</html>