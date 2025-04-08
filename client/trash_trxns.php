<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_journal_report')) {
    return $permissions->isNotPermitted(true);
}


require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'TransactionReport', 'ReportModelMethod' => 'getTrashedTransactionReport', 'is_expense_report' => false];
$request_data = array_merge($request_data, $_REQUEST);
$_REQUEST['transaction_start_date'] = $_REQUEST['transaction_start_date'] ?? date('Y-m-d');
$_REQUEST['transaction_end_date'] = $_REQUEST['transaction_end_date'] ?? date('Y-m-d');
$report_reponse = $ReportService->generateReport($request_data);
$records = @$report_reponse['data'] ?? [];
// var_dump($report_reponse);
// exit;

$response = new Response();
$branches = $response->getBankBranches($_SESSION['session_user']['bankId']);
$staff = $response->getBankStaff($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);


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


                        <form class="ajax_results_form" method="GET">
                            <input type="hidden" name="filtered" value="1">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select id="bankacc" class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
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


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Authorized By *</label>
                                        <select id="payment_methods" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="authorized_by_id">
                                            <option value=""> All </option>
                                            <?php
                                            if ($staff !== '') {
                                                foreach ($staff as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['authorized_by_id'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['position'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php }
                                            } else { ?>
                                                <option readonly>No Staff Added yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> Start Date *</label>
                                        <input type="date" class="form-control" name="transaction_start_date" value="<?= @$_REQUEST['transaction_start_date'] ?? date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> End Date *</label>
                                        <input type="date" class="form-control" name="transaction_end_date" value="<?= @$_REQUEST['transaction_end_date'] ?? date('Y-m-d'); ?>" placeholder="End Date">
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
                            <?= 'Trashed Transactions' ?>
                        </h4>

                        <?php if (count($records)) :

                        ?>
                            <div>


                                <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                                    <i class="fas fa-file-pdf"></i>&nbsp;Print
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

                        <table class="main-header">
                            <tr>
                                <td> <strong> <?= 'Trashed Transactions' ?> </strong> </td>
                            </tr>
                        </table>

                        <table>
                            <?php if (@$_REQUEST['branchId']) : ?>
                                <tr>
                                    <td width="18%"> Branch:</td>
                                    <td> <strong> <?= $branch_name; ?> </strong> </td>
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
                                <th>#</th>
                                <th>Deletion date</th>
                                <th>Deleted by</th>
                                <th>Deletion Notes</th>
                                <th>Reference No</th>
                                <th>Trxn Type</th>
                                <th>Trxn Date</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Client</th>

                                <th>Mode of Payment</th>
                                <th>Authorized By</th>
                                <th>Branch</th>
                                <th>Actions</th>

                            </thead>
                            <tbody>

                                <?php

                                foreach ($records as $record) {


                                    $ref =    $record['t_type'] . '-ref-' . $record['pay_method'] . '-' . $record['transaction_id'] . '-' . $record['_authorizedby'];

                                    $title1 = 'Other Entries';
                                    if ($record['t_type'] == 'E') {
                                        $title1 =   'Expense Entry';
                                    }
                                    if ($record['t_type'] == 'A') {
                                        $title1 =   'Loan Dsibursement';
                                    }
                                    if ($record['t_type'] == 'A') {
                                        $title1 =   'Loan Repayment';
                                    }
                                    if ($record['t_type'] == 'WLI') {
                                        $title1 =   'Waived Loan Interest';
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

                                    if ($record['t_type'] == 'D') {
                                        $title1 =   'Deposit Entry';
                                    }
                                    if ($record['t_type'] == 'W') {
                                        $title1 =   'Withdraw Entry';

                                    }

                                    if ($record['t_type'] == 'BF') {
                                        $title1 =   'Imported Entry';
                                    }
                                    if ($record['t_type'] == 'I' || $record['t_type'] == 'R' || $record['t_type'] == 'SMS' || $record['t_type'] == 'C') {
                                        $title1 =   'Income Entry';
                                    } 

                                ?>
                                    <tr>
                                        <td><?= @$record['trash_id'] ?></td>
                                        <td> <?= normal_date(@$record['trash_date']) ?> </td>
                                        <td> <?= @$record['trashed_by_names'] ?> </td>

                                        <td><?= @$record['trash_reason'] ?></td>

                                        <td class="no_print clickable_ref_no" ref-no="<?= @$ref ?> " tid="<?= @$record['transaction_id'] ?>"><?= @$ref ?></td>
                                        <td> <?= $title1 ?> </td>

                                        <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>

                                        <td> <?= number_format(@$record['amount']) ?> </td>

                                        <td> <?= @$record['transaction_description'] ?> </td>
                                        <td> <?= @$record['client_names'] ?> </td>

                                        <td> <?= @$record['pay_method'] ?> </td>

                                        <td> <?= @$record['authorized_by_names'] ?> </td>

                                        <td> <?= @$record['branch_name'] ?> </td>
                                        <td>
                                            <div class="dropdown custom-dropdown mb-0">
                                                <div class="btn sharp btn-primary tp-btn"
                                                    data-bs-toggle="dropdown">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                                                        height="18px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"></rect>
                                                            <circle fill="#000000" cx="12" cy="5" r="2">
                                                            </circle>
                                                            <circle fill="#000000" cx="12" cy="12" r="2">
                                                            </circle>
                                                            <circle fill="#000000" cx="12" cy="19" r="2">
                                                            </circle>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                   
                                                    <a class="dropdown-item text-danger confirm delete-record"
                                                        href="undo_trash.php?id=<?= @$record['transaction_id'] ?>"> <i class="fa fa-trash"></i> Undo-Trash </a>
                                                </div>
                                            </div>
                                        </td>


                                    </tr>
                                <?php

                                } ?>


                            </tbody>
                        </table>

                        <?php
                        if (!count($records)) {
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