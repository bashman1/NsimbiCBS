<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');

$response = new Response();
$ReportService = new ReportService();
$report_reponse = $ReportService->generateBalanceSheet($_REQUEST);
$records = @$report_reponse['data'] ?? [];
$sub_report_reponse = $ReportService->generateBalanceSheetSubAccounts($_REQUEST);
$sub_records = @$sub_report_reponse['data'] ?? [];

$assets_accounts = @$records['assets'] ?? [];
$liabilites_accounts = @$records['liabilities'] ?? [];
$capital_accounts = @$records['capital'] ?? [];


$capital_sub_accounts = @$sub_records['capital'] ?? [];

$liabilities_sub_accounts = @$sub_records['liabilities'] ?? [];

$assets_sub_accounts = @$sub_records['assets'] ?? [];
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Balance sheet </strong> </td>
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
                <th colspan="2">ASSETS</th>
            </tr>
            <?php
            $i = 1;
            $total_assets = 0;
            $ass_bal_value = 0;
            foreach ($assets_accounts as $record) {
                $ac_bal = 0;
                if (is_null($record['main_account_id']) || $record['subs'] > 0) {
                    if (@$record['is_cash_account'] > 0 || @$subs['reserve_account']) {
                        $ac_bal = $record['balance'];
                        $ass_bal_value = $ac_bal;
                    } else {
                        if ($record['lpid']) {
                            $ass_bal_value = $response->getAccBalValue($record['lpid'], $record['account_id'], $record['branchId']);
                        } else {
                            $ass_bal_value = $ac_bal;
                        }
                    }

                    if ($record['account_id'] == 'b50c469f-da6a-4f0d-9c19-3504aaf73f2f') {
                        $ac_bal = 0;
                        $ass_bal_value = $ac_bal;
                    }
            ?>
                    <tr style="font-weight: bolder;">
                        <td> <?= $record['account_code_used'] ?? ''; ?></td>
                        <td> <?= $record['account_name'] ?> </td>

                        <td style="text-align:right;" >
                            <?= number_format($ass_bal_value) ?>
                        </td>

                    </tr>
                    <?php
                    $total_assets += $ass_bal_value;
                }
                foreach ($assets_sub_accounts as $subs) {
                    $ac_bal1 = 0;
                    $ass_bal_valuex = 0;
                    if ($subs['main_account_id'] == $record['account_id'] && $subs['subs'] == 0) {
                        if ($subs['is_cash_account'] > 0  || $subs['reserve_account']) {
                            $ac_bal1 = $subs['balance'];
                            $ass_bal_valuex = $ac_bal1;
                        } else {
                            $ac_bal1 = $subs['total_amount'];

                            if ($subs['lpid']) {
                                $ass_bal_valuex = $response->getAccBalValue($subs['lpid'], $subs['account_id'], $subs['branchId']);
                            } else {
                                $ass_bal_valuex = $subs['total_amount'];
                            }
                        }

                        if ($subs['account_id'] == 'b50c469f-da6a-4f0d-9c19-3504aaf73f2f') {
                            $ac_bal1 = 0;
                            $ass_bal_valuex = $ac_bal1;
                        }
                    ?>

                        <tr>
                            <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                            <td> <?= $subs['account_name'] ?> </td>

                            <td style="text-align:right;" >
                                <?= number_format($ass_bal_valuex) ?>
                            </td>
                        </tr>

            <?php
                        $total_assets += $ass_bal_valuex;
                    }
                }
            } ?>
            <tr>
                <th colspan="2">TOTAL</th>
                <th><?= number_format($total_assets) ?></th>
            </tr>
            <tr>
                <th>#</th>
                <th colspan="2">LIABILITIES</th>
            </tr>

            <?php
            $i = 1;
            $total_liabilites = 0;
            $lia_bal_value = 0;
            foreach ($liabilites_accounts as $record) {
                if (is_null($record['main_account_id']) || $record['subs'] > 0) {
                    if ($record['said']) {
                        $lia_bal_value = $response->getAccBalValue($record['said'], $record['account_id'], $record['branchId'], 1);
                    } else {
                        $lia_bal_value = $record['total_amount'];
                    }
            ?>
                    <tr style="font-weight: bolder;">
                        <td> <?= $record['account_code_used'] ?? ''; ?></td>
                        <td> <?= $record['account_name'] ?> </td>

                        <td style="text-align:right;" >
                            <?= number_format($lia_bal_value) ?>
                        </td>
                    </tr>
                    <?php
                    $total_liabilites += $lia_bal_value;
                }
                $lia_bal_valuex = 0;
                foreach ($liabilities_sub_accounts as $subs) {
                    if ($subs['main_account_id'] == $record['account_id'] && $subs['subs'] == 0) {
                        if ($subs['said']) {
                            $lia_bal_valuex = $response->getAccBalValue($subs['said'], $subs['account_id'], $subs['branchId'], 1);
                        } else {
                            $lia_bal_valuex = $subs['total_amount'];
                        }
                    ?>

                        <tr>
                            <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                            <td> <?= $subs['account_name'] ?> </td>

                            <td style="text-align:right;" >
                                <?= number_format($lia_bal_valuex) ?>
                            </td>
                        </tr>

            <?php
                        $total_liabilites += $lia_bal_valuex;
                    }
                }
            } ?>
            <tr>
                <th colspan="2">TOTAL</th>
                <th><?= number_format($total_liabilites) ?></th>
            </tr>


            <tr>
                <th>#</th>
                <th colspan="2">CAPITAL</th>
            </tr>

            <?php
            $i = 1;
            $total_capital = 0;
            foreach ($capital_accounts as $record) {
                if (is_null($record['main_account_id']) || $record['subs'] > 0) {
            ?>
                    <tr style="font-weight: bolder;">
                        <td> <?= $record['account_code_used'] ?? ''; ?></td>
                        <td> <?= $record['account_name'] ?> </td>

                        <td style="text-align:right;" >
                            <?= number_format($record['total_amount']) ?>
                        </td>
                    </tr>
                    <?php
                    $total_capital += $record['total_amount'];
                }
                foreach ($capital_sub_accounts as $subs) {
                    if ($subs['main_account_id'] == $record['account_id'] && $subs['subs'] == 0) { ?>

                        <tr>
                            <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                            <td> <?= $subs['account_name'] ?> </td>

                            <td style="text-align:right;" >
                                <?= number_format($subs['total_amount']) ?>
                            </td>
                        </tr>

            <?php
                        $total_capital += $subs['total_amount'];
                    }
                }
            } ?>
            <tr>
                <th colspan="2">TOTAL</th>
                <th><?= number_format($total_capital) ?></th>
            </tr>

            <tr>
                <th colspan="2">TOTAL ASSETS</th>
                <th style="text-align:right;">
                    <?= number_format($total_assets + $total_capital + $total_liabilites) ?>
                </th>
            </tr>

            <tr>
                <th colspan="2">TOTAL LIABILITIES/EQUITY + CURRENT PERIOD EARNINGS</th>
                <th style="text-align:right;">
                    <?= number_format($total_assets + $total_capital + $total_liabilites) ?>
                </th>
            </tr>



        </tbody>

    </table>
</section>