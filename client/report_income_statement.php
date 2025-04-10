<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_income_statement')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php
// Set the default time zone to Kampala
date_default_timezone_set('Africa/Kampala');
$title = 'INCOME STATEMENT';
require_once('includes/response.php');
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = [];
$request_data['is_trial_balance'] = true;
$request_data['with_income_totals'] = false;
$request_data['with_expenditure_totals'] = false;
$request_data['with_assets_totals'] = false;
$request_data['with_liabilities_totals'] = false;
$request_data['with_capital_totals'] = false;
$request_data['with_suspense_totals'] = false;

if (@$_REQUEST['branch']) {
    $request_data['bid'] = @$_REQUEST['branch'];
    $request_data['bankk'] = '';
    $_REQUEST['bankk'] = '';
} else {
    $request_data['bid'] = '';
}

// $request_data['bankk'] = @$user[0]['bankId'] ?? '';
$request_data = array_merge($request_data, $_REQUEST);
$report_reponse = $ReportService->generateTrialBalance($request_data);
$records = @$report_reponse['data'] ?? [];

$sub_report_reponse = $ReportService->generateTrialBalanceSubAccounts($request_data);
$sub_records = @$sub_report_reponse['data'] ?? [];


$income_accounts = @$records['income'] ?? [];
$income_sub_accounts = @$sub_records['income'] ?? [];
$expenses_accounts = @$records['expenses'] ?? [];
$expenses_sub_accounts = @$sub_records['expenses'] ?? [];
$report_type = "Income Statement";

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
                            <input type="hidden" name="bankk" class="form-control" value="<?= @$user[0]['bankId'] ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                            <input type="hidden" name="branch" class="form-control" value="<?= @$user[0]['branchId'] ?>">
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
                                        <label class="text-label form-label" ` for="exampleInputEmail3"> From *</label>
                                        <input type="date" name="transaction_start_date" class="form-control" name="from_date" value="<?= @$_REQUEST['transaction_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">To *</label>
                                        <input type="date" class="form-control" name="transaction_end_date" value="<?= @$_REQUEST['transaction_end_date'] ?? date('Y-m-d') ?>" placeholder="End Date">
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

                                <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                                    <i class="fas fa-file-pdf"></i>&nbsp;PDF
                                </a>


                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> <?= $report_type ?>: </strong> <?php if (@$_REQUEST['branch'] && @$_REQUEST['branch']) : ?> <strong> - <?= strtoupper($response->getBranchDetails($_REQUEST['branch'])['branch_name'] ?? '') ?> </strong>
                                    <?php else : ?> ALL BRANCHES <?php endif ?></td>
                            </tr>
                        </table>


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
                            <?php else : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            As at: <strong> <?= @$_REQUEST['transaction_end_date'] ? normal_date($_REQUEST['transaction_end_date']) :  date('Y-m-d H:i:s') ?> </strong>

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
                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th>Account Code</th>
                                    <th>Account Name</th>
                                    <th>Debit</th>
                                    <th>Credit</th>

                                </tr>

                            </thead>
                            <tbody>



                                <tr>
                                    <th>4</th>
                                    <th colspan="5">INCOMES</th>
                                </tr>
                                <?php
                                $i = 1;

                                foreach ($income_accounts as $income) {


                                    if (is_null($income['main_account_id']) || $income['subs'] > 0) {




                                ?>
                                        <tr style="font-weight: bolder;" id="row-<?= $income['account_id'] ?>" data-id="<?= $income['account_id'] ?>" cr_dr="row-cr" data-cr_dr="cr" acc_typ="row-inc" data-acc_typ="inc">
                                            <td> <?= $income['account_code_used'] ?? ''; ?></td>
                                            <td> <?= $income['account_name'] ?> </td>

                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=<?= @$_REQUEST['branch'] ?>&transaction_type=&authorized_by_id=&acid=<?= $income['account_id'] ?>&transaction_start_date=<?= @$_REQUEST['transactions_start_date'] ?>&transaction_end_date=<?= @$_REQUEST['transactions_end_date'] ?>" class="text-primary">
                                                </a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=<?= @$_REQUEST['branch'] ?>&transaction_type=&authorized_by_id=&acid=<?= $income['account_id'] ?>&transaction_start_date=<?= @$_REQUEST['transactions_start_date'] ?>&transaction_end_date=<?= @$_REQUEST['transactions_end_date'] ?>" class="text-primary details">
                                                    <?= number_format(0.00) ?></a>
                                            </td>

                                        </tr>
                                        <?php

                                    }

                                    foreach ($income_sub_accounts as $subs) {
                                        if ($subs['main_account_id'] == $income['account_id'] && $subs['subs'] == 0) {

                                        ?>

                                            <tr id="row-<?= $subs['account_id'] ?>" data-id="<?= $subs['account_id'] ?>" cr_dr="row-cr" data-cr_dr="cr" acc_typ="row-inc" data-acc_typ="inc">
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>

                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=<?= $_REQUEST['branch'] ?>&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=<?= @$_REQUEST['transactions_start_date'] ?>&transaction_end_date=<?= @$_REQUEST['transactions_end_date'] ?>" class="text-primary">
                                                    </a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="<?= ($subs['lpid'] > 0 && (!str_contains(@$subs['account_name'], 'Penalty'))) ? 'report_journal_entries_loans_interest.php?id=' . $subs['lpid'] . '&branch=' . @$_REQUEST['branch'] . '&acid=' . $subs['account_id'] . '&transaction_start_date=' . @$_REQUEST['transactions_start_date'] . '&transaction_end_date=' . @$_REQUEST['transactions_end_date'] . '' : 'report_journal_entries.php?filtered=1&branchId=' . $_REQUEST['branch'] . '&transaction_type=&authorized_by_id=&acid=' . $subs['account_id'] . '&transaction_start_date=' . @$_REQUEST['transactions_start_date'] . '&transaction_end_date=' . @$_REQUEST['transactions_end_date'] . ''  ?>" class="text-primary details">
                                                        <?= number_format(0.00) ?></a>
                                                </td>

                                            </tr>

                                <?php

                                        }
                                    }
                                } ?>

                                <tr style="text-align:right;">
                                    <th colspan="2">Total for Income</th>

                                    <!-- closing totals -->
                                    <th><?= number_format(0.00) ?></th>
                                    <th class="text-primary inc_tot"><?= number_format(0.00) ?></th>
                                </tr>


                                <tr>
                                    <th>5</th>
                                    <th colspan="5">EXPENSES</th>
                                </tr>

                                <?php
                                $i = 1;

                                foreach ($expenses_accounts as $expense) {
                                    if (is_null($expense['main_account_id']) || $expense['subs'] > 0) {
                                ?>
                                        <tr style="font-weight: bolder;" id="row-<?= $expense['account_id'] ?>" data-id="<?= $expense['account_id'] ?>" cr_dr="row-dr" data-cr_dr="dr" acc_typ="row-exp" data-acc_typ="exp">
                                            <td> <?= $expense['account_code_used'] ?? ''; ?></td>
                                            <td> <?= $expense['account_name'] ?> </td>

                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=&transaction_type=&authorized_by_id=&acid=<?= $expense['account_id'] ?>&transaction_start_date=<?= @$_REQUEST['transactions_start_date'] ?>&transaction_end_date=<?= @$_REQUEST['transactions_end_date'] ?>" class="text-primary details">
                                                    <?= number_format(0.00) ?></a>
                                            </td>
                                            <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=<?= @$_REQUEST['branch'] ?>&transaction_type=&authorized_by_id=&acid=<?= $expense['account_id'] ?>&transaction_start_date=<?= @$_REQUEST['transactions_start_date'] ?>&transaction_end_date=<?= @$_REQUEST['transactions_end_date'] ?>" class="text-primary">
                                                    <?= number_format(0.00) ?></a>
                                            </td>

                                        </tr>
                                        <?php

                                    }
                                    foreach ($expenses_sub_accounts as $subs) {
                                        if ($subs['main_account_id'] == $expense['account_id'] && $subs['subs'] == 0) { ?>

                                            <tr id="row-<?= $subs['account_id'] ?>" data-id="<?= $subs['account_id'] ?>" cr_dr="row-dr" data-cr_dr="dr"
                                                acc_typ="row-exp" data-acc_typ="exp">
                                                <td> <?= $subs['account_code_used'] ?? ''; ?></td>
                                                <td> <?= $subs['account_name'] ?> </td>


                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=<?= $_REQUEST['branch'] ?>&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=<?= @$_REQUEST['transactions_start_date'] ?>&transaction_end_date=<?= @$_REQUEST['transactions_end_date'] ?>" class="text-primary details">
                                                        <?= number_format(0.00) ?></a>
                                                </td>
                                                <td style="text-align:right;" class="text-primary"><a href="report_journal_entries.php?filtered=1&branchId=<?= $_REQUEST['branch'] ?>&transaction_type=&authorized_by_id=&acid=<?= $subs['account_id'] ?>&transaction_start_date=<?= @$_REQUEST['transactions_start_date'] ?>&transaction_end_date=<?= @$_REQUEST['transactions_end_date'] ?>" class="text-primary">
                                                        <?= number_format(0.00) ?></a>
                                                </td>

                                            </tr>

                                <?php

                                        }
                                    }
                                } ?>
                                <tr style="text-align:right;">
                                    <th colspan="2">Total for Expenses</th>

                                    <!-- closing totals -->
                                    <th class="text-primary exp_tot"><?= number_format(0.00) ?></th>
                                    <th><?= number_format(0.00) ?></th>
                                </tr>



                                <tr>

                                    <th colspan="2">GRAND TOTALS</th>

                                    <!-- closing totals -->
                                    <th class="text-primary dr_tot"><?= number_format(0.00) ?></th>
                                    <th class="text-primary cr_tot"><?= number_format(0.00) ?></th>


                                </tr>
                                <tr>

                                    <th colspan="3">Net Income</th>

                                    <!-- closing totals -->

                                    <th class="text-primary net_tot"><?= number_format(0.00) ?></th>


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
        <script>
            $(document).ready(function() {
                let totalCredit = 0; // Counter for total credit
                let totalDebit = 0; // Counter for total debit
                let totalNet = 0; // Counter for total net

                let totalIncomes = 0; // Counter for total incomes
                let totalExpenses = 0; // Counter for total expenses

                $('tr[data-id]').each(function() {
                    var rowId = $(this).data('id');
                    var cr_dr = $(this).data('cr_dr');
                    var acc_typ = $(this).data('acc_typ');
                    $.ajax({
                        url: 'https://app.ucscucbs.net/backend/api/Bank/fetchTrialValues.php?start=<?= @$_REQUEST['transaction_start_date'] ?>&end=<?= @$_REQUEST['transaction_end_date'] ?>&branch=<?= @$_REQUEST['branch'] ?>&bankk=<?= @$_REQUEST['bankk'] ?>',
                        method: 'GET',
                        data: {
                            id: rowId,
                            cr_dr: cr_dr,
                            acc_typ: acc_typ
                        },
                        success: function(response) {
                            // Assuming response is JSON with a 'details' field
                            $('#row-' + rowId + ' .details').text(response.details);

                            // Parse credit and debit values from the response
                            const credit = parseFloat(response.cr || 0);
                            const debit = parseFloat(response.dr || 0);

                            // parse category values for each response

                            const incomes = parseFloat(response.inc || 0);
                            const expenses = parseFloat(response.exp || 0);

                            // Update the totals
                            totalCredit += credit;
                            totalDebit += debit;
                            totalNet += (credit - debit);


                            totalIncomes += incomes;
                            totalExpenses += expenses;

                            // Update the total credit and debit elements
                            $('.inc_tot').text(Number(totalIncomes.toFixed(0)).toLocaleString());
                            $('.exp_tot').text(Number(totalExpenses.toFixed(0)).toLocaleString());

                            $('.cr_tot').text(Number(totalCredit.toFixed(0)).toLocaleString());
                            $('.dr_tot').text(Number(totalDebit.toFixed(0)).toLocaleString());
                            $('.net_tot').text(Number(totalNet.toFixed(0)).toLocaleString());




                        },
                        error: function() {
                            $('#row-' + rowId + ' .details').text('0');
                        }
                    });
                });
            });
        </script>

</body>

</html>