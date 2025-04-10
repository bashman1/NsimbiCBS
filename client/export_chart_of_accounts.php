<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');

$ReportService = new ReportService();
$request_data = [
    'is_trial_balance' => false,
    'with_income_totals' => false,
    'with_expenditure_totals' => false,
    'with_assets_totals' => false,
    'with_liabilities_totals' => false,
    'with_capital_totals' => false,
    'transaction_end_date' => "",
    'transaction_start_date' => "",
    'bank_id' => "",
    'bankId' => "",
    'bankk' => "",
    'branch' => @$_REQUEST['branch'],
    'bid' => @$_REQUEST['branch'],
    'branchId' => @$_REQUEST['branch'],
];

$report_reponse = $ReportService->generateTrialBalance($request_data);

$records = @$report_reponse['data'] ?? [];

$reponse = new Response();
$sub_accs = $reponse->getAllSubAccounts(@$_REQUEST['branch'], '');

?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Chart of Accounts </strong> </td>
        </tr>
    </table>

    <table class="report_table">
        <thead>
            <th>#</th>
            <th>CHART ACCOUNT</th>
            <th>BRANCH</th>
        </thead>
        <tbody>
            <?php foreach ($records as $main_account => $account) { ?>
                <tr>
                    <td colspan="3">
                        <strong> <?= strtoupper($main_account == 'SUSPENSES' || $main_account == 'suspenses' ? 'SUSPENSE AND ERROR ACCOUNTS' : $main_account) ?> </strong>
                    </td>
                </tr>

                <?php
                $i = 1;
                foreach ($account as $this_account) {
                    if ($this_account['subs'] > 0 || is_null($this_account['main_account_id'])) {
                ?>
                        <tr style="font-weight: bolder;">
                            <td> <?= $this_account['account_code_used'] ?> </td>
                            <td>
                                <?= $this_account['account_name'] ?>
                            </td>
                            <td>
                                <?= @$this_account['branch_name'] ?? '' ?>
                            </td>
                        </tr>

                    <?php
                    }
                    if ($this_account['subs'] > 0) {

                    ?>
                        <?php
                        $j = 1;
                        foreach ($sub_accs as $sub_account) {
                            if ($sub_account['main_account_id'] == $this_account['account_id'] && $sub_account['subs'] == 0) {
                        ?>
                                <tr>
                                    <td> <?= $sub_account['account_code_used'] ?? '' ?> </td>
                                    <td>
                                        <?= $sub_account['name'] ?>
                                    </td>
                                    <td><?= $sub_account['branch'] ?></td>
                                </tr>

                    <?php }
                        }
                    } ?>

                <?php } ?>



            <?php } ?>
        </tbody>
    </table>

</section>