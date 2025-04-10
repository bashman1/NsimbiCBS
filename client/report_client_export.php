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
            <td> <strong> Client Schedule Report: </strong> </td>
        </tr>
    </table>

    <table>
        <?php if (@$_REQUEST['branchName']) : ?>
            <tr>
                <td width="18%"> Branch:</td>
                <td> <strong> <?= $_REQUEST['branchName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['accountName']) : ?>
            <tr>
                <td width="18%"> Savings Account:</td>
                <td> <strong> <?= $_REQUEST['accountName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['client_type']) : ?>
            <tr>
                <td width="18%"> Client Type:</td>
                <td> <strong> <?= strtoupper($_REQUEST['client_type']); ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['gender']) : ?>
            <tr>
                <td width="18%"> Gender:</td>
                <td> <strong> <?= $_REQUEST['gender']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
            <tr>
                <td colspan="2">
                    <div>
                        From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                        To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                    </div>
                </td>
            </tr>
        <?php endif ?>
    </table>

    <table class="report_table">
        <thead>
            <tr>
                <th style="width:1%;">ID</th>
                <th style="width:20%">A/C N0</th>
                <th style="width:30%">Names</th>
                <th style="width:15%">Registration Date</th>
                <th style="width:12%;text-align:center;">A/C Balance</th>
                <th style="width:12%;text-align:center;">No. of Loans</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $total_loans = 0;
            $total_bal = 0;
            foreach ($members as $member) { ?>
                <tr>
                    <td> <?= @$member['client_id'] ?>0 </td>
                    <td> <?= @$member['membership_no'] == 0 ? '-' : @$member['membership_no']; ?> </td>
                    <td> <?= @$member['client_names'] ?> </td>
                    <td> <?= normal_date_short(@$member['client_created_at']) ?> </td>
                    <td class="text-center"> <?= number_format(@$member['acc_balance']) ?> </td>
                    <td class="text-center"> <?= number_format(@$member['total_loans']) ?> </td>
                </tr>
            <?php
                $total_bal += (int) @$member['acc_balance'];
                $total_loans += (int) @$member['total_loans'];
            } ?>
            <tr>
                <th colspan="5">Total No. Of Loans </th>
                <th class="text-center"> <?= number_format($total_loans) ?> </th>
            </tr>
            <tr>
                <th colspan="5">Total A/C Balance </th>
                <th class="text-center"> <?= number_format($total_bal) ?> </th>
            </tr>
        </tbody>
    </table>
</section>