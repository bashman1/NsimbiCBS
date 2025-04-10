<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'FIXED DEPOSITS';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
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

                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Fixed Deposits Register

                            <hr class="hr-dashed">

                            <div class="row pricingTable1">
                                <div class="col-md-4">
                                    <?php
                                    $details = $response->getBankFDDetails($user[0]['bankId'], $user[0]['bankId'] == '' ? $user[0]['branchId'] : '')[0];
                                    ?>

                                    <h4 class="mt-0 header-title">Fixed Deposits Summary</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>Total Fixed Deposit A/Cs: </b><?= number_format($details['accs']??0) ?></li>
                                        <li><b>Total Fixed Deposits:(Inclusive of Cleared) </b><?= number_format($details['tot_bal']??0) ?></li>

                                    </ul>

                                </div>
                                <div class="col-md-4">

                                    <h4 class="mt-0 header-title">Interest Details</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>Total Interest Due: </b><?= number_format($details['int_due']??0, 2, '.', '') ?></li>
                                        <li><b>Total Interest Paid: </b><?= number_format($details['int_paid']??0, 2, '.', '') ?></li>
                                    </ul>

                                </div>

                                <div class="col-md-4">

                                    <h4 class="mt-0 header-title">WHT Details</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">

                                        <li><b>Total WHT Due: </b><?= number_format($details['wht_due']??0, 2, '.', '') ?></li>
                                        <li><b>Total WHT Paid: </b><?= number_format($details['wht_paid']??0, 2, '.', '') ?></li>
                                    </ul>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <?php if ($menu_permission->hasSubPermissions('create_fixed_deposits')) : ?>
                                        <a href="register_fixed_deposit_search.php" class="btn btn-primary light btn-xs mb-1">Register Fixed Deposit</a>
                                    <?php endif; ?>
                                </h4>



                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="fd_register" class="table table-striped fixed-layout dataTable" style="min-width: 845px;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>A/C No & Name</th>
                                                <th>Amount</th>
                                                <th>Period</th>
                                                <th>Comp. Freq.</th>
                                                <th>Status</th>
                                                <th>Deposit Date</th>
                                                <th>Maturity Date</th>
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_fds.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>',
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
                        "data": "client"
                    }, {
                        "data": "amount"
                    },
                    {
                        "data": "period"
                    }, {
                        "data": "freq"
                    },
                    {
                        "data": "status"
                    }, {
                        "data": "open_date"
                    }, {
                        "data": "close_date"
                    }, {
                        "data": "branch"
                    }, {
                        "data": "actions"
                    }
                ]
            })

        }
    </script>

</body>

</html>