<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
// if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_all_deposits')) {
//     return $permissions->isNotPermitted(true);
// }
$title = 'CREDIT HISTORY';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
$selected_client = $response->getClientDetails($_GET['id'])[0];
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


                <div class="row">


                    <div class="card">
                        <div class="card-body">

                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Loans

                            <hr class="hr-dashed">


                            <div class="btc-price">
                                <p class="text-muted mb-3">Client Details</p>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <span class="text-muted">A/C Name: </span>
                                        <h6 class="mt-0">
                                            <?= @$selected_client['name'] ?>
                                        </h6>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="text-muted">Member No:-</span>
                                        <h6 class="mt-0">
                                            <?= @$selected_client['accno'] ?>
                                        </h6>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="text-muted">A/C Balance:</span>
                                        <h6 class="mt-0">
                                            <?= number_format(@$selected_client['accbalance']) ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <h4 class="card-title">
                                    <a href="register_fixed_deposit_search" class="btn btn-primary light btn-xs mb-1">Register Fixed Deposit</a>
                                </h4> -->



                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="fd_register" class="display dataTable" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Loan Amount</th>
                                                <th>Interest Rate</th>
                                                <th>Duration</th>
                                                <th>Frequency</th>
                                                <th>Status</th>
                                                <th>Amount Paid</th>
                                                <th>Outstanding Balance</th>
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

            $.ajax({
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_user_loans.php?id=<?php echo $_GET['id']; ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    bindtoDatatable(data.data);
                    // console.log(data.data);
                }
            });

        });

        function bindtoDatatable(data) {

            var table = $('#fd_register').dataTable({
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
                        "data": "principal"
                    }, {
                        "data": "rate"
                    },
                    {
                        "data": "duration"
                    }, {
                        "data": "freq"
                    },
                    {
                        "data": "status"
                    }, {
                        "data": "amountpaid"
                    }, {
                        "data": "balance"
                    }, {
                        "data": "actions"
                    }
                ]
            })

        }
    </script>

</body>

</html>