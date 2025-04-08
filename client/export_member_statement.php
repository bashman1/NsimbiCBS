<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();

?>
<?php if ($_REQUEST['submit']) {

    $s =  date('Y-m-d', strtotime($_REQUEST['from_date']));
    $e =  date('Y-m-d', strtotime($_REQUEST['to_date']));
    $by_tid = $_REQUEST['by_tid'];
    $by_date = $_REQUEST['by_date'];
    $deposits = $reponse->getCustomerTrans($_REQUEST['id'], $s, $e, $by_tid, $by_date);
    $dur_stat =  normal_date_short($_REQUEST['from_date']) . ' TO ' . normal_date_short($_REQUEST['to_date']);
} else {

    $s =  '';
    $e =  '';
    $by_tid = 1;
    $by_date = 0;
    $deposits = $reponse->getCustomerTrans($_REQUEST['id'], $s, $e, $by_tid, $by_date);
    $dur_stat = 'SINCE JOIN';
}

?>

<?php
require_once('includes/report_header.php');
?>


<section class="report-section">
    <table class="main-header">
        <tr>
            <td> <strong> STATEMENT OF ACCOUNT </strong> </td>
        </tr>
        <tr>
            <td>Account Name : </td>
            <td><?php echo $deposits[0]['account_name']; ?></td>
            <td>Account Number : </td>
            <td><?php echo $deposits[0]['_account_no']; ?></td>
        </tr>
        <tr>
            <td>Customer Address : </td>
            <td><?php echo $deposits[0]['address']; ?></td>
            <td>Currency : </td>
            <td>UGX</td>
        </tr>
        <tr>
            <td>Branch : </td>
            <td><?php echo $deposits[0]['branch_name']; ?></td>
            <td>Statement Period : </td>
            <td> <?php echo $dur_stat; ?></td>

        </tr>
        <br />

        <tr>
            <td> </td>
            <td>BOOK BALANCE </td>
            <td>OVER-DRAFT AMOUNT </td>
            <td colspan="3" style="text-align:center;"> FREEZED AMOUNT</td>
            <td> SANCTION LIMIT</td>

        </tr>
        <tr style=" border: none;">
            <td>Opening Balance</td>
            <td style="text-align:center;">0</td>
            <td style="text-align:center;">0</td>
            <td colspan="3" style="text-align:center;">
                0</td>
            <td style="text-align:center;">N/A</td>

        </tr>
        <tr style=" border: none;">
            <td>Closing Balance</td>
            <td style="text-align:center;">
                <?php echo number_format($deposits[0]['acc_balance']); ?></td>
            <td style="text-align:center;"><?php echo number_format($deposits[0]['over_draft']); ?></td>
            <td colspan="3" style="text-align:center;"><?php echo number_format($deposits[0]['freezed_amount']); ?></td>
            <td style="text-align:center;">N/A</td>

        </tr>

    </table>
    <hr>


    <table class="report_table">
        <thead>
            <th>Booking Date</th>
            <th>Value Date</th>
            <th>Reference</th>
            <th>Description</th>
            <th>Cheque.no</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Closing Balance</th>
        </thead>
        <tbody>
            <?php
            $ccount = 0;
            $dcount = 0;
            $val = 0;
            $ctotal = 0;
            $dtotal = 0;
            if ($s != '') {
                $per_bal = $reponse->getCustomerTransBalBF($_REQUEST['id'], $s, $e);
                $val = $val + $per_bal;
                echo '
                     <tr style="color:#004797 !important;">
                            <td class="no_print">' . '' . '</td>
                             <td>' . '' . '</td>
                            <td>' . '' . '</td>
                            <td>' . 'Balance at the Period Start' . '</td>
                            <td class="f-w-600" >' . '' . '</td>
                              <td class="f-w-600" > ' . '' . '</td>
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

                                        // $time = strtotime($deposit['_date_created']);

                                        $newformat = normal_date($deposit['_date_created']);
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
                                            // if ($deposit['pay_method'] != 'cash' && $deposit['pay_method'] != 'cheque') {
                                            $val = $val - ($deposit['_amount'] + @$deposit['loan_interest']);
                                            $dtotal = $dtotal + ($deposit['_amount'] + @$deposit['loan_interest']);
                                            // }

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
                       <td>' . $newformat . '</td>
                            <td>' . $newformat . '</td>
                            <td class="no_print clickable_ref_no" ref-no="' . $deposit['ref'] . ' " tid="' . $deposit['_did'] . '">' . $deposit['ref'] . '</td>
                           
                            <td>' . $deposit['_reason'] . '</td>
                            <td>' . $deposit['cheque_no']  . '</td>
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

        </tbody>
    </table>
    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> Statement Summary </strong> </td>
        </tr>
    </table>
    <table class="main-header">
        <tr>
            <td>Total Debit Transactions : </td>
            <td><?php echo number_format($ccount); ?></td>
            <td>Total Debit Amount : </td>
            <td> <?php echo number_format($dtotal); ?></td>

        </tr>
        <tr>
            <td>Total Credit Transactions : </td>
            <td><?php echo number_format($dcount); ?></td>
            <td>Total Credit Amount : </td>
            <td> <?php echo number_format($ctotal); ?></td>

        </tr>
    </table>



</section>