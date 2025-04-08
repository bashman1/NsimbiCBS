<?php
include('../backend/config/session.php');
?>
<?php

$title = 'SHARE STATEMENT';
require_once('includes/functions.php');
require_once('includes/head_tag.php');

include_once('includes/response.php');

require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
$response = new Response();

$_REQUEST['id'] = parsed_id($_REQUEST['id']);

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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Shares Statement
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <form method="post">
                                        <input class="form-control " type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="example">
                                                    <p class="mb-1">From:</p>
                                                    <input class="form-control" type="date" name="from_date" value="<?= isset($_REQUEST['from_date']) ? date('Y-m-d', strtotime($_POST['from_date'])) : '' ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="example">
                                                    <p class="mb-1">To:</p>
                                                    <input class="form-control" type="date" name="to_date" value="<?= isset($_REQUEST['to_date']) ? date('Y-m-d', strtotime($_POST['to_date'])) : '' ?>">
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

                <?php

                if (isset($_REQUEST['submit'])) {

                    $s =  date('Y-m-d', strtotime(@$_REQUEST['from_date']));
                    $e =  date('Y-m-d', strtotime(@$_REQUEST['to_date']));
                    $deposits = $response->getCustomerShareTrans(@$_REQUEST['id'], $s, $e);
                    $deposits2 = $response->getCustomerShareTrans2(@$_REQUEST['id'], $s, $e);
                    $deposits3 = $response->getCustomerShareTrans3(@$_REQUEST['id'], $s, $e);
                    $dur_stat =  @$_REQUEST['from_date'] . ' TO ' . @$_REQUEST['to_date'];
                    $m_type = 1;
                } else {

                    $s =  '';
                    $e =  '';
                    $deposits = $response->getCustomerShareTrans(@$_REQUEST['id'], $s, $e);
                    $deposits2 = $response->getCustomerShareTrans2(@$_REQUEST['id'], $s, $e);
                    $deposits3 = $response->getCustomerShareTrans3(@$_REQUEST['id'], $s, $e);
                    $dur_stat = 'SINCE JOIN';
                    $m_type = 0;
                }

                ?>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-primary">Shares Statement</h4>
                        <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                            <i class="fas fa-file-pdf"></i>&nbsp;PDF
                        </a>
                    </div>
                    <div class="card-body" id="exreportn">
                        <?php
                        $det = $response->getShareAccDetails(@$_REQUEST['id']);

                        ?>


                        <div class="table-responsive recentOrderTable">
                            <table class="table table-striped" style="min-width: 845px">
                                <thead>

                                    <tr>
                                        <th colspan="7" style="text-align:center;">SHARE STATEMENT</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">Account Name :
                                            <?php echo $det[0]['account_name']; ?></th>
                                        <th colspan="3">Account Number :
                                            <?php echo $det[0]['_account_no']; ?></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">Address : <?php echo $det[0]['address']; ?></th>
                                        <th colspan="3">Account Currency : UGX</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">Branch : <?php echo $det[0]['branch_name']; ?></th>
                                        <th colspan="3">Statement Period : <?php echo $dur_stat; ?></th>
                                    </tr>
                                    <tr style=" border: none;">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th colspan="3" style="text-align:center;"></th>
                                        <th></th>

                                    </tr>
                                    <tr style=" border: none;">
                                        <th>Share Amount</th>
                                        <th style="text-align:center;"><?php echo number_format($det[0]['tot_amount']); ?></th>
                                        <th style="text-align:center;"></th>
                                        <th colspan="3" style="text-align:center;"></th>
                                        <th style="text-align:center;"></th>

                                    </tr>
                                    <tr style=" border: none;">
                                        <th>No. of Shares</th>
                                        <th style="text-align:center;">
                                            <?php echo number_format($det[0]['tot_shares']); ?></th>
                                        <th style="text-align:center;"></th>
                                        <th colspan="3" style="text-align:center;"></th>
                                        <th></th>

                                    </tr>
                                    <tr>
                                        <th>REF</th>
                                        <th>TRXN DATE</th>
                                        <th>DESCRIPTION</th>
                                        <th>AMOUNT</th>
                                        <th>SHARES</th>
                                        <th>BALANCE</th>



                                    </tr>

                                </thead>
                                <tbody>


                                    <?php
                                    $ccount = 0;
                                    $dcount = 0;
                                    $shares = 0;
                                    $amount = 0;
                                    $shares2 = 0;
                                    $shares3 = 0;
                                    $amount2 = 0;
                                    $amount3 = 0;
                                    $val = 0;
                                    $ctotal = 0;
                                    $dtotal = 0;
                                    $bal = 0;
                                    if ($s != '') {
                                        $per_bal = $response->getCustomerSharesBalBF($_REQUEST['id'], $s, $e, 1)[0];

                                        $val = $val + $per_bal['amount'] + $per_bal['filtered_amount'];
                                        $bal = $bal + $per_bal['shares'] + $per_bal['filtered_shares'];
                                        echo '
                     <tr style="color:#004797 !important;">
                            <td>' . '' . '</td>
                             <td>' . '' . '</td>
                              <td>' . 'Balance at the Period Start' . '</td>
                               <td class="f-w-600" >' . number_format($val) . '</td>
                            <td>' . number_format($per_bal['shares'] + $per_bal['filtered_shares']) . '</td>
                            <td>' . number_format($bal) . '</td>
                           
                               
                          </tr>
                     ';
                                    } else {
                                        $per_bal = $response->getCustomerSharesBalBF($_REQUEST['id'], $s, $e, 0)[0];
                                        $val = $val + $per_bal['amount'];
                                        $bal = $bal + $per_bal['shares'];
                                        if ($per_bal['amount'] > 0) {
                                            echo '
                     <tr style="color:#004797 !important;">
                            <td>' . '' . '</td>
                             <td>' . '' . '</td>
                              <td>' . 'Imported Shares' . '</td>
                               <td class="f-w-600" >' . number_format($val) . '</td>
                            <td>' . number_format($per_bal['shares']) . '</td>
                            <td>' . number_format($bal) . '</td>
                           
                               
                          </tr>
                     ';
                                        } else {
                                            echo '
                     <tr style="color:#004797 !important;">
                            <td>' . '' . '</td>
                             <td>' . '' . '</td>
                              <td>' . 'Balance at the Period Start' . '</td>
                               <td class="f-w-600" >' . number_format($val) . '</td>
                            <td>' . number_format($per_bal['shares']) . '</td>
                           
                                <td>' . number_format($bal) . '</td>
                          </tr>
                     ';
                                        }
                                    }

                                    if ($deposits !== "") {


                                        foreach ($deposits as $deposit) {
                                            // if ($deposit['entry_chanel'] != 'data_importer') {
                                            $shares = $shares + $deposit['shares'];
                                            $amount = $amount + $deposit['amount'];
                                            $bal = $bal + $deposit['shares'];

                                            echo '
                     <tr>
                            <td class="no_print clickable_ref_no_shares" ref-no="' . $deposit['ref'] . ' " tid="' . $deposit['tid'] . '">' . $deposit['ref'] . '</td>
                             <td>' . normal_date_short($deposit['date_created']) . '</td>
                            <td>' . (@$deposit['notes'] ?? '') . '</td>
                            <td class="f-w-600" >' . number_format($deposit['amount'])  . '</td>
                              <td class="f-w-600" > ' . number_format($deposit['shares'])  . '</td>
                               <td class="f-w-600" > ' . number_format($bal)  . '</td>
                          </tr>
                     ';
                                            // }
                                        }
                                    }


                                    if ($deposits2 !== "") {


                                        foreach ($deposits2 as $deposit2) {
                                            // if ($deposit['entry_chanel'] != 'data_importer') {
                                            $shares2 = $shares2 + $deposit2['shares'];
                                            $amount2 = $amount2 + $deposit2['amount'];

                                            $bal = $bal + $deposit2['shares'];

                                            echo '
                     <tr>
                            <td class="no_print clickable_ref_no2" ref-no="' . $deposit2['ref'] . ' " tid="' . $deposit2['tid'] . '">' . $deposit2['ref'] . '</td>
                             <td>' . normal_date_short($deposit2['date_created']) . '</td>
                            <td>' . (@$deposit2['notes'] ?? '') . '</td>
                            <td class="f-w-600" >' . number_format($deposit2['amount'])  . '</td>
                              <td class="f-w-600" > ' . number_format($deposit2['shares'])  . '</td>
                               <td class="f-w-600" > ' . number_format($bal)  . '</td>
                          </tr>
                     ';
                                            // }
                                        }
                                    }


                                    if ($deposits3 !== "") {


                                        foreach ($deposits3 as $deposit3) {
                                            // if ($deposit['entry_chanel'] != 'data_importer') {
                                            $shares3 = $shares3 + $deposit3['shares'];
                                            $amount3 = $amount3 + $deposit3['amount'];
                                            $bal = $bal - $deposit3['shares'];

                                            echo '
                     <tr>
                            <td class="no_print clickable_ref_no2" ref-no="' . $deposit3['ref'] . ' " tid="' . $deposit3['tid'] . '">' . $deposit3['ref'] . '</td>
                             <td>' . normal_date_short($deposit3['date_created']) . '</td>
                            <td>' . (@$deposit3['notes'] ?? '') . '</td>
                            <td class="f-w-600" >-' . number_format($deposit3['amount'])  . '</td>
                              <td class="f-w-600" > -' . number_format($deposit3['shares'])  . '</td>
                               <td class="f-w-600" >' . number_format($bal)  . '</td>
                          </tr>
                     ';
                                            // }
                                        }
                                    }




                                    ?>




                                </tbody>

                            </table>

                            <table class="table table-striped" style="min-width: 845px">
                                <thead>
                                    <th colspan="7" style="text-align: center !important;">ACCOUNT SUMMARY</th>
                                </thead>
                                <tbody>
                                    <tr colspan="7">
                                        <th colspan="3">Total No. of Shares:
                                            <?php echo number_format($shares + $shares2 + $per_bal['shares'] - $shares3); ?></th>
                                        <th colspan="4">Total Share Amount:
                                            <?php echo number_format($amount + $amount2 + $per_bal['amount'] + $per_bal['filtered_amount'] - $amount3); ?></th>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>



            </div>
            <?php include('includes/footer.php'); ?>


        </div>


        <div class="modal fade" id="pageGeneralModal2">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>

                </div>
            </div>
        </div>

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

            $('body').on('change', '.reconcile-account-statement', function(event) {
                let value = $(this).val();
                let form = $(this).parents('form');
                let amount_field = form.find('.amount');
                amount_field.val(number_format($(this).data('amount'))).attr('disabled', 'disabled');
                if (value == 'other') {
                    amount_field.removeAttr('disabled');
                }
            });
        </script>


</body>

</html>