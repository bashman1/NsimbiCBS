<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}


$title = 'LOAN REPORTS';
require_once('includes/head_tag.php');
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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Loan Reports
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#fixed" data-bs-toggle="tab" class="nav-link active">Loan Reports</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">

                                        <div id="fixed" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Loan Reports </h4>



                                                <?php if ($menu_permission->hasSubPermissions('view_loan_status_report')) { ?>
                                                    <a href="report_loan_status.php" class="btn btn-primary light btn-xs mb-1">Loan Status Report</a>
                                                    <a href="report_journal_entries_loans.php?filtered=1&branch=0&authorized_by_id=0&acid=&transaction_start_date=<?= date('Y-m-d') ?>&transaction_end_date=<?= date('Y-m-d') ?>" class="btn btn-primary light btn-xs mb-1">Loan Ledger Report</a>
                                                    <a href="report_disbursement_filters.php" class="btn btn-primary light btn-xs mb-1">Loan Disbursement Report (Excel)</a>
                                                    <a href="report_loan_disbursement.php" class="btn btn-primary light btn-xs mb-1">Loan Disbursement Report(PDF)</a>
                                                    <a href="staff_filter.php" class="btn btn-primary light btn-xs mb-1">Oustanding Loans Report</a>
                                                    <a href="staff_filter_2.php" class="btn btn-primary light btn-xs mb-1">Loans Ageing Report (Detailed)</a>

                                                    <a href="to_be_written_off_loans.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>" class="btn btn-primary light btn-xs mb-1">Loans to be Written Off</a>
                                                    <a href="written_off_loans.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>" class="btn btn-primary light btn-xs mb-1">Written Off Loans</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_loan_arrears_report')) { ?>
                                                    <a href="report_loan_arrears.php" class="btn btn-primary light btn-xs mb-1">Loan Arrears Report</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_ageing_report')) { ?>
                                                    <a href="report_ageing.php" class="btn btn-primary light btn-xs mb-1">Ageing Report (Principal Balance)</a>
                                                    <a href="report_ageing_arr.php" class="btn btn-primary light btn-xs mb-1">Ageing Report (Principal Arrears)</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_par_report')) {

                                                    if ($user[0]['branchId']) {
                                                        echo '  <a href="report_par.php?branchId=' . $user[0]['branchId'] . '" class="btn btn-primary light btn-xs mb-1">Portfolio At Risk</a>';
                                                        echo '
                                                          <a href="pars_new.php" class="btn btn-primary light btn-xs mb-1">PAR</a>
                                                        ';
                                                    } else {
                                                        echo '
                                                          <a href="report_par.php" class="btn btn-primary light btn-xs mb-1">Portfolio At Risk</a>
                                                        ';
                                                        echo '
                                                          <a href="pars_new.php" class="btn btn-primary light btn-xs mb-1">PAR</a>
                                                        ';
                                                    }
                                                ?>


                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_par_aging_report')) { ?>
                                                    <a href="report_par_ageing.php" class="btn btn-primary light btn-xs mb-1">Par - Ageing Report</a>
                                                <?php } ?>

                                                <?php if ($menu_permission->hasSubPermissions('view_credit_officers_report')) { ?>
                                                    <a href="report_credit_officers.php" class="btn btn-primary light btn-xs mb-1">Credit Officer's Report</a>




                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_loan_status_report')) { ?>
                                                    <a href="report_loan_repayments.php" class="btn btn-primary light btn-xs mb-1">Loan Repayments Report</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_loan_status_report')) { ?>
                                                    <a href="interest_waiver_stmt.php" class="btn btn-primary light btn-xs mb-1">Interest Waiver Report</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_loan_status_report')) { ?>
                                                    <a href="penalty_waiver_stmt.php" class="btn btn-primary light btn-xs mb-1">Penalty Waiver Report</a>
                                                <?php } ?>

                                            </div>
                                            <!-- </div> -->
                                        </div>




                                    </div>
                                    <!-- </div> -->
                                    <!-- Modal -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!--**********************************
            Content body end
        ***********************************-->

            <!--**********************************
            Footer start
        ***********************************-->
            <?php include('includes/footer.php'); ?>
            <!--**********************************
            Footer end
        ***********************************-->

            <!--**********************************
           Support ticket button start
        ***********************************-->

            <!--**********************************
           Support ticket button end
        ***********************************-->


        </div>
        <!--**********************************
        Main wrapper end
    ***********************************-->

        <!--**********************************
        Scripts
    ***********************************-->
        <!-- Required vendors -->
        <?php include('includes/bottom_scripts.php'); ?>
</body>

</html>