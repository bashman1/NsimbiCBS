<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_trial_balance')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php
// Set the default time zone to Kampala
date_default_timezone_set('Africa/Kampala');
$title = 'TRIAL BALANCE';
require_once('includes/response.php');
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = [];
$request_data['is_trial_balance'] = true;

if (@$_REQUEST['branch']) {
    $request_data['bid'] = @$_REQUEST['branch'];
} else {
    $request_data['bid'] = '';
}

$request_data['bankk'] = @$user[0]['bankId'] ?? '';
$request_data = array_merge($request_data, $_REQUEST);
$report_reponse = $ReportService->generateTrialBalance($request_data);
$records = @$report_reponse['data'] ?? [];

$sub_report_reponse = $ReportService->generateTrialBalanceSubAccounts($request_data);
$sub_records = @$sub_report_reponse['data'] ?? [];


$income_accounts = @$records['income'] ?? [];
$income_sub_accounts = @$sub_records['income'] ?? [];
$expenses_accounts = @$records['expenses'] ?? [];
$expenses_sub_accounts = @$sub_records['expenses'] ?? [];
$suspense_accounts = @$records['suspenses'] ?? [];
$suspense_sub_accounts = @$sub_records['suspenses'] ?? [];
$assets_accounts = @$records['assets'] ?? [];
$assets_sub_accounts = @$sub_records['assets'] ?? [];
$liabilites_accounts = @$records['liabilities'] ?? [];
$liabilities_sub_accounts = @$sub_records['liabilities'] ?? [];
$capital_accounts = @$records['capital'] ?? [];
$capital_sub_accounts = @$sub_records['capital'] ?? [];
$report_type = "Trial Balance";

