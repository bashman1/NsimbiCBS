<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasPermissions('accounting')) {
    return $permissions->isNotPermitted(true);
}

require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();


$branches = $response->getBankBranches($user[0]['bankId']);
$staffs = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);

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

                                        <?php if ($menu_permission->hasSubPermissions('view_transactions', false, 'accounting')) { ?>
                                            <li class="nav-item"><a href="#transactions" data-bs-toggle="tab" class="nav-link <?= @$_REQUEST['transactionsFilter'] ? ' active ' : '' ?>">Transactions</a>
                                            </li>
                                        <?php } ?>

                                        <li class="nav-item"><a href="#tillsheet" data-bs-toggle="tab" class="nav-link ">Staff Till Sheet</a>
                                        </li>

                                        <li class="nav-item"><a href="#payables" data-bs-toggle="tab" class="nav-link">Payables</a>
                                        </li>
                                        <li class="nav-item"><a href="#receivables" data-bs-toggle="tab" class="nav-link ">Receivables</a>
                                        </li>

                                        <li class="nav-item"><a href="#journal_entries" data-bs-toggle="tab" class="nav-link ">Journal Entries</a>
                                        </li>
                                        <li class="nav-item"><a href="#journal_ledgers" data-bs-toggle="tab" class="nav-link ">Journal Ledgers</a>
                                        </li>
                                        <li class="nav-item"><a href="#bank_tool" data-bs-toggle="tab" class="nav-link ">Bank Reconciliation Tool</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">

                                        <div id="chart_of_accounts" class="tab-pane fade <?= @$_REQUEST['transactionsFilter'] ? '' : ' show active ' ?>" role="tabpanel">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <div class="d-flex bd-highlight mb-4">
                                                <h4 class="text-primary w-100 bd-highlight">Chart of Accounts </h4>
                                                <div class="flex-shrink-1 bd-highlight">
                                                </div>
                                            </div> -->

                                            <div class="d-flex bd-highlight mb-4">
                                                <div class="me-auto p-2 bd-highlight">
                                                    <h4 class="text-primary">Chart of Accounts </h4>
                                                </div>
                                                <div class="p-2 bd-highlight">
                                                    <a href="export_report?exportFile=export_chart_of_accounts&useFile=1" class="btn btn-primary btn-sm" target="_blank">
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

                                                                <a href="add_sub_account?id=<?= $main_account['account_code'] . '&name=' . $main_account['use_name'] ?>" class="load_via_ajax"><i class="ti-plus"></i> Add</a></span>


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
                                                                                    <td> <?= $j++ ?>. </td>
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
                                                                                                <a class="dropdown-item" href="add_sub_sub_account?id=<?= $account['aid'] ?>">Add Sub Account</a>
                                                                                                <a class="dropdown-item" href="view_account_details?id=<?= $account['aid'] ?>">View Details</a>
                                                                                                <a class="dropdown-item text-danger confirm-action" data-href="trash_account?id=<?= $account['aid'] ?>">Trash Account</a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>

                                                                                </tr>

                                                                                <?php if (count($account['sub_accounts'])) { ?>
                                                                                    <tr>
                                                                                        <td colspan="4">
                                                                                            <div class="card transparent-card">
                                                                                                <div class="card-header d-block">
                                                                                                    <h4 class="card-title" style="font-size: 14px;font-weight: bold;">
                                                                                                        SUB ACCOUNTS FOR, <?= $account['aname'] ?>
                                                                                                    </h4>
                                                                                                </div>
                                                                                                <div class="card-body">

                                                                                                    <table class="w-100">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th style="width:1px;">#</th>
                                                                                                                <th> Name </th>
                                                                                                                <th class="text-end"> Actions</th>
                                                                                                            </tr>
                                                                                                        </thead>

                                                                                                        <tbody>
                                                                                                            <?php
                                                                                                            $k = 1;
                                                                                                            foreach ($account['sub_accounts'] as $sub_account) { ?>
                                                                                                                <td> <?= $k++ ?>. </td>
                                                                                                                <td> <?= $sub_account['name'] ?> </td>

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
                                                                                                                            <a class="dropdown-item" href="view_account_details?id=<?= $sub_account['id'] ?>">View Details</a>
                                                                                                                            <a class="dropdown-item text-danger confirm-action" data-href="trash_account?id=<?= $sub_account['id'] ?>">Trash Account</a>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                            <?php
                                                                                                            } ?>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php } ?>

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







                                            <!-- </div> -->
                                        </div>

                                        <div id="transactions" class="tab-pane fade <?= @$_REQUEST['transactionsFilter'] ? ' show active ' : '' ?>">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Transactions</h4>

                                            <div class="card">
                                                <div class="card-body">

                                                    <p class="text-muted mb-3">Filters</p>

                                                    <form class="ajax_results_form" method="post">
                                                        <input type="hidden" name="transactionsFilter" value="1">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">

                                                                    <label class="text-label form-label">Branch *</label>
                                                                    <?php if ($_SESSION['session_user']['branchName']) { ?>
                                                                        <div>
                                                                            <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                                                        </div>
                                                                    <?php } else { ?>
                                                                        <select class="me-sm-2 default-select- form-control wide" name="branchId" style="display: block;">
                                                                            <option value=""> All</option>
                                                                            <?php

                                                                            $default_selected = @$_REQUEST['branchId'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                                            if ($user[0]['branchId']) { ?>
                                                                                <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                                                ';
                                                                            <?php } ?>

                                                                            <?php
                                                                            if ($branches !== '') {
                                                                                foreach ($branches as $row) {
                                                                                    $is_seleceted = @$_REQUEST['branchId'] == $row['id'] ? "selected" : "";
                                                                            ?>
                                                                                    <option value="<?= @$row['id'] ?>" <?= $is_seleceted ?>>
                                                                                        <?= $row['name'] ?>
                                                                                    </option>
                                                                            <?php }
                                                                            } ?>

                                                                        </select>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>


                                                            <div class="col-md-2">
                                                                <div class="form-group">

                                                                    <label class="text-label form-label">
                                                                        Select Transaction Type *
                                                                    </label>

                                                                    <select class="me-sm-2 default-select- form-control wide" name="transaction_type" style="display: block;">
                                                                        <option value="">All </option>
                                                                        <option value="L" <?= $_REQUEST['transaction_type'] == 'L' ? 'selected' : '' ?>>Loan Repayments</option>

                                                                        <option value="A" <?= $_REQUEST['transaction_type'] == 'A' ? 'selected' : '' ?>>Loan Disbursements</option>

                                                                        <option value="LP" <?= $_REQUEST['transaction_type'] == 'LP' ? 'selected' : '' ?>>Loan Disbursement Charges</option>

                                                                        <option value="LP" <?= $_REQUEST['transaction_type'] == 'LP' ? 'selected' : '' ?>>Loan Penalty</option>

                                                                        <option value="D" <?= $_REQUEST['transaction_type'] == 'D' ? 'selected' : '' ?>>Deposits</option>

                                                                        <option value="W" <?= $_REQUEST['transaction_type'] == 'W' ? 'selected' : '' ?>>Withdraws</option>

                                                                        <option value="I" <?= $_REQUEST['transaction_type'] == 'I' ? 'selected' : '' ?>>Other Incomes</option>

                                                                        <option value="E" <?= $_REQUEST['transaction_type'] == 'E' ? 'selected' : '' ?>>Expenses</option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="form-group">

                                                                    <label class="text-label form-label">Trxn Method *</label>

                                                                    <select name="transaction_method" class="me-sm-2 default-select- form-control wide" style="display: block;">
                                                                        <option value="">All</option>
                                                                        <option value="cash" <?= $_REQUEST['transaction_method'] == 'cash' ? 'selected' : '' ?>>Cash</option>
                                                                        <option value="bank" <?= $_REQUEST['transaction_method'] == 'bank' ? 'selected' : '' ?>>Cheque/Bank Account/Mobile Money</option>
                                                                        <option value="savings" <?= $_REQUEST['transaction_method'] == 'savings' ? 'selected' : '' ?>>Via Savings A/C</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">

                                                                    <label class="text-label form-label">Sub-Account *</label>

                                                                    <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="sub_account_id">
                                                                        <option value=""> All </option>
                                                                        <?php
                                                                        $sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);
                                                                        if ($sub_accs) {
                                                                            foreach ($sub_accs as $acc) { ?>
                                                                                <option value="<?= $acc['id'] ?> <?= $_REQUEST['sub_account_id'] == $acc['id'] ? 'selected' : '' ?>"><?= $acc['name'] ?></option>
                                                                        <?php }
                                                                        }
                                                                        ?>

                                                                    </select>


                                                                </div>
                                                            </div>
                                                        </div><br />
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="text-label form-label">Start
                                                                        Date *</label>
                                                                    <input type="date" class="form-control" name="start_date" value="<?php $_REQUEST['start_date']; ?>" id="exampleInputEmail3" placeholder="Start Date">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="text-label form-label">End
                                                                        Date *</label>
                                                                    <input type="date" class="form-control" name="end_date" value="<?php $_REQUEST['end_date']; ?>" placeholder="End Date">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label class="text-label form-label"> </label>
                                                                    <button type="submit" class="btn btn-primary form-control">Fetch
                                                                        Entries</button>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </form>


                                                </div>
                                            </div>


                                            <div class="card">
                                                <div class="card-header">
                                                    <h1 style="font-size: 16px"> <small>All Transactions </small></h1>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="transactions_table" class="display fixed-layout" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Date</th>
                                                                    <th>Description</th>
                                                                    <th>Amount ( UGX )</th>
                                                                    <th>Account</th>
                                                                    <th>Vendor</th>
                                                                    <th>Entry Type</th>
                                                                    <th>Entered by</th>
                                                                    <th>Branch</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Date</th>
                                                                    <th>Description</th>
                                                                    <th>Amount ( UGX )</th>
                                                                    <th>Account</th>
                                                                    <th>Vendor</th>
                                                                    <th>Entry Type</th>
                                                                    <th>Entered by</th>
                                                                    <th>Branch</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>


                                                </div>

                                            </div>
                                        </div>

                                        <div id="tillsheet" class="tab-pane fade">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Staff Till Sheet</h4>

                                            <h1 style="font-size: 16px"> <small>Filter By Teller Account & Date
                                                    Range </small></h1>
                                            <br>

                                            <form class="form-inlines select_datess ajax_results_form" method="post" id="filterBydates">

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="sr-onlys" for="single-select">Select
                                                                Teller Account
                                                                <i>*</i>: </label>
                                                            <select id="branchselect" class=" form-control" name="cash_account">

                                                                <?php
                                                                $cash_accounts = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
                                                                if ($cash_accounts) {
                                                                    foreach ($cash_accounts as $c_acc) {

                                                                        echo '<option value="' . $c_acc['id'] . '">' . $c_acc['name'] . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">

                                                        <div class="form-group">
                                                            <label class="sr-onlys" for="exampleInputEmail3">Start
                                                                Date</label>
                                                            <input type="date" class="form-control" name="from_date" value="<?= isset($_POST['from_date']) ? $_POST['from_date'] : date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label class="sr-onlys" for="exampleInputPassword3">End
                                                                Date</label>
                                                            <input type="date" class="form-control" name="to_date" value="<?= isset($_POST['to_date']) ? $_POST['to_date'] : date('Y-m-d'); ?>" id="exampleInputPassword3" placeholder="End Date">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="sr-onlys" for="fetch">&nbsp;&nbsp;</label>
                                                            <button type="submit" class="btn btn-primary light btn-xs mb-1 form-control" name="submit">Fetch
                                                                Transactions</button>
                                                        </div>
                                                    </div>

                                                </div>

                                            </form>


                                            <!-- </div> -->
                                            <!-- </div> -->

                                            <?php if (isset($_POST['submit'])) : ?>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title text-primary">Till Sheet Journal Entries :
                                                            <?php echo date('Y-m-d', strtotime($_POST['from_date'])) . '   -   ' . date('Y-m-d', strtotime($_POST['to_date'])) ?>
                                                        </h4>

                                                        <button class="btn btn-primary" onclick="PrintContent('exreportn')">Print</button>
                                                    </div>
                                                    <div class="card-body" id="exreportn">
                                                        <!--<div class="table-responsive">-->
                                                        <?php
                                                        $start = $_POST['from_date'];
                                                        $end = $_POST['to_date'];
                                                        $staff = $_POST['cash_account'];



                                                        $details = $response->getStaffTillEntries($start, $end, $staff);


                                                        ?>

                                                        <center style="font-size:15px">
                                                            <img src="<?php echo is_null($user[0]['blogo']) ? 'icons/favicon.png' : $user[0]['blogo']; ?>" width="10%" onerror="this.onerror=null; this.src='icons/favicon.png'">
                                                            <h4 style="line-height:1.0em"> <b>
                                                                    <?= is_null($user[0]['bankName']) ? '' : strtoupper($user[0]['bankName']); ?>
                                                                </b> </h4>
                                                            <p style="line-height:1.0em;font-weight:bold">Location:
                                                                <?php echo is_null($user[0]['blocation']) ? '' : $user[0]['blocation']; ?>
                                                            </p>
                                                            <p style="line-height:1.0em;font-weight:bold"> Tel:
                                                                <?php echo is_null($user[0]['bcontacts']) ? '' : $user[0]['bcontacts']; ?>
                                                            </p>
                                                            <p style="line-height:1.0em;font-weight:bold"> Email:
                                                                <?php echo is_null($user[0]['bemail']) ? '' : $user[0]['bemail']; ?>
                                                            </p>
                                                        </center><br /><br />

                                                        <br />
                                                        <?php
                                                        if ($details) {
                                                        ?>


                                                            <table class="table verticle-middle table-responsive-md">
                                                                <thead>

                                                                    <tr>
                                                                        <th colspan="7" style="text-align:center;">
                                                                            <h4 class="page-title">Till Sheet Journal Entries
                                                                                Report
                                                                            </h4>
                                                                        </th>
                                                                    </tr>

                                                                    <tr style="text-align: center !important;">
                                                                        <th>#</th>
                                                                        <th>CHART ACCOUNT:</th>

                                                                        <th>DR:</th>
                                                                        <th>CR:</th>
                                                                        <th>BALANCE:</th>

                                                                        <th>REFERENCE NO:</th>
                                                                        <th>DATE:</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $count = 1;
                                                                    $ccount = 0;
                                                                    $dcount = 0;
                                                                    $val = 0;
                                                                    $ctotal = 0;
                                                                    $dtotal = 0;
                                                                    foreach ($details as $deposit) {
                                                                        $trxn_date = date('Y-m-d', strtotime($deposit['_date_created']));

                                                                        if (
                                                                            $deposit['type'] == "W" or $deposit['type'] == "LE" or $deposit['type'] == "C" or $deposit['type'] == "CW" or $deposit['type'] == "CS" or $deposit['type'] == "SMS" or
                                                                            $deposit['type'] == "LP" or
                                                                            $deposit['type'] == "RC" or  $deposit['type'] == "E"
                                                                        ) {
                                                                            $credit = number_format($deposit['_amount']);
                                                                            $debit = "-";
                                                                            $val = $val - $deposit['_amount'];
                                                                            $dtotal = $dtotal + $deposit['_amount'];
                                                                            $ccount++;
                                                                        }
                                                                        if (
                                                                            $deposit['type'] == "L"
                                                                        ) {
                                                                            $credit = number_format($deposit['_amount'] + @$deposit['loan_interest']);
                                                                            $debit = "-";
                                                                            $val = $val - ($deposit['_amount'] + @$deposit['loan_interest']);
                                                                            $dtotal = $dtotal + ($deposit['_amount'] + @$deposit['loan_interest']);
                                                                            $ccount++;
                                                                        } else  if ($deposit['type'] == "D" or $deposit['type'] == "A" or $deposit['type'] == "LC" or  $deposit['type'] == "I") {
                                                                            $debit = number_format($deposit['_amount']);
                                                                            $credit = "-";
                                                                            $val = $val +  $deposit['_amount'];
                                                                            $ctotal = $ctotal + $deposit['_amount'];
                                                                            $dcount++;
                                                                        }


                                                                        echo '
                                                    <tr style="text-align: center !important;">
                                        <td>' . $count++ . '</td>
                                        <td>' . $deposit['mode'] . '</td>
                                       
                                          <td>' . $debit . '</td>
                                        <td>' . $credit . '</td>
                                        <td> ' . number_format($val) . '</td>
                                        <td>' . $deposit['_did'] . '</td>
                                        <td>' . $trxn_date . '</td>
                                   
                                        </tr>
                                        ';
                                                                    }
                                                                    echo '
                                                                    <tr style="text-align: center !important;">
                                                        <td></td>
                                                        <td><b>Totals<b></td>
                                                       
                                                          <td><b>' . $ctotal . '</b></td>
                                                        <td><b>' . $dtotal . '</b></td>

                                                        <td> <b>' . number_format($ctotal - $dtotal) . '<b></td>

                                                        <td></td>
                                                        <td></td>
                                                   
                                                        </tr>
                                                        ';

                                                                    ?>

                                                                </tbody>

                                                            </table>

                                                            <div class="row show_on_print">
                                                                <div class="col-md-4" style="width: 369px;float: left;">

                                                                    <h4><small>TELLER:
                                                                        </small><b><?= $details[0]['_authorisedby']; ?></b></h4>

                                                                    <br>

                                                                    <h4><small>SIGNATURE:</small><b>
                                                                            ------------------------</b></h4>

                                                                </div>

                                                                <div class="col-md-4" style="width: 369px"></div>
                                                                <div class="col-md-4" style="width: 369px;float: right;">

                                                                    <div style="width: 313px;height: 96px;border: 1px solid;">
                                                                    </div>
                                                                    <br>
                                                                    <i>Official Use Only</i>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        } else {
                                                            echo '<div class="alert alert-warning"><span class="semibold">Caution: </span>No Journal Entries found' . $_POST['cash_account'] . '</div>';
                                                        }
                                                        ?>

                                                        <!--</div>-->
                                                    </div>
                                                </div>

                                            <?php endif; ?>



                                        </div>
                                        <!-- </div> -->

                                        <div id="payables" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Payables</h4>

                                            <!-- <p>Coming soon...</p> -->
                                            <a href="all_payables" class="btn btn-primary light btn-xs mb-1">View all Payables</a>
                                            <a href="register_creditor" class="btn btn-primary light btn-xs mb-1">Register Creditor</a>
                                            <a href="register_payable" class="btn btn-primary light btn-xs mb-1">Register Payable</a>







                                            <!-- </div> -->
                                        </div>

                                        <div id="receivables" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <h4 class="text-primary mb-4">Receivables</h4>

                                            <p>Coming soon...</p>






                                            <!-- </div> -->
                                        </div>

                                        <div id="journal_entries" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Journal Entries</h4> -->

                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Journal Entries</h4>
                                                <a href="register_income" class="btn btn-primary light btn-xs mb-1">Register Income</a>
                                                <a href="register_expense" class="btn btn-primary light btn-xs mb-1">Register Expenses</a>
                                                <a href="register_capital" class="btn btn-primary light btn-xs mb-1">Register Capital</a>
                                                <a href="register_liability" class="btn btn-primary light btn-xs mb-1">Register Liability</a>
                                                <a href="register_asset" class="btn btn-primary light btn-xs mb-1">Register Asset</a>
                                                <a href="advanced_journal_entry" class="btn btn-primary light btn-xs mb-1">Advanced Journal Entry</a>
                                            </div>



                                            <!-- </div> -->
                                        </div>

                                        <div id="journal_ledgers" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Journal Ledgers</h4> -->
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Journal Ledgers</h4>
                                                <a href="income_ledger" class="btn btn-primary light btn-xs mb-1">Income
                                                    Ledger</a>
                                                <a href="expense_ledger.php" class="btn btn-primary light btn-xs mb-1">Expenses Ledger</a>
                                                <a href="capital_ledger" class="btn btn-primary light btn-xs mb-1">Capital Ledger</a>
                                                <a href="liability_ledger" class="btn btn-primary light btn-xs mb-1">Liabilities Ledger</a>
                                                <a href="assets_ledger" class="btn btn-primary light btn-xs mb-1">Assets Ledger</a>
                                                <a href="javascript:void(0);" class="btn btn-primary light btn-xs mb-1">General Journal Ledger</a>
                                            </div>





                                            <!-- </div> -->
                                        </div>
                                        <div id="bank_tool" class="tab-pane fade ">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->
                                            <!-- <h4 class="text-primary mb-4">Journal Ledgers</h4> -->
                                            <div class="profile-skills mb-5">
                                                <h4 class="text-primary mb-4">Bank Reconciliation Tool</h4>
                                                <p>Coming soon ...</p>
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
        <!-- <script src="./vendor/global/global.min.js"></script>

        <script src="./vendor/jquery-steps/build/jquery.steps.min.js"></script>
        <script src="./vendor/jquery-validation/jquery.validate.min.js"></script>
        <script src="./js/plugins-init/jquery.validate-init.js"></script>


        <script src="./vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js"></script>
        <script src="vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

        <script src="./js/custom.min.js"></script>
        <script src="./js/dlabnav-init.js"></script>
        <script src="./js/demo.js"></script>
        <script src="./vendor/datatables/js/jquery.dataTables.min.js"></script>
        <script src="./js/plugins-init/datatables.init.js"></script>

        <script src="./vendor/select2/js/select2.full.min.js"></script>
        <script src="./js/plugins-init/select2-init.js"></script> -->

        <?php include('includes/bottom_scripts.php'); ?>

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {



                bindtoDatatable();

            });

            function bindtoDatatable(data) {

                var table = $('#transactions_table').dataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    pageLength: 10,
                    paging: true,

                    ajax: {
                        url: `<?= BACKEND_BASE_LOCALLY_URL ?>/Bank/get_all_transactions_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>&branchId=<?= @$_REQUEST['branchId'] ?>&transaction_type=<?= @$_REQUEST['transaction_type'] ?>&transaction_method=<?= @$_REQUEST['transaction_method'] ?>&sub_account_id=<?= @$_REQUEST['sub_account_id'] ?>&next_due_date=<?= @$_REQUEST['next_due_date'] ?>&start_date=<?= @$_REQUEST['start_date'] ?>&end_date=<?= @$_REQUEST['end_date'] ?>`,

                        type: "POST",
                        datatype: "json",
                        dataSrc: function(response) {
                            var data = response.data;
                            var datatable_data = [];
                            for (let record of data) {

                                var trasaction_type_label = '';
                                if (record.t_type == 'D') {
                                    trasaction_type_label = '<span class="badge light badge-primary">DEBIT</span>';
                                } else {
                                    trasaction_type_label = '<span class="badge light badge-danger">CREDIT</span>';
                                }

                                datatable_data.push({
                                    'transaction_id': record.tid,
                                    'date': to_normal_date(record.date_created),
                                    'description': record.description ? record.description.toUpperCase() : '',
                                    'amount': `<span class="text-danger"> ${number_format(record.amount)} </span>`,
                                    'account': record.aname ? record.aname.toUpperCase() : '',
                                    'vendor': record.acc_name ? record.acc_name.toUpperCase() : '',
                                    'type': trasaction_type_label,
                                    'auth': `${record.firstName ? record.firstName.toUpperCase() : ''} ${record.lastName ? record.lastName.toUpperCase() : ''}`,
                                    'branch': record.branch_name ? record.branch_name : '',
                                });
                            }
                            return datatable_data;
                        },
                    },

                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },

                    "aaData": data,

                    "columns": [{
                        "data": "transaction_id",
                        "width": "40px"
                    }, {
                        "data": "date",
                        "width": "70px"
                    }, {
                        "data": "description",
                        "width": "300px"
                    }, {
                        "data": "amount",
                        "width": "126px"
                    }, {
                        "data": "account",
                        "width": "150px"
                    }, {
                        "data": "vendor",
                        "width": "130px"
                    }, {
                        "data": "type",
                        "width": "85px"
                    }, {
                        "data": "auth",
                        "width": "130px"
                    }, {
                        "data": "branch",
                        "width": "160px"
                    }]
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