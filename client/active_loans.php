<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()  || !$permissions->hasSubPermissions('view_active_loans')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php
$title = 'ACTIVE LOANS';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
$lps = $response->getAllBankLoanProducts($user[0]['bankId'], $user[0]['branchId']);
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
                                            <select class="me-sm-2 default-select form-control wide" id="payment_methods" name="branchId">
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

                                        <label class="text-label form-label">Loan Status *</label>

                                        <select name="loan_status" class="me-sm-2 default-select form-control wide" id="cash_acc">
                                            <option value="">All</option>
                                            <option value="2" <?= @$_REQUEST['loan_status'] == 2 ? "selected" : "" ?>>Active - On Time</option>
                                            <option value="3" <?= @$_REQUEST['loan_status'] == 3 ? "selected" : "" ?>>Active - Due</option>
                                            <option value="4" <?= @$_REQUEST['loan_status'] == 4 ? "selected" : "" ?>>Active - Overdue</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select class="me-sm-2 default-select form-control wide" name="loan_product_id" id="branchselect">
                                            <option selected value="">All</option>
                                            <?php
                                            foreach ($lps as $row) { ?>
                                                <option value="<?= $row['id'] ?>" <?= @$_REQUEST['loan_product_id'] == $row['id'] ? "selected" : "" ?>>
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
                                        <label class="text-label form-label">Next Due Date *</label>
                                        <input type="date" class="form-control" name="next_due_date" value="<?= @$_REQUEST['next_due_date'] ?>" placeholder="Due Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Disbursement Start
                                            Date *</label>
                                        <input type="date" name="disbursement_start_date" class="form-control" name="from_date" value="<?= @$_REQUEST['disbursement_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">Disbursement End
                                            Date *</label>
                                        <input type="date" class="form-control" name="disbursement_end_date" value="<?= @$_REQUEST['disbursement_end_date'] ?>" placeholder="End Date">
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
                            Active Loans
                        </h4>

                        <?php
                        if (isset($_GET['success'])) {
                            echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                            // unset($_SESSION['success']);
                        }
                        if (isset($_GET['rsuccess'])) {
                            echo '<script type="text/javascript">
                    myrSuccess();
                   </script>';
                            // unset($_SESSION['success']);
                        }
                        if (isset($_GET['tsuccess'])) {
                            echo '<script type="text/javascript">
                    mytSuccess();
                   </script>';
                            // unset($_SESSION['success']);
                        }
                        if (isset($_GET['dsuccess'])) {
                            echo '<script type="text/javascript">
                    mydSuccess();
                   </script>';
                            // unset($_SESSION['success']);
                        }
                        if (isset($_GET['error'])) {
                            echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                        }
                        if (isset($_GET['terror'])) {
                            echo '<script type="text/javascript">
                                    mytError();
                                   </script>';
                        }
                        if (isset($_GET['rerror'])) {
                            echo '<script type="text/javascript">
                                    myrError();
                                   </script>';
                        }
                        if (isset($_GET['updateerror'])) {
                            echo '<script type="text/javascript">
                                    myUError();
                                   </script>';
                        }
                        if (isset($_GET['updatesuccess'])) {
                            echo '<script type="text/javascript">
                                    myUSuccess();
                                   </script>';
                        }
                        // unset($_SESSION['error']);

                        ?>
                        <!-- <div class="btn-group" role="group"> -->


                        <!-- </div> -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="active_loans_table" class="table table-striped export-datatable" style="min-width: 845px;" data-title="Active Loans">
                                <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">A/C No.</th>
                                        <th rowspan="2">Client's Name</th>
                                        <th rowspan="2">Savings Balance</th>
                                        <th rowspan="2">Principal</th>
                                        <th rowspan="2">Duration</th>
                                        <th rowspan="2">Interest</th>

                                        <th rowspan="2">Status</th>
                                        <th colspan="4" style="text-align: center;">Amount Paid</th>
                                        <th colspan="4" style="text-align: center;">Amount Due</th>
                                        <th colspan="3">Outst. Balance</th>

                                        <th rowspan="2">Next Due Date</th>
                                        <th colspan="3" style="text-align: center;">Amount in Arrears</th>
                                        <!-- <th>Mode of Disbursement</th> -->

                                        <th rowspan="2">Actions</th>
                                    </tr>
                                    <tr>

                                        <th>Principal</th>
                                        <th>Interest</th>
                                        <th>Penalty</th>
                                        <th>Total</th>

                                        <th>Principal</th>
                                        <th>Interest</th>
                                        <th>Penalty</th>
                                        <th>Total</th>


                                        <th>Principal</th>
                                        <th>Interest</th>
                                        <th>Total</th>

                                        <th>Principal</th>
                                        <th>Interest</th>
                                        <th>Total</th>


                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tr class="datatable-totals" style="background: #cccccc !important">
                                    <th colspan="3">Totals</th>
                                    <th class="total_savs"></th>
                                    <th class="total_loans"></th>
                                    <th class=""></th>
                                    <th class="total_interest"></th>
                                    <th class=""></th>
                                    <th class="payments_principal"></th>
                                    <th class="payments_interest"></th>
                                    <th class="payments_penalty"></th>
                                    <th class="payments_total"></th>

                                    <th class="amount_due_principal"></th>
                                    <th class="amount_due_interest"></th>
                                    <th class="tot_penalty_due"></th>
                                    <th class="amount_due_total"></th>

                                    <th class="outstanding_principal"></th>
                                    <th class="outstanding_interest"></th>
                                    <th class="outstanding_total"></th>

                                    <th class=""></th>

                                    <th class="arrears_principal"></th>
                                    <th class="arrears_interest"></th>
                                    <th class="total_arrears"></th>

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

            var table = $('#active_loans_table').dataTable({
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
                    url: `<?= BACKEND_BASE_URL ?>Bank/get_all_loans_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>&branchId=<?= @$_REQUEST['branchId'] ?>&status=active&loan_status=<?= @$_REQUEST['loan_status'] ?>&loan_product_id=<?= @$_REQUEST['loan_product_id'] ?>&next_due_date=<?= @$_REQUEST['next_due_date'] ?>&disbursement_start_date=<?= @$_REQUEST['disbursement_start_date'] ?>&disbursement_end_date=<?= @$_REQUEST['disbursement_end_date'] ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];
                        for (let record of data) {
                            var ftype = "";
                            if (record.repay_cycle_id == 1) {
                                ftype = 'DAYS';
                            } else if (record.repay_cycle_id == 2) {
                                ftype = 'WEEKS';
                            } else if (record.repay_cycle_id == 3 || record.repay_cycle_id == 6) {
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
                            }



                            datatable_data.push({
                                'loan_no': record.loan_no,
                                'membership_no': `<a class="text-primary" href='loan_details_page.php?id=${record.loan_no}'>${smart_record(record.membership_no)}</a>`,
                                'name': ` <a class="text-primary" href ='loan_details_page.php?id=${record.loan_no}' > ${smart_record(record.client_names || record.shared_name)}</a>`,
                                'sav_bal': number_format(record.acc_balance),
                                'principal': number_format(record.principal),
                                'duration': `${record.approved_loan_duration} - ${ftype}`,
                                'rate': number_format(record.interest_amount),
                                'status': status_label,
                                'princpaid': number_format(record.total_principal_paid),
                                'interestpaid': number_format(record.total_interest_paid),
                                'penaltypaid': number_format(record.total_loan_penalty),
                                'amountpaid': number_format(record.amount_paid),
                                'princdue': number_format(record.principal_due),
                                'interestdue': number_format(record.interest_due),
                                'penaltydue': number_format(record.penalty_balance),
                                'totaldue': number_format(to_number(record.penalty_balance) + to_number(record.interest_due) + to_number(record.principal_due)),
                                'princarrear': number_format(record.principal_arrears),
                                'interestarrear': number_format(record.interest_arrears),
                                'totalarrear': number_format(to_number(record.principal_arrears) + to_number(record.interest_arrears)),
                                'princ_bal': number_format(record.principal_balance),
                                'int_bal': number_format(record.interest_balance),
                                'balance': number_format(record.current_balance),
                                'duedate': to_normal_date(record.date_of_next_pay),
                                'actions': `
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
                                            <a class="dropdown-item" href="loan_details_page.php?id=${record.loan_no}"> <i class="fa fa-eye"></i>View Loan Details</a>
                                            <a class="dropdown-item" href="rectify_loans.php?id=${record.loan_no}"> <i class="fa fa-eye"></i>Rectify Loan</a>
                                            <a class="dropdown-item" href="rectify_principal.php?id=${record.loan_no}"> <i class="fa fa-eye"></i>Rectify Principal</a>
                                              
                                        </div>
                                    </div>
                                `,
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

                        // var fil_payments_principal = data.reduce((a, b) => a + to_number(b.fil_principal_paid), 0);
                        // $('.fil_princ').text(number_format(fil_payments_principal));

                        // var fil_payments_interest = data.reduce((a, b) => a + to_number(b.fil_interest_paid), 0);
                        // $('.fil_int').text(number_format(fil_payments_interest));

                        var total_payments_interest = data.reduce((a, b) => a + to_number(b.total_interest_paid), 0);
                        $('.payments_interest').text(number_format(total_payments_interest));

                        var total_payments_penalty = data.reduce((a, b) => a + to_number(b.total_loan_penalty), 0);
                        $('.payments_penalty').text(number_format(total_payments_penalty));

                        var total_payments = total_payments_principal + total_payments_interest + total_payments_penalty;
                        $('.payments_total').text(number_format(total_payments));

                        // var fil_payments = fil_payments_principal + fil_payments_interest;
                        // $('.fil_total').text(number_format(fil_payments));

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
                        "data": "loan_no",
                        "width": "70px",
                    }, {
                        "data": "membership_no",
                        "width": "85px",
                    }, {
                        "data": "name",
                        "width": "136px",
                    }, {
                        "data": "sav_bal",
                        "width": "136px",
                    }, {
                        "data": "principal",
                        "width": "76px",
                    }, {
                        "data": "duration",
                        "width": "84px",
                    }, {
                        "data": "rate",
                        "width": "67px",
                    }, {
                        "data": "status",
                        "width": "92px",
                    }, {
                        "data": "princpaid",
                        "width": "106px",
                    }, {
                        "data": "interestpaid",
                        "width": "106px",
                    }, {
                        "data": "penaltypaid",
                        "width": "106px",
                    }, {
                        "data": "amountpaid",
                        "width": "106px",
                    }, {
                        "data": "princdue",
                        "width": "106px",
                    }, {
                        "data": "interestdue",
                        "width": "106px",
                    }, {
                        "data": "penaltydue",
                        "width": "106px",
                    }, {
                        "data": "totaldue",
                        "width": "106px",
                    },
                    {
                        "data": "princ_bal",
                        "width": "161px",
                    },
                    {
                        "data": "int_bal",
                        "width": "161px",
                    },
                    {
                        "data": "balance",
                        "width": "161px",
                    }, {
                        "data": "duedate",
                        "width": "116px",
                    }, {
                        "data": "princarrear",
                        "width": "145px",
                    }, {
                        "data": "interestarrear",
                        "width": "145px",
                    }, {
                        "data": "totalarrear",
                        "width": "145px",
                    },
                    {
                        "data": "actions",
                        "width": "64px",
                    }
                ]
            })

        }
    </script>


</body>

</html>