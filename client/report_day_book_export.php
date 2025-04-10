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
            <td> <strong> Day Book: </strong> </td>
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

        <?php if (@$_REQUEST['transaction_type']) :
            $tt = '';
            if (@$_REQUEST['transaction_type'] == "D") {
                $tt = 'Deposit';
            } else if (@$_REQUEST['transaction_type'] == "W") {
                $tt = 'Withdraw';
            } else if (@$_REQUEST['transaction_type'] == "L") {
                $tt = 'Loan Repayment';
            } else if (@$_REQUEST['transaction_type'] == "ASS") {
                $tt = 'Asset Entry';
            } else if (@$_REQUEST['transaction_type'] == "LIA") {
                $tt = 'Liability Entry';
            } else if (@$_REQUEST['transaction_type'] == "E") {
                $tt = 'Expense Entry';
            } else if (@$_REQUEST['transaction_type'] == "I") {
                $tt = 'Income Entry';
            } else if (@$_REQUEST['transaction_type'] == "CAP") {
                $tt = 'Capital Entry';
            } else if (@$_REQUEST['transaction_type'] == "WLI") {
                $tt = 'Interest Waived';
            } else if (@$_REQUEST['transaction_type'] == "WLP") {
                $tt = 'Penalty Waives';
            }
        ?>
            <tr>
                <td width="18%"> Transaction Type:</td>
                <td> <strong> <?= @$tt; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['transaction_start_date'] && @$_REQUEST['transaction_end_date']) : ?>
            <tr>
                <td colspan="2">
                    <div>
                        From: <strong> <?= normal_date($_REQUEST['transaction_start_date'] ?? date('Y-m-d')) ?> </strong>
                        To: <strong> <?= normal_date($_REQUEST['transaction_end_date'] ?? date('Y-m-d')) ?> </strong>
                    </div>
                </td>
            </tr>
        <?php endif ?>
    </table>

    <table class="report_table">
        <thead>
            <tr>
                <th rowspan="2">ID</th>
                <th rowspan="2">Trxn Type</th>
                <th rowspan="2">A/C </th>
                <th rowspan="2">A/C Names</th>
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

                if ($record['t_type'] == 'E' || $record['t_type'] == 'ASS' || $record['t_type'] == 'WLP' || $record['t_type'] == 'WLI' || $record['t_type'] == 'W') {
                    $is_deposit = true;
                } else {
                    $is_withdraw = true;
                }

                $ttn = '';
                if (@$record['t_type'] == "D") {
                    $ttn = 'Deposits';
                } else if (@$record['t_type'] == "W") {
                    $ttn = 'Withdraws';
                } else if (@$record['t_type'] == "L") {
                    $ttn = 'Loan Repayments';
                } else if (@$record['t_type'] == "ASS") {
                    $ttn = 'Assets Registered';
                } else if (@$record['t_type'] == "LIA") {
                    $ttn = 'Liabilities Registered';
                } else if (@$record['t_type'] == "E") {
                    $ttn = 'Expenses Registered';
                } else if (@$record['t_type'] == "I") {
                    $ttn = 'Income Registered';
                } else if (@$record['t_type'] == "CAP") {
                    $ttn = 'Capital Registered';
                } else if (@$record['t_type'] == "WLI") {
                    $ttn = 'Interest Waived';
                } else if (@$record['t_type'] == "WLP") {
                    $ttn = 'Penalty Waives';
                }
            ?>
                <tr>
                    <td> <?= @$record['transaction_id'] ?> </td>
                    <td> <?= @$ttn ?> </td>
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
                <th colspan="4">Total </th>
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