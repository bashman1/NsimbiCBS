<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
// $permiss
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
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
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    All Share Transfer Transactions
                                </h4>



                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="share_transfer_transactions" class="table table-striped dataTable" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Transfered from</th>
                                                <th>Received by</th>
                                                <th>No. Of Shares</th>
                                                <th>Current Share Value</th>
                                                <th>Share Amount</th>
                                                <th>Authorised by</th>
                                                <th>Comments</th>
                                                <th>Record Date</th>
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_share_transfers.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    bindtoDatatable(data.data);
                    // console.log(data.data);
                }
            });

        });

        function bindtoDatatable(data) {

            var table = $('#share_transfer_transactions').dataTable({
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
                        "data": "from"
                    }, {
                        "data": "to"
                    },
                    {
                        "data": "shares"
                    }, {
                        "data": "sharevalue"
                    },
                    {
                        "data": "shareamount"
                    }, {
                        "data": "auth"
                    }, {
                        "data": "notes"
                    },
                    {
                        "data": "dateCreated"
                    }
                ]
            })

        }
    </script>

</body>

</html>