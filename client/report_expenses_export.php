<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');

$ReportService = new ReportService();
$report_reponse = $ReportService->generateReport($_REQUEST);
$records = @$report_reponse['data'];
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Expenses Report: </strong> </td>
        </tr>
    </table>

    <table>
        <?php if (@$_REQUEST['branchName']) : ?>
            <tr>
                <td width="18%"> Branch:</td>
                <td> <strong> <?= $_REQUEST['branchName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['journalAccount']) : ?>
            <tr>
                <td width="18%"> Journal Account:</td>
                <td> <strong> <?= $_REQUEST['journalAccount']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['staffName']) : ?>
            <tr>
                <td width="18%"> Authorized By:</td>
                <td> <strong> <?= $_REQUEST['staffName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['transaction_start_date'] && @$_REQUEST['transaction_end_date']) : ?>
            <tr>
                <td colspan="2">
                    <div>
                        From: <strong> <?= normal_date($_REQUEST['transaction_start_date']) ?> </strong>
                        To: <strong> <?= normal_date($_REQUEST['transaction_end_date']) ?> </strong>
                    </div>
                </td>
            </tr>
        <?php endif ?>
    </table>

    <table class="report_table">
        <thead>
            <th>REF</th>
            <th>Amount</th>
            <th>Narration</th>
            <th>Mode of Payment</th>
            <th>Authorized By</th>
            <th>Branch</th>
            <th>Journal Account</th>
            <th>Trxn date</th>
        </thead>
        <tbody>

            <?php
            $total_amount = 0;
            foreach ($records as $record) {
                $ref =    $record['t_type'] . '-ref-' . $record['pay_method'] . '-' . $record['transaction_id'] . '-' . $record['_authorizedby'];
            ?>
                <tr>
                    <td class="no_print clickable_ref_no" ref-no="<?= @$ref ?> " tid="<?= @$record['transaction_id'] ?>"><?= @$ref ?></td>
                    <td> <?= number_format(@$record['amount']) ?> </td>
                    <td> <?= @$record['transaction_description'] ?> </td>
                    <td> <?= @$record['pay_method'] ?> </td>
                    <td> <?= @$record['authorized_by_names'] ?> </td>
                    <td> <?= @$record['branch_name'] ?> </td>
                    <td> <?= @$record['journal_account'] ?> </td>
                    <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>
                </tr>
            <?php
                $total_amount += (int) $record['amount'];
            } ?>

            <tr>
                <th>Total </th>
                <th> <?= number_format($total_amount) ?> </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tbody>
    </table>
</section>