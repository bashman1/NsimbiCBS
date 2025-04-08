<?php
include('../backend/config/session.php');


require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
// if (!$permissions->IsBankAdmin()) {
//     return $permissions->isNotPermitted(true);
// }
?>
<?php
$title = 'Branch SMS BALANCES';
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

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    All Branches SMS Balances As of Now
                                </h4>


                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="table table-striped" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Bank</th>
                                                <th>Branch</th>
                                                <th>SMS Balance</th>
                                                <th>SMS Income Collected</th>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_branch_sms_status.php?bank=<?php echo $user[0]['bankId']; ?>',
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
                        "data": "id"
                    }, {
                        "data": "bname"
                    },
                    {
                        "data": "name"
                    }, {
                        "data": "balance"
                    }, {
                        "data": "income"
                    }

                ]
            })

        }
    </script>

</body>

</html>