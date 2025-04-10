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


$title = 'FINANCIAL STATEMENTS';
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
                                    Financial Statements
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#fixed" data-bs-toggle="tab" class="nav-link active">Financial Statements</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">

                                        <div id="fixed" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Financial Statements </h4>

                                                <?php if ($menu_permission->hasSubPermissions('view_trial_balance')) {
                                                    if ($menu_permission->IsBankAdmin()) {
                                                        echo '<a href="trial_balance_merged.php?bankk=' . $user[0]['bankId'] . '&branch=' . $user[0]['branchId'] . '&transaction_start_date=&transaction_end_date=' . date('Y-m-d') . '" class="btn btn-primary light btn-xs mb-1">Trial Balance</a>';

                                                        // echo '<a href="trial_balance_list.php" class="btn btn-primary light btn-xs mb-1">Trial Balance (Detailed)</a>';
                                                    } else {

                                                        echo '<a href="trial_balance_merged.php?bankk=' . $user[0]['bankId'] . '&branch=' . $user[0]['branchId'] . '&transaction_start_date=&transaction_end_date=" class="btn btn-primary light btn-xs mb-1">Trial Balance</a>';

                                                        // echo '<a href="report_trial_balance.php?branch=' . $user[0]['branchId'] . '&transaction_start_date=&transaction_end_date=" class="btn btn-primary light btn-xs mb-1">Trial Balance (Detailed)</a>';
                                                    }
                                                ?>

                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_income_statement')) {
                                                    if ($menu_permission->IsBankAdmin()) {
                                                        echo ' <a href="report_income_statement.php?bankk=' . $user[0]['bankId'] . '&branch=' . $user[0]['branchId'] . '&transaction_start_date=&transaction_end_date=' . date('Y-m-d') . '" class="btn btn-primary light btn-xs mb-1"> Income Statement</a>';
                                                    } else {
                                                        echo ' <a href="report_income_statement.php?bankk=' . $user[0]['bankId'] . '&branch=' . $user[0]['branchId'] . '&transaction_start_date=&transaction_end_date=' . date('Y-m-d') . '" class="btn btn-primary light btn-xs mb-1"> Income Statement</a>';
                                                    }
                                                ?>

                                                <?php } ?>

                                                <?php if ($menu_permission->hasSubPermissions('view_balance_sheet')) {
                                                    if ($menu_permission->IsBankAdmin()) {
                                                        echo ' <a href="report_balance_sheet.php?bankk=' . $user[0]['bankId'] . '&branch=' . $user[0]['branchId'] . '&transaction_start_date=&transaction_end_date=' . date('Y-m-d') . '" class="btn btn-primary light btn-xs mb-1"> Balance Sheet</a>';
                                                    } else {
                                                        echo ' <a href="report_balance_sheet.php?bankk=' . $user[0]['bankId'] . '&branch=' . $user[0]['branchId'] . '&transaction_start_date=&transaction_end_date=' . date('Y-m-d') . '" class="btn btn-primary light btn-xs mb-1"> Balance Sheet</a>';
                                                    }
                                                ?>

                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_journal_report')) { ?>
                                                    <a href="report_expenses.php" class="btn btn-primary light btn-xs mb-1">Expenses Report</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_journal_report')) { ?>
                                                    <a href="report_journal_entries.php" class="btn btn-primary light btn-xs mb-1">Journal Ledgers-Based Report</a>

                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_journal_report')) { ?>
                                                    <a href="journal_till.php" class="btn btn-primary light btn-xs mb-1">Journal A/C Breakdown Report</a>
                                                    <a href="report_journal_entries_pagination.php" class="btn btn-primary light btn-xs mb-1">General Ledger Report</a>

                                                    <a href="debtors_report.php" class="btn btn-primary light btn-xs mb-1">Debtors' Report</a>

                                                    <a href="creditors_report.php" class="btn btn-primary light btn-xs mb-1">Creditors' Report</a>

                                                    <a href="receivables_report.php" class="btn btn-primary light btn-xs mb-1">Receivables Report</a>
                                                    <a href="payables_report.php" class="btn btn-primary light btn-xs mb-1">Payables Report</a>
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