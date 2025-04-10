<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'ALL FEES';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
$bank_details = $response->getBankDetails()[0];

// var_dump($bank_details);
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
                                    Fees Settings
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->
                                    <?php
                                    if (isset($_GET['success'])) {
                                        echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                                        // unset($_SESSION['success']);
                                    }
                                    if (isset($_GET['error'])) {
                                        echo '<script type="text/javascript">
                                    myError();
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
                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#fees" data-bs-toggle="tab" class="nav-link  <?= !in_array(@$_REQUEST['current_tab'], ['account_opening_fees']) ? 'active' : '' ?> ">Fees</a>
                                        </li>

                                        <!-- <li class="nav-item"><a href="#fees" data-bs-toggle="tab" class="nav-link active">Fees</a>
                                        </li> -->
                                        <li class="nav-item"><a href="#transaction_charges" data-bs-toggle="tab" class="nav-link ">Transaction Charges</a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="#account_opening_fees" data-bs-toggle="tab" class="nav-link <?= @$_REQUEST['current_tab'] == 'account_opening_fees' ? 'active' : '' ?>">
                                                Account opening fees
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">
                                        <div id="fees" class="tab-pane fade <?= !in_array(@$_REQUEST['current_tab'], ['account_opening_fees']) ? 'show active' : '' ?>" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Fees</h4>
                                                    <a href="add_fees.php" class="btn btn-primary light btn-xs mb-1">Add New Fee</a>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="bank_fees_table" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Fee Type</th>
                                                                    <th>Payment Type</th>
                                                                    <th>Rate/Amount</th>
                                                                    <th>Status</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <div id="transaction_charges" class="tab-pane fade ">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Transaction Charges</h4>
                                                    <a href="add_transaction_charge.php" class="btn btn-primary light btn-xs mb-1">Add New Transaction Charge</a>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="transaction_charges_table" class="display">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Applied On</th>
                                                                    <th>Charge</th>
                                                                    <th>Type</th>
                                                                    <th>Trxn Amount - Min</th>
                                                                    <th>Trxn Amount - Max</th>
                                                                    <th>Status</th>
                                                                    <th>Date Created</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div id="account_opening_fees" class="tab-pane fade <?= @$_REQUEST['current_tab'] == 'account_opening_fees' ? 'show active' : '' ?>">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">Account Opening Fees</h4>
                                                    <div>
                                                        <a href="account_opening_fee_settings.php" class="btn btn-primary light btn-xs mb-1">General Settings</a>

                                                        <a href="create_account_opening_fee.php" class="btn btn-primary light btn-xs mb-1">Add New Fee</a>
                                                    </div>
                                                </div>
                                                <div class="card-body">

                                                    <div class="table-responsive">
                                                        <table class="table display" id="account_opening_fees_table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Fee Name</th>
                                                                    <th>Membership Fees</th>
                                                                    <th>PassBook Fees</th>
                                                                    <th>Shares</th>
                                                                    <th>Applies To</th>
                                                                    <th>Savings Products</th>
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
                                        </div>


                                    </div>

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

                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_fees.php?bank=<?php echo $user[0]['bankId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        getBankFees(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function getBankFees(data) {

                var table = $('#bank_fees_table').dataTable({
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
                        "data": "ftype"
                    }, {
                        "data": "ptype"
                    }, {
                        "data": "rate"
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
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_transaction_charges.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        getTransactionCharges(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function getTransactionCharges(data) {
                // console.log(data);
                var table = $('#transaction_charges_table').dataTable({
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
                            "data": "cname"
                        }, {
                            "data": "cappln"
                        }, {
                            "data": "charge"
                        }, {
                            "data": "mode"
                        },
                        {
                            "data": "min"
                        }, {
                            "data": "max"
                        },
                        {
                            "data": "status"
                        }, {
                            "data": "date"
                        },
                        {
                            "data": "actions",
                        }
                    ]
                })

            }
        </script>


        <script type="text/javascript">
            $(document).ready(function() {
                getAccountOpeningFees();
            });

            function getAccountOpeningFees() {
                var table = $('#account_opening_fees_table').dataTable({
                    destroy: true,
                    ajax: {
                        url: '<?= BACKEND_BASE_URL ?>Fees/get_account_opening_fees.php?bankId=<?= $_SESSION['session_user']['bankId'] ?>&branchId=<?= $_SESSION['session_user']['branchId'] ?>',
                        type: "GET",
                        datatype: "json",
                        dataSrc: function(response) {
                            var data = response.data;
                            var datatable_data = [];
                            for (let record of data) {
                                let accounts = "";
                                for (let account of record.saving_accounts) {
                                    accounts += `${account.account_name}, `
                                }

                                datatable_data.push({
                                    'fee_id': record.fee_id,
                                    'fee_name': record.fee_name,
                                    'amount': number_format(record.amount),
                                    'pass': number_format(record.passbook_charges),
                                    'shares': record.no_shares,
                                    'applies_to': record.applies_to.replace(/_/g, ' '),
                                    'accounts': accounts,
                                    'branch_name': record.branch_name,
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
                                                href="create_account_opening_fee?id=${record.fee_id}"> <i class="fa fa-eye"></i> Edit Fee </a>

                                                <a class="dropdown-item text-danger delete-record"
                                                href="delete_account_opening_fee?id=${record.fee_id}"> <i class="fa fa-trash"></i>  Trash </a>
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
                            next: '<i class="fa fa-angle-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>'
                        }
                    },

                    "columns": [{
                            "data": "fee_id"
                        }, {
                            "data": "fee_name"
                        }, {
                            "data": "amount"
                        },
                        {
                            "data": "pass"
                        },
                        {
                            "data": "shares"
                        }, {
                            "data": "applies_to"
                        }, {
                            "data": "accounts"
                        }, {
                            "data": "branch_name"
                        },
                        {
                            "data": "actions",
                        }
                    ]
                })
            }
        </script>




</body>

</html>