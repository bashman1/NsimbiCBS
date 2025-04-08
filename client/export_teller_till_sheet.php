<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();
$details = $reponse->getStaffTillEntries($_GET['start'], $_GET['end'], $_GET['staff']);
$acc_details = $reponse->getCashAccBranding($_GET['staff']);
?>

<?php
require_once('includes/report_header.php');
?>
<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> TELLER TILL SHEET </strong> </td>
        </tr>
    </table>
    <table class="">
        <tr>
            <td> <strong> STAFF NAME </strong> </td>
            <td><?= $acc_details[0]['staff_names'] ?></td>
            <td> <strong> STAFF ROLE </strong> </td>
            <td><?= $acc_details[0]['staff_role'] ?></td>
        </tr>

        <tr>
            <td> <strong> CASH A/C </strong> </td>
            <td><?= $acc_details[0]['cash_acc_name'] ?></td>
            <td> <strong> CLOSING BALANCE </strong> </td>
            <td>UGX <?= number_format($acc_details[0]['cash_acc_balance'] ?? 0) ?></td>
        </tr>
        <tr>
            <td> <strong> BRANCH </strong> </td>
            <td><?= $acc_details[0]['staff_branch'] ?></td>
            <td> <strong> DATE RANGE </strong> </td>
            <td>From: <?php echo normal_date($_GET['start'] ?? date('Y-m-d')) . '   To:    ' . normal_date($_GET['end'] ?? date('Y-m-d')) ?></td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>REFERENCE NO.</th>
            <th>TRXN DATE</th>
            <th>CHART ACCOUNT</th>
            <th>DR</th>
            <th>CR</th>
            <th>BALANCE</th>
        </thead>
        <tbody>
            <?php
            if ($details) {
                $dess = '';
                $count = 1;
                $ccount = 0;
                $dcount = 0;
                $val = 0;
                $ctotal = 0;
                $dtotal = 0;

                $val = $val;
                foreach ($details as $deposit) {
                    $trxn_date = date('Y-m-d', strtotime($deposit['_date_created']));

                    if (
                        $deposit['type'] == "W" or $deposit['type'] == "LE" or $deposit['type'] == "C" or $deposit['type'] == "CW" or $deposit['type'] == "CS" or $deposit['type'] == "SMS" or
                        $deposit['type'] == "LP" or
                        $deposit['type'] == "RC" or  $deposit['type'] == "E" || $deposit['type'] == "TTS"
                        || $deposit['type'] == "A"
                    ) {

                        $credit = number_format($deposit['_amount']);
                        $debit = "-";
                        $val = $val - $deposit['_amount'];
                        $dtotal = $dtotal + $deposit['_amount'];

                        if ($deposit['type'] == "A") {
                            $dess = 'Loan Disbursement: ' . @$deposit['acc_name'];
                        }
                        if ($deposit['type'] == "W") {
                            if ($deposit['is_reversal']) {
                                $dess = 'Reversal: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . '  ';
                            } else {
                                $dess = 'Withdraw: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . '  ';
                            }
                        }
                        if ($deposit['type'] == "E") {
                            $dess = 'Expense: ';
                        }

                        if ($deposit['type'] == "TTS") {
                            $dess = 'Cash Transfer: ';
                        }


                        $ccount++;
                    }


                    if (
                        $deposit['type'] == "AJE"
                    ) {
                        if ($deposit['dr_acid'] == $staff) {
                            $credit = number_format($deposit['_amount']);
                            $debit = "-";
                            $val = $val - ($deposit['_amount']);
                            $dtotal = $dtotal + ($deposit['_amount']);
                        } else {
                            $debit = number_format($deposit['_amount']);
                            $credit = "-";
                            $val = $val - ($deposit['_amount']);
                            $ctotal = $ctotal + ($deposit['_amount']);
                        }
                        $dess = 'Advanced Journal Entry: ';
                        $ccount++;
                    }


                    if (
                        $deposit['type'] == "LIA"
                    ) {
                        if ($deposit['cr_dr'] == 'credit') {
                            $credit = number_format($deposit['_amount']);
                            $debit = "-";
                            $val = $val - ($deposit['_amount']);
                            $dtotal = $dtotal + ($deposit['_amount']);
                        } else {
                            $debit = number_format($deposit['_amount']);
                            $credit = "-";
                            $val = $val - ($deposit['_amount']);
                            $ctotal = $ctotal + ($deposit['_amount']);
                        }
                        $dess = 'Liability Journal Entry: ';
                        $ccount++;
                    }

                    if ($deposit['type'] == "TTT") {

                        $dess = 'Cash Transfer: ';


                        if ($deposit['cr_acid'] == $staff) {

                            $debit = number_format($deposit['_amount']);
                            $credit = "-";
                            $val = $val +  $deposit['_amount'];
                            $ctotal = $ctotal + $deposit['_amount'];
                        } else {

                            $credit = number_format($deposit['_amount']);
                            $debit = "-";
                            $val = $val - $deposit['_amount'];
                            $dtotal = $dtotal + $deposit['_amount'];
                        }
                    }

                    if (
                        $deposit['type'] == "L"
                    ) {
                        $debit = number_format($deposit['_amount'] + @$deposit['loan_interest']);
                        $credit = "-";
                        $val = $val + ($deposit['_amount'] + @$deposit['loan_interest']);
                        $ctotal = $ctotal + ($deposit['_amount'] + @$deposit['loan_interest']);
                        $dess = 'Loan Payment: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . ' ';
                        $ccount++;
                    } else  if ($deposit['type'] == "D"  or $deposit['type'] == "LC" or  $deposit['type'] == "I" || $deposit['type'] == "R" || $deposit['type'] == "STT") {



                        $debit = number_format($deposit['_amount']);
                        $credit = "-";
                        $val = $val +  $deposit['_amount'];
                        $ctotal = $ctotal + $deposit['_amount'];

                        if ($deposit['type'] == "D") {
                            $dess = 'Deposit: (' . $deposit['_account_no'] . ') by ' . $deposit['_paidby_name'] . ' ';
                        }

                        if ($deposit['type'] == "STT") {
                            $dess = 'Cash Transfer: ';
                        }

                        if ($deposit['type'] == "I") {
                            $dess = 'Income: ';
                        }
                        if ($deposit['type'] == "R") {
                            $dess = 'Membership: ';
                        }



                        $dcount++;
                    } else if ($deposit['type'] == "ASS") {

                        if (@$deposit['cr_dr'] == 'debit' || @$deposit['entry_channel'] == 'data_importer') {
                            $debit = number_format($deposit['_amount']);
                            $credit = "-";
                            $val = $val +  $deposit['_amount'];
                            $ctotal = $ctotal + $deposit['_amount'];
                            $dcount++;
                        } else {
                            $credit = number_format($deposit['_amount']);
                            $debit = "-";
                            $val = $val - $deposit['_amount'];
                            $dtotal = $dtotal + $deposit['_amount'];
                            $ccount++;
                        }

                        $dess = 'Asset Registered: ';
                    }


            ?>
                    <tr>
                        <td>
                            <?= $count++; ?>
                        </td>
                        <td><?= $deposit['ref']  ?></td>
                        <td><?= $trxn_date   ?></td>
                        <td><?= $dess . $deposit['_reason'] ?></td>
                        <td><?= $debit  ?></td>
                        <td><?= $credit ?></td>
                        <td><?= number_format($val)  ?></td>
                    </tr>

                <?php

                } ?>
        <tfoot>
            <td colspan="4"><strong>TOTAL:</strong></td>
            <td><strong><?= number_format($ctotal) ?></strong></td>
            <td><strong><?= number_format($dtotal)  ?></strong></td>
            <td><strong><?= number_format($ctotal - $dtotal) ?></strong></td>
        </tfoot>
    <?php
            } else {
                echo '<div class="col-md-4"><div class="alert alert-warning"><span class="semibold">Caution: </span>No Journal Entries found</div></div>';
            }
    ?>
    </tbody>
    </table>
    <br /><br />
    <div class="row show_on_print">
        <div class="col-md-4" style="width: 369px;float: left;">

            <h4><small>TELLER:
                </small><b><?= $details[0]['cash_acc_details'] ?? '' ?></b></h4>

            <br>

            <h4><small>SIGNATURE:</small><b>
                    ------------------------</b></h4>

        </div>

        <div class="col-md-4" style="width: 369px"></div>
        <div class="col-md-4" style="width: 369px;float: right;">

            <div style="width: 313px;height: 96px;border: 1px solid;">
            </div>
            <br>
            <i>Official Use Only</i>
        </div>
    </div>

</section>