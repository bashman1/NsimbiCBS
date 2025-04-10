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

    <table class="report_table">
        <thead>
            <tr>
                <th rowspan="2">Loan No.</th>
                <th rowspan="2">A/C No.</th>
                <th rowspan="2">Names</th>
                <th rowspan="2">Physical Address</th>
                <th rowspan="2">Guarantors</th>
                <th rowspan="2">Disbursement Date</th>
                <th rowspan="2">Duration</th>
                <th rowspan="2">Int. Rate</th>
                <th rowspan="2">Loan Amount</th>

                <th rowspan="2">Amount Paid</th>
                <th rowspan="2">Amount Due</th>
                <th rowspan="2">Out. Balance</th>
                <th rowspan="2">Amount in Arrears</th>
                <th rowspan="2">Days in Arrears</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $total_loans_amount = 0;
            $total_principal_due = 0;
            $total_interest_due = 0;
            $total_penalty_due = 0;

            $total_amount_paid = 0;
            $total_princ_paid = 0;
            $total_int_paid = 0;
            $total_princ_bal = 0;
            $total_int_bal = 0;

            $total_outstanding_amount = 0;
            $total_principal_arrears = 0;
            $total_interest_arrears = 0;
            foreach ($loans as $loan) {
                $count = $count + 1;
                $outstanding_amount = @$loan['principal_balance'] + @$loan['interest_balance'] + @$loan['penalty_balance'];
                $ftype = '';

                if ($loan['repay_cycle_id'] == 1) {
                    $ftype = 'DAYS';
                } else if ($loan['repay_cycle_id'] == 2) {
                    $ftype = 'WEEKS';
                } else if ($loan['repay_cycle_id'] == 3) {
                    $ftype = 'MONTHS';
                } else if ($loan['repay_cycle_id'] == 4) {
                    $ftype = 'DAYS';
                } else if ($loan['repay_cycle_id'] == 5) {
                    $ftype = 'YEARS';
                }
            ?>
                <tr>
                    <td> <?= @$loan['loan_no'] ?> </td>
                    <td> <?= in_array(@$loan['membership_no'], [0, null]) ? '-' : @$loan['membership_no']; ?> </td>
                    <td> <?= @$loan['client_initials'] ?> </td>
                    <td> <?= @$loan['client_address'] ?> </td>
                    <td>
                        <?php foreach (@$loan['guarantors'] as $guarantor) : ?>
                            <?= $guarantor['guarantor_initials'] ? $guarantor['guarantor_initials'] . ' , <br>' : '' ?>
                        <?php endforeach ?>
                    </td>
                    <td> <?= normal_date_short(@$loan['date_disbursed']) ?> </td>
                    <td> <?= @$loan['approved_loan_duration'] . ' ' . $ftype ?> </td>
                    <td> <?= @$loan['monthly_interest_rate']  ?>% </td>
                    <td> <?= number_format(@$loan['principal']) ?> </td>


                    <td> <?= number_format(@$loan['principal_paid'] + @$loan['interest_paid']) ?> </td>


                    <td> <?= number_format(@$loan['principal_due'] + @$loan['interest_due'] + @$loan['penalty_balance']) ?> </td>

                    <td> <?= number_format(@$loan['principal_balance'] + @$loan['interest_balance'] + @$loan['penalty_balance']) ?> </td>


                    <td> <?= number_format(@$loan['principal_arrears'] + @$loan['interest_arrears']) ?> </td>
                    <td class="text-center">
                        <?= days_between_dates(@$loan['arrearsbegindate'], date('Y-m-d')) ?>
                    </td>
                </tr>
            <?php
                $total_loans_amount += (int)(@$loan['principal']);
                $total_principal_due += (int)(@$loan['principal_due']);
                $total_interest_due += (int)(@$loan['interest_due']);
                $total_penalty_due += (int)(@$loan['penalty_balance']);

                $total_amount_paid += (int)(@$loan['amount_paid']);

                $total_princ_paid += (int)(@$loan['principal_paid']);
                $total_int_paid += (int)(@$loan['interest_paid']);

                $total_princ_bal += (int)(@$loan['principal_balance']);
                $total_int_bal += (int)(@$loan['interest_balance']);


                $total_outstanding_amount += (int) @$outstanding_amount;
                $total_principal_arrears += (int)(@$loan['principal_arrears']);
                $total_interest_arrears += (int)(@$loan['interest_arrears']);
            } ?>

            <tr>
                <th><?= number_format($count) ?> </th>
                <th colspan="7">Totals </th>
                <th> <?= number_format($total_loans_amount) ?> </th>
                <th> <?= number_format($total_princ_paid + $total_int_paid) ?> </th>
                <th> <?= number_format($total_principal_due + $total_interest_due + $total_penalty_due) ?> </th>

                <th> <?= number_format($total_princ_bal + $total_int_bal) ?> </th>

                <th> <?= number_format($total_principal_arrears + $total_interest_arrears) ?> </th>
                <th></th>
            </tr>

            <tr>

                <th colspan="8">Recovery Rate </th>
                <th> </th>
                <th> <?= number_format((($total_princ_paid + $total_int_paid) / ($total_principal_due + $total_interest_due + $total_penalty_due)) * 100) ?> %</th>
                <th> </th>

                <th> </th>

                <th> </th>
                <th></th>
            </tr>
        </tbody>
    </table>
</section>