<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'SAVING INITIATIONS';
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


                  
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> | Savings Interest Disbursement Initiations

                                <hr class="hr-dashed">



                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="fd_register" class="table table-striped fixed-layout dataTable" style="min-width: 845px;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Execution Date</th>
                                                <th>Status</th>
                                                <th>Branch</th>
                                                <th>Product</th>
                                                <th>Min. Balance</th>
                                                <th>Interest(%)</th>
                                                <th>WHT (%)</th>
                                                <th>Send SMS</th>
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_saving_ints.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>',
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
                        "data": "name"
                    }, {
                        "data": "exec_date"
                    },
                    {
                        "data": "status"
                    }, {
                        "data": "branch"
                    },
                    {
                        "data": "spdt"
                    }, {
                        "data": "amount"
                    }, {
                        "data": "int_rate"
                    }, {
                        "data": "wht_rate"
                    }, {
                        "data": "sms"
                    }, {
                        "data": "actions"
                    }
                ]
            })

        }
    </script>

</body>

</html>