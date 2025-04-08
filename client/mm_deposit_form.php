<?php
include('../backend/config/session.php');

require_once('includes/head_tag.php');
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
                                    Airtel Money Deposit Form
                                </h4>

                            </div>
                            <div class="card-body">
                                <div class="basic-form">

                                    <form action="<?= BACKEND_BASE_URL ?>mobile_money/process_mm_deposit.php" method="POST" id="idx">
                                        <input type="hidden" name="t_type" value="D" />
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Mobile Number </label>
                                                    <input type="tel" name="phone" class="form-control" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Account Number (e.g 5933000001)</label>
                                                    <input type="number" name="acc_no" class="form-control" maxlength="10" minlength="10" required="required" oninvalid="this.setCustomValidity('A/C No. is required & must be all the 10 digits')" onvalid="this.setCustomValidity('')">
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Amount (in UGX)</label>
                                                    <input type="text" name="amount" class="form-control comma_separated" placeholder="" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Full Names</label>
                                                    <input type="text" name="acc_name" class="form-control" placeholder="" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Reason</label>
                                                    <input type="text" name="reason" class="form-control" placeholder="" required>
                                                </div>
                                            </div>


                                            <div class="col-lg-12">
                                                <div class="send-btn">
                                                    <br /><br />
                                                    <input type="submit" class="btn btn-primary" onClick="this.form.submit(); this.disabled=true; this.value='Processing…'; " value="Deposit">


                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </form>




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



</body>

</html>