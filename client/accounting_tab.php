<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'ACCOUNTING';
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
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>

                                    Accounting
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">
                                        <?php if ($menu_permission->hasSubPermissions('chart_of_accounts', false, 'accounting')) { ?>
                                            <li class="nav-item"><a href="#chart_of_accounts" data-bs-toggle="tab" class="nav-link <?= @$_REQUEST['transactionsFilter'] ? '' : ' active ' ?>">Chart of Accounts</a>
                                            </li>
                                        <?php } ?>

                                        <li class="nav-item"><a href="#payables" data-bs-toggle="tab" class="nav-link">Payables</a>
                                        </li>
                                        <li class="nav-item"><a href="#receivables" data-bs-toggle="tab" class="nav-link ">Receivables</a>
                                        </li>

                                        <li class="nav-item"><a href="#journal_entries" data-bs-toggle="tab" class="nav-link ">Journal Entries</a>
                                        </li>
                                        <li class="nav-item"><a href="#journal_ledgers" data-bs-toggle="tab" class="nav-link ">Journal Ledgers</a>
                                        </li>
                                        <li class="nav-item"><a href="#bank_tool" data-bs-toggle="tab" class="nav-link ">Reconciliation Tool</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">

                                        <div id="chart_of_accounts" class="tab-pane fade <?= @$_REQUEST['transactionsFilter'] ? '' : ' show active ' ?>" role="tabpanel">
                                            <br /><br /><br />

                                            <div class="d-flex bd-highlight mb-4">
                                                <div class="me-auto p-2 bd-highlight">
                                                    <h4 class="text-primary">Chart of Accounts </h4>
                                                </div>
                                                <div class="p-2 bd-highlight">
                                                    <a href="print_chart_accounts_branch.php" class="btn btn-primary btn-sm" target="_blank">
                                                        <i class="fas fa-print"></i> Print
                                                    </a>
                                                </div>
                                            </div>

                                            <p class="m-0 subtitle">All Accounts</p><br />
                                            <?php
                                            $main_accs =    $response->getAllMainAccounts();
                                            // $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);

                                            $main_accounts = $main_accs;

                                            foreach ($main_accounts as $main_account) { ?>

                                                <div class="accordion accordion-primary" id="accordion-<?= $main_account['account_code'] ?>">
                                                    <div class="accordion-item">
                                                        <div class="accordion-header rounded-lg collapsed">
                                                            <span class="accordion-header-icon"></span>
                                                            <span class="accordion-header-text"><a href="" id="heading<?= $main_account['account_code'] ?>" data-bs-toggle="collapse" data-bs-target="#collapse<?= $main_account['account_code'] ?>" aria-controls="collapse<?= $main_account['account_code'] ?>" aria-expanded="false" role="button"> <?= $main_account['account_name'] ?> | </a>

                                                                <a href="add_sub_account.php?id=<?= $main_account['account_code'] . '&name=' . $main_account['use_name'] ?>" class="load_via_ajax"><i class="ti-plus"></i> Add</a></span>


                                                        </div>
                                                        <div id="collapse<?= $main_account['account_code'] ?>" class="collapse" aria-labelledby="heading<?= $main_account['account_code'] ?>" data-bs-parent="#accordion-<?= $main_account['account_code'] ?>">

                                                            <div class="accordion-body-text">
                                                                <div class="table-responsive">
                                                                    <table class="table table-responsive-md">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>Name</th>
                                                                                <th>Branch</th>
                                                                                <th>Actions</th>

                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $j = 1;
                                                                            foreach ($main_account['accounts'] as $account) { ?>

                                                                                <tr>
                                                                                    <td> <?= $account['account_code_used'] ?> </td>
                                                                                    <td> <?= $account['aname'] ?> </td>
                                                                                    <td> <?= $account['bname'] ?> </td>
                                                                                    <td>
                                                                                        <div class="dropdown ms-auto text-end">
                                                                                            <div class="btn-link" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                                                                        <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                                                                                        <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                                                                                        <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                                                                                    </g>
                                                                                                </svg>
                                                                                            </div>
                                                                                            <div class="dropdown-menu dropdown-menu-end" style="margin: 0px;">
                                                                                                <a class="dropdown-item" href="add_sub_sub_account.php?id=<?= $account['aid'] ?>">Add Sub Account</a>
                                                                                                <a class="dropdown-item" href="view_account_details.php?id=<?= $account['aid'] ?>">View Details</a>
                                                                                                <a class="dropdown-item text-danger confirm-action" data-href="trash_account.php?id=<?= $account['aid'] ?>">Trash Account</a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>


                                                                            <?php
                                                                            } ?>

                                                                        </tbody>

                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>


                                            <?php }

                                            ?>

                                        </div>

                                        <div id="payables" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Payables</h4>


                                            <div class="card">
                                                <div class="card-header">
                                                    <p class="m-0 subtitle">All Creditors</p>
                                                    <?php
                                                    // if (!$permissions->hasPermissions('view_everything')) :
                                                    ?>
                                                    <a href="register_creditor.php" class="btn btn-primary light btn-xs mb-1">Register Creditor</a>
                                                    <?php
                                                    //  endif;
                                                    ?>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="payable" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Creditor</th>
                                                                    <th>Chart Account</th>
                                                                    <th>Total Payables</th>
                                                                    <th>Total Paid</th>
                                                                    <th>Outstanding</th>
                                                                    <th>Branch</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>




                                                            </tbody>
                                                        </table>
                                                    </div>



                                                </div>
                                            </div>





                                            <!-- </div> -->
                                        </div>

                                        <div id="receivables" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Receivables</h4>


                                            <div class="card">
                                                <div class="card-header">
                                                    <p class="m-0 subtitle">All Institution Debtors</p>
                                                    <?php
                                                    // if ($permissions->hasPermissions('manage_receivables')) :
                                                    ?>
                                                    <a href="register_debtor.php" class="btn btn-primary light btn-xs mb-1">Register Debtor</a>
                                                    <?php
                                                    //  endif;
                                                    ?>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="debtor" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Debtor</th>
                                                                    <th>Chart Account</th>
                                                                    <th>Receivables</th>
                                                                    <th>Total Paid</th>
                                                                    <th>Balance</th>
                                                                    <th>Branch</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>




                                                            </tbody>
                                                        </table>
                                                    </div>



                                                </div>
                                            </div>

                                            <!-- </div> -->
                                        </div>

                                        <div id="journal_entries" class="tab-pane fade ">
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
                                        <div id="bank_tool" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Journal Ledgers</h4> -->

                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Bank Reconciliation Tool</h4>
                                                <a href="bank_reconciliation_tool.php" class="btn btn-primary light btn-xs mb-1">Bank Reconciliation Tool</a>
                                                <a href="reconciliation_tool_all.php" class="btn btn-primary light btn-xs mb-1">Safe Reconciliation Tool</a>
                                                <a href="teller_till_sheet.php" class="btn btn-primary light btn-xs mb-1">Teller Till Sheet</a>
                                                <a href="journal_till.php" class="btn btn-primary light btn-xs mb-1">Journal A/C Breakdown Report</a>
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




        <script type="text/javascript">
            $(document).ready(function() {

                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_payables.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['branchId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bindPayables(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function bindPayables(data) {

                var table = $('#payable').dataTable({
                    destroy: true,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },

                    "aaData": data,

                    "columns": [{
                            "data": "id"
                        }, {
                            "data": "name"
                        }, {
                            "data": "chart"
                        }, {
                            "data": "payable"
                        }, {
                            "data": "paid"
                        }, {
                            "data": "oustanding"
                        },
                        {
                            "data": "branch"
                        }, {
                            "data": "actions",
                        }
                    ]
                })

            }
        </script>


        <script type="text/javascript">
            $(document).ready(function() {

                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_debtors.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['branchId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bindReceivables(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function bindReceivables(data) {

                var table = $('#debtor').dataTable({
                    destroy: true,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },

                    "aaData": data,

                    "columns": [{
                            "data": "id"
                        }, {
                            "data": "name"
                        }, {
                            "data": "chart"
                        }, {
                            "data": "payable"
                        }, {
                            "data": "paid"
                        }, {
                            "data": "oustanding"
                        },
                        {
                            "data": "branch"
                        }, {
                            "data": "actions",
                        }
                    ]
                })

            }
        </script>

        <script>
            $(document).ready(function() {
                // SmartWizard initialize
                $('#smartwizard').smartWizard();
            });
        </script>


</body>

</html>