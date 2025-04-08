<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();
// $collates = $reponse->getLoanSchedule($_GET['id']);
$details = $reponse->getLoanDetails($_GET['id']);

$repays = $reponse->getLoanSchedule($_GET['id']);
// $loan_details = @$details[0]['loan'];

$freq = '';
$dur = '';
?>

<?php
require_once('includes/report_header.php');
?>

<?php
if ($details[0]['loan']['repay_cycle_id'] == 1) {
    $ftype = 'DAYS';
} else if ($details[0]['loan']['repay_cycle_id']  == 2) {
    $ftype = 'WEEKS';
} else if ($details[0]['loan']['repay_cycle_id']  == 3) {
    $ftype = 'MONTHS';
} else if ($details[0]['loan']['repay_cycle_id']  == 4) {
    $ftype = 'DAYS';
} else if ($details[0]['loan']['repay_cycle_id']  == 5) {
    $ftype = 'YEARS';
}

$dur = number_format(@$details[0]['loan']['approved_loan_duration']) . ' ' . $ftype;
?>

<?php

if ($details[0]['loan']['repay_cycle_id'] == 1) {
    $freq = '  DAILY';
} else if ($details[0]['loan']['repay_cycle_id'] == 2) {
    $freq = '  WEEKLY';
} else if ($details[0]['loan']['repay_cycle_id'] == 3) {
    $freq = '  MONTHLY';
} else if ($details[0]['loan']['repay_cycle_id'] == 4) {
    $freq =  '  DAILY';
} else if ($details[0]['loan']['repay_cycle_id'] == 5) {
    $freq = '  ANNUALLY';
}

if (@$details[0]['loan']['arrearsbegindate'] && (@$details[0]['loan']['principal_arrears'] + @$details[0]['loan']['interest_arrears']) > 0) {
    $now = time(); // or your date as well
    $your_date = strtotime(@$details[0]['loan']['arrearsbegindate']);
    $datediff = $now - $your_date;

    $days_in_arrears =  round($datediff / (60 * 60 * 24));
} else {
    $days_in_arrears = 0;
}

