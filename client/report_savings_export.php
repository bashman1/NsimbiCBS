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
            <td> <strong> Savings Report: </strong> </td>
        </tr>
    </table>

    <table>
        <?php if (@$_REQUEST['branchName']) : ?>
            <tr>
                <td width="18%"> Branch:</td>
                <td> <strong> <?= $_REQUEST['branchName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['staffName']) : ?>
            <tr>
                <td width="18%"> Authorized By:</td>
                <td> <strong> <?= $_REQUEST['staffName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['accountName']) : ?>
            <tr>
                <td width="18%"> Savings Account:</td>
                <td> <strong> <?= $_REQUEST['accountName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['transaction_type']) : ?>
            <tr>
                <td width="18%"> Transaction Type:</td>
                <td> <strong> <?= $_REQUEST['transaction_type'] == "D" ? 'Deposits' : 'Withdrawals'; ?> </strong> </td>
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
            <tr>
                <th rowspan="2">ID</th>
                <th rowspan="2">A/C N0</th>
                <th rowspan="2">Client Names</th>
                <th colspan="2">Amount</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Authorized By</th>
                <th rowspan="2">Branch</th>
                <th rowspan="2">Trxn date</th>
                <th rowspan="2">Mode of Payment</th>
            </tr>

            <tr>
                <th>DR</th>
                <th>CR</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_deposits = 0;
            $total_withdrawals = 0;
            foreach ($records as $record) {
                $is_deposit = false;
                $is_withdraw = false;

                if ($record['t_type'] == 'D') {
                    $is_deposit = true;
                } else {
                    $is_withdraw = true;
                }
            ?>
                <tr>
                    <td> <?= @$record['transaction_id'] ?> </td>
                    <td> <?= @$record['membership_no'] == 0 ? $record['client_id'] : @$record['membership_no']; ?> </td>
                    <td> <?= @$record['client_names'] ?> </td>
                    <td>
                        <?= $is_withdraw ? number_format(@$record['amount']) : '-' ?>
                    </td>
                    <td>
                        <?= $is_deposit ? number_format(@$record['amount']) : '-' ?>
                    </td>


                    <td> <?= @$record['transaction_description'] ?> </td>
                    <td> <?= @$record['authorized_by_names'] ?> </td>
                    <td> <?= @$record['branch_name'] ?> </td>
                    <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>
                    <td> <?= @$record['pay_method'] ?> </td>
                </tr>
            <?php
                if ($is_deposit) {
                    $total_deposits += (int) $record['amount'];
                } else {
                    $total_withdrawals += (int) $record['amount'];
                }
            } ?>

            <tr>
                <th colspan="3">Total </th>
                <th> <?= number_format($total_deposits) ?> </th>
                <th> <?= number_format($total_withdrawals) ?> </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tbody>
    </table>
</section>