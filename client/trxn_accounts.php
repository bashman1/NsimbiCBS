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

include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {
    $res = $response->addBankAccount($_POST['name'], $_POST['id'], $_POST['bname'], $_POST['acno'], $_POST['branch'], $_POST['account_id']);
    if ($res) {
        setSessionMessage(true, 'Bank Chart Account Created Successfully!');
        header('location:trxn_accounts.php');
        exit;
    } else {
        setSessionMessage(false, 'Process failed. Try again!');
        header('location:trxn_accounts.php');
        exit;
    }

    // header('location:all_banks');
}

if (isset($_POST['submitc'])) {
    $res = $response->addCashAccount($_POST['bname'], $_POST['branch'], $_POST['account_id']);
    if ($res) {
        setSessionMessage(true, 'Cash Chart Account Successfully Created!');
        header('location:trxn_accounts.php');
        exit;
    } else {
        setSessionMessage(false, 'Cash A/C not created!');
        header('location:trxn_accounts.php');
        exit;
    }

    // header('location:all_banks');
}

if (isset($_POST['submits'])) {
    $res = $response->addSafeAccount($_POST['bname'], $_POST['branch'], $_POST['account_id']);
    if ($res) {
        setSessionMessage(true, 'Safe Chart Account Successfully Created!');
        header('location:trxn_accounts.php');
        exit;
    } else {
        setSessionMessage(false, 'Safe Chart Account not Created!');
        header('location:trxn_accounts.php');
        exit;
    }

    // header('location:all_banks');
}
$title = 'Accounts';
require_once('includes/head_tag.php');
$branches = $response->getBankBranches($user[0]['bankId']);
$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);

