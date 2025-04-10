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
            <td> <strong> Dormant Accounts Report: </strong> </td>
        </tr>
    </table>

    <table>
        <tr>
            <td>
                <?php if (@$_REQUEST['branchName']) : ?>
                    <div> Branch: <strong> <?= $_REQUEST['branchName']; ?> </strong> </div>
                <?php endif ?>

                <?php if (@$_REQUEST['accountName']) : ?>
                    <div> Savings Account: <strong> <?= $_REQUEST['accountName']; ?> </strong> </div>
                <?php endif ?>

                <?php if (@$_REQUEST['gender']) : ?>
                    <div> Gender: <strong> <?= $_REQUEST['gender'] ?> </strong> </div>
                <?php endif ?>

                <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
                    <div>
                        From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                        To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                    </div>
                <?php endif ?>
            </td>
        </tr>
    </table>

    <div style="font-size:14px; margin-bottom:5px;">
        Total Members: <?= number_format(count($members)) ?>
    </div>

    <table class="report_table">
        <thead>
            <tr>
                <th>ID</th>
                <th style="width:8%">A/C N0</th>
                <th style="width:18%">Names</th>
                <th style="width:15%; text-align:center;">Membership Fee</th>
                <th style="width:14%;text-align:center;">Savings Balance</th>
                <th style="width:11%;text-align:center;">Total Shares</th>
                <th style="width:12%;;text-align:center;">No. of Shares</th>
                <th style="width:15%">Registration Date</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $total_account_balance = 0;
            $total_shares = 0;
            $total_number_shares = 0;
            $total_membership_fee = 0;
            foreach ($members as $member) { ?>
                <tr>
                    <td> <?= @$member['user_id'] ?>. </td>
                    <td class="text-center"> <?= @$member['membership_no'] ?> </td>
                    <td>
                        <?= strtoupper(@$member['client_names']) ?>
                    </td>
                    <td style=" text-align:center;"> <?= number_format(@$member['membership_fee']); ?> </td>
                    <td style=" text-align:center;"> <?= number_format(@$member['acc_balance']); ?> </td>
                    <td style="text-align:center;"> 0 </td>
                    <td style="text-align:center;"> 0 </td>
                    <td> <?= normal_date_short(@$member['member_created_at']) ?> </td>
                </tr>
            <?php
                $total_account_balance += (int) @$member['acc_balance'];
                $total_membership_fee += (int) @$member['membership_fee'];
            } ?>

            <tr>
                <th colspan="3">Totals </th>
                <th> <?= number_format($total_membership_fee) ?> </th>
                <th> <?= number_format($total_account_balance) ?> </th>
                <th> <?= number_format($total_shares) ?> </th>
                <th> <?= number_format($total_number_shares) ?> </th>
                <th></th>
            </tr>
        </tbody>
    </table>
</section>