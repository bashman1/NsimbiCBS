<?php
include('../backend/config/session.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}


require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_everything')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php
$title = 'SYSTEM ROLES';
require_once('includes/head_tag.php');
?>
<?php
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

                <!-- <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Roles</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Roles & Permissions</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <div class="row">

                    <div class="col-xl-12 col-lg-12 col-xxl-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Roles & Permissions
                                </h4>

                                <!-- <div class="btn-group" role="group"> -->
                                <button type="button" class="btn btn-primary"> <a href="create_role.php" style="color:#fff;">Add New Role</a></button>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive recentOrderTable">
                                    <table id="roles" class="table table-striped" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Role Name</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Permissions</th>
                                                <th scope="col">Branch</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Actions</th>
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
                url: '<?= BACKEND_BASE_URL ?>/Bank/get_all_bank_roles.php?id=<?php echo $user[0]['bankId']; ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    bindtoDatatable(data.data);
                    // console.log(data.data);
                }
            });

        });

        function bindtoDatatable(data) {

            var table = $('#roles').dataTable({
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
                    "data": "description"
                }, {
                    "data": "permissions"
                }, {
                    "data": "branch"
                }, {
                    "data": "status"
                }, {
                    "data": "actions",
                }]
            })

        }
    </script>

</body>

</html>