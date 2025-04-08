<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_loan_arrears_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'PORTIFOLIO AT RISK REPORT';
?>
<?php

require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'LoanReport', 'ReportModelMethod' => 'getPARReport2', 'is_loan_arrears' => 0, 'par_type' => @$_GET['type'], 'bankk' => @$_GET['bank'], 'branch' => @$_GET['branch'], 'end_date' => @$_GET['end_date']];

$request_data = array_merge($request_data, $_REQUEST);
$report_reponse = $ReportService->generateReport($request_data);
$loans = @$report_reponse['data'];
// var_dump($loans);
// exit;
// $response = new Response();

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
                            <input type="hidden" name="bank" class="form-control" value="<?= @$_GET['bank'] ?>">
                            <input type="hidden" name="branch" class="form-control" value="<?= @$_GET['branch'] ?>">
                            <input type="hidden" name="type" class="form-control" value="<?= @$_GET['type'] ?>">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">As at *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?? date('Y-m-d') ?>" placeholder="End Date">
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
                            Portifolio At Risk Report
                        </h4>


                        <div>
                            <!-- <a href="#" class="btn btn-primary light btn-xs">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </a> -->
                            <!-- <a href="export_report.php?exportFile=report_loan_par&orientation=landscape" target="_blank" class="btn btn-primary light btn-xs">
                                <i class="fas fa-file-pdf"></i> Export to PDF
                            </a> -->
                            <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                                <i class="fas fa-file-pdf"></i>&nbsp;PDF
                            </a>
                        </div>

                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> Portifolio At Risk Report: </strong> </td>
                            </tr>
                        </table>


                        <table>

                            <?php if (@$_GET['type'] == 'branch') : ?>
                                <tr>
                                    <td width="18%"> Based On:</td>
                                    <td> <strong> Branch </strong> </td>
                                </tr>

                            <?php endif ?>

                            <?php if (@$_GET['type'] == 'officer') : ?>
                                <tr>
                                    <td width="18%"> Based On:</td>
                                    <td> <strong> Credit Officer </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_GET['type'] == 'product') : ?>
                                <tr>
                                    <td width="18%"> Based On:</td>
                                    <td> <strong> Loan Product </strong> </td>
                                </tr>
                            <?php endif ?>

                            <tr>
                                <td width="18%"> As at:</td>
                                <td> <strong> <?= @$_GET['end_date'] ?> </strong> </td>
                            </tr>


                        </table>

                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>No. of Loans</th>
                                    <th>Portifolio</th>
                                    <th>Arrears</th>
                                    <th>Arrears (>30 Days)</th>
                                    <th>Arrears (>90 Days)</th>

                                    <th>PAR(%)</th>
                                </tr>


                            </thead>
                            <tbody>
                                <?php
                                $total_portifolio = 0;
                                $total_arrears = 0;
                                $total_arrears_30 = 0;
                                $total_arrears_90 = 0;
                                $total_loans = 0;

                                $count = 0;
                                $link = '';
                                foreach ($loans as $loan) {
                                    if ($_GET['type'] == 'branch') {
                                        $link = 'report_loan_arrears.php?branchId=' . @$loan['bid'] . '&loan_status=&loan_product_id=&loan_officer_id=&disbursement_start_date=&disbursement_end_date=&days_arrears=';

                                        $link2 = 'report_loan_arrears.php?branchId=' . @$loan['bid'] . '&loan_status=&loan_product_id=&loan_officer_id=&disbursement_start_date=&disbursement_end_date=&days_arrears=30';

                                        $link3 = 'report_loan_arrears.php?branchId=' . @$loan['bid'] . '&loan_status=&loan_product_id=&loan_officer_id=&disbursement_start_date=&disbursement_end_date=&days_arrears=90';

                                        $link4 = 'report_journal_entries_loans.php?filtered=1&branch=' . @$loan['bid'] . '&authorized_by_id=0&acid=0&transaction_start_date=1900-01-01&transaction_end_date=' . date('Y-m-d') . '';
                                    }
                                    if ($_GET['type'] == 'product') {
                                        $link = 'report_loan_arrears.php?branchId=&loan_status=&loan_product_id=' . @$loan['bid'] . '&loan_officer_id=&disbursement_start_date=&disbursement_end_date=&days_arrears=';

                                        $link2 = 'report_loan_arrears.php?branchId=&loan_status=&loan_product_id=' . @$loan['bid'] . '&loan_officer_id=&disbursement_start_date=&disbursement_end_date=&days_arrears=30';

                                        $link3 = 'report_loan_arrears.php?branchId=&loan_status=&loan_product_id=' . @$loan['bid'] . '&loan_officer_id=&disbursement_start_date=&disbursement_end_date=&days_arrears=90';

                                        $link4 = '';
                                    }
                                    if ($_GET['type'] == 'officer') {
                                        $link = 'report_loan_arrears.php?branchId=&loan_status=&loan_product_id=&loan_officer_id=' . @$loan['bid'] . '&disbursement_start_date=&disbursement_end_date=&days_arrears=';

                                        $link2 = 'report_loan_arrears.php?branchId=&loan_status=&loan_product_id=&loan_officer_id=' . @$loan['bid'] . '&disbursement_start_date=&disbursement_end_date=&days_arrears=30';

                                        $link3 = 'report_loan_arrears.php?branchId=&loan_status=&loan_product_id=&loan_officer_id=' . @$loan['bid'] . '&disbursement_start_date=&disbursement_end_date=&days_arrears=90';

                                        $link4 = 'report_journal_entries_loans.php?filtered=1&branch=&authorized_by_id=' . @$loan['bid'] . '&acid=0&transaction_start_date=1900-01-01&transaction_end_date=' . date('Y-m-d') . '';
                                    }
                                ?>
                                    <tr>
                                        <td> <?= ++$count ?> </td>
                                        <td> <?= @$loan['bname']; ?> </td>
                                        <td> <?= number_format(@$loan['no_loans'] ?? 0); ?> </td>
                                        <td><a href="<?= $link4 ?>"> <?= number_format(@$loan['tot_portifolio'] ?? 0) ?></a> </td>
                                        <td><a href="<?= $link ?>"> <?= number_format(@$loan['tot_portifolio_arrears'] ?? 0) ?></a> </td>
                                        <td><a href="<?= $link2 ?>"><?= number_format(@$loan['arrears_30'] ?? 0) ?></a></td>
                                        <td><a href="<?= $link3 ?>"><?= number_format(@$loan['arrears_90'] ?? 0) ?></a></td>
                                        <td><?= number_format(((@$loan['tot_portifolio_arrears'] ?? 0) / (@$loan['tot_portifolio'] < 1 ? 1 : @$loan['tot_portifolio'])) * 100) ?></td>


                                    </tr>
                                <?php
                                    $total_portifolio += (int)(@$loan['tot_portifolio'] ?? 0);
                                    $total_loans += (int)(@$loan['no_loans'] ?? 0);
                                    $total_arrears += (int)(@$loan['tot_portifolio_arrears'] ?? 0);
                                    $total_arrears_30 += (int)(@$loan['arrears_30'] ?? 0);
                                    $total_arrears_90 += (int)(@$loan['arrears_90'] ?? 0);
                                } ?>

                                <tr>
                                    <th colspan="2">TOTALS </th>
                                    <th> <?= number_format(@$total_loans ?? 0) ?> </th>
                                    <th> <?= number_format(@$total_portifolio ?? 0) ?> </th>
                                    <th> <?= number_format(@$total_arrears ?? 0) ?> </th>
                                    <th><?= number_format(@$total_arrears_30 ?? 0) ?></th>
                                    <th><?= number_format(@$total_arrears_90 ?? 0) ?></th>
                                    <th> <?= number_format((@$total_arrears / @$total_portifolio) * 100) ?> </th>




                                </tr>
                            </tbody>
                        </table>

                        <?php
                        if (!count($loans)) {
                            require_once('./not_records_found.php');
                        }
                        ?>
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