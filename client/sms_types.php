<?php
include('../backend/config/session.php');


require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
// if (!$permissions->IsBankAdmin() || !$permissions->IsSuperAdmin()) {
//     return $permissions->isNotPermitted(true);
// }
?>
<?php
$title = 'SMS TYPES';
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
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a> |

                                    Automatic SMS Types <?php if ($permissions->IsBankAdmin()) : ?>
                                        | <button type="button" class="btn btn-primary"> <a href="create_sms_type.php" style="color:#fff;">Add New SMS Type</a></button>
                                    <?php endif; ?>
                                    <?php if ($permissions->IsBankAdmin()) : ?>
                                        | <a href="subscribe_to_all_sms_types.php?id=<?php echo $user[0]['bankId']; ?>" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-check"></i> Subscribe to All</a>
                                        | <a href="unsubscribe_to_all_sms_types.php?id=<?php echo $user[0]['bankId']; ?>" class="btn btn-danger light btn-xs mb-1 is-back-btn hide"><i class="fa fa-times"></i> Un-Subscribe from All</a>
                                    <?php endif; ?>
                                </h4>

                                <!-- <div class="btn-group" role="group"> -->

                            </div>
                            <div class="card-body">
                                <div class="table-responsive recentOrderTable">
                                    <table class="display table verticle-middle table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">SMS On</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Charge Applies to</th>
                                                <th scope="col">Charge</th>
                                                <th scope="col">Settings</th>
                                                <th scope="col">Status</th>
                                                <?php echo $permissions->IsBankAdmin() ? '<th scope="col">Actions</th>' : ''; ?>
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_sms_types.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    bindtoDatatable(data.data);
                    // console.log(data.data);
                }
            });

        });

        function bindtoDatatable(data) {

            var table = $('.table-responsive-md').dataTable({
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
                        "data": "sms_on"
                    }, {
                        "data": "act_name"
                    }, {
                        "data": "charge_to"
                    }, {
                        "data": "charge"
                    },
                    {
                        "data": "action"
                    }, {
                        "data": "status"
                    },
                    <?php echo $permissions->IsBankAdmin() ? '
                    {
                        "data": "trash"
                    }
                    
                    ' : ''; ?>
                ]
            })

        }
    </script>

</body>

</html>