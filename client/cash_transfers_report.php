<?php
require_once('../backend/config/session.php');
require_once('includes/functions.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_savings_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'CASH TRANSFERS';
require_once('includes/response.php');
require_once('includes/ReportService.php');
$ReportService = new ReportService();
$request_data = ['ReportModel' => 'TransactionReport', 'ReportModelMethod' => 'getCashTransfersTransactionReport', 'is_savings_report' => false];
$request_data = array_merge($request_data, $_REQUEST);
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

$account_type_name = '';
if (@$_REQUEST['actype']) {
    $key = array_search($_REQUEST['actype'], array_column($actypes, 'id'));
    $account_type_name = $actypes[$key]['ucode'] . '-' . $actypes[$key]['name'];
}

$staff_name = '';
if (@$_REQUEST['authorized_by_id']) {
    $key = array_search($_REQUEST['authorized_by_id'], array_column($staff, 'id'));
    $staff_name = $staff[$key]['name'] . '-' . $staff[$key]['position'] . '-' . $staff[$key]['branch'];
}

$report_type = "Cash Transfers Report";

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
                                            <select class="me-sm-2 default-select form-control wide" name="branchId" style="display: none;">
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

                                        <label class="text-label form-label">Transaction Type *</label>

                                        <select name="transaction_type" class="me-sm-2 default-select form-control wide" style="display: none;">
                                            <option value="">All </option>
                                            <option value="TTS" <?= @$_REQUEST['transaction_type'] == 'TTS' ? "selected" : "" ?>>Teller to Safe</option>
                                            <option value="STT" <?= @$_REQUEST['transaction_type'] == 'STT' ? "selected" : "" ?>>Safe to Teller</option>
                                            <option value="TTT" <?= @$_REQUEST['transaction_type'] == 'TTT' ? "selected" : "" ?>>Teller to Teller</option>
                                            <option value="STS" <?= @$_REQUEST['transaction_type'] == 'STS' ? "selected" : "" ?>>Safe to Safe</option>
                                            <option value="STB" <?= @$_REQUEST['transaction_type'] == 'STB' ? "selected" : "" ?>>Safe to Bank</option>
                                            <option value="BTB" <?= @$_REQUEST['transaction_type'] == 'BTB' ? "selected" : "" ?>>Bank to Bank</option>
                                            <option value="BTS" <?= @$_REQUEST['transaction_type'] == 'BTS' ? "selected" : "" ?>>Bank to Safe</option>
                                            <option value="BRTBR" <?= @$_REQUEST['transaction_type'] == 'BRTBR' ? "selected" : "" ?>>Inter-Branch</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Authorized By *</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="me-sm-2 default-select form-control wide" aria-hidden="true" name="authorized_by_id">
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


                            </div><br />
                            <div class="row">
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
                            <?= $report_type ?>
                        </h4>

                        <?php if (count($records)) :
                            $request_string = 'branchName=' . $branch_name . '&accountName=' . $account_type_name . '&staffName=' . $staff_name;
                            foreach ($request_data as $key => $param) {
                                $request_string .= '&' . $key . '=' . $param;
                            }
                        ?>
                            <div>

                                <a class="btn btn-primary light btn-xs" onclick="h_print_div('exreportn');">
                                    <i class="fas fa-file-pdf"></i>&nbsp;PDF
                                </a>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="card-body report-section" id="exreportn">
                        <?php require_once('./includes/report_header.php') ?>

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

                            <?php if (@$_REQUEST['actype']) : ?>
                                <tr>
                                    <td width="18%"> Savings Account:</td>
                                    <td> <strong> <?= $account_type_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['transaction_type']) :
                                $tt = '';
                                if (@$_REQUEST['transaction_type'] == "STB") {
                                    $tt = 'Safe to Bank';
                                } else if (@$_REQUEST['transaction_type'] == "BTS") {
                                    $tt = 'Bank to Safe';
                                } else if (@$_REQUEST['transaction_type'] == "TTS") {
                                    $tt = 'Teller to Safe';
                                } else if (@$_REQUEST['transaction_type'] == "TTT") {
                                    $tt = 'Teller to Teller';
                                } else if (@$_REQUEST['transaction_type'] == "STT") {
                                    $tt = 'Safe to Teller';
                                } else if (@$_REQUEST['transaction_type'] == "STS") {
                                    $tt = 'Safe to Safe';
                                } else {
                                    $tt = 'Cash Transfer';
                                }
                            ?>
                                <tr>
                                    <td width="18%"> Transaction Type:</td>
                                    <td> <strong> <?= @$tt; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['authorized_by_id']) : ?>
                                <tr>
                                    <td width="18%"> Authorized by:</td>
                                    <td> <strong> <?= $staff_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>


                            <tr>
                                <td colspan="2">
                                    <div>
                                        From: <strong> <?= normal_date($_REQUEST['transaction_start_date'] ?? date('Y-m-d')) ?> </strong>
                                        To: <strong> <?= normal_date($_REQUEST['transaction_end_date'] ?? date('Y-m-d')) ?> </strong>
                                    </div>
                                </td>
                            </tr>

                        </table>

                        <table class="report_table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Trxn Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Authorized By</th>
                                    <th>Branch</th>
                                    <th>Trxn date</th>
                                </tr>


                            </thead>
                            <tbody>
                                <?php

                                foreach ($records as $record) {

                                    $ttn = '';

                                    if (@$record['t_type'] == "STB") {
                                        $ttn = 'Safe to Bank';
                                    } else if (@$record['t_type'] == "BTS") {
                                        $ttn = 'Bank to Safe';
                                    } else if (@$record['t_type'] == "TTS") {
                                        $ttn = 'Teller to Safe';
                                    } else if (@$record['t_type'] == "TTT") {
                                        $ttn = 'Teller to Teller';
                                    } else if (@$record['t_type'] == "STT") {
                                        $ttn = 'Safe to Teller';
                                    } else if (@$record['t_type'] == "STS") {
                                        $ttn = 'Safe to Safe';
                                    } else {
                                        $ttn = 'Cash Transfer';
                                    }
                                ?>
                                    <tr>
                                        <td> <?= @$record['transaction_id'] ?> </td>
                                        <td> <?= @$ttn ?> </td>
                                        <td> <?= @$record['dr_acc'] ?> </td>
                                        <td> <?= @$record['cr_acc'] ?> </td>
                                        <td>
                                            <?= number_format(@$record['amount']) ?>
                                        </td>

                                        <td> <?= @$record['transaction_description'] ?> </td>
                                        <td> <?= @$record['authorized_by_names'] ?> </td>
                                        <td> <?= @$record['branch_name'] ?> </td>
                                        <td> <?= normal_date_short(@$record['transaction_date']) ?> </td>

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