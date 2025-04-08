<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();
// $collates = $reponse->getLoanSchedule($_GET['id']);
$details = $reponse->getLoanDetails($_GET['id']);

$repays = $reponse->getLoanRepayments($_GET['id']);
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
            <td> <strong> Loan Statement </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>REPAYMENT DATE</th>
            <th>PRINCIPAL PAID</th>
            <th>INTEREST PAID</th>
            <th>PENALTY PAID</th>
            <th>TOTAL AMOUNT PAID</th>
            <th>OUSTANDING BALANCE</th>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $total_amount = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_penalty = 0;


            $total_ob = $details[0]['loan']['approvedamount'] + $details[0]['loan']['interest_amount'] + $details[0]['loan']['penalty_balance'] + $details[0]['loan']['int_waivered'];
            if ($repays != '') {

                foreach ($repays as $row) {
                    $total_amount = $total_amount + $row['amount'];
                    $usetotal = $total_ob - $total_amount;
                    $total_principal = $total_principal + $row['principal'];
                    $total_interest = $total_interest + $row['interest'];
                    $total_penalty = $total_penalty + $row['penalty'];

                    echo '
                                                    <tr style="text-align: center !important;">
                                        <td>' . $i . '
                                        </td>
                                        <td>' . $row['date_created'] . '</td>
                                       
                                          
                                        <td>' . number_format($row['principal'] ?? 0) . '</td>
                                        <td> ' . number_format($row['interest'] ?? 0) . '</td>
                                        <td> ' . number_format($row['penalty'] ?? 0) . '</td>
                                        <td>' . number_format($row['amount'] ?? 0) . '</td>
                                        <td>' . number_format($total_ob - $total_amount) . '</td>
                                   
                                        </tr>

                                        ';

                    $i++;
                }
            }

            ?>
        </tbody>
        <tfoot>

            <?php
            echo '
                <tr style="text-align: center !important;">
                <td></td>
                <td><strong>Total</strong></td>
                
                <td> <strong>' . number_format($total_principal) . '</strong></td>
                <td><strong> ' . number_format($total_interest) . '</strong></td>
                <td><strong> ' . number_format($total_penalty) . '</strong></td>
                <td> <strong>' . number_format($total_amount) . '</strong></td>
                <td><strong>' . number_format($total_ob - $total_amount) . '</strong></td>
                </tr>
                ';
            ?>

        </tfoot>
    </table>

</section>