$accounts = $response->getSubAccounts2($_SESSION['user']['branchId'], $_SESSION['user']['bankId']);

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
                                    Transaction Accounts
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#chart_of_accounts" data-bs-toggle="tab" class="nav-link active">Bank Accounts</a>
                                        </li>
                                        <li class="nav-item"><a href="#transactions" data-bs-toggle="tab" class="nav-link ">Cash Accounts</a>
                                        </li>
                                        <li class="nav-item"><a href="#tillsheet" data-bs-toggle="tab" class="nav-link ">Safe Accounts</a>
                                        </li>

                                        <li class="nav-item"><a href="#payables" data-bs-toggle="tab" class="nav-link">Cash Transfers</a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">

                                        <div id="chart_of_accounts" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Bank Accounts </h4>

                                            <div class="card">
                                                <div class="card-header">
                                                    <p class="m-0 subtitle">All Bank Accounts</p>
                                                    <a href="bank_reconciliation_tool.php" class="btn btn-primary light btn-xs mb-1">Bank Reconciliation Tool</a>
                                                    <?php
                                                    // if(!$permissions->hasPermissions('view_everything')):
                                                    ?>
                                                    <a class="btn btn-primary light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">Add New Bank Account</a>
                                                    <?php
                                                    // endif;
                                                    ?>
                                                </div>
                                                <div class="card-body">



                                                    <div class="table-responsive">
                                                        <table id="bankaccs" class="table table-striped" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Associated Bank</th>
                                                                    <th>Account Name</th>
                                                                    <th>A/C No</th>
                                                                    <th>Balance</th>
                                                                    <th>Branch</th>

                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>




                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div id="transactions" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Cash Accounts</h4>
                                            <div class="card">
                                                <div class="card-header">
                                                    <p class="m-0 subtitle">All Cash Accounts</p>
                                                    <?php
                                                    // if(!$permissions->hasPermissions('view_everything')):
                                                    ?>
                                                    <a class="btn btn-primary light btn-xs mb-1" href="teller_till_sheet.php">Till Sheet</a>
                                                    <a class="btn btn-primary light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg4">Add New Cash Account</a>
                                                    <?php
                                                    // endif;
                                                    ?>
                                                </div>
                                                <div class="card-body">



                                                    <div class="table-responsive">
                                                        <table id="cashacc" class="table table-striped" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Associated Staff</th>
                                                                    <th>Currency</th>
                                                                    <th>Balance</th>

                                                                    <th>Branch</th>

                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>




                                                            </tbody>
                                                        </table>
                                                    </div>





                                                </div>
                                            </div>
                                        </div>

                                        <div id="tillsheet" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Safe Accounts</h4>
                                            <div class="card">
                                                <div class="card-header">
                                                    <p class="m-0 subtitle">All Safe Accounts</p>
                                                    <?php
                                                    // if(!$permissions->hasPermissions('view_everything')):
                                                    ?>
                                                    <a class="btn btn-primary light btn-xs mb-1" href="reconciliation_tool_all.php">Reconciliation Tool</a>
                                                    <a class="btn btn-primary light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg5">Add New
                                                        Safe Account</a>
                                                    <?php
                                                    // endif;
                                                    ?>
                                                </div>
                                                <div class="card-body">



                                                    <div class="table-responsive">
                                                        <table id="safeacc" class="table table-striped" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Branch</th>
                                                                    <th>Balance</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>




                                                            </tbody>
                                                        </table>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>

                                        <div id="payables" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Cash Transfers </h4>
                                                <?php if ($permissions->hasSubPermissions('safe_to_teller')) { ?>
                                                    <a href="safe_to_teller.php" class="btn btn-primary light btn-xs mb-1">Safe to Teller</a>
                                                <?php } ?>
                                                <?php if ($permissions->hasSubPermissions('teller_to_safe')) { ?>
                                                    <a href="teller_to_safe.php" class="btn btn-primary light btn-xs mb-1">Teller to Safe</a>
                                                <?php } ?>

                                                <a href="teller_to_teller.php" class="btn btn-primary light btn-xs mb-1">Teller to Teller</a>

                                                <?php if ($permissions->hasSubPermissions('safe_to_safe')) { ?>
                                                    <a href="safe_to_safe.php" class="btn btn-primary light btn-xs mb-1">Safe to Safe</a>
                                                <?php } ?>
                                                <?php if ($permissions->hasSubPermissions('bank_to_safe')) { ?>
                                                    <a href="bank_to_safe.php" class="btn btn-primary light btn-xs mb-1">Bank to Safe</a>
                                                <?php } ?>
                                                <?php if ($permissions->hasSubPermissions('safe_to_bank')) { ?>
                                                    <a href="safe_to_bank.php" class="btn btn-primary light btn-xs mb-1">Safe to Bank</a>
                                                <?php } ?>
                                                <?php if ($permissions->hasSubPermissions('bank_to_bank')) { ?>
                                                    <a href="bank_to_bank.php" class="btn btn-primary light btn-xs mb-1">Bank to Bank</a>
                                                <?php } ?>
                                                <?php if ($permissions->hasSubPermissions('inter_branch')) { ?>
                                                    <a href="inter_branch_requests.php" class="btn btn-primary light btn-xs mb-1">Inter-Branch Request</a>
                                                <?php } ?>
                                                <?php
                                                // endif;
                                                ?>

                                                <a href="cash_transfers.php?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>" class="btn btn-primary light btn-xs mb-1">List Cash Transfers</a>
                                                <a href="cash_transfers_report.php" class="btn btn-primary light btn-xs mb-1">Cash Transfers Report</a>
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

            <div class="modal fade bd-example-modal-lg3">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Bank Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $user[0]['bankId']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Associated Bank Name*
                                    </label>
                                    <input type="text" name="bname" class="form-control" placeholder="">
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Account Belongs to Which Branch*
                                    </label>
                                    <?php
                                    echo '
                                <select class="me-sm-2 default-select form-control wide"
                                id="branchselect" name="branch"
                                style="display: none;" >
    <option value="0">None</option>
        ';
                                    if ($branches !== '') {
                                        foreach ($branches as $row) {
                                            echo '
    <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
    
    ';
                                        }
                                    } else {
                                        echo '
    <option readonly>No Branches Added yet</option>
    ';
                                    }

                                    echo
                                    '

    </select>
                                ';
                                    ?>

                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Parent Account *</label>

                                    <select id="osector" class="me-sm-2 default-select form-control wide" name="account_id" style="display: none;" required>
                                        <option> Select </option>
                                        <?php foreach ($accounts as $account) { ?>
                                            <option value="<?= $account['id'] ?>">
                                                <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Account Name*
                                    </label>
                                    <input type="text" name="name" class="form-control" placeholder="">
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">A/C No*
                                    </label>
                                    <input type="text" name="acno" class="form-control" placeholder="">
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="submit" class="btn btn-primary">Create Account</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>


            <div class="modal fade bd-example-modal-lg4">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Cash Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Parent Account *</label>

                                    <select id="ocategory" name="account_id" required class="me-sm-2 default-select form-control wide" style="display: none;">
                                        <option> Select </option>
                                        <?php foreach ($accounts as $account) { ?>
                                            <option value="<?= $account['id'] ?>">
                                                <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Cash A/C Name*
                                    </label>
                                    <input type="text" name="bname" class="form-control" placeholder="">
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Assign Account to Staff:*
                                    </label>
                                    <?php
                                    echo '
                                <select id="payment_methods" name="branch" class="me-sm-2 default-select form-control wide" style="display: none;">
    <option value="0">None</option>
        ';
                                    if ($staffs !== '') {
                                        foreach ($staffs as $row) {
                                            echo '
    <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] . '</option>
    
    ';
                                        }
                                    } else {
                                        echo '
    <option readonly>No Staffs Added yet</option>
    ';
                                    }

                                    echo
                                    '

    </select>
                                ';
                                    ?>

                                </div>


                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="submitc" class="btn btn-primary">Create Account</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>



            <div class="modal fade bd-example-modal-lg5">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Safe Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">


                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Parent Account *</label>

                                    <select id="oscategory" name="account_id" required class="form-control">
                                        <option> Select </option>
                                        <?php foreach ($accounts as $account) { ?>
                                            <option value="<?= $account['id'] ?>">
                                                <?= $account['name'] . ' - (' . strtoupper($account['branch']) . ')' ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Reserve Name*
                                    </label>
                                    <input type="text" name="bname" class="form-control" placeholder="">
                                </div>

                                <?php
                                if (!$user[0]['branchId']) {


                                    echo '
                          <div class="col-lg-6 mb-2">
                          <div class="mb-3">
                              <label class="text-label form-label">Branch *</label>
                              <select class="me-sm-2 default-select form-control wide"
                              id="clientsselect" name="branch"
                              style="display: none;" >
                              <option value="0">None</option>
                                  ';
                                    if ($branches !== '') {
                                        foreach ($branches as $row) {
                                            echo '
                              <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
                              
                              ';
                                        }
                                    } else {
                                        echo '
                              <option readonly>No Branches Added yet</option>
                              ';
                                    }

                                    echo
                                    '
                          
                              </select>
                          </div>
                          </div>
                          
                          ';
                                } else {
                                    echo '

                            <input type="hidden" name="branch" value="' . $user[0]['branchId'] . '" class="form-control" >

                            
                            ';
                                }
                                ?>
                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="submits" class="btn btn-primary">Create Account</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

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
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_bank_accounts.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bindtoDatatable(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function bindtoDatatable(data) {

                var table = $('#bankaccs').dataTable({
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
                        "data": "bank"
                    }, {
                        "data": "acname"
                    }, {
                        "data": "acno"
                    }, {
                        "data": "acc_balance"
                    }, {
                        "data": "branch"
                    }, {
                        "data": "status"
                    }, {
                        "data": "actions",
                    }]
                })

            }
        </script>


        <script type="text/javascript">
            $(document).ready(function() {

                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_cash_accounts.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bindtoDatatable2(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function bindtoDatatable2(data) {

                var table = $('#cashacc').dataTable({
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
                            "data": "acname"
                        }, {
                            "data": "staff"
                        }, {
                            "data": "currency"
                        },
                        {
                            "data": "balance"
                        },
                        {
                            "data": "branch"
                        }, {
                            "data": "status"
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
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_safe_accounts.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bindtoDatatable3(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function bindtoDatatable3(data) {

                var table = $('#safeacc').dataTable({
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
                            "data": "acname"
                        }, {
                            "data": "branch"
                        },
                        {
                            "data": "balance"
                        },

                        {
                            "data": "status"
                        }, {
                            "data": "actions",
                        }
                    ]
                })

            }
        </script>
</body>

</html>