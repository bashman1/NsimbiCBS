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
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search(@$_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}

$staff_names = '';
if (@$_REQUEST['loan_officer_id']) {
    $key = array_search($_REQUEST['loan_officer_id'], array_column($staff, 'id'));
    $staff_names = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Portifolio At Risk Report: </strong> </td>
        </tr>
    </table>


    <table>

        <?php if (@$_REQUEST['branchId']) : ?>
            <tr>
                <td width="18%"> Branch:</td>
                <td> <strong> <?= @$branch_name; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['loan_officer_id']) : ?>
            <tr>
                <td width="18%"> Credit Officer:</td>
                <td> <strong> <?= @$staff_names; ?> </strong> </td>
            </tr>
        <?php endif ?>


    </table>

    <table class="report_table">
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Loan Product</th>
                <th rowspan="2">Loan Amount</th>
                <th rowspan="2">Principal Outstanding</th>
                <!-- <th rowspan="2">(PAR)</th> -->
                <th colspan="3" class="text-center">Amount in Arrears</th>
                <th rowspan="2">PAR(%)</th>
                <!-- <th rowspan="2">Risk(%)</th> -->
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
            $total_princ = 0;
            foreach ($loans as $loan) {
                $total_arrears = (@$loan['principal_arrears'] + @$loan['interest_arrears']) ?? 0;
            ?>
                <tr>
                    <td> <?= @$loan['type_id'] ?> </td>
                    <td> <?= @$loan['type_name'] . ' ( ' . @$loan['num_loans'] . ' )'; ?> </td>
                    <td> <?= number_format(@$loan['loan_amount'] ?? 0) ?> </td>
                    <td> <?= number_format(@$loan['principal_balance'] ?? 0) ?> </td>

                    <td> <?= number_format(@$loan['principal_arrears'] ?? 0) ?> </td>
                    <td> <?= number_format(@$loan['interest_arrears'] ?? 0) ?> </td>
                    <td>
                        <?= number_format(@$total_arrears) ?>
                    </td>

                    <td class="text-center"> <?= number_format((float)((($loan['principal_arrears'] ?? 0) / ($loan['principal_balance'] ?? 1)) * 100), 2, '.', '') ?>% </td>
                    <!-- <td class="text-center"><?= number_format((float)((($loan['principal_arrears'] ?? 0) / ($loan['loan_amount'] ?? 1)) * 100), 2, '.', '') ?>% </td> -->
                </tr>
            <?php
                $total_loans_amount += (int)(@$loan['loan_amount']);
                $total_principal_arrears += (int)(@$loan['principal_arrears']);
                $total_interest_arrears += (int)(@$loan['interest_arrears']);
                $total_princ += (int)(@$loan['principal_balance']);
                $total_arrears_absolute += (int)@$total_arrears;
            } ?>

            <tr>
                <th colspan="2">Grand Totals </th>
                <th> <?= number_format($total_loans_amount) ?> </th>
                <th> <?= number_format($total_princ) ?> </th>
                <!-- <th> <?= number_format($total_princ) ?> </th> -->
                <th> <?= number_format($total_principal_arrears) ?> </th>
                <th> <?= number_format($total_interest_arrears) ?> </th>

                <th> <?= number_format($total_arrears_absolute) ?> </th>
                <th class="text-danger"> <?= number_format((float)((($total_principal_arrears ?? 0) / ($total_princ ?? 1)) * 100), 2, '.', '') ?>% </th>
                <!-- <th class="text-danger"> <?= number_format((float)((($total_principal_arrears ?? 0) / ($total_loans_amount ?? 1)) * 100), 2, '.', '') ?>% </th> -->
            </tr>
        </tbody>
    </table>

</section>