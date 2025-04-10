<?php
include('../backend/config/session.php');
?>
<?php



include_once('includes/response.php');
$response = new Response();

if (isset($_POST['submit'])) {
    $min = [];
    $max = [];
    $charge = [];
    $count = $_POST['count'];


    for ($i = 1; $i <= $count; $i++) {
        array_push($min, $_POST['min-' . $i]);
        array_push($max, $_POST['max-' . $i]);
        array_push($charge, $_POST['charge-' . $i]);
    }
    $res = $response->createRangeCharge($_POST['name'], $_POST['apply'], $user[0]['bankId'], $min, $max, $charge);
    if ($res) {
        setSessionMessage(true, 'Transaction Charge Set Successfully!');
        header('location:fees_tab');
        exit;
    } else {
        setSessionMessage(false, 'Something went wrong! Trxn Charge not set. Try again');
        header('location:fees_tab');
        exit;
    }
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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Transaction Amount Range Charges
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="name" value="<?php echo $_GET['name']; ?>">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="apply" value="<?php echo $_GET['apply']; ?>">
                                        <input type="hidden" class="form-control input-rounded" placeholder="" name="count" value="<?php echo $_GET['count']; ?>">


                                        <div class="mb-3">
                                            <label class="text-label form-label">Kindly specify the ranges with their
                                                respective fixed charge amounts*</label>
                                        </div>
                                        <?php
                                        for ($i = 1; $i <= $_GET['count']; $i++) {
                                            echo '
                                            <div class="mb-3" id="rangeEntry">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="text-label form-label">Min</span>
                                                    <input type="number" class="input-min form-control" value=""
                                                        min="0" name="min-' . $i . '" required>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="text-label form-label">&nbsp;</span>
                                                    <h2 class="text-primary">-</h2>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-label form-label">Max</span>
                                                    <input type="number" class="input-max form-control" value=""
                                                        min="0" name="max-' . $i . '" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-label form-label">Charge</span>
                                                    <input type="number" class="charge form-control" value="" min="0" name="charge-' . $i . '" required>
                                                </div>
                                            </div>
                                        </div>
                                            
                                            ';
                                        }
                                        ?>


                                        <br /><br /><br />

                                        <button type="submit" name="submit" class="btn btn-primary">Create
                                            Charge</button>



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




</body>

</html>