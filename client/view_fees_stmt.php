<?php
include('../backend/config/session.php');
?>
<?php


require_once('includes/functions.php');
require_once('includes/head_tag.php');

include_once('includes/response.php');
$response = new Response();

$_GET['id'] = parsed_id($_GET['id']);

?>


<body>

    <!--*******************
 Preloader start
 ********************-->
    <?php include_once('includes/preloader.php'); ?>
    <!--*******************
 Preloader end
 ********************-->


    <!--**********************************
 Main wrapper start
 ***********************************-->
    <div id="main-wrapper">

        <?php
        include_once('includes/nav_bar.php');
        include_once('includes/side_bar.php');
        ?>
        <!--**********************************
 Content body start
 ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Fees Collection Statement
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <form method="post">
                                        <input class="form-control " type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="example">
                                                    <p class="mb-1">Select Date Range</p>
                                                    <input class="form-control input-daterange-datepicker" type="text" name="daterange" value="01/01/2023 - 01/31/2023">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-1">&nbsp;</p>
                                                <button type="submit" class="btn btn-primary" name="submit">Filter Entries</button>
                                            </div>
                                        </div>

                                    </form>


                                </div>
                            </div>
                        </div>



                    </div>
                </div>

                <?php if (isset($_POST['submit'])) {
                    $splitdates = explode('-', $_POST['daterange']);
                    $s =  date('Y-m-d', strtotime($splitdates[0]));
                    $e =  date('Y-m-d', strtotime($splitdates[1]));
                    $deposits = $response->getCustomerTransFees($_POST['id'], $s, $e);
                    $dur_stat =  $splitdates[0] . ' TO ' . $splitdates[1];
                } else {

                    $s =  date('Y-m-d');
                    $e =  date('Y-m-d');
                    $deposits = $response->getCustomerTransFees($_GET['id'], $s, $e);
                    $dur_stat = 'SINCE JOIN';
                }

                ?>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-primary">School Fees Payment Statement</h4>

                        <button class="btn btn-primary" onclick="PrintContent('exreportn')">Print</button>
                    </div>
                    <div class="card-body" id="exreportn">
                        <?php if ($deposits) : ?>

                            <center style="font-size:15px">

                                <img src="<?php echo is_null($user[0]['blogo']) ? 'icons/favicon.png' : $user[0]['blogo']; ?>" width="10%" onerror="this.onerror=null; this.src='icons/favicon.png'">
                                <h6 style="line-height:1.0em"> <b> <?= is_null($user[0]['bankName']) ? '' : strtoupper($user[0]['bankName']); ?> </b> </h6>
                                <p style="line-height:1.0em;font-weight:bold">Location: <?php echo is_null($user[0]['blocation']) ? '' : $user[0]['blocation']; ?> </p>
                                <p style="line-height:1.0em;font-weight:bold"> Tel: <?php echo is_null($user[0]['bcontacts']) ? '' : $user[0]['bcontacts']; ?> </p>
                                <p style="line-height:1.0em;font-weight:bold"> Email: <?php echo is_null($user[0]['bemail']) ? '' : $user[0]['bemail']; ?> </p>

                            </center><br /><br />
                            <div class="table-responsive recentOrderTable">
                                <table class="table verticle-middle table-responsive-md">
                                    <thead>

                                        <tr>
                                            <th colspan="7" style="text-align:center;">STATEMENT OF ACCOUNT</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">Account Name :
                                                <?php echo $deposits[0]['account_name']; ?></th>
                                            <th colspan="3">Account Number :
                                                <?php echo $deposits[0]['_account_no']; ?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">Address : <?php echo $deposits[0]['address']; ?></th>
                                            <th colspan="3">Account Currency : UGX</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">Branch : HEAD OFFICE</th>
                                            <th colspan="3">Statement Period : <?php echo $dur_stat; ?></th>
                                        </tr>
                                        <tr style=" border: none;">
                                            <th></th>
                                            <th>BOOK BALANCE</th>
                                            <th>CLEARED BALANCE</th>
                                            <th colspan="3">FREEZED AMOUNT</th>
                                            <th>SANCTION LIMIT</th>

                                        </tr>
                                        <tr style=" border: none;">
                                            <th>Fees Collection Bal.</th>
                                            <th style="text-align:center;"><?php echo number_format($deposits[0]['tot']); ?></th>
                                            <th style="text-align:center;"><?php echo number_format($deposits[0]['tot']); ?></th>
                                            <th colspan="3" style="text-align:center;">
                                                <?php echo number_format($deposits[0]['freezed_amount']); ?></th>
                                            <th style="text-align:center;">NA</th>

                                        </tr>
                                        <tr style=" border: none;">
                                            <th>Closing Bal.</th>
                                            <th style="text-align:center;">
                                                <?php echo number_format($deposits[0]['acc_balance']); ?></th>
                                            <th style="text-align:center;">
                                                <?php echo number_format($deposits[0]['acc_balance']); ?></th>
                                            <th colspan="3" style="text-align:center;"></th>
                                            <th></th>

                                        </tr>
                                        <tr>
                                            <th>REF</th>
                                            <th>TRAN DATE</th>
                                            <th>VALUE DATE</th>
                                            <th>DESCRIPTION</th>
                                            <th>DEBIT</th>
                                            <!--<th>INTEREST</th>-->
                                            <th>CREDIT</th>
                                            <th>BALANCE</th>



                                        </tr>

                                    </thead>
                                    <tbody>


                                        <?php
                                        $ccount = 0;
                                        $dcount = 0;
                                        $val = 0;
                                        $ctotal = 0;
                                        $dtotal = 0;
                                        if ($deposits !== "") {


                                            foreach ($deposits as $deposit) {

                                                $time = strtotime($deposit['_date_created']);

                                                $newformat = date('d-m-Y', $time);

                                                if (
                                                    $deposit['type'] == "W" or $deposit['type'] == "LE" or $deposit['type'] == "C" or $deposit['type'] == "CW" or $deposit['type'] == "CS" or $deposit['type'] == "SMS" or
                                                    $deposit['type'] == "LP" or
                                                    $deposit['type'] == "RC" or   $deposit['type'] == "I" or $deposit['type'] == 'R'
                                                ) {
                                                    $credit = number_format($deposit['_amount']);
                                                    $debit = "-";
                                                    if ($deposit['type'] == 'LP') {
                                                        if ($deposit['pay_method'] != 'cash' || $deposit['pay_method'] != 'cheque') {
                                                            $val = $val - $deposit['_amount'];
                                                            $dtotal = $dtotal + $deposit['_amount'];
                                                        }
                                                    } else {
                                                        $val = $val - $deposit['_amount'];
                                                        $dtotal = $dtotal + $deposit['_amount'];
                                                    }

                                                    $ccount++;
                                                }
                                                if (
                                                    $deposit['type'] == "L"
                                                ) {
                                                    $credit = number_format($deposit['_amount'] + @$deposit['loan_interest']);
                                                    $debit = "-";
                                                    if ($deposit['pay_method'] != 'cash' && $deposit['pay_method'] != 'cheque') {
                                                        $val = $val - ($deposit['_amount'] + @$deposit['loan_interest']);
                                                        $dtotal = $dtotal + ($deposit['_amount'] + @$deposit['loan_interest']);
                                                    }

                                                    $ccount++;
                                                } else  if ($deposit['type'] == "D" or $deposit['type'] == "A" or $deposit['type'] == "LC" or   $deposit['type'] == "E") {
                                                    $debit = number_format($deposit['_amount']);
                                                    $credit = "-";
                                                    if ($deposit['type'] == 'A') {
                                                        if ($deposit['pay_method'] != 'cash' || $deposit['pay_method'] != 'cheque') {
                                                            $val = $val +  $deposit['_amount'];
                                                            $ctotal = $ctotal + $deposit['_amount'];
                                                        }
                                                    } else {
                                                        $val = $val +  $deposit['_amount'];
                                                        $ctotal = $ctotal + $deposit['_amount'];
                                                    }

                                                    $dcount++;
                                                }
                                                echo '
                     <tr>
                            <td>' . $deposit['_did'] . '</td>
                             <td>' . $newformat . '</td>
                            <td>' . $newformat . '</td>
                            <td>' . $deposit['_reason'] . '</td>
                            
                         
                       
                            <td class="f-w-600" >' . $credit . '</td>
                              <td class="f-w-600" > ' . $debit . '</td>
                                <td class="f-w-600" >' . number_format($val) . '</td>
                          </tr>
                     ';
                                            }
                                        }


                                        ?>


                                    </tbody>

                                </table>

                                <table class="display" id="example3">
                                    <thead>
                                        <th colspan="7" style="text-align: center !important;">ACCOUNT SUMMARY</th>
                                    </thead>
                                    <tbody>
                                        <tr colspan="7">
                                            <th colspan="3">Total No. of Debits:
                                                <?php echo number_format($ccount); ?></th>
                                            <th colspan="4">Total No. of Credits:
                                                <?php echo number_format($dcount); ?></th>
                                        </tr>
                                        <tr colspan="7">
                                            <th colspan="3">Total Debit Amount:
                                                <?php echo number_format($dtotal); ?></th>
                                            <th colspan="4">Total Credit Amount:
                                                <?php echo number_format($ctotal); ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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
        <script src="./vendor/global/global.min.js"></script>

        <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
        <script src="./vendor/jquery-validation/jquery.validate.min.js"></script>
        <!-- Form validate init -->
        <script src="./js/plugins-init/jquery.validate-init.js"></script>
        <script src="./vendor/sweetalert2/dist/sweetalert2.min.js"></script>
        <script src="./js/plugins-init/sweetalert.init.js"></script>

        <!-- Form Steps -->
        <script src="./vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js"></script>
        <script src="vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

        <script src="./js/custom.min.js"></script>
        <script src="./js/dlabnav-init.js"></script>
        <script src="./js/demo.js"></script>
        <script src="./vendor/moment/moment.min.js"></script>
        <script src="./vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
        <script src="./js/plugins-init/bs-daterange-picker-init.js"></script>
        <script src="./vendor/pickadate/picker.js"></script>
        <script src="./vendor/pickadate/picker.time.js"></script>
        <script src="./vendor/pickadate/picker.date.js"></script>
        <script src="./vendor/datatables/js/jquery.dataTables.min.js"></script>
        <script src="./js/plugins-init/datatables.init.js"></script>
        <!-- <script src="./js/styleSwitcher.js"></script> -->

        <?php include('includes/bottom_scripts.php'); ?>



</body>

</html>