?>
<section class="report-section">
    <table class="main-header">
        <tr>
            <td> <strong> Loan Summary </strong> </td>
        </tr>
        <tr>
            <td>Client Name: </td>
            <td><?= $details[0]['client']['firstName'] . ' ' . $details[0]['client']['lastName']  ?></td>
            <td>A/C No.: </td>
            <td><?= $details[0]['client']['membership_no']; ?></td>
        </tr>
        <tr>
            <td>Contacts: </td>
            <td><?= @$details[0]['client']['primaryCellPhone'] . ' / ' . @$details[0]['client']['secondaryCellPhone']; ?></td>
            <td>Physical Address: </td>
            <td><?= @$details[0]['client']['addressLine1'] . ' , ' . @$details[0]['client']['addressLine2'] . ' , ' . @$details[0]['client']['country'] ?></td>
        </tr>

        <tr>
            <td>Loan Amount: </td>
            <td>UGX <?php echo number_format($details[0]['loan']['principal']); ?></td>
            <td>Total Interest: </td>
            <td><?= number_format(($details[0]['loan']['interest_amount'] ?? 0) + ($details[0]['loan']['int_waivered'] ?? 0))  ?> Waived Interest: <?= number_format($details[0]['loan']['int_waivered'] ?? 0)  ?></td>

        </tr>
        <tr>
            <td>Disbursed Net Amount: </td>
            <td>UGX <?php echo number_format($details[0]['loan']['principal'] - ($details[0]['loan']['deductions'] ?? 0)); ?> ( <?= strtoupper(@$details[0]['loan']['meth'] ?? 'CASH') ?> )</td>
            <td>Total Deductions: </td>
            <td>UGX <?= number_format($details[0]['loan']['deductions'] ?? 0)  ?></td>

        </tr>
        <tr>
            <td>Duration: </td>
            <td><?php echo $dur; ?></td>
            <td>Loan No.: </td>
            <td><?php echo $details[0]['loan']['loan_no']; ?></td>

        </tr>
        <tr>
            <td>Loan Product: </td>
            <td><?php echo $details[0]['product']['type_name']; ?></td>
            <td>Interest Rate: </td>
            <td><?php echo $details[0]['loan']['monthly_interest_rate'] . '% PER ANNUM'; ?></td>

        </tr>
        <tr>
            <td>Frequency: </td>
            <td><?php echo $freq ?></td>
            <td>Grace Period: </td>
            <td><?php echo $details[0]['loan']['num_grace_periods'] ?? 0; ?> Days</td>

        </tr>
        <tr>
            <td>Application Date: </td>
            <td><?php echo normal_date($details[0]['loan']['application_date']); ?></td>
            <td>Approval Date: </td>
            <td><?php echo normal_date($details[0]['loan']['date_disbursed']); ?></td>

        </tr>
        <tr>
            <td>Disbursement Date: </td>
            <td><?php echo normal_date($details[0]['loan']['date_disbursed']); ?></td>
            <td>Maturity Date: </td>
            <td><?php echo normal_date(date('Y-m-d', strtotime("+" . $dur, strtotime(date('Y-m-d', strtotime("+ 1" . $ftype, strtotime($details[0]['loan']['requesteddisbursementdate']))))))); ?></td>

        </tr>

        <tr>
            <td>Next Due Date: </td>
            <td><?php echo normal_date($details[0]['loan']['date_of_next_pay']); ?></td>
            <td>Credit Officer: </td>
            <td><?php echo @$details[0]['staff']['firstName'] . ' ' . @$details[0]['staff']['lastName'] . ' - ' . @$details[0]['staff']['positionTitle']; ?></td>

        </tr>
        <tr>
            <td>Amount Due: </td>
            <td><?php echo number_format($details[0]['loan']['principal_due'] + $details[0]['loan']['interest_due']); ?></td>
            <td>Amount in Arrears: </td>
            <td><strong><span style="color: red;"><?php echo number_format($details[0]['loan']['principal_arrears'] + $details[0]['loan']['interest_arrears']); ?></span></strong></td>

        </tr>
        <tr>

            <td>Days in Arrears: </td>
            <td><strong><span style="color: red;"><?= @$days_in_arrears ?? 0 ?></span></strong></td>
            <td>Penalty Balance: </td>
            <td><strong><span style="color: red;"><?php echo number_format($details[0]['loan']['penalty_balance'] ?? 0); ?></span></strong></td>

        </tr>
    </table>
    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> Loan Repayment Schedule against Payments made </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>EXPECTED DATE:</th>
            <th>OPENING BAL:</th>
            <th>PRINCIPAL EXPECTED:</th>
            <th>INTEREST EXPECTED:</th>
            <th>TOTAL EXPECTED:</th>
            <th>EXPECTED CLOSING BAL:</th>
            <th>WAIVERED INT:</th>
            <th>PRINCIPAL PAID:</th>
            <th>PRINCIPAL BAL:</th>
            <th>INTEREST PAID:</th>
            <th>INTEREST BAL:</th>
            <th>AMOUNT PAID:</th>
            <th>REMAINING BAL:</th>

        </thead>
        <tbody>
            <?php
            $i = 1;
            $total_amount = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_penalty = 0;
            $total_bal = 0;
            $total_princ_bal = 0;
            $total_int_bal = 0;

            $total_princ_exp = 0;
            $total_int_exp = 0;
            $total_amount_exp = 0;
            $total_waived_int = 0;


            $total_ob = $details[0]['loan']['approvedamount'] + $details[0]['loan']['interest_amount'] + $details[0]['loan']['penalty_balance'] + $details[0]['loan']['int_waivered'];

            if ($repays != '') {

                foreach ($repays as $row) {

                    $clear_st = $row['amount'] - ($row['interest_paid'] + $row['principal_paid'] + $row['interest_waivered']);
                    $vll = $clear_st > 0 ? number_format($clear_st) : '<span class="text-primary">CLEARED</span>';

                    echo '
                                    <tr style="text-align: center !important;">
                                        <td>' . $i . ' </td>
                                        <td>' . date('Y-m-d', strtotime($row['date_of_payment'])) . '</td>
                                       
                                        <td>' . number_format($total_ob - $total_amount_exp) . '</td>

                                        <td>' . number_format($row['principal'] ?? 0) . '</td>
                                        <td> ' . number_format($row['interest'] ?? 0) . '</td>
                                        <td> ' . number_format($row['amount'] ?? 0) . '</td>

                                        <td>' . number_format($row['balance'] ?? 0) . '</td>

                                         <td>' . number_format($row['interest_waivered'] ?? 0) . '</td>
                                        <td>' . number_format($row['principal_paid'] ?? 0) . '</td>
                                        <td>' . number_format($row['outstanding_principal'] ?? 0) . '</td>
                                        <td>' . number_format($row['interest_paid'] ?? 0) . '</td>
                                        <td>' . number_format($row['outstanding_interest'] ?? 0) . '</td>

                                        <td>' . number_format($row['interest_paid'] + $row['principal_paid'] + $row['interest_waivered']) . '</td>

                                        <td>' . $vll . '</td>
                                        </tr>

                                        ';


                    $total_bal = $total_bal + $clear_st;
                    // paid totals
                    $total_amount = $total_amount + $row['principal_paid'] + $row['interest_paid'] + $row['interest_waivered'];
                    $total_principal = $total_principal + $row['principal_paid'];
                    $total_interest = $total_interest + $row['interest_paid'];

                    $total_princ_bal = $total_princ_bal + $row['outstanding_principal'];
                    $total_int_bal = $total_int_bal + $row['outstanding_interest'];

                    // waived totals
                    $total_waived_int = $total_waived_int + ($row['interest_waivered'] ?? 0);

                    // expected totals
                    $total_amount_exp = $total_amount_exp + $row['amount'];
                    $total_princ_exp = $total_princ_exp + $row['principal'];
                    $total_int_exp = $total_int_exp + $row['interest'];
                    $i++;
                }
            }

            ?>
        </tbody>
        <tfoot>

            <?php
            echo '
                <tr style="text-align: center !important;">
               
                <td colspan="2"><strong>TOTALS</strong></td>
                <td></td>
                
                <td> <strong>' . number_format($total_princ_exp) . '</strong></td>
                <td><strong> ' . number_format($total_int_exp) . '</strong></td>
                <td><strong> ' . number_format($total_amount_exp) . '</strong></td>
                <td></td>
                <td> <strong>' . number_format($total_waived_int) . '</strong></td>
                <td><strong>' . number_format($total_principal) . '</strong></td>
                <td><strong>' . number_format($total_princ_bal) . '</strong></td>
                <td><strong>' . number_format($total_interest) . '</strong></td>
                <td><strong>' . number_format($total_int_bal) . '</strong></td>
                <td><strong>' . number_format($total_amount) . '</strong></td>
                  <td><strong>' . number_format($total_bal) . '</strong></td>
                
                </tr>
                ';
            ?>

        </tfoot>
    </table>

</section>