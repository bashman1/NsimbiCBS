<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_journal_report')) {
    return $permissions->isNotPermitted(true);
}
$title = 'A/C LEDGER REPORT';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);
$staff = $response->getBankStaff($user[0]['bankId'], $user[0]['branchId']);
$sub_accs = $response->getSubAccounts2($user[0]['branchId'], $user[0]['bankId']);

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

$journal_account = '';
if (@$_REQUEST['acid']) {
    $key = array_search($_REQUEST['acid'], array_column($sub_accs, 'id'));
    $journal_account = $sub_accs[$key]['name'];
}
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

                        <form class="ajax_results_form" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="form-control" id="select2" name="branch" style="display: none;">
                                                <option value=""> All</option>
                                                <?php

                                                $default_selected = @$_REQUEST['branch'] == $user[0]['branchId'] || !@$_REQUEST['branch'] ? "selected" : "";

                                                if ($user[0]['branchId']) { ?>
                                                    <option value="<?= $user[0]['branchId'] ?>" <?= $default_selected ?>> <?= $user[0]['branchName'] ?> </option>
                                                    ';
                                                <?php } ?>

                                                <?php
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        $is_seleceted = @$_REQUEST['branch'] == $row['id'] ? "selected" : "";
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
                                        <select id="payment_methods" class="form-control" name="authorized_by_id">
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
                                        <label class="text-label form-label">Journal Account*</label>
                                        <select id="clientsselect" class="form-control" name="acid">
                                            <option value=""> All </option>
                                            <?php
                                            if ($sub_accs !== '') {
                                                foreach ($sub_accs as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['acid'] == $row['id'] ? 'selected' : '' ?>>
                                                        <?= $row['name'] . ' - ' . $row['branch'] ?>
                                                    </option>
                                                <?php }
                                            } else { ?>
                                                <option readonly>No Journal Accounts yet</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Start Date *</label>
                                        <input type="date" class="form-control" name="start_date" value="<?= @$_REQUEST['start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">End Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?= @$_REQUEST['end_date'] ?>" placeholder="End Date">
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
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            General Ledger Report
                        </h4>


                    </div>
                    <div class="card-body">

                        <table class="main-header">
                            <tr>
                                <td> <strong> <?= 'General Ledger Report' ?> </strong> </td>
                            </tr>
                        </table>

                        <table>
                            <?php if (@$_REQUEST['branchId']) : ?>
                                <tr>
                                    <td width="18%"> Branch:</td>
                                    <td> <strong> <?= $branch_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$journal_account) : ?>
                                <tr>
                                    <td width="18%"> Journal Account:</td>
                                    <td> <strong> <?= $journal_account; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['authorized_by_id']) : ?>
                                <tr>
                                    <td width="18%"> Authorized by:</td>
                                    <td> <strong> <?= $staff_name; ?> </strong> </td>
                                </tr>
                            <?php endif ?>

                            <?php if (@$_REQUEST['start_date'] && @$_REQUEST['end_date']) : ?>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                            From: <strong> <?= normal_date($_REQUEST['start_date']) ?> </strong>
                                            To: <strong> <?= normal_date($_REQUEST['end_date']) ?> </strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif ?>
                        </table>
                        <div class="table-responsive">
                            <table id="ledger_table" class="table table-striped" style="min-width: 845px;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Trxn date</th>
                                        <th>Entry Type</th>
                                        <th>DR</th>
                                        <th>CR</th>
                                        <th>Balance</th>
                                        <th>Narration</th>
                                        <th>Mode of Payment</th>
                                        <th>Authorized By</th>
                                        <th>Branch</th>
                                    </tr>
                                </thead>
                                <tbody>




                                </tbody>
                                <tr class="datatable-totals" style="background: #cccccc !important">
                                    <th colspan="3">Totals</th>
                                    <th class="total_amount_dr"></th>
                                    <th class="total_amount"></th>
                                    <th class="total_amount_bal"></th>

                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class=""></th>

                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>



            <!-- </div>
            </div> -->
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
    <?php include('includes/bottom_scripts.php'); ?>

    <script type="text/javascript">
        $(document).ready(function() {
            bindtoDatatable();

        });

        function bindtoDatatable(data) {

            var table = $('#ledger_table').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,
                "lengthMenu": [
                    [10, 25, 50, 100, 150, 200, 500, -1],
                    [10, 25, 50, 100, 150, 200, 500, "All"]
                ],

                ajax: {
                    url: `<?= BACKEND_BASE_URL ?>Bank/get_all_journal_entries_datatables.php?bank=<?= $user[0]['bankId'] ?>&branch=<?= @$_REQUEST['branch'] ?>&auth=<?= @$_REQUEST['authorized_by_id'] ?>&acid=<?= @$_REQUEST['acid'] ?>&start_date=<?= @$_REQUEST['start_date']??date('Y-m-d') ?>&end_date=<?= @$_REQUEST['end_date']??date('Y-m-d') ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        // console.log(data);
                        var datatable_data = [];
                        var balance = 0;
                        var total_amount = 0;
                        var total_amount_dr = 0;
                        var total_amount_bal = 0;
                        for (let record of data) {
                            let cr_amount = 0;
                            let dr_amount = 0;

                            let ref = `${record.t_type}-ref-${record.pay_method}-${record.tid}-${record._authorizedby}`;

                            let title1 = '';

                            if (record.t_type == 'E') {
                                title1 = 'Expense Entry';

                                cr_amount = 0;
                                dr_amount = record.amount ?? 0;

                                // $balance = $balance + $dr_amount;
                            }
                            if (record.t_type == 'LIA') {
                                title1 = 'Liability Entry';

                                if (record.dr_acid == `<?= @$_REQUEST['acid'] ?>`) {


                                    dr_amount = 0;
                                    cr_amount = record.amount ?? 0;
                                } else {
                                    cr_amount = 0;
                                    dr_amount = record.amount ?? 0;
                                }

                                // $balance = $balance + ($cr_amount  - $dr_amount);
                            }
                            if (record.t_type == 'CAP') {
                                title1 = 'Capital Entry';
                                if (record.dr_acid == `<?= @$_REQUEST['acid'] ?>`) {
                                    dr_amount = 0;
                                    cr_amount = record.amount ?? 0;
                                } else {
                                    cr_amount = 0;
                                    dr_amount = record.amount ?? 0;
                                }

                            }
                            if (record.t_type == 'AJE') {
                                title1 = 'Advanced Journal Entry';

                                if (record.cr_acid == `<?= @$_REQUEST['acid'] ?>`) {
                                    cr_amount = 0;
                                    dr_amount = record.amount ?? 0;
                                } else {
                                    dr_amount = 0;
                                    cr_amount = record.amount ?? 0;
                                }

                            }
                            if (record.t_type == 'ASS') {
                                title1 = 'Asset Entry';


                                if (record.cr_acid == `<?= @$_REQUEST['acid'] ?>` || record.acid == `<?= @$_REQUEST['acid'] ?>`) {

                                    cr_amount = 0;
                                    dr_amount = record.amount ?? 0;
                                } else {
                                    dr_amount = 0;
                                    cr_amount = record.amount ?? 0;
                                }

                            }

                            if (record.t_type == 'D') {
                                title1 = 'Deposit Entry';

                                cr_amount = 0;
                                dr_amount = record.amount ?? 0;

                            }
                            if (record.t_type == 'W') {
                                title1 = 'Withdraw Entry';

                                cr_amount = 0;
                                dr_amount = record.amount ?? 0;

                            }

                            if (record.t_type == 'BF') {
                                title1 = 'Imported Entry';

                                cr_amount = 0;
                                dr_amount = record.amount ?? 0;

                            }
                            if (record.t_type == 'I' || record.t_type == 'R' || record.t_type == 'SMS' || record.t_type == 'C') {
                                title1 = 'Income Entry';

                                cr_amount = 0;
                                dr_amount = record.amount ?? 0;

                            }

                            dr_amount = Number(dr_amount);
                            cr_amount = Number(cr_amount);

                            balance = balance + (dr_amount - cr_amount);

                            total_amount += cr_amount;
                            total_amount_dr += dr_amount;
                            total_amount_bal = balance;


                            datatable_data.push({
                                'ref': ref,
                                'trxn_date': to_normal_date(record.transaction_date),
                                'type': title1,
                                'dr': number_format(dr_amount),
                                'cr': number_format(cr_amount),
                                'balance': number_format(balance),
                                'notes': `${record.transaction_description} - ${record.client_names??''}`,
                                'pay_meth': record.pay_method,
                                'auth': `${record.authorized_by_names ? record.authorized_by_names :''}`,
                                'branch': record.branch_name,

                            })
                        }
                        $('.total_amount_dr').text(number_format(total_amount_dr));
                        $('.total_amount').text(number_format(total_amount));
                        $('.total_amount_bal').text(number_format(total_amount_bal));
                        // console.log("return_data ::: ", datatable_data);

                        return datatable_data;
                    },
                },

                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },

                "columns": [{
                    "data": "ref"
                }, {
                    "data": "trxn_date"
                }, {
                    "data": "type"
                }, {
                    "data": "dr"
                }, {
                    "data": "cr"
                }, {
                    "data": "balance"
                }, {
                    "data": "notes"
                }, {
                    "data": "pay_meth"
                }, {
                    "data": "auth"
                }, {
                    "data": "branch"
                }]
            })

        }
    </script>

</body>

</html>