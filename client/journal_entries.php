<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'JOURNAL ENTRIES';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();



// var_dump($branches);

// exit;

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

                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>

                                    Journal Entries
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">



                                        <li class="nav-item"><a href="#journal_entries" data-bs-toggle="tab" class="nav-link active">Journal Entries</a>
                                        </li>
                                        <li class="nav-item"><a href="#journal_ledgers" data-bs-toggle="tab" class="nav-link ">Journal Ledgers</a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">


                                        <div id="journal_entries" class="tab-pane fade show active">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Journal Entries</h4> -->

                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Journal Entries</h4>
                                                <?php
                                                // if (!$permissions->hasPermissions('view_everything')) :
                                                ?>
                                                <a href="register_income.php" class="btn btn-primary light btn-xs mb-1">Register Income</a>
                                                <a href="register_expense.php" class="btn btn-primary light btn-xs mb-1">Register Expenses</a>
                                                <a href="register_capital.php" class="btn btn-primary light btn-xs mb-1">Register Capital</a>
                                                <a href="register_liability.php" class="btn btn-primary light btn-xs mb-1">Register Liability</a>
                                                <a href="register_asset.php" class="btn btn-primary light btn-xs mb-1">Register Asset</a>
                                                <a href="advanced_journal_entry.php" class="btn btn-primary light btn-xs mb-1">Advanced Journal Entry</a>
                                                <?php
                                                //  endif;
                                                ?>
                                            </div>



                                            <!-- </div> -->
                                        </div>

                                        <div id="journal_ledgers" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Journal Ledgers</h4> -->
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Journal Ledgers</h4>
                                                <a href="income_ledger.php" class="btn btn-primary light btn-xs mb-1">Income
                                                    Ledger</a>
                                                <a href="expense_ledger.php" class="btn btn-primary light btn-xs mb-1">Expenses Ledger</a>
                                                <a href="capital_ledger.php" class="btn btn-primary light btn-xs mb-1">Capital Ledger</a>
                                                <a href="liability_ledger.php" class="btn btn-primary light btn-xs mb-1">Liabilities Ledger</a>
                                                <a href="assets_ledger.php" class="btn btn-primary light btn-xs mb-1">Assets Ledger</a>
                                                <a href="advanced_ledger.php" class="btn btn-primary light btn-xs mb-1">Advanced Journal Entries</a>
                                                <?php if ($menu_permission->hasSubPermissions('view_transactions', false, 'accounting')) { ?>
                                                    <a href="general_ledger.php" class="btn btn-primary light btn-xs mb-1">General Journal Ledger</a>
                                                <?php } ?>
                                                <?php if ($menu_permission->hasSubPermissions('view_transactions', false, 'accounting')) { ?>
                                                    <a href="day_book_report.php" class="btn btn-primary light btn-xs mb-1">View All Transactions</a>
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

        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>


</body>

</html>