<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');

$ReportService = new ReportService();
$report_reponse = $ReportService->generateReport($_REQUEST);
$loans = @$report_reponse['data'];
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Credit Officers Report: </strong> </td>
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

    <div>
        Total Loans: <?= number_format(count($loans)) ?>
    </div>

    <table class="report_table">
        <thead>
            <tr>
                <th rowspan="2">ID</th>
                <th rowspan="2">A/C N0</th>
                <th rowspan="2">Client Names</th>
                <th rowspan="2">Disbursement Date</th>
                <th rowspan="2">Loan Amount</th>
                <th rowspan="2">Principal Outstanding</th>
                <th rowspan="2">Principal in Arrears</th>
                <th colspan="6" class="text-center">Portfolio at Risk</th>
            </tr>

            <tr>
                <th>1-30</th>
                <th>31-60</th>
                <th>61-90</th>
                <th>91-120</th>
                <th>121-180</th>
                <th>More than 180</th>
            </tr>

        </thead>
        <tbody>
            <?php
            $total_loans = 0;
            $total_outstanind_principal = 0;
            $total_arrears_principal = 0;
            $total_1_30 = 0;
            $total_31_60 = 0;
            $total_61_90 = 0;
            $total_91_120 = 0;
            $total_121_180 = 0;
            $total_above_180 = 0;
            foreach ($loans as $loan) {
                $days_between_dates = days_between_dates(@$loan['arrearsbegindate'], date('Y-m-d'));

                $total_loans += @$loan['principal'];
                $total_outstanind_principal += @$loan['principal_balance'];
                $total_arrears_principal += @$loan['principal_arrears'];
            ?>
                <tr>
                    <td> <?= @$loan['loan_no'] ?> </td>
                    <td> <?= in_array(@$loan['membership_no'], [0, null]) ? '-' : @$loan['membership_no']; ?> </td>
                    <td> <?= @$loan['client_names'] ?> </td>
                    <td> <?= normal_date_short(@$loan['date_disbursed']) ?> </td>
                    <td> <?= number_format(@$loan['principal']) ?> </td>
                    <td> <?= number_format(@$loan['principal_balance']) ?> </td>
                    <td> <?= number_format(@$loan['principal_arrears']) ?> </td>
                    <td>
                        <?php if (number_is_between($days_between_dates, 1, 30)) {
                            $total_1_30 += @$loan['principal_arrears'];
                        ?>
                            <?= number_format(@$loan['principal_arrears']) ?>
                        <?php } else { ?>
                            -
                        <?php } ?>

                    </td>
                    <td>
                        <?php if (number_is_between($days_between_dates, 31, 60)) {
                            $total_31_60 += @$loan['principal_arrears'];
                        ?>
                            <?= number_format(@$loan['principal_arrears']) ?>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                    <td>
                        <?php if (number_is_between($days_between_dates, 61, 90)) {
                            $total_61_90 += @$loan['principal_arrears'];
                        ?>
                            <?= number_format(@$loan['principal_arrears']) ?>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                    <td>
                        <?php if (number_is_between($days_between_dates, 91, 120)) {
                            $total_91_120 += @$loan['principal_arrears'];
                        ?>
                            <?= number_format(@$loan['principal_arrears']) ?>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                    <td>
                        <?php if (number_is_between($days_between_dates, 121, 180)) {
                            $total_121_180 += @$loan['principal_arrears'];
                        ?>
                            <?= number_format(@$loan['principal_arrears']) ?>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>

                    <td>
                        <?php if ($days_between_dates > 180) {
                            $total_above_180 += @$loan['principal_arrears'];
                        ?>
                            <?= number_format(@$loan['principal_arrears']) ?>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>

            <tr>
                <th colspan="4">
                    Totals
                </th>
                <th> <?= number_format($total_loans) ?> </th>
                <th> <?= number_format($total_outstanind_principal) ?> </th>
                <th> <?= number_format($total_arrears_principal) ?> </th>
                <th> <?= number_format($total_1_30) ?> </th>
                <th> <?= number_format($total_31_60) ?> </th>
                <th> <?= number_format($total_61_90) ?> </th>
                <th> <?= number_format($total_91_120) ?> </th>
                <th> <?= number_format($total_121_180) ?> </th>
                <th> <?= number_format($total_above_180) ?> </th>
            </tr>
        </tbody>
    </table>
</section>