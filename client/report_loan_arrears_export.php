<?php
require_once('includes/response.php');
require_once('includes/functions.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');

$ReportService = new ReportService();
$report_reponse = $ReportService->generateReport($_REQUEST);
$loans = @$report_reponse['data'];

// var_dump($loans);
// exit;
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Loan Arrears Report: </strong> </td>
        </tr>
    </table>

    <table>
        <?php if (@$_REQUEST['staffName']) : ?>
            <tr>
                <td width="18%"> Received By:</td>
                <td> <strong> <?= $_REQUEST['staffName']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['loanProduct']) : ?>
            <tr>
                <td width="18%"> Loan Product:</td>
                <td> <strong> <?= $_REQUEST['loanProduct']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['loanStatus']) : ?>
            <tr>
                <td width="18%"> Loan Status:</td>
                <td> <strong> <?= $_REQUEST['loanStatus']; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['disbursement_start_date'] && @$_REQUEST['disbursement_end_date']) : ?>
            <tr>
                <td colspan="2">
                    <div>
                        From: <strong> <?= normal_date($_REQUEST['disbursement_start_date']) ?> </strong>
                        To: <strong> <?= normal_date($_REQUEST['disbursement_end_date']) ?> </strong>
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
                <th rowspan="2">Names</th>
                <th rowspan="2">Disbursement Date</th>
                <th rowspan="2">Loan Amount</th>
                <th colspan="3" class="text-center">Total Arrears</th>
                <th rowspan="2">Days in Arrears</th>
                <th rowspan="2">Penalty</th>
            </tr>

            <tr>
                <th> Principal </th>
                <th>Interest</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $total_loans_amount = 0;
            $total_principal_arrears = 0;
            $total_interest_arrears = 0;
            $total_arrears_absolute = 0;
            $total_penalty = 0;
            foreach ($loans as $loan) {
                $total_arrears = @$loan['principal_arrears'] + @$loan['interest_arrears'];
            ?>
                <tr>
                    <td> <?= @$loan['loan_no'] ?> </td>
                    <td> <?= in_array(@$loan['membership_no'], [0, null]) ? '-' : @$loan['membership_no']; ?> </td>
                    <td> <?= @$loan['client_initials'] ?> </td>
                    <td> <?= normal_date_short(@$loan['date_disbursed']) ?> </td>
                    <td> <?= number_format(@$loan['principal']) ?> </td>
                    <td> <?= number_format(@$loan['principal_arrears']) ?> </td>
                    <td> <?= number_format(@$loan['interest_arrears']) ?> </td>
                    <td>
                        <?= number_format(@$total_arrears) ?>
                    </td>
                    <td class="text-center">
                        <?= days_in_arrears(@$loan) ?>
                    </td>
                    <td> <?= number_format(@$loan['penalty_balance']) ?> </td>
                </tr>
            <?php
                $total_loans_amount += (int)(@$loan['principal']);
                $total_principal_due += (int)(@$loan['principal_arrears']);
                $total_interest_arrears += (int)(@$loan['interest_arrears']);
                $total_arrears_absolute += (int)@$total_arrear;
            } ?>

            <tr>
                <th colspan="4">Totals </th>
                <th> <?= number_format($total_loans_amount) ?> </th>
                <th> <?= number_format($total_principal_arrears) ?> </th>
                <th> <?= number_format($total_interest_arrears) ?> </th>
                <th> <?= number_format($total_arrears_absolute) ?> </th>
                <th></th>
                <th> <?= number_format($total_penalty) ?> </th>
            </tr>
        </tbody>
    </table>
</section>