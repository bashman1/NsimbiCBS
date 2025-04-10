<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');
$response = new Response();
$ReportService = new ReportService();
$report_reponse = $ReportService->generateIncomeStatementReport($_REQUEST);
$records = @$report_reponse['data'];


$sub_report_reponse = $ReportService->generateIncomeStatementReportSubAccounts($_REQUEST);
$sub_records = @$sub_report_reponse['data'] ?? [];

$income_accounts = @$records['income'];
$expenses_accounts = @$records['expenses'];


$income_sub_accounts = @$sub_records['income'] ?? [];

$expenses_sub_accounts = @$sub_records['expenses'] ?? [];
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Income Statement </strong> </td>
        </tr>
    </table>

    <table>
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
        <tbody>
            <tr>
                <th>#</th>
                <th colspan="2">INCOMES</th>
            </tr>
            <?php
            $i = 1;
            $total_income = 0;
            $acc_bal_value = 0;
            foreach ($income_accounts as $income) {
                if (is_null($income['main_account_id']) || @$income['subs'] > 0) {
                    $acc_bal_value = $response->getAccBalValue($income['lpid'], $income['account_id'], $income['branchId']);
            ?>
                    <tr style="font-weight: bolder;">
                        <td> <?= $income['account_code_used'] ?? ''; ?></td>
                        <td> <?= $income['account_name'] ?> </td>

                        <td style="text-align:right;" class="text-primary">
                            <?= number_format($acc_bal_value) ?>
                        </td>
                    </tr>
                    <?php
                    $total_income += $acc_bal_value;
                }
                $acc_bal_valuen = 0;
                foreach ($income_sub_accounts as $subs) {
                    if ($subs['main_account_id'] == $income['account_id'] && $subs['subs'] == 0) {
                        $acc_bal_valuen = $response->getAccBalValue($subs['lpid'], $subs['account_id'], $subs['branchId']);
                    ?>

                        <tr>
                            <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                            <td> <?= $subs['account_name'] ?> </td>

                            <td style="text-align:right;" class="text-primary">
                                <?= number_format($acc_bal_valuen) ?>
                            </td>
                        </tr>

            <?php
                        $total_income += $acc_bal_valuen;
                    }
                }
            } ?>

            <tr class="fw-bold">
                <td colspan="2">Total Income</td>
                <td style="text-align:right;">
                    <?= number_format($total_income) ?>
                </td>
            </tr>

            <tr>
                <th>#</th>
                <th colspan="2">EXPENSES</th>
            </tr>

            <?php
            $i = 1;
            $total_expense = 0;
            foreach ($expenses_accounts as $expense) {
                if (is_null($expense['main_account_id']) || $expense['subs'] > 0) {

            ?>
                    <tr style="font-weight: bolder;">
                        <td> <?= $expense['account_code_used'] ?? ''; ?></td>
                        <td> <?= $expense['account_name'] ?> </td>

                        <td style="text-align:right;" class="text-primary">
                            <?= number_format($expense['total_amount']) ?>
                        </td>
                    </tr>
                    <?php

                }
                foreach ($expenses_sub_accounts as $subs) {
                    if ($subs['main_account_id'] == $expense['account_id'] && $subs['subs'] == 0) {

                    ?>

                        <tr>
                            <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                            <td> <?= $subs['account_name'] ?> </td>

                            <td style="text-align:right;" class="text-primary">
                                <?= number_format($subs['total_amount']) ?>
                            </td>
                        </tr>

            <?php
                        $total_expense += $subs['total_amount'];
                    }
                }
                $total_expense += $expense['total_amount'];
            }
            ?>
            <tr class="fw-bold">
                <td colspan="2">Total Expense</td>
                <td style="text-align:right;">
                    <?= number_format($total_expense) ?>
                </td>
            </tr>

            <tr>
                <th colspan="2">Net Income</th>
                <th style="text-align:right;">
                    <?= number_format($total_income - $total_expense) ?>
                </th>
            </tr>

        </tbody>

    </table>
</section>