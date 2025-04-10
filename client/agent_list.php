<?php
include('../backend/config/session.php');

require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->hasSubPermissions('view_agent_transactions')) {
    return $permissions->isNotPermitted(true);
}

require_once('includes/head_tag.php');
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

                    <div class="col-xl-12 col-lg-12 col-xxl-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    All Field Officers
                                </h4>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive recentOrderTable">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Names & Phone</th>
                                                <th>Branch</th>
                                                <th>Deposits Taken</th>
                                                <th>Loan Repayments Taken</th>
                                                <th>Customers Served</th>
                                                <th>New Members (Active)</th>
                                                <th ">Allowance (Off Deposits)</th>
                                                <th >Commision (Off New Members)</th>
                                                <th >Total Payment</th>
                                                <th >Actions</th>
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

    <script type=" text/javascript">
                                                    $(document).ready(function() {

                                                    $.ajax({
                                                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_agents.php?id=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>',
                                                    type: 'GET',
                                                    dataType: 'json',
                                                    success: function(data) {
                                                    bindtoDatatable(data.data);
                                                    // console.log(data.data);
                                                    }
                                                    });

                                                    });

                                                    function bindtoDatatable(data) {

                                                    var table = $('#example3').dataTable({
                                                    destroy: true,
                                                    language: {
                                                    paginate: {
                                                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                                                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                                                    }
                                                    },
                                                    "aaData": data,
                                                    "columns": [{
                                                    "data": "count"
                                                    }, {
                                                    "data": "name"
                                                    }, {
                                                    "data": "branch"
                                                    }, {
                                                    "data": "deposits"
                                                    },
                                                    {
                                                    "data": "loan"
                                                    },

                                                    {
                                                    "data": "customers_served"
                                                    },
                                                    {
                                                    "data": "members"
                                                    }, {
                                                    "data": "allowance"
                                                    }, {
                                                    "data": "commision",
                                                    },
                                                    {
                                                    "data": "total_pay",
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