?>
<?php require_once('includes/head_tag.php');
require_once('includes/reports_css.php');
?>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <?php include('includes/preloader.php'); ?>

    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php
        include('includes/nav_bar.php');
        include('includes/side_bar.php');
        ?>
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">

                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

                        <form class="ajax_results_form" method="get">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="branch" style="display: none;">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branch'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branch'] == $row['id'] ? "selected" : "";
                                                ?>
                                                        <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                            <?= $row['name'] ?>
                                                        </option>
                                                <?php }
                                                } ?>

                                            </select>
                                        <?php } ?>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" ` for="exampleInputEmail3"> Start
                                            Date *</label>
                                        <input type="date" name="transaction_start_date" class="form-control" name="from_date" value="<?= @$_REQUEST['transaction_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">End
                                            Date *</label>
                                        <input type="date" class="form-control" name="transaction_end_date" value="<?= @$_REQUEST['transaction_end_date'] ?>" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch Entries</button>
                                    </div>
                                </div>

                            </div>

                        </form>

                    </div>
                </div>


                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn"><i class="fa fa-arrow-left"></i> Back</a>
                            <?= $report_type ?>
                        </h4>

                        <?php if (count($records)) :
                            $request_string = '';
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>

                                <a class="btn btn-primary light btn-xs" onclick="PrintContent('exreportn')">
                                    <i class="fas fa-file-pdf"></i>Print
                                </a>
                                <a href="export_report.php?exportFile=report_trial_balance&<?= $request_string ?>" target="_blank" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-pdf"></i> Export to PDF
                                </a>


                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> <?= $report_type ?>: </strong> <?php if (@$_REQUEST['branch'] && @$_REQUEST['branch']) : ?> <strong> - <?= strtoupper($response->getBranchDetails($_REQUEST['branch'])['branch_name'] ?? '') ?> </strong>
                                    <?php endif ?> </td>
                            </tr>
                        </table>

                        <!-- <div class="mt-2 mb-2"> -->
                        <!-- A trial balance is a list of all the general ledger accounts (both revenue and capital) contained in the ledger of a business. This list will contain the name of each nominal ledger account and the value of that nominal ledger balance. Each nominal ledger account will hold either a debit balance or a credit balance. -->

                        <!-- </div> -->
                        <br />

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
                            <tr>
                                <td colspan="2">
                                    <div>
                                        Report Date: <strong> <?= date('Y-m-d H:i:s') ?> </strong>

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div>
                                        Generated By: <strong> <?= $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' - ' . $user[0]['positionTitle']; ?> </strong>

                                    </div>
                                </td>
                            </tr>

                        </table>
                        <br />
                        <table class="report_table" id="trial_bal">
                            <thead>
                                <tr>
                                    <th rowspan="2">Account Code</th>
                                    <th rowspan="2">Account Label</th>
                                    <th colspan="2" style="text-align: center;">Balance as at 01/01/2024</th>
                                    <th colspan="2" style="text-align: center;">Transactions</th>
                                    <th colspan="2" style="text-align: center;">Balance as at 26/06/2024</th>

                                </tr>
                                <tr>
                                    <th>Debit</th>
                                    <th>Credit</th>

                                    <th>Debit</th>
                                    <th>Credit</th>

                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <th>1</th>
                                    <th colspan="7">ASSETS</th>
                                </tr>
                                <?php
                                $i = 1;
                                $total_assets = 0;
                                $total_assets_opening = 0;
                                $total_assets_opening_cr = 0;
                                $total_assets_closing = 0;
                                $ass_bal_value = 0;
                                $ass_bal_value_cr = 0;
                                foreach ($assets_accounts as $record) {
                                    $ac_bal = 0;
                                    if (is_null($record['main_account_id']) || $record['subs'] > 0) {
                                        if (@$record['is_cash_account'] > 0 || @$subs['reserve_account'] || @$record['bank_account'] > 0) {
                                            $ac_bal = $record['balance'];
                                            $ass_bal_value = $ac_bal;
                                        } else {
                                            $ac_bal = $record['total_amount'];

                                            if ($record['lpid']) {
                                                $ass_bal_value = $response->getAccBalValue($record['lpid'], $record['account_id'], $record['branchId'], @$_REQUEST['transaction_start_date'], @$_REQUEST['transaction_end_date']);
                                                $ass_bal_value_cr = $response->getAccBalValue2($record['lpid'], $record['account_id'], $record['branchId']);
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
                                            <td style="text-align:right;">
                                                <?= number_format($record['bf_bal'] ?? 0); ?>
                                            </td>
                                            <td style="text-align:right;">
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($ass_bal_value) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <!-- <td style="text-align:right;"></td> -->
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($ass_bal_value + ($record['bf_bal'] ?? 0)) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_assets += $ass_bal_value;
                                        $total_assets_opening += $record['bf_bal'];
                                        $total_assets_opening_cr += $ass_bal_value_cr;
                                        $total_assets_closing += ($record['bf_bal'] + $ass_bal_value);
                                    }
                                    foreach ($assets_sub_accounts as $subs) {
                                        $ac_bal1 = 0;
                                        $ass_bal_valuex = 0;
                                        $ass_bal_valuex_cr = 0;
                                        $ass_bal_valuex_cr2 = 0;
                                        if ($subs['main_account_id'] == $record['account_id'] && $subs['subs'] == 0) {
                                            if ($subs['is_cash_account'] > 0  || $subs['reserve_account'] || $subs['bank_account'] > 0) {
                                                $ac_bal1 = $subs['balance'];
                                                $ass_bal_valuex = $ac_bal1;
                                            } else {
                                                $ac_bal1 = $subs['total_amount'];

                                                if ($subs['lpid']) {
                                                    $ass_bal_valuex = $response->getAccBalValue($subs['lpid'], $subs['account_id'], $subs['branchId'], @$_REQUEST['transaction_start_date'], @$_REQUEST['transaction_end_date']);
                                                    $ass_bal_valuex_cr2 = $response->getAccBalValue2($subs['lpid'], $subs['account_id'], $subs['branchId']);
                                                } else {
                                                    $ass_bal_valuex = $subs['total_amount'];
                                                }
                                                $ass_bal_valuex_cr = $subs['cr_bal'];
                                            }

                                            if ($subs['account_id'] == 'b50c469f-da6a-4f0d-9c19-3504aaf73f2f') {
                                                $ac_bal1 = 0;
                                                $ass_bal_valuex = $ac_bal1;
                                            }
                                        ?>

                                            <tr>
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>
                                                <td style="text-align:right;">
                                                    <?= number_format($subs['bf_bal'] ?? 0); ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?= number_format($ass_bal_valuex_cr); ?>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($ass_bal_valuex) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($ass_bal_valuex_cr2) ?></a>
                                                </td>
                                                <!-- <td style="text-align:right;"></td> -->
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($ass_bal_valuex + ($subs['bf_bal'] ?? 0)) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($ass_bal_valuex_cr) ?></a>
                                                </td>
                                            </tr>

                                <?php
                                            $total_assets += ($ass_bal_valuex - $ass_bal_valuex_cr);
                                            $total_assets_opening +=
                                                $subs['bf_bal'];
                                            $total_assets_opening_cr +=
                                                $ass_bal_valuex_cr;
                                            $total_assets_closing += ($ass_bal_valuex + $subs['bf_bal'] - $ass_bal_valuex_cr);
                                        }
                                    }
                                } ?>
                                <tr style="text-align:right;">
                                    <th colspan="2">Total for Assets</th>
                                    <!-- opening totals -->
                                    <th><?= number_format($total_assets_opening) ?></th>
                                    <th><?= number_format($total_assets_opening_cr) ?></th>

                                    <!-- trxn totals -->
                                    <th><?= number_format($total_assets) ?></th>
                                    <th><?= number_format(0.00) ?></th>

                                    <!-- closing totals -->
                                    <th><?= number_format($total_assets_closing - $total_assets_opening_cr) ?></th>
                                    <th><?= number_format(0.00) ?></th>
                                </tr>
                                <tr>
                                    <th>2</th>
                                    <th colspan="7">LIABILITIES</th>
                                </tr>

                                <?php
                                $i = 1;
                                $total_liabilites = 0;
                                $total_liabilites_opening = 0;
                                $total_liabilites_closing = 0;
                                $lia_bal_value = 0;
                                $lia_bal_value_dr = 0;
                                foreach ($liabilites_accounts as $record) {
                                    if (is_null($record['main_account_id']) || $record['subs'] > 0) {
                                        if ($record['said']) {
                                            // $lia_bal_value = $response->getAccBalValue($record['said'], $record['account_id'], $record['branchId'], 1);
                                            $lia_bal_value = $record['balance'];
                                        } else {
                                            $lia_bal_value = $record['balance'];
                                        }
                                        $lia_bal_value_dr = $record['dr_bal'];
                                ?>
                                        <tr style="font-weight: bolder;">
                                            <td> <?= $record['account_code_used'] ?? ''; ?></td>
                                            <td> <?= $record['account_name'] ?> </td>
                                            <td style="text-align:right;">
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td style="text-align:right;">
                                                <?= number_format($record['bf_bal']); ?>
                                            </td>
                                            <!-- <td style="text-align:right;"></td> -->
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($lia_bal_value_dr) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($lia_bal_value) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($lia_bal_value + $record['bf_bal'] - $lia_bal_value_dr) ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_liabilites += ($lia_bal_value - $lia_bal_value_dr);
                                        $total_liabilites_opening += $record['bf_bal'];
                                        $total_liabilites_closing += ($lia_bal_value + $record['bf_bal'] - $lia_bal_value_dr);
                                    }
                                    $lia_bal_valuex = 0;
                                    $lia_bal_valuex_dr = 0;
                                    foreach ($liabilities_sub_accounts as $subs) {
                                        if ($subs['main_account_id'] == $record['account_id'] && $subs['subs'] == 0) {

                                            if ($subs['said']) {
                                                // $lia_bal_valuex = $response->getAccBalValue($subs['said'], $subs['account_id'], $subs['branchId'], 1);
                                                $lia_bal_valuex = $subs['balance'];
                                            } else {
                                                $lia_bal_valuex = $subs['balance'];
                                            }
                                            $lia_bal_valuex_dr = $subs['dr_bal'];


                                        ?>

                                            <tr>
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?= number_format($subs['bf_bal']); ?>
                                                </td>
                                                <!-- <td style="text-align:right;"></td> -->
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($lia_bal_valuex_dr) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($lia_bal_valuex) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($lia_bal_valuex + $subs['bf_bal'] - $lia_bal_valuex_dr) ?></a>
                                                </td>
                                            </tr>

                                <?php
                                            $total_liabilites += ($lia_bal_valuex - $lia_bal_valuex_dr);
                                            $total_liabilites_opening +=
                                                $subs['bf_bal'];
                                            $total_liabilites_closing += ($lia_bal_valuex + $subs['bf_bal'] - $lia_bal_valuex_dr);
                                        }
                                    }
                                } ?>
                                <tr style="text-align:right;">


                                    <th colspan="2">Total for Liabilities</th>
                                    <!-- opening totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_liabilites_opening) ?></th>

                                    <!-- trxn totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_liabilites) ?></th>

                                    <!-- closing totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_liabilites_closing) ?></th>
                                </tr>

                                <tr>
                                    <th>3</th>
                                    <th colspan="7">CAPITAL</th>
                                </tr>

                                <?php
                                $i = 1;
                                $total_capital = 0;
                                $total_capital_opening = 0;
                                $total_capital_closing = 0;
                                foreach ($capital_accounts as $record) {
                                    if (is_null($record['main_account_id']) || $record['subs'] > 0) {

                                ?>
                                        <tr style="font-weight: bolder;">
                                            <td> <?= $record['account_code_used'] ?? ''; ?></td>
                                            <td> <?= $record['account_name'] ?> </td>
                                            <td style="text-align:right;">
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td style="text-align:right;">
                                                <?= number_format($record['bf_bal']); ?>
                                            </td>
                                            <!-- <td style="text-align:right;"></td> -->
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($record['total_amount']) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $record['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($record['total_amount'] + $record['bf_bal']) ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_capital += $record['total_amount'];
                                        $total_capital_opening += $record['bf_bal'];
                                        $total_capital_closing += ($record['total_amount'] + $record['bf_bal']);
                                    }
                                    foreach ($capital_sub_accounts as $subs) {
                                        if ($subs['main_account_id'] == $record['account_id'] && $subs['subs'] == 0) {
                                            $ac_bal = $subs['total_amount'];
                                            if ($ac_bal == 0) {
                                                $ac_bal = $subs['balance'];
                                            }
                                        ?>

                                            <tr>
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?= number_format($subs['bf_bal']); ?>
                                                </td>
                                                <!-- <td style="text-align:right;"></td> -->
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($ac_bal) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($ac_bal + $subs['bf_bal']) ?></a>
                                                </td>
                                            </tr>

                                <?php
                                            $total_capital += $ac_bal;
                                            $total_capital_opening +=
                                                $subs['bf_bal'];
                                            $total_capital_closing += ($ac_bal + $subs['bf_bal']);
                                        }
                                    }
                                } ?>
                                <tr style="text-align:right;">


                                    <th colspan="2">Total for Capital</th>
                                    <!-- opening totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_capital_opening) ?></th>

                                    <!-- trxn totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_capital) ?></th>

                                    <!-- closing totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_capital_closing) ?></th>
                                </tr>

                                <tr>
                                    <th>4</th>
                                    <th colspan="7">INCOMES</th>
                                </tr>
                                <?php
                                $i = 1;
                                $total_income = 0;
                                $acc_bal_value = 0;
                                foreach ($income_accounts as $income) {


                                    if (is_null($income['main_account_id']) || $income['subs'] > 0) {

                                        // if($income['lpid']){
                                        $acc_bal_value = $response->getAccBalValue($income['lpid'], $income['account_id'], $income['branchId'], @$_REQUEST['transaction_start_date'], @$_REQUEST['transaction_end_date']);
                                        // }else{
                                        //     $acc_bal_value = $income['total_amount'];
                                        // }

                                ?>
                                        <tr style="font-weight: bolder;">
                                            <td> <?= $income['account_code_used'] ?? ''; ?></td>
                                            <td> <?= $income['account_name'] ?> </td>
                                            <td style="text-align:right;">
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td style="text-align:right;">
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <!-- <td style="text-align:right;"></td> -->
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $income['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $income['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($acc_bal_value) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $income['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $income['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($acc_bal_value) ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_income += $acc_bal_value;
                                    }
                                    $acc_bal_valuen = 0;
                                    foreach ($income_sub_accounts as $subs) {
                                        if ($subs['main_account_id'] == $income['account_id'] && $subs['subs'] == 0) {
                                            // if ($subs['lpid']) {
                                            $acc_bal_valuen = $response->getAccBalValue($subs['lpid'], $subs['account_id'], $subs['branchId'], @$_REQUEST['transaction_start_date'], @$_REQUEST['transaction_end_date']);
                                            // } else {
                                            //     $acc_bal_value = $subs['total_amount'];
                                            // }
                                        ?>

                                            <tr>
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>
                                                <!-- <td style="text-align:right;"></td> -->
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($acc_bal_valuen) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($acc_bal_valuen) ?></a>
                                                </td>
                                            </tr>

                                <?php
                                            $total_income += $acc_bal_valuen;
                                        }
                                    }
                                } ?>

                                <tr style="text-align:right;">
                                    <th colspan="2">Total for Income</th>
                                    <!-- opening totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format(0.00) ?></th>

                                    <!-- trxn totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_income) ?></th>

                                    <!-- closing totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format($total_income) ?></th>
                                </tr>


                                <tr>
                                    <th>5</th>
                                    <th colspan="7">EXPENSES</th>
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
                                            <td>
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td>
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $expense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($expense['total_amount']) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $expense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <!-- <td style="text-align:right;"></td> -->
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $expense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($expense['total_amount']) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $expense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_expense += $expense['total_amount'];
                                    }
                                    foreach ($expenses_sub_accounts as $subs) {
                                        if ($subs['main_account_id'] == $expense['account_id'] && $subs['subs'] == 0) { ?>

                                            <tr>
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>

                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($subs['total_amount']) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <!-- <td style="text-align:right;"></td> -->
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($subs['total_amount']) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                            </tr>

                                <?php
                                            $total_expense += $subs['total_amount'];
                                        }
                                    }
                                } ?>
                                <tr style="text-align:right;">
                                    <th colspan="2">Total for Expenses</th>
                                    <!-- opening totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format(0.00) ?></th>

                                    <!-- trxn totals -->
                                    <th><?= number_format($total_expense) ?></th>
                                    <th><?= number_format(0.00) ?></th>

                                    <!-- closing totals -->
                                    <th><?= number_format($total_expense) ?></th>
                                    <th><?= number_format(0.00) ?></th>
                                </tr>


                                <tr>
                                    <th>6</th>
                                    <th colspan="7">SUSPENSE AND ERROR ACCOUNTS</th>
                                </tr>

                                <?php
                                $i = 1;
                                $total_suspense = 0;
                                foreach ($suspense_accounts as $suspense) {
                                    if (is_null($suspense['main_account_id']) || $suspense['subs'] > 0) {
                                ?>
                                        <tr style="font-weight: bolder;">
                                            <td> <?= $suspense['account_code_used'] ?? ''; ?></td>
                                            <td> <?= $suspense['account_name'] ?> </td>
                                            <td>
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td>
                                                <?= number_format(0.00); ?>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $suspense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($suspense['balance']) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $suspense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <!-- <td style="text-align:right;"></td> -->
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $suspense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format($suspense['balance']) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $suspense['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_suspense += $suspense['balance'];
                                    }
                                    foreach ($suspense_sub_accounts as $subs) {
                                        if ($subs['main_account_id'] == $suspense['account_id'] && $subs['subs'] == 0) { ?>

                                            <tr>
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>
                                                <td style="text-align:right;">
                                                    <?= number_format(0.00); ?>
                                                </td>

                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($subs['balance']) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <!-- <td style="text-align:right;"></td> -->
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format($subs['balance']) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=&transaction_end_date=" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                            </tr>

                                <?php
                                            $total_suspense += $subs['balance'];
                                        }
                                    }
                                } ?>
                                <tr style="text-align:right;">
                                    <th colspan="2">Total for Suspenses</th>
                                    <!-- opening totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th><?= number_format(0.00) ?></th>

                                    <!-- trxn totals -->
                                    <th><?= number_format($total_suspense) ?></th>
                                    <th><?= number_format(0.00) ?></th>

                                    <!-- closing totals -->
                                    <th><?= number_format($total_suspense) ?></th>
                                    <th><?= number_format(0.00) ?></th>
                                </tr>




                                <tr>

                                    <th colspan="2">GRAND TOTALS</th>
                                    <!-- opening totals -->
                                    <th><?= number_format($total_assets_opening - $total_assets_opening_cr) ?></th>
                                    <th><?= number_format($total_liabilites_opening + $total_capital_opening) ?></th>
                                    <!-- $total_suspense + $total_expense + $total_assets-$total_assets_opening_cr -->
                                    <!-- trxn totals -->
                                    <th><?= number_format($total_suspense + $total_expense + $total_assets - $total_assets_opening_cr) ?></th>
                                    <th><?= number_format($total_income + $total_liabilites + $total_capital) ?></th>
                                    <!-- $total_suspense + $total_expense + $total_assets_closing - $total_assets_opening_cr -->
                                    <!-- closing totals -->
                                    <th><?= number_format($total_suspense + $total_expense + $total_assets_closing - $total_assets_opening_cr) ?></th>
                                    <th><?= number_format($total_income + $total_liabilites_closing + $total_capital_closing) ?></th>


                                </tr>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
        <?php
        include('includes/bottom_scripts.php');
        ?>
      
</body>

</html>