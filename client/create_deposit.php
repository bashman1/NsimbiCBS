<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    // $res = $response->createDeposit($_POST['client'], $_POST['amount'],$_POST['reason'],$_POST['deposited'],$user[0]['branchId'],$user[0]['userId']);
    // if ($res) {

    //     header('location:create_deposit?success');
    // } else {

    //     header('location:create_deposit?error');
    // }

}
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
                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Savings</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Create Deposit</a></li>
                    </ol>
                </div> -->
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Cash Deposit Form
                                </h4>
                                <?php
                                if (isset($_GET['success'])) {
                                    echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                                    // unset($_SESSION['success']);
                                }
                                if (isset($_GET['error'])) {
                                    echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                                }

                                ?>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" class="confirm-form-submission">


                                        <!-- <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                    <option value="AL" data-select2-id="2">Alabama</option>
                                    <option value="WY" data-select2-id="104">Wyoming</option>
                                </select> -->


                                        <div class="mb-3">
                                            <label class="text-label form-label">Search for the Client's Account *</label>

                                            <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="client">
                                                <option selected></option>
                                                <?php
                                                foreach ($response->getAllBankClients($user[0]['bankId'], $user[0]['branchId']) as $row) {
                                                    echo '
                                                    <option value="' . $row['userId'] . '">' . $row['accno'] . '  - ' . $row['name'] . '   - UGX: ' . number_format($row['acc_balance'] + $row['loan_wallet']) . '</option>
                                                    ';
                                                }
                                                ?>


                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Amount*</label>

                                            <input type="number" class="form-control input-rounded" placeholder="" name="amount" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Reason*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="reason" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-label form-label">Deposited by*</label>

                                            <input type="text" class="form-control input-rounded" placeholder="" name="deposited">
                                        </div>




                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="deposit" class="btn btn-primary action-btn">Deposit</button>
                                        <!-- </div> -->

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