<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');

$ReportService = new ReportService();
$report_reponse = $ReportService->generateReport($_REQUEST);
$members = @$report_reponse['data'];
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> <?= $_GET['rtt'] ?>: </strong> </td>
        </tr>
    </table>

    <table>
        <?php if (@$_REQUEST['branchName']) : ?>
            <tr>
                <td width="18%"> Branch:</td>
                <td> <strong> <?= $_REQUEST['branchName']; ?> </strong> </td>
            </tr>
        <?php endif ?>



        <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
            <tr>
                <td colspan="2">
                    <div>
                        Maturity Date From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                        To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                    </div>
                </td>
            </tr>
        <?php endif ?>
    </table>

    <table class="report_table">
        <thead>
            <tr>
                <th>#</th>
                <th>A/C N0 & Name</th>
                <th>Amount</th>
                <th>Interest Rate(%)</th>
                <th>WHT(%)</th>
                <th>Period</th>
                <th>Compounding Frequency</th>
                <th>Deposit Date</th>
                <th>Status</th>
                <th>Maturity Date</th>
                <th class="text-center" colspan="4">Amount Paid</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Principal</th>
                <th>Interest</th>
                <th>WHT</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $total_amount = 0;
            $total_princ = 0;
            $total_wht = 0;
            $total_int = 0;
            $count = 0;
            foreach ($members as $member) {

                $mno = $member['membership_no'] == 0 ? '' :  $member['membership_no'];
                $dtype = '';
                $dtype1 = '';
                if ($member['duration_type'] == 'y') {
                    $dtype = 'Years';
                } else if ($member['duration_type'] == 'm') {
                    $dtype = 'Months';
                } else if ($member['duration_type'] == 'd') {
                    $dtype = 'Days';
                }


                if ($member['compound_freq'] == 'y') {
                    $dtype1 = 'Annually';
                } else if ($member['compound_freq'] == 'm') {
                    $dtype1 = 'Monthly';
                } else if ($member['compound_freq'] == 'q') {
                    $dtype1 = 'Quarterly';
                } else if ($member['compound_freq'] == 'h') {
                    $dtype1 = 'Half Yearly';
                }

                $stt = '';
                if ($member['fd_status'] == 0) {
                    $currentDate = strtotime(date('Y-m-d'));
                    $startDate = strtotime(date('Y-m-d', strtotime($member['fd_maturity_date'])));


                    if ($startDate <= $currentDate) {
                        $stt = '<span class="text-danger">Due</span>';
                    } else {
                        $stt = '<span class="text-primary">Running</span>';
                    }
                } else {
                    $stt = '<span class="text-success">Closed</span>';
                }
                $princ_paid = 0;
                if ($member['fd_int_paid'] > 0) {
                    $princ_paid = $member['fd_amount'];
                }


            ?>
                <tr>
                    <td> <?= ++$count ?> </td>
                    <td><?= @$mno . ' : ' . @$member['client_names']; ?> </td>
                    <td> <?= number_format($member['fd_amount'] ?? 0); ?> </td>
                    <td> <?= number_format($member['int_rate'] ?? 0); ?> </td>
                    <td> <?= number_format($member['wht'] ?? 0); ?> </td>
                    <td> <?= number_format($member['fd_duration'] ?? 0) . ' ' . $dtype ?> </td>
                    <td> <?= $dtype1 ?> </td>
                    <td> <?= normal_date_short(@$member['fd_date']) ?> </td>
                    <td> <?= $stt ?> </td>
                    <td> <?= normal_date_short(@$member['fd_maturity_date']) ?> </td>
                    <td class="text-center"> <?= number_format(@$princ_paid) ?> </td>
                    <td class="text-center"> <?= number_format(@$member['fd_int_paid']) ?> </td>
                    <td class="text-center"> <?= number_format(@$member['wht_paid']) ?> </td>
                    <td class="text-center"> <?= number_format(@$member['fd_int_paid'] + @$member['wht_paid'] + $princ_paid) ?> </td>
                </tr>
            <?php
                $total_amount += (int) @$member['fd_amount'];
                $total_princ += (int) @$princ_paid;
                $total_int += (int) @$member['fd_int_paid'];
                $total_wht += (int) @$member['wht_paid'];
            } ?>
            <tr>
                <th colspan="10">Total Amount Fixed</th>
                <th class="text-center" colspan="4"> <?= number_format($total_amount) ?> </th>
            </tr>
            <tr>
                <th colspan="10">Total Principal Paid</th>
                <th class="text-center" colspan="4"> <?= number_format($total_princ) ?> </th>
            </tr>
            <tr>
                <th colspan="10">Total Interest Paid</th>
                <th class="text-center" colspan="4"> <?= number_format($total_int) ?> </th>
            </tr>
            <tr>
                <th colspan="10">Total WHT Collected</th>
                <th class="text-center" colspan="4"> <?= number_format($total_wht) ?> </th>
            </tr>
        </tbody>
    </table>
</section>