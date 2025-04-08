<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'ATTACHED FEES';
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

                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Loan Attached Fees

                            <hr class="hr-dashed">

                            <div class="row pricingTable1">
                                <div class="col-md-4">
                                    <?php
                                    $details = $response->getLoanDetails($_GET['id']);
                                    ?>

                                    <h4 class="mt-0 header-title">Loan Summary</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>Client Name: </b><?= $details[0]['client']['firstName'] . ' ' . $details[0]['client']['lastName']  ?></li>
                                        <li><b>A/C No: </b><?= $details[0]['client']['membership_no']; ?></li>

                                    </ul>

                                </div>
                                <div class="col-md-8">

                                    <h4 class="mt-0 header-title">Loan Details</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>Loan Amount: </b>UGX <?php echo number_format($details[0]['loan']['principal']); ?></li>
                                        <li><b>Product: </b><?php echo $details[0]['product']['type_name']; ?></li>
                                    </ul>

                                </div>


                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">




                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="fd_register" class="table table-striped fixed-layout dataTable" style="min-width: 845px;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Fee Name</th>
                                                <th>Amount</th>
                                                <th>Payment Method</th>
                                                <th>Description</th>
                                                <th>Authorized by</th>
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_loan_fees_payments.php?id=<?php echo $_GET['id']; ?>',
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
                        "data": "date"
                    }, {
                        "data": "name"
                    },
                    {
                        "data": "amount"
                    }, {
                        "data": "meth"
                    },
                    {
                        "data": "description"
                    }, {
                        "data": "auth"
                    },
                ]
            })

        }
    </script>

</body>

</html>