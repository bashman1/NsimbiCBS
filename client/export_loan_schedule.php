<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();
$collates = $reponse->getLoanSchedule($_GET['id']);
$details = $reponse->getLoanDetails($_GET['id']);
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
            <td>UGX <?= number_format($details[0]['loan']['interest_amount'])  ?></td>

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
            <td>Disbursement Date: </td>
            <td><?php echo normal_date($details[0]['loan']['requesteddisbursementdate']); ?></td>
            <td>Mode of Disbursement: </td>
            <td><?php echo strtoupper($details[0]['loan']['mode_of_disbursement'] ?? 'SAVINGS'); ?></td>

        </tr>

    </table>
    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> Loan Schedule </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>DUE DATE</th>
            <th>PRINCIPAL DUE</th>
            <th>INTEREST DUE</th>
            <th>TOTAL AMOUNT DUE</th>
            <th>OUSTANDING BALANCE</th>
        </thead>
        <tbody>
            <?php
            if ($collates != '') {
                $count = 1;
                $princ = 0;
                $int = 0;
                $bal = 0;
                foreach ($collates as $row) { ?>
                    <tr>
                        <td>
                            <?= $count++; ?>
                        </td>
                        <td><?= normal_date($row['date_of_payment']); ?></td>
                        <td><?= number_format($row['principal']) ?></td>
                        <td><?= number_format($row['interest'])  ?></td>
                        <td><?= number_format($row['amount']) ?></td>
                        <td><?= number_format($row['balance']) ?></td>
                    </tr>

                <?php
                    $princ = $princ + $row['principal'];
                    $int = $int + $row['interest'];
                    $bal = $bal + $row['amount'];
                } ?>
                <tr>
                    <td colspan="2"><strong>TOTAL:</strong></td>
                    <td><strong><?= number_format($princ) ?></strong></td>
                    <td><strong><?= number_format($int) ?></strong></td>
                    <td><strong><?= number_format($bal) ?></strong></td>
                    <td></td>


                </tr>
            <?php
            } ?>
        </tbody>
    </table>

</section>