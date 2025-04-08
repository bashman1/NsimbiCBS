<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');

$ReportService = new ReportService();
$report_reponse = $ReportService->generateReport($_REQUEST);
$records = @$report_reponse['data'];

$response = new Response();
$branches = $response->getBankBranches($_SESSION['session_user']['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);
$sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);

$branch_name = '';
if (@$_REQUEST['branchId']) {
    $key = array_search($_REQUEST['branchId'], array_column($branches, 'id'));
    $branch_name = $branches[$key]['name'];
}

$staff_name = '';
if (@$_REQUEST['authorized_by_id']) {
    $key = array_search($_REQUEST['authorized_by_id'], array_column($staff, 'id'));
    $staff_name = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}

$journal_account = '';
$report_type = '';
if (@$_REQUEST['transaction_type'] == 'I') {
    $report_type = "Income Report";
} else if (@$_REQUEST['transaction_type'] == 'E') {
    $report_type = "Expense Report";
} else if (@$_REQUEST['transaction_type'] == 'LIA') {
    $report_type = "Liabilities Report";
} else if (@$_REQUEST['transaction_type'] == 'ASS') {
    $report_type = "Assets Report";
} else {
    $report_type = "Ledger Report";
}
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> <?= $report_type ?> </strong> </td>
        </tr>
    </table>

    <table>
        <?php if (@$_REQUEST['branchId']) : ?>
            <tr>
                <td width="18%"> Branch:</td>
                <td> <strong> <?= $branch_name; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$journal_account) : ?>
            <tr>
                <td width="18%"> Journal Account:</td>
                <td> <strong> <?= $journal_account; ?> </strong> </td>
            </tr>
        <?php endif ?>

        <?php if (@$_REQUEST['authorized_by_id']) : ?>
            <tr>
                <td width="18%"> Authorized by:</td>
                <td> <strong> <?= $staff_name; ?> </strong> </td>
            </tr>
        <?php endif ?>

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
        <thead>
            <th>REF</th>
            <th>Entry Type</th>
            <th>Amount</th>
            <th>Narration</th>
            <th>Mode of Payment</th>
            <th>Authorized By</th>
            <th>Branch</th>
            <th>Journal Account</th>
            <th>Trxn date</th>
        </thead>
        <tbody>

            <?php
            $total_amount = 0;
            foreach ($records as $record) {
                $ref =    $record['t_type'] . '-ref-' . $record['pay_method'] . '-' . $record['transaction_id'] . '-' . $record['_authorizedby'];

                $title1 = '';
                if ($record['t_type'] == 'E') {
                    $title1 =   'Expense Entry';
                }
                if ($record['t_type'] == 'LIA') {
                    $title1 =   'Liability Entry';
                }
                if ($record['t_type'] == 'CAP') {
                    $title1 =   'Capital Entry';
                }
                if ($record['t_type'] == 'AJE') {
                    $title1 =   'Advanced Journal Entry';
                }
                if ($record['t_type'] == 'ASS') {
                    $title1 =   'Asset Entry';
                }
                if ($record['t_type'] == 'I' || $record['t_type'] == 'R') {
                    $title1 =   'Income Entry';
                }
            ?>
                <tr>

                    <td class="no_print clickable_ref_no" ref-no="<?= @$ref ?> " tid="<?= @$record['transaction_id'] ?>"><?= @$ref ?></td>
                    <td> <?= $title1 ?> </td>
                    <td> <?= number_format(@$record['amount']) ?> </td>
                    <td> <?= @$record['transaction_description'] ?> </td>
                    <td> <?= @$record['pay_method'] ?> </td>
                    <td> <?= @$record['authorized_by_names'] ?> </td>
                    <td> <?= @$record['branch_name'] ?> </td>
                    <td> <?= @$record['journal_account'] ?> </td>
                    <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>
                </tr>
            <?php
                $total_amount += (int) $record['amount'];
            } ?>

            <tr>
                <th colspan="2">Total </th>
                <th> <?= number_format($total_amount) ?> </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tbody>
    </table>
</section>