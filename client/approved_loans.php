<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'APPROVED LOANS';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);
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

                                        <label class="text-label form-label">Loan Product *</label>

                                        <select class="me-sm-2 default-select form-control wide" name="loan_product_id" style="display: none;">
                                            <option selected="" value="">All</option>
                                            <?php
                                            foreach ($lps as $row) { ?>
                                                <option value="<?= $row['id'] ?>" id="<?= $row['frequency'] ?>" <?= @$_REQUEST['loan_product_id'] == $row['id'] ? "selected" : "" ?>>
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

                                        <label class="text-label form-label">Proposed Disbursement Date *</label>

                                        <input type="date" class="form-control" name="disbursement_date" value="<?= @$_REQUEST['disbursement_date'] ?>" placeholder="Due Date">
                                    </div>
                                </div>
                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Application Start Date *</label>
                                        <input type="date" class="form-control" name="application_start_date" value="<?= @$_REQUEST['application_start_date'] ?>" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label">Application End Date *</label>
                                        <input type="date" class="form-control" name="application_end_date" value="<?= @$_REQUEST['application_end_date'] ?>" placeholder="End Date">
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
                            Loans Awaiting Disbursement
                        </h4>

                        <!-- <div class="btn-group" role="group"> -->


                        <!-- </div> -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="approved_loans_table" class="table table-striped" style="min-width: 845px;">
                                <thead>
                                    <tr>
                                        <th>Loan No.</th>
                                        <th>A/C No.</th>
                                        <th>Client's Name</th>
                                        <th>Principal</th>
                                        <th>Duration</th>
                                        <th>Interest Rate</th>
                                        <th>Loan Product</th>
                                        <th>Status</th>

                                        <th>Application Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>




                                </tbody>
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

            var table = $('#approved_loans_table').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searchable: true,
                pageLength: 10,
                paging: true,

                ajax: {
                    url: `<?= BACKEND_BASE_URL ?>Bank/get_all_loans_datatables.php?bankId=<?= $user[0]['bankId'] ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>&branchId=<?= @$_REQUEST['branchId'] ?>&status=1&loan_status=<?= @$_REQUEST['loan_status'] ?>&loan_product_id=<?= @$_REQUEST['loan_product_id'] ?>&disbursement_date=<?= @$_REQUEST['disbursement_date'] ?>&application_start_date=<?= @$_REQUEST['application_start_date'] ?>&application_end_date=<?= @$_REQUEST['application_end_date'] ?>`,

                    type: "POST",
                    datatype: "json",
                    dataSrc: function(response) {
                        var data = response.data;
                        var datatable_data = [];
                        for (let record of data) {
                            var frequency = get_cycle_text(record.repay_cycle_id);
                            var status_label = '<span class="badge light badge-warning">Pending Disbursement</span>';

                            datatable_data.push({
                                'loan_no': record.loan_no,
                                'membership_no': `<a class="text-primary" href='loan_details_page.php?id=${record.loan_no}'>${record.membership_no ?? '-'}</a>`,
                                'name': `<a class="text-primary" href='loan_details_page.php?id=${record.loan_no}'>${record.client_names}</a>`,
                                'principal': number_format(record.principal),
                                'duration': `${record.approved_loan_duration} - ${frequency}`,
                                'rate': `${number_format(record.monthly_interest_rate)}%`,
                                'loanproduct': record.type_name,
                                'status': status_label,
                                'application_date': to_normal_date(record.application_date),
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
                                            <a class="dropdown-item"
                                                href="loan_details_page.php?id=${record.loan_no}"> <i class="fa fa-eye"></i> View Loan Details </a>
                                        </div>
                                    </div>
                                `,
                            })
                        }

                        // console.log("return_data ::: ", return_data);

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
                    "data": "principal"
                }, {
                    "data": "duration"
                }, {
                    "data": "rate"
                }, {
                    "data": "loanproduct"
                }, {
                    "data": "status"
                }, {
                    "data": "application_date"
                }, {
                    "data": "actions",
                }]
            })

        }
    </script>

</body>

</html>