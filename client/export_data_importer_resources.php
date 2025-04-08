<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
include_once('includes/response.php');
include_once('includes/functions.php');

$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('data_importer')) {
    return $permissions->isNotPermitted(true);
}
$responser = new Response();
$resources = $responser->getDataImporterResources();

// var_dump($resources);
            // var_dump($resources['loan_products']);
// exit;

$transaction_types = array(
    "D" => "Deposit",
    "W" => "Withdrawal",
    "SMS" => "SMS Charges",
    "C" => "Charges",
    "I" => "Interest",
    "E" => "Expenditure",
    "L" => "Loan",
    "LP" => "Loan Repayment",
);

?>

<?php
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');
?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">

    <table class="main-header">
        <tr>
            <td> <strong> Data Importer Resources </strong> </td>
        </tr>
    </table>

    <div style="font-size:12px; margin:5px auto 1px 0px; font-weight:bold;">Branches</div>
    <table class="report_table">
        <thead>
            <th>Branch Name</th>CURRENT_TIMESTAMP
            <th>Branch Code</th>
        </thead>
        <tbody>
            <?php
            foreach ($resources['branches'] as $record) { ?>
                <tr>
                    <td width="50%"> <?= @$record['name'] ?? @$record['bank_name'] ?> </td>
                    <td> <?= @$record['bcode'] ?? @$record['branch_code'] ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div style="font-size:12px; margin:10px auto 1px 0px; font-weight:bold;">Loan Products</div>
    <table class="report_table">
        <thead>
            <th>Name</th>
            <th>Code</th>
        </thead>
        <tbody>
            <?php
            foreach ($resources['loan_products'] as $record) { ?>
                <tr>
                    <td width="50%"> <?= @$record['type_name'] ?> </td>
                    <td> <?= @$record['type_id'] ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


    <div style="font-size:12px; margin:10px auto 1px 0px; font-weight:bold;">Saving Products</div>
    <table class="report_table">
        <thead>
            <th>Name</th>
            <th>Code</th>
        </thead>
        <tbody>
            <?php
            foreach ($resources['saving_products'] as $record) { ?>
                <tr>
                    <td width="50%"> <?= @$record['name'] . ' (' . @$record['ucode'] . ')' ?> </td>
                    <!-- <td> <?= @$record['ucode'] ?> </td> -->
                    <td> <?= @$record['id'] ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div style="font-size:12px; margin:10px auto 1px 0px; font-weight:bold;">Loan/Credit/Savings Officers(Staff)</div>
    <table class="report_table">
        <thead>
            <th>Names</th>
            <th>ID</th>
        </thead>
        <tbody>
            <?php
            foreach ($resources['credit_officers'] as $record) { ?>
                <tr>
                    <td width="50%"> <?= @$record['firstName'] . ' ' . @$record['lastName'] ?> </td>
                    <td> <?= @$record['suserid'] ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


    <div style="font-size:12px; margin:10px auto 1px 0px; font-weight:bold;">
        Transaction Types
    </div>
    <table class="report_table">
        <thead>
            <th>Transaction Type</th>
            <th>CODE</th>
        </thead>
        <tbody>
            <?php
            foreach ($transaction_types as $code => $value) { ?>
                <tr>
                    <td width="50%"> <?= $value ?> </td>
                    <td> <?= @$code ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>