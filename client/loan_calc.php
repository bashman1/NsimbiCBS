<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
$title = 'LOAN CALCULATOR';
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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-primary">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Loan Terms
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="LoanCalcForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Repayment Frequency*</label>
                                                    <select class="me-sm-2 default-select form-control wide" id="interestDuration" name="interestDuration" style="display: none;" required>
                                                        <?php
                                                        if (isset($_POST['interestDuration'])) {
                                                            if ($_POST['interestDuration'] == 1) {
                                                                $va = 'Daily';
                                                            } else if ($_POST['interestDuration'] == 2) {
                                                                $va = 'Weekly';
                                                            } else if ($_POST['interestDuration'] == 3) {
                                                                $va = 'Monthly';
                                                            } else if ($_POST['interestDuration'] == 4) {
                                                                $va = 'Bi-Monthly';
                                                            } else if ($_POST['interestDuration'] == 5) {
                                                                $va = 'Annually';
                                                            } else if ($_POST['interestDuration'] == 6) {
                                                                $va = 'Quarterly';
                                                            }
                                                            echo '
<option selected value="' . $_POST['interestDuration'] . '">' . $va . '</option>
';
                                                        }
                                                        ?>
                                                        <option value="1">Daily</option>
                                                        <option value="2">Weekly</option>
                                                        <option value="3">Monthly</option>
                                                        <option value="4">Bi-Monthly</option>
                                                        <option value="6">Quarterly</option>
                                                        <option value="5">Annually</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Interest Method*</label>
                                                    <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="interestMethod" style="display: none;" required>
                                                        <?php
                                                        if (isset($_POST['interestMethod'])) {
                                                            if ($_POST['interestMethod'] == 1) {
                                                                $mname = 'Flat Rate Method';
                                                            } else if ($_POST['interestMethod'] == 2) {
                                                                $mname = 'Declining Balance Method';
                                                            } else {
                                                                $mname = 'Amortization Method';
                                                            }
                                                            echo '
<option selected value="' . $_POST['interestMethod'] . '">' . $mname . '</option>
';
                                                        }
                                                        ?>

                                                        <option value="1">Flat Rate Method</option>
                                                        <option value="2">Declining Balance Method</option>
                                                        <option value="2">Amortization Method</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div><br />
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Enter Duration in <label id="dtype"> DAYS</label> *</label>

                                                    <input type="number" class="form-control input-rounded" placeholder="" name="duration" value="<?php echo isset($_POST['duration']) ? $_POST['duration'] : ''; ?>" required>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Annual Interest Rate (PER ANNUM) *</label>

                                                    <input type="text" class="form-control input-rounded" placeholder="" name="rate" value="<?php echo isset($_POST['rate']) ? $_POST['rate'] : ''; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Disbursement Date*</label>

                                                    <input type="date" class="form-control input-rounded" placeholder="" name="start" value="<?php echo isset($_POST['start']) ? $_POST['start'] : date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="text-label form-label">Loan Amount*</label>

                                                    <input type="text" class="form-control input-rounded" placeholder="" name="amount" value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : ''; ?>" required data-type="amount">
                                                </div>
                                            </div>
                                        </div>




                                        <br /><br /><br />
                                        <!-- <div class="mb-3"> -->

                                        <button type="submit" name="submit" class="btn btn-primary">Calculate</button>
                                        <!-- </div> -->

                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->


                    </div>
                </div>
                <?php if (isset($_POST['submit'])) : ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-primary">Repayment Schedule</h4>

                            <button class="btn btn-primary" onclick="PrintContent('exreportn')">Print</button>
                        </div>
                        <div class="card-body" id="exreportn">
                            <!--<div class="table-responsive">-->
                            <?php
                            $principal = str_replace(",", "", $_POST['amount']);
                            $duration = $_POST['duration'];
                            $cycle = $_POST['interestDuration'];
                            $date_first = $_POST['start'];
                            $rate = $_POST['rate'];
                            $grace_period = 0;
                            $grace_type = 'pay_none';
                            $refine = 1;
                            $method = '';
                            if ($_POST['interestMethod'] == 1) {
                                $method_name = 'FLAT RATE';
                                $method = 'flat';
                            } else if ($_POST['interestMethod'] == 2) {
                                $method_name = 'DECLINING BALANCE METHOD';

                                $method = 'declining';
                            } else if ($_POST['interestMethod'] == 3) {
                                $method_name = 'AMORTIZATION METHOD';

                                $method = 'amortization';
                            }

                            if ($cycle == 1) {
                                $cycle_name = "Daily";
                                $use_date_first = date('Y-m-d', strtotime($date_first . ' + 1 days'));
                                $frequency = 'd';
                                $ftype = 'DAYS';
                            } else if ($cycle == 2) {
                                $cycle_name = "Weekly";
                                $use_date_first = date('Y-m-d', strtotime($date_first . ' + 7 days'));
                                $frequency = 'w';
                                $ftype = 'WEEKS';
                            } else if ($cycle == 3) {
                                $cycle_name = "Monthly";
                                $use_date_first = date('Y-m-d', strtotime($date_first . ' + 30 days'));
                                $frequency = 'm';
                                $ftype = 'MONTHS';
                            } else if ($cycle == 6) {
                                $cycle_name = "Quarterly";
                                $use_date_first = date('Y-m-d', strtotime($date_first . ' + 90 days'));
                                $frequency = 'q';
                                $ftype = 'MONTHS';
                            } else if ($cycle == 4) {
                                $cycle_name = "Bimonthly";
                                $use_date_first = date('Y-m-d', strtotime($date_first . ' + 15 days'));
                                $frequency = 'd';
                                $ftype = 'DAYS';
                            } else if ($cycle == 5) {
                                $cycle_name = "Annually";
                                $use_date_first = date('Y-m-d', strtotime($date_first . ' + 360 days'));
                                $frequency = 'y';
                                $ftype = 'YEARS';
                            } else {
                                $cycle_name = "";
                            }

                            // get calculation details from loan calculator api
                            $details = $response->getRightSchedule($rate, $duration, $principal, $use_date_first, $method, $grace_period, $frequency, $ftype, $grace_type, $refine);


                            ?>

                            <center style="font-size:15px">

                                <img src="<?php echo is_null($user[0]['blogo']) ? 'icons/favicon.png' : $user[0]['blogo']; ?>" width="8%" onerror="this.onerror=null; this.src='icons/favicon.png'">
                                <h4 style="line-height:1.0em"> <b> <?= is_null($user[0]['bankName']) ? '' : strtoupper($user[0]['bankName']); ?> </b> </h4>
                                <p style="line-height:1.0em;font-weight:bold">Location: <?php echo is_null($user[0]['blocation']) ? '' : $user[0]['blocation']; ?> </p>
                                <p style="line-height:1.0em;font-weight:bold"> Tel: <?php echo is_null($user[0]['bcontacts']) ? '' : $user[0]['bcontacts']; ?> </p>
                                <p style="line-height:1.0em;font-weight:bold"> Email: <?php echo is_null($user[0]['bemail']) ? '' : $user[0]['bemail']; ?> </p>

                            </center><br /><br />

                            <br />
                            <table class="display" id="example3">
                                <thead>
                                    <tr colspan="8">
                                        <th colspan="8" style="text-align: center !important;"> SUMMARY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr colpan="4">
                                        <td>Interest Method :</td>

                                        <td><strong style="color:red">&nbsp;
                                                <?= $method_name; ?>
                                            </strong> </td>

                                        <td>Interest Rate :</td>

                                        <td> <strong style="color:red">&nbsp;
                                                <?= $rate . ' % PER ANNUM'; ?>
                                            </strong></td>
                                    </tr>
                                    <tr colpan="4">
                                        <td>Total Principal :</td>

                                        <td><strong style="color:red"> UGX&nbsp;
                                                <?= number_format($details['total_principal']); ?>
                                            </strong> </td>

                                        <td>Total Interest :</td>

                                        <td> <strong style="color:red"> UGX&nbsp;
                                                <?= number_format($details['total_interest']); ?>
                                            </strong></td>
                                    </tr>

                                    <tr colpan="4">

                                        <td>Total Paid :</td>

                                        <td><strong style="color:red"> UGX&nbsp;
                                                <?= number_format($details['total_all_paid']); ?>
                                            </strong> </td>
                                        <td>Installment Amount :</td>

                                        <td><strong style="color:red"> UGX&nbsp;
                                                <?= number_format($details['all_payments'][0]['total_payment']); ?>
                                            </strong> </td>


                                    </tr>

                                </tbody>
                            </table>
                            <br />


                            <table class="table verticle-middle table-responsive-md">
                                <thead>

                                    <tr>
                                        <th colspan="7" style="text-align:center;">
                                            <h4 class="page-title">LOAN SCHEDULE</h4>
                                        </th>
                                    </tr>

                                    <tr style="text-align: center !important;">
                                        <th>#</th>
                                        <th>Payment Due Date</th>

                                        <th>Interest Due (UGX)</th>
                                        <th>Principal Due (UGX)</th>
                                        <th>Total Due (UGX)</th>

                                        <th>Outstanding Balance (UGX)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $usedate = $use_date_first;
                                    foreach ($details['all_payments'] as $row) {

                                        echo '
                                        <tr style="text-align: center !important;">
                                        <td>' . $row['payment_number'] . '</td>
                                        <td>' . normal_date($usedate) . '</td>
                                       
                                          <td>' . number_format($row['interest_expected']) . '</td>
                                        <td>' . number_format($row['principal_expected']) . '</td>
                                        <td> ' . number_format($row['total_payment']) . '</td>
                                        <td>' . number_format($row['brought_forward']) . '</td>
                                   
                                        </tr>
                                        ';
                                        if ($cycle == 1) {
                                            $usedate = date('Y-m-d', strtotime($usedate . ' + 1 days'));
                                        } else if ($cycle == 2) {
                                            $usedate = date('Y-m-d', strtotime($usedate . ' + 7 days'));
                                        } else if ($cycle == 3) {
                                            $usedate = date('Y-m-d', strtotime($usedate . ' + 30 days'));
                                        } else if ($cycle == 4) {
                                            $usedate = date('Y-m-d', strtotime($usedate . ' + 15 days'));
                                        } else if ($cycle == 5) {
                                            $usedate = date('Y-m-d', strtotime($usedate . ' + 360 days'));
                                        } else if ($cycle == 6) {
                                            $usedate = date('Y-m-d', strtotime($usedate . ' + 90 days'));
                                        }
                                    }

                                    ?>

                                </tbody>

                            </table>

                            <table class="table table-responsive-md">

                                <tbody>
                                    <?php
                                    echo '
                                                <tr style="text-align: center !important;">
                                                <td></td>
                                                <td><h4>Total</h4></td>
                                               
                                               
                                                
                                                <td><h4> ' . number_format($details['total_interest']) . '</h4></td>
                                             
                                            
                                                <td> <h4>' . number_format($details['total_principal']) . '</h4></td>
                                                <td> <h4>' . number_format($details['total_all_paid']) . '</h4></td>
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
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>

        <script>
            $(document).ready(function() {
                $("input[data-type='amount']").keyup(function(event) {
                    // skip for arrow keys
                    if (event.which >= 37 && event.which <= 40) {
                        event.preventDefault();
                    }
                    var $this = $(this);
                    var num = $this.val().replace(/,/gi, "");
                    var num2 = num.split(/(?=(?:\d{3})+$)/).join(",");
                    // console.log(num2);
                    // the following line has been simplified. Revision history contains original.
                    $this.val(num2);
                });
            });
        </script>
        <!-- <script src="./js/styleSwitcher.js"></script> -->
        <script>
            $('#interestDuration').change(function() {

                if ($(this).find('option:selected').val() == 1) {
                    $('#dtype').html(' DAYS');
                } else if ($(this).find('option:selected').val() == 2) {
                    $('#dtype').html(' WEEKS');

                } else if ($(this).find('option:selected').val() == 3) {
                    $('#dtype').html(' MONTHS');

                } else if ($(this).find('option:selected').val() == 4) {
                    $('#dtype').html(' DAYS');

                } else if ($(this).find('option:selected').val() == 5) {
                    $('#dtype').html(' YEARS');
                } else if ($(this).find('option:selected').val() == 6) {
                    $('#dtype').html(' MONTHS');
                } else {
                    $('#dtype').html(' DAYS');
                }

            });
        </script>
        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>


</body>

</html>