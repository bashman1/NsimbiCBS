<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();
$details = $reponse->getLoanDetails($_GET['id']);

$cott = $reponse->getLoanCollaterals($_GET['id']);
$gats = $reponse->getLoanGuarantors($_GET['id']);


$ftype = '';
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
            <td><?= strtoupper($details[0]['client']['firstName'] . ' ' . $details[0]['client']['lastName'])  ?></td>
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
            <td>A/C Balance: </td>
            <td>UGX <?= number_format($details[0]['client']['acc_balance']) ?></td>
            <td>Loan No.: </td>
            <td><?php echo $details[0]['loan']['loan_no']; ?></td>

        </tr>

        <tr>
            <td>Requested Loan Amount: </td>
            <td>UGX <?php echo number_format($details[0]['loan']['principal']); ?></td>
            <td>Expected Interest: </td>
            <td>UGX <?= number_format($details[0]['loan']['interest_amount'])  ?></td>

        </tr>
        <tr>
            <td>Requested Duration: </td>
            <td><?php echo $dur; ?></td>
            <td>Date of Application: </td>
            <td><?= normal_date($details[0]['loan']['date_created']) ?></td>

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
            <td>Proposed Disbursement Date: </td>
            <td><?php echo normal_date($details[0]['loan']['requesteddisbursementdate']) ?> </td>

        </tr>

    </table>
    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> Collaterals </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>NAME</th>
            <th>TYPE</th>
            <th>MARKET VALUE</th>
            <th>FORCED SALE VALUE</th>
            <th>REGISTERED BY</th>
        </thead>
        <tbody>
            <?php
            if ($cott != '') {

                foreach ($cott as $row) { ?>
                    <tr>
                        <td>
                            <?= $row['_cid'] ?>
                        </td>
                        <td><?= $row['_collateral'] ?></td>
                        <td><?= $row['_catname']   ?></td>
                        <td><?= number_format($row['_mvalue']) ?></td>
                        <td><?= number_format($row['_fvalue'])  ?></td>
                        <td><?= $row['_receivedby'] ?></td>
                    </tr>

                <?php

                } ?>

            <?php
            } ?>
        </tbody>
    </table>

    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> Guarantors </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>GUARANTOR'S A/C</th>
            <th>GUARANTOR'S Names</th>
            <th>A/C BALANCE</th>
        </thead>
        <tbody>
            <?php
            if ($gats != '') {

                foreach ($gats as $row) { ?>
                    <tr>
                        <td>
                            <?= @$row['gid'] ?>
                        </td>
                        <td><?= @$row['membership_no']  ?></td>
                        <td><?= @$row['name']   ?></td>
                        <td><?= number_format($row['acc_balance'] ?? 0) ?></td>
                    </tr>

                <?php

                } ?>

            <?php
            } ?>
        </tbody>
    </table>
    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> APPROVED SUMMARY </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>PARTICULARS</th>
            <th>APPROVED</th>
            <th>COMMENTS</th>
        </thead>
        <tbody>

            <tr>
                <td>AMOUNT</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>INTEREST RATE</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>DURATION</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>FREQUENCY</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>DISBURSEMENT DATE</td>
                <td></td>
                <td></td>
            </tr>




        </tbody>
    </table>

    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> COMMITTEE MEMBERS PRESENT IN THE
                    MEETING </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>NAME</th>
            <th>COMMENTS</th>
            <th>SIGNATURE</th>
        </thead>
        <tbody>
            <?php
            $i = 1;

            while ($i <= 10) { ?>
                <tr>
                    <td >
                        <?= $i ?>.
                    </td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                </tr>
               

            <?php
                $i++;
            } ?>


        </tbody>
    </table>

</section>