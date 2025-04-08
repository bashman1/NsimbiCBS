<?php
include('../backend/config/session.php');
?>
<?php

$title = 'ACCOUNT STATEMENT';
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
                                    Transaction Statement
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
                                                    <input class="form-control" type="date" name="from_date" value="<?= isset($_REQUEST['from_date']) ? date('Y-m-d', strtotime($_REQUEST['from_date'])) : '' ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="example">
                                                    <p class="mb-1">To:</p>
                                                    <input class="form-control" type="date" name="to_date" value="<?= isset($_REQUEST['to_date']) ? date('Y-m-d', strtotime($_REQUEST['to_date'])) : '' ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-1">&nbsp;</p>
                                                <button type="submit" class="btn btn-primary" name="submit">Filter Entries</button>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check custom-checkbox mb-3">
                                                    <br />
                                                    <input type="checkbox" class="form-check-input" id="customCheckBox1" name="by_tid" onchange="toggleCheckboxAjax('customCheckBox1', 'customCheckBox2')" value="<?= @$_REQUEST['by_tid'] ? 1 : 0 ?>" <?= @$_REQUEST['by_tid'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="customCheckBox1">Order by transaction ID</label>
                                                    <p class="text-muted mb-3">If checked system will sort the statement in ascending order by column transaction ID</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check custom-checkbox mb-3">
                                                    <br />
                                                    <input type="checkbox" class="form-check-input" id="customCheckBox2" name="by_date" onchange="toggleCheckboxAjax('customCheckBox2', 'customCheckBox1')" value="<?= @$_REQUEST['by_date'] ? 1 : 0 ?>" <?= @$_REQUEST['by_date'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="customCheckBox2">Order by transaction record Date</label>
                                                    <p class="text-muted mb-3">If checked system will sort the statement in ascending order by column transaction date</p>
                                                </div>
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

                    $by_tid = @$_REQUEST['by_tid'];
                    $by_date = @$_REQUEST['by_date'];

                    $deposits = $response->getCustomerTrans(@$_REQUEST['id'], $s, $e, $by_tid, $by_date);
                    $dur_stat =  @$_REQUEST['from_date'] . ' TO ' . @$_REQUEST['to_date'];
                    $m_type = 1;
                } else {

                    $s =  '';
                    $e =  '';
                    $by_tid = 1;
                    $by_date = 0;
                    $deposits = $response->getCustomerTrans(@$_REQUEST['id'], $s, $e, $by_tid, $by_date);
                    $dur_stat = 'SINCE JOIN';
                    $m_type = 0;
                }

                ?>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-primary">Transaction Statement</h4>
                        <!-- onclick="PrintContent('exreportn')" -->
                        <!-- <button class="btn btn-primary" >Print</button> -->

                        <a href="export_report.php?exportFile=export_member_statement&useFile=1&submit=<?= $m_type ?>&from_date=<?= $s ?>&to_date=<?= $e ?>&id=<?= $_REQUEST['id'] ?>&by_tid=<?= $by_tid ?>&by_date=<?= $by_date ?>" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                            <i class="fas fa-file-pdf"></i>&nbsp;PDF
                        </a>
                    </div>
                    <div class="card-body" id="exreportn">
                        <?php if ($deposits) : ?>


                            <div class="table-responsive recentOrderTable">
                                <table class="table table-striped" style="min-width: 845px">
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
                                            <th colspan="4">Branch : <?php echo $deposits[0]['branch_name']; ?></th>
                                            <th colspan="3">Statement Period : <?php echo $dur_stat; ?></th>
                                        </tr>
                                        <tr style=" border: none;">
                                            <th></th>
                                            <th>BOOK BALANCE</th>
                                            <th>OVER-DRAFT AMOUNT</th>
                                            <th colspan="3" style="text-align:center;">FREEZED AMOUNT</th>
                                            <th>SANCTION LIMIT</th>

                                        </tr>
                                        <tr style=" border: none;">
                                            <th>Opening Bal.</th>
                                            <th style="text-align:center;">0</th>
                                            <th style="text-align:center;"><?php echo number_format($deposits[0]['over_draft']); ?></th>
                                            <th colspan="3" style="text-align:center;">
                                                <?php echo number_format($deposits[0]['freezed_amount']); ?></th>
                                            <th style="text-align:center;">NA</th>

                                        </tr>
                                        <tr style=" border: none;">
                                            <th>Closing Bal.</th>
                                            <th style="text-align:center;">
                                                <?php echo number_format($deposits[0]['acc_balance']); ?></th>
                                            <th style="text-align:center;"></th>
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
                                        if ($s != '') {
                                            $per_bal = $response->getCustomerTransBalBF($_REQUEST['id'], $s, $e);
                                            $val = $val + $per_bal;
                                            echo '
                     <tr style="color:#004797 !important;">
                            <td class="no_print">' . '' . '</td>
                             <td>' . '' . '</td>
                            <td>' . '' . '</td>
                            <td>' . 'Balance at the Period Start' . '</td>
                            <td class="f-w-600" >' . '' . '</td>
                              <td class="f-w-600" > ' . '' . '</td>
                                <td class="f-w-600" >' . number_format($val) . '</td>
                          </tr>
                     ';
                                        } else {
                                            $val = $val + 0;
                                            echo '
                     <tr style="color:#004797 !important;">
                            <td class="no_print">' . '' . '</td>
                             <td>' . '' . '</td>
                            <td>' . '' . '</td>
                            <td>' . 'Balance at the Period Start' . '</td>
                            <td class="f-w-600" >' . '' . '</td>
                              <td class="f-w-600" > ' . '' . '</td>
                                <td class="f-w-600" >' . number_format(0) . '</td>
                          </tr>
                     ';
                                        }
                                        if ($deposits !== "") {


                                            foreach ($deposits as $deposit) {
                                                if ($deposit['entry_chanel'] != 'data_importer') {
                                                    if ($deposit['type'] != "WLP") {
                                                        if ($deposit['type'] != "WLI") {
                                                            if ($deposit['type'] != "LIA") {
                                                                if ($deposit['type'] != "ASS") {

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
                                                                        
                                                                        if ($deposit['pay_method'] != 'cash' && $deposit['pay_method'] != 'cheque') {
                                                                            $val = $val - ($deposit['_amount'] + @$deposit['loan_interest']);
                                                                            $dtotal = $dtotal + ($deposit['_amount'] + @$deposit['loan_interest']);
                                                                            $credit = number_format($deposit['_amount'] + @$deposit['loan_interest']);
                                                                            $debit = "-";
                                                                        }

                                                                        $ccount++;
                                                                    } else  if ($deposit['type'] == "D" or $deposit['type'] == "A" or $deposit['type'] == "LC" or   $deposit['type'] == "E" or $deposit['type'] == "CAP") {
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
                            <td class="no_print clickable_ref_no" ref-no="' . $deposit['ref'] . ' " tid="' . $deposit['_did'] . '">' . $deposit['ref'] . '</td>
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
                                                        }
                                                    }
                                                }
                                            }
                                        }


                                        ?>


                                        <?php if ($deposits[0]['acc_balance'] + $deposits[0]['freezed_amount'] != $val && $permissions->hasSubPermissions('reconcile_saving_statement')) { ?>
                                            <tr>
                                                <td colspan="7" class="text-end">
                                                    <button class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#account_statement_reconciliation_form">
                                                        Account Statement Reconciliation
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>

                                </table>

                                <table class="table table-striped" style="min-width: 845px">
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
            <?php include('includes/footer.php'); ?>


        </div>

        <div class="modal fade" id="account_statement_reconciliation_form">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reconcile Account Statement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= BACKEND_BASE_URL ?>Accounting/account_statement_reconciliation.php" class="custom-form" id="account_statement_reconciliation" data-reload-page="1" data-confirm-action="1">
                            <div class="row">
                                <input type="hidden" name="account_balance" value="<?= @$deposits[0]['acc_balance'] ?? 0; ?>">
                                <input type="hidden" name="freezed" value="<?= @$deposits[0]['freezed_amount'] ?? 0; ?>">
                                <input type="hidden" name="closing_balance" value="<?= @$val; ?>">
                                <input type="hidden" name="client_id" value="<?= parsed_id(@$_GET['id']); ?>">

                                <div class="col-md-12">
                                    What do you want to reconcile with?
                                    <br>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input reconcile-account-statement" value="account_balance" type="radio" name="reconcile" required data-amount="<?= @$deposits[0]['acc_balance']; ?>" id="account_balance">
                                            <label class="form-check-label" for="account_balance">Account Balance</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input reconcile-account-statement" value="closing_balance" type="radio" name="reconcile" required data-amount="<?= $val; ?>" id="closing_balance">
                                            <label class="form-check-label" for="closing_balance">Closing Balance</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input reconcile-account-statement" value="other" type="radio" name="reconcile" required data-amount="0" id="other">
                                            <label class="form-check-label" for="other">Other</label>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="amount" class="form-control amount" placeholder=" " required disabled value="0">
                                        <label for="amount">Amount</label>
                                    </div>
                                </div><br /><br /><br />
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="date" name="rd" class="form-control" placeholder=" " required value="<?= date('Y-m-d') ?>">
                                        <label for="date">Reconciliation Date</label>
                                    </div>
                                </div>
                                <br /> <br /> <br />
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="reason" class="form-control" placeholder=" " required>
                                        <label for="reason">Reason for Reconciliation</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Reconcile Account Statement</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="pageGeneralModal">
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
            function toggleCheckboxAjax(checkedId, otherId) {
                const checkedBox = document.getElementById(checkedId);
                const otherBox = document.getElementById(otherId);

                if (checkedBox.checked) {
                    checkedBox.value = "1";
                    otherBox.checked = false;
                    otherBox.value = "0";
                } else {
                    checkedBox.value = "0";
                }
            }

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