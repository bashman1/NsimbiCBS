<?php
include('../backend/config/session.php');
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_loan_status_report')) {
    return $permissions->isNotPermitted(true);
}
$title  = 'LOAN STATUS REPORT';
require_once('includes/head_tag.php');
include_once('includes/response.php');

$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
$lps = $response->getAllBankLoanProducts($user[0]['bankId'], $user[0]['branchId']);
$staff = $response->getBankStaff2($_SESSION['session_user']['bankId'], $_SESSION['session_user']['branchId']);
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
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Branch *</label>
                                        <?php if ($_SESSION['session_user']['branchName']) { ?>
                                            <div>
                                                <strong> <?= $_SESSION['session_user']['branchName'] ?> </strong>
                                            </div>
                                        <?php } else { ?>
                                            <select class="me-sm-2 default-select form-control wide" id="branchselect" name="branchId">
                                                <option value="0"> All</option>
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

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select class="me-sm-2 default-select form-control wide" name="loan_product_id" id="payment_methods">
                                            <option selected="" value="0">All</option>
                                            <?php
                                            foreach ($lps as $row) { ?>
                                                <option value="<?= $row['id'] ?>" id="<?= $row['id'] ?>" <?= @$_REQUEST['loan_product_id'] == $row['id'] ? "selected" : "" ?>>
                                                    <?= $row['name'] . '  - ' . $row['rate'] ?>
                                                </option>
                                                ';
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Status *</label>

                                        <select name="loan_status" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;">
                                            <option value="9">All</option>
                                            <option value="active" <?= @$_REQUEST['loan_status'] == 'active' || @$_REQUEST['loan_status'] == '' ? "selected" : "" ?>>Active</option>
                                            <option value="2" <?= @$_REQUEST['loan_status'] == 2 ? "selected" : "" ?>>Active - On Time</option>
                                            <option value="3" <?= @$_REQUEST['loan_status'] == 3 ? "selected" : "" ?>>Active - Due</option>
                                            <option value="4" <?= @$_REQUEST['loan_status'] == 4 ? "selected" : "" ?>>Active - Overdue</option>
                                            <option value="5" <?= @$_REQUEST['loan_status'] == 5 ? "selected" : "" ?>>Cleared</option>
                                            <option value="1" <?= @$_REQUEST['loan_status'] == 1 ? "selected" : "" ?>>Awaiting Disbursement</option>
                                            <option value="0" <?= @$_REQUEST['loan_status'] == 0 ? "selected" : "" ?>>Pending Approval</option>
                                            <option value="6" <?= @$_REQUEST['loan_status'] == 6 ? "selected" : "" ?>>Declined</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Credit Officer*</label>

                                        <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible form-control" aria-hidden="true" name="loan_officer_id">
                                            <option value="0">All </option>
                                            <?php
                                            if ($staffs !== '') {
                                                foreach ($staff as $row) { ?>
                                                    <option value="<?= $row['id'] ?>" <?= @$_REQUEST['loan_officer_id'] == $row['id'] ? 'selected' : '' ?>>
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
                                        <label class="text-label form-label" for="exampleInputEmail3">Appln/Disbursement Start
                                            Date *</label>
                                        <input type="date" name="disbursement_start_date" class="form-control" name="from_date" value="<?= @$_REQUEST['disbursement_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Appln/Disbursement End
                                            Date *</label>
                                        <input type="date" class="form-control" name="disbursement_end_date" value="<?= @$_REQUEST['disbursement_end_date'] ?>" placeholder="End Date">
                                    </div>
                                </div>
                            </div><br />
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail5">Repayments Start
                                            Date *</label>
                                        <input type="date" name="appln_start_date" class="form-control" value="<?= @$_REQUEST['appln_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail6">Repayments End
                                            Date *</label>
                                        <input type="date" name="appln_end_date" class="form-control" value="<?= @$_REQUEST['appln_end_date'] ?>" placeholder="End Date">
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
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Loan Status Report
                        </h4>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Export as</button>
                            <div class="dropdown-menu" style="margin: 0px;">
                                <a class="dropdown-item" onclick="exportToPDF('loan_status_report','loan_status_export_pdf')">PDF</a>
                                <a class="dropdown-item" onclick="exportToExcel('loan_status_report','loan_status_export_excel')">EXCEL</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="loan_status_report" class="table table-striped" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Loan No.</th>
                                        <th rowspan="2">A/C No.</th>
                                        <th rowspan="2">Client's Name</th>
                                        <th rowspan="2">Gender</th>
                                        <th rowspan="2">Savings Balance</th>
                                        <th rowspan="2">Loan Amount</th>
                                        <th rowspan="2">Loan Status</th>
                                        <th rowspan="2">Loan Product</th>
                                        <th rowspan="2">Int. Rate(% Annum)</th>
                                        <th rowspan="2">Expected Interest</th>
                                        <th rowspan="2">Application Date</th>
                                        <th rowspan="2">Disbursement Date</th>
                                        <th rowspan="2">Duration</th>
                                        <th rowspan="2">Estimated closing date</th>
                                        <th colspan="3" style="text-align: center;">Amount Due</th>
                                        <th colspan="4" style="text-align: center;">Amount Paid</th>
                                        <th colspan="4" style="text-align: center;">Outstanding Balance</th>
                                        <th colspan="4" style="text-align: center;">Amount In Arrears</th>
                                        <th colspan="3" style="text-align: center;">Amount Paid (Filtered Period)</th>
                                        <th rowspan="2" style="text-align: center;">Last Repayment Date</th>
                                    </tr>

                                    <tr>
                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>

                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Penalty </th>
                                        <th> Total </th>

                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Penalty </th>
                                        <th> Total </th>

                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>
                                        <th> Days </th>

                                        <th> Principal </th>
                                        <th> Interest </th>
                                        <th> Total </th>

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                                <tr class="datatable-totals" style="background: #cccccc !important">
                                    <th colspan="4">Totals</th>
                                    <th class="total_savs"></th>
                                    <th class="total_loans"></th>
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class="total_interest"></th>
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class=""></th>

                                    <th class="amount_due_principal"></th>
                                    <th class="amount_due_interest"></th>
                                    <th class="amount_due_total"></th>

                                    <th class="payments_principal"></th>
                                    <th class="payments_interest"></th>
                                    <th class="payments_penalty"></th>
                                    <th class="payments_total"></th>

                                    <th class="outstanding_principal"></th>
                                    <th class="outstanding_interest"></th>
                                    <th class="outstanding_penalty"></th>
                                    <th class="outstanding_total"></th>

                                    <th class="arrears_principal"></th>
                                    <th class="arrears_interest"></th>
                                    <th class="total_arrears"></th>
                                    <th class=""></th>

                                    <th class="fil_princ"></th>
                                    <th class="fil_int"></th>
                                    <th class="fil_total"></th>
                                    <th class=""></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- </div>



                </div> -->
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
    <?php include('includes/bottom_scripts.php'); ?>

    <script type="text/javascript">
        $(document).ready(function() {
            bindtoDatatable();
        });

        function bindtoDatatable(data) {

            var table = $('#loan_status_report').dataTable({
                destroy: true,
                // fixedColumns: {
                //     left: 3,
                // },
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,
                ajax: {
                    url: `<?= BACKEND_BASE_LOCALLY_URL ?>/Loan/get_loan_status_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['branchId'] ?>&branchId=<?= @$_REQUEST['branchId'] ?>&loan_officer_id=<?= @$_REQUEST['loan_officer_id'] ?>&loan_product_id=<?= @$_REQUEST['loan_product_id'] ?>&next_due_date=<?= @$_REQUEST['next_due_date'] ?>&disbursement_start_date=<?= @$_REQUEST['disbursement_start_date'] ?>&disbursement_end_date=<?= @$_REQUEST['disbursement_end_date'] ?>&loan_status=<?= @$_REQUEST['loan_status'] ?>&trxn_start_date=<?= (@$_REQUEST['appln_start_date'] ?? '2000-01-01') ?>&trxn_end_date=<?= (@$_REQUEST['appln_end_date'] ?? date('Y-m-d')) ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];
                        for (let record of data) {
                            var ftype = "";

                            const runningLoans = [2, 3, 4, 5];

                            if (!runningLoans.includes(record.lstatus)) {
                                record.principal_balance = 0;
                                record.interest_balance = 0;
                                record.penalty_balance = 0;

                            }


                            var total_payments = to_number(record.total_principal_paid) + to_number(record.total_interest_paid) + to_number(record.total_loan_penalty);

                            var total_balance = to_number(record.principal_balance) + to_number(record.interest_balance) + to_number(record.penalty_balance);
                            var total_savs = to_number(record.acc_balance);
                            const dateOfFirstPay = new Date(record.date_disbursed ?? record.requesteddisbursementdate);
                            let noDays = 0;
                            if (record.repay_cycle_id == 1 || record.repay_cycle_id == 4) {
                                // daily
                                noDays = record.approved_loan_duration;

                            } else if (record.repay_cycle_id == 2) {
                                // weekly
                                noDays = record.approved_loan_duration * 7;


                            } else if (record.repay_cycle_id == 3) {
                                // monthly
                                noDays = record.approved_loan_duration * 30;

                            } else if (record.repay_cycle_id == 5) {
                                // annually
                                noDays = record.approved_loan_duration * 360;

                            }
                            let newDate = new Date(dateOfFirstPay.setDate(dateOfFirstPay.getDate() + noDays));


                            if (record.repay_cycle_id == 1) {
                                ftype = 'DAYS';
                            } else if (record.repay_cycle_id == 2) {
                                ftype = 'WEEKS';
                            } else if (record.repay_cycle_id == 3) {
                                ftype = 'MONTHS';
                            } else if (record.repay_cycle_id == 4) {
                                ftype = 'DAYS';
                            } else if (record.repay_cycle_id == 5) {
                                ftype = 'YEARS';
                            }

                            var status_label = '';
                            if (record.lstatus == 2) {
                                status_label = '<span class="badge light badge-primary">ACTIVE - ON TIME</span>';
                            } else if (record.lstatus == 3) {
                                status_label = '<span class="badge light badge-warning">ACTIVE - DUE</span>';
                            } else if (record.lstatus == 4) {
                                status_label = '<span class="badge light badge-danger">ACTIVE - OVERDUE</span>';
                            } else if (record.lstatus == 5) {
                                status_label = '<span class="badge light badge-primary">CLEARED</span>';
                            } else if (record.lstatus == 0) {
                                status_label = '<span class="badge light badge-danger">PENDING</span>';
                            } else if (record.lstatus == 1) {
                                status_label = '<span class="badge light badge-warning">AWAITING DISBURSEMENT</span>';
                            }

                            var tot_arrears = to_number(record.principal_arrears) + to_number(record.interest_arrears);
                            var fil_tot = to_number(record.fil_principal_paid) + to_number(record.fil_interest_paid);
                            var days_arrears = days_in_arrears(record.arrearsbegindate);


                            datatable_data.push({
                                'loan_no': `<a class="text-primary" href='loan_details_page.php?id=${record.loan_no}'>${record.loan_no}</a>`,
                                'membership_no': `<a class="text-primary" href='client_profile_page.php?id=${encrypt_data(record.account_id)}'>${record.membership_no}</a>`,
                                'name': record.client_names,
                                'gender': smart_record(record.gender),
                                'principal': number_format(record.principal),
                                'product': record.type_name,
                                'status': status_label,
                                'rate': record.monthly_interest_rate,
                                'acc_balance': `<a class="text-primary" href="member_statement_range.php?id=${record.account_id}"> ${number_format(record.acc_balance)} </a>`,
                                'interest_amount': number_format(record.interest_amount),
                                'appln_date': to_normal_date(record.application_date),
                                'disbursement_date': to_normal_date(record.requesteddisbursementdate),
                                'est_term_closing_date': to_normal_date(newDate),
                                'principal_due': number_format(record.principal_due),
                                'interest_due': number_format(record.interest_due),
                                'total_dues': number_format(to_number(record.principal_due) + to_number(record.interest_due)),
                                'total_principal_paid': number_format(record.total_principal_paid),
                                'fil_principal_paid': number_format(record.fil_principal_paid),
                                'fil_interest_paid': number_format(record.fil_interest_paid),
                                'total_interest_paid': number_format(record.total_interest_paid),
                                'total_loan_penalty': number_format(record.total_loan_penalty),
                                'total_paid': number_format(total_payments),
                                'principal_balance': number_format(record.principal_balance),
                                'interest_balance': number_format(record.interest_balance),
                                'penalty_balance': number_format(record.penalty_balance),
                                'duration': `${record.approved_loan_duration} - ${ftype}`,
                                'total_balance': number_format(total_balance),
                                'princ_arrears': number_format(record.principal_arrears),
                                'int_arrears': number_format(record.interest_arrears),
                                'tot_arrears': number_format(tot_arrears),
                                'days_arrears': number_format(days_arrears),
                                'fil_tot': number_format(fil_tot),
                                'last_pay': to_normal_date(record.last_pay_d),
                            })
                        }

                        // console.log("return_data ::: ", return_data);
                        var total_savs = data.reduce((a, b) => a + to_number(b.acc_balance), 0);
                        $('.total_savs').text(number_format(total_savs));

                        var total_princ_arr = data.reduce((a, b) => a + to_number(b.principal_arrears), 0);
                        $('.arrears_principal').text(number_format(total_princ_arr));

                        var total_int_arr = data.reduce((a, b) => a + to_number(b.interest_arrears), 0);
                        $('.arrears_interest').text(number_format(total_int_arr));

                        var total_arr = total_princ_arr + total_int_arr;
                        $('.total_arrears').text(number_format(total_arr));

                        var total_loans = data.reduce((a, b) => a + to_number(b.principal), 0);
                        $('.total_loans').text(number_format(total_loans));

                        var total_interest = data.reduce((a, b) => a + (parseInt(b.interest_amount) || 0), 0);
                        $('.total_interest').text(number_format(total_interest));
                        /**
                         * Amount due
                         */
                        var total_amount_due_principal = data.reduce((a, b) => a + to_number(b.principal_due), 0);
                        $('.amount_due_principal').text(number_format(total_amount_due_principal));

                        var total_amount_due_interest = data.reduce((a, b) => a + to_number(b.interest_due), 0);
                        $('.amount_due_interest').text(number_format(total_amount_due_interest));

                        var total_amount_due = total_amount_due_principal + total_amount_due_interest;
                        $('.amount_due_total').text(number_format(total_amount_due));

                        /**
                         * Payments
                         */
                        var total_payments_principal = data.reduce((a, b) => a + to_number(b.total_principal_paid), 0);
                        $('.payments_principal').text(number_format(total_payments_principal));

                        var fil_payments_principal = data.reduce((a, b) => a + to_number(b.fil_principal_paid), 0);
                        $('.fil_princ').text(number_format(fil_payments_principal));

                        var fil_payments_interest = data.reduce((a, b) => a + to_number(b.fil_interest_paid), 0);
                        $('.fil_int').text(number_format(fil_payments_interest));

                        var total_payments_interest = data.reduce((a, b) => a + to_number(b.total_interest_paid), 0);
                        $('.payments_interest').text(number_format(total_payments_interest));

                        var total_payments_penalty = data.reduce((a, b) => a + to_number(b.total_loan_penalty), 0);
                        $('.payments_penalty').text(number_format(total_payments_penalty));

                        var total_payments = total_payments_principal + total_payments_interest + total_payments_penalty;
                        $('.payments_total').text(number_format(total_payments));

                        var fil_payments = fil_payments_principal + fil_payments_interest;
                        $('.fil_total').text(number_format(fil_payments));

                        /**
                         * Outstanding
                         */
                        var total_outstanding_principal = data.reduce((a, b) => a + to_number(b.principal_balance), 0);
                        $('.outstanding_principal').text(number_format(total_outstanding_principal));

                        var total_outstanding_interest = data.reduce((a, b) => a + to_number(b.interest_balance), 0);
                        $('.outstanding_interest').text(number_format(total_outstanding_interest));

                        var total_outstanding_penalty = data.reduce((a, b) => a + to_number(b.penalty_balance), 0);
                        $('.outstanding_penalty').text(number_format(total_outstanding_penalty));

                        var total_outstanding = total_outstanding_principal + total_outstanding_interest + total_outstanding_penalty;
                        $('.outstanding_total').text(number_format(total_outstanding));

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
                        "data": "loan_no"
                    }, {
                        "data": "membership_no"
                    }, {
                        "data": "name"
                    }, {
                        "data": "gender"
                    }, {
                        "data": "acc_balance",
                        "className": "text-right"
                    }, {
                        "data": "principal",
                        "className": "text-right"
                    },
                    {
                        "data": "status",
                    },
                    {
                        "data": "product",
                    },

                    {
                        "data": "rate",
                    },
                    {
                        "data": "interest_amount"
                    }, {
                        "data": "appln_date"
                    },
                    {
                        "data": "disbursement_date"
                    }, {
                        "data": "duration"
                    }, {
                        "data": "est_term_closing_date"
                    }, {
                        "data": "principal_due"
                    }, {
                        "data": "interest_due"
                    }, {
                        "data": "total_dues"
                    }, {
                        "data": "total_principal_paid"
                    }, {
                        "data": "total_interest_paid"
                    }, {
                        "data": "total_loan_penalty"
                    },
                    {
                        "data": "total_paid",
                    },
                    {
                        "data": "principal_balance",
                    },
                    {
                        "data": "interest_balance",
                    },
                    {
                        "data": "penalty_balance",
                    },
                    {
                        "data": "total_balance",
                    },
                    {
                        "data": "princ_arrears",
                    },
                    {
                        "data": "int_arrears",
                    },
                    {
                        "data": "tot_arrears",
                    },
                    {
                        "data": "days_arrears",
                    },
                    {
                        "data": "fil_principal_paid",
                    },
                    {
                        "data": "fil_interest_paid",
                    },
                    {
                        "data": "fil_tot",
                    },
                    {
                        "data": "last_pay",
                    },
                ]
            })

        }
    </script>


</body>

</html>