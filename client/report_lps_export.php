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
            <td> <strong>
                    LOAN REPAYMENT REPORT </strong> </td>
        </tr>
    </table>

    <table>
        <?php if (@$_REQUEST['branchId']) : ?>
            <tr>
                <td width="18%"> Branch:</td>
                <td> <strong> <?= @$branch_name; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['actype']) : ?>
            <tr>
                <td width="18%"> Loan Product:</td>
                <td> <strong> <?= @$account_type_name; ?> </strong> </td>
            </tr>
        <?php endif ?>



        <?php if (@$_REQUEST['authorized_by_id']) : ?>
            <tr>
                <td width="18%"> Authorized by:</td>
                <td> <strong> <?= @$staff_name; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php
        //  if (@$_REQUEST['transaction_start_date'] && @$_REQUEST['transaction_end_date']) :
        ?>
        <tr>
            <td colspan="2">
                <div>
                    From: <strong> <?= normal_date($_REQUEST['transaction_start_date'] ?? date('Y-m-d')) ?> </strong>
                    To: <strong> <?= normal_date($_REQUEST['transaction_end_date'] ?? date('Y-m-d')) ?> </strong>
                </div>
            </td>
        </tr>
        <?php
        //  endif 
        ?>
    </table>

    <table class="report_table">
        <thead>
            <tr>
                <th rowspan="2">REF NO.</th>
                <th rowspan="2">A/C N0</th>
                <th rowspan="2">Client Names</th>
                <th colspan="3" style="text-align: center;">Amount</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Authorized By</th>
                <th rowspan="2">Branch</th>
                <th rowspan="2">Trxn date</th>
                <th rowspan="2">Mode of Payment</th>
            </tr>

            <tr>
                <th>Interest</th>
                <th>Principal</th>
                <th>Penalty</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_deposits = 0;
            $total_withdrawals = 0;
            $total_penalty = 0;
            foreach ($records as $record) {
            ?>
                <tr>
                    <td> <?= @$record['transaction_id'] ?> </td>
                    <td> <?= @$record['membership_no'] == 0 ? $record['client_id'] : @$record['membership_no']; ?> </td>
                    <td> <?= @$record['client_names'] ?> </td>
                    <td>
                        <?= number_format(@$record['loan_interest']) ?>
                    </td>
                    <td>
                        <?= number_format(@$record['amount']) ?>
                    </td>
                    <td>
                        <?= number_format($record['loan_penalty'] ?? 0) ?>
                    </td>

                    <td> <?= @$record['transaction_description'] ?> </td>
                    <td> <?= @$record['authorized_by_names'] ?> </td>
                    <td> <?= @$record['branch_name'] ?> </td>
                    <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>
                    <td> <?= @$record['pay_method'] ?> </td>
                </tr>
            <?php

                $total_deposits += (int) $record['loan_interest'];

                $total_withdrawals += (int) $record['amount'];
                $total_penalty += (int) $record['loan_penalty'] ?? 0;
            } ?>

            <tr>
                <th colspan="3">Total </th>
                <th> <?= number_format($total_deposits) ?> </th>
                <th> <?= number_format($total_withdrawals) ?> </th>
                <th> <?= number_format($total_penalty) ?> </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tbody>
    </table>

</section>