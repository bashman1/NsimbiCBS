<?php
include('../backend/config/session.php');
?>
<?php

$title  = 'FIXED CALCULATOR';

require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();


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
                                <h4 class="card-title text-primary">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Fixed Deposit Calculator
                                </h4>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-12">

                                        <form method="post" class="">

                                            <div class="row">
                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <label>Fixed Deposit Amount
                                                            (<?= strtoupper('ugx'); ?>)</label>
                                                        <input id="total_amount" type="text" min="1" value="<?= @$_POST['amount']; ?>" name="amount" class="form-control comma_separated" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">

                                                    <div class="form-group">
                                                        <label for="frequencyInputField">Compounding Frequency</label>
                                                        <select class="form-control" name="frequency" id="frequency" data-name="frequency" required>
                                                            <?=
                                                            isset($_POST['submit']) ? '
                                                            <option value="' . $_POST['frequency'] . '" selected>' . $_POST['frequency'] . '
                                                            </option>
                                                            ' : '';
                                                            ?>
                                                            <option value="12">Monthly</option>
                                                            <option value="4">Quarterly</option>
                                                            <option value="2">Half Yearly</option>
                                                            <option value="1">Yearly</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1">Rate of Interest (%) per
                                                            Annum</label>
                                                        <input id="interest" step="any" type="number" value="<?= @$_POST['interest']; ?>" name="interest" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="periodInputField">Fixed Deposit
                                                            Period</label>
                                                        <input type="number" class="form-control" id="period" value="<?= @$_POST['period']; ?>" placeholder="" data-name="period" name="period" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group" id="periodTypeField">
                                                        <label for="periodTypeInputField">Period Type</label>
                                                        <select class="form-control" name="period_type" id="period_type" data-name="period_type" required>
                                                            <?=
                                                            isset($_POST['submit']) ? '
                                                            <option value="' . $_POST['period_type'] . '" selected="selected">' . $_POST['period_type'] . '
                                                            </option>
                                                            ' : '';
                                                            ?>
                                                            <option value="m">Months
                                                            </option>
                                                            <option value="y">Years</option>

                                                            <option value="d">Days</option>
                                                        </select>
                                                    </div>
                                                </div><br /><br />
                                                <div class="col-md-4">
                                                    <div class="form-group" style="padding: 30px !important;">

                                                        <button type="submit" class="btn btn-primary" id="fxd_c_submit_btn" name="submit">Calculate</button>
                                                    </div>
                                                </div>


                                            </div>
                                        </form>

                                    </div>


                                </div>

                            </div>
                        </div>


                        <!-- end card -->


                    </div>
                </div>
                <?php if (isset($_POST['submit'])) : ?>
                    <div class="card" id="results" style="display:block;">
                        <div class="card-header">
                            <h4 class="card-title text-primary">Fixed Deposit Term Sheet</h4>

                            <button class="btn btn-primary" onclick="PrintContent('exreportn')">Print</button>
                        </div>
                        <div class="card-body" id="exreportn">
                            <!--<div class="table-responsive">-->
                            <?php

                            $amount = str_replace(",", "", $_POST['amount']);
                            $interest_rate = $_POST['interest'];
                            $period = $_POST['period'];
                            $period_type = $_POST['period_type'];
                            $frequency = $_POST['frequency'];

                            $interest = 0;
                            $freq_days = 1;
                            $total_period_interest = 0;
                            $inst_interest = 0;
                            $no_times = 0;


                            if ($period_type == 'y') {
                                $period_no_days = $period * 360;
                                $daily_rate  = $interest_rate / 36000;
                                $daily_interest = $amount * $daily_rate;
                                $total_period_interest = round($daily_interest * $period_no_days);

                                if ($frequency == 12) {
                                    $freq_days = 30;
                                } else  if ($frequency == 4) {
                                    $freq_days = 90;
                                } else  if ($frequency == 2) {
                                    $freq_days = 180;
                                } else  if ($frequency == 1) {
                                    $freq_days = 360;
                                }

                                $no_times = $period_no_days / $freq_days;

                                $inst_interest = round($total_period_interest / $no_times);
                            } else if ($period_type == 'm') {
                                $period_no_days = $period * 30;
                                $daily_rate  = $interest_rate / 36000;
                                $daily_interest = $amount * $daily_rate;
                                $total_period_interest = round($daily_interest * $period_no_days);

                                if ($frequency == 12) {
                                    $freq_days = 30;
                                } else  if ($frequency == 4) {
                                    $freq_days = 90;
                                } else  if ($frequency == 2) {
                                    $freq_days = 180;
                                } else  if ($frequency == 1) {
                                    $freq_days = 360;
                                }

                                $no_times = $period_no_days / $freq_days;

                                $inst_interest = round($total_period_interest / $no_times);
                            } else if ($period_type == 'd') {
                                $period_no_days = $period;
                                $daily_rate  = $interest_rate / 36000;
                                $daily_interest = $amount * $daily_rate;
                                $total_period_interest = round($daily_interest * $period_no_days);

                                if ($frequency == 12) {
                                    $freq_days = 30;
                                } else  if ($frequency == 4) {
                                    $freq_days = 90;
                                } else  if ($frequency == 2) {
                                    $freq_days = 180;
                                } else  if ($frequency == 1) {
                                    $freq_days = 360;
                                }

                                $no_times = $period_no_days / $freq_days;

                                $inst_interest = round($total_period_interest / $no_times);
                            }
                            ?>


                            <br />
                            <div class="card">
                                <div class="card-body btc-price">

                                    <p class="text-muted mb-3 text-center">SUMMARY</p>
                                    <hr class="hr-dashed">

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <span class="text-muted">Maturity Amount</span>
                                            <h3 class="mt-0"><?= 'UGX'; ?> <span id="futureValue"><?= number_format($amount + $total_period_interest); ?></span>
                                            </h3>
                                        </div>
                                        <div class="col-lg-6">
                                            <span class="text-muted">Interest Earned</span>
                                            <h3 class="mt-0"><?= 'UGX'; ?> <span id="totalInterestEarned"><?= number_format($total_period_interest); ?></span>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br />


                            <table class="table verticle-middle table-responsive-md">
                                <thead>

                                    <tr>
                                        <th colspan="7" style="text-align:center;">
                                            <h4 class="page-title">DISTRIBUTION</h4>
                                        </th>
                                    </tr>

                                    <tr style="text-align: center !important;">
                                        <th>#</th>
                                        <th>OPENING BALANCE</th>

                                        <th>INTEREST EARNED</th>
                                        <th>CLOSING BALANCE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    $open_bal = $amount;
                                    $int = $inst_interest;
                                    if ($no_times < 1 && $no_times > 0) {
                                        $no_times = 1;
                                    }
                                    while ($no_times) {

                                        echo '
                                                    <tr style="text-align: center !important;">
                                        <td>' . $count . '</td>
                                        <td>' . number_format($open_bal) . '</td>
                                       
                                          <td>' . number_format($int) . '</td>
                                        <td>' . number_format($open_bal + $int) . '</td>
                                   
                                        </tr>
                                        ';
                                        $open_bal = $open_bal + $int;
                                        $count++;
                                        $no_times--;
                                    }

                                    ?>

                                </tbody>

                            </table>

                            <table class="table table-responsive-md">

                                <tbody>
                                    <?php
                                    echo '
                                                <tr style="text-align: center !important;">
                                                <td><h4>Total</h4></td>
                                                <td> <h4>' . number_format($total_period_interest) . '</h4></td>
                                                <td></td>
                                                <td></td>
                                               
                                                </tr>
                                                ';
                                    ?>
                                </tbody>
                            </table>
                            <!--</div>-->
                        </div>
                    </div>
                <?php endif; ?>

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
        <!-- <script src="./js/styleSwitcher.js"></script> -->

        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>

        <script>

        </script>


</body>

</html>