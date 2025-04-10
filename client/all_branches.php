<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff() || !$permissions->hasSubPermissions('view_everything')) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

include_once('includes/head_tag.php');
?>
<?php
include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {
    $res = $response->addBranch($_POST['name'], $_POST['id'], $_POST['location'], $_POST['bcode'], $user[0]['userId']);
    if ($res) {
        $_SESSION['success_message'] = 'Branch Created Successfully!';
        header('location:all_branches.php');
    } else {
        $_SESSION['error_message'] = 'Branch not Created! Try Again';

        header('location:all_branches.php');
    }
    // exit;
}


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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Branches</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Branches</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    All Branches
                                </h4>

                                <!-- <div class="btn-group" role="group"> -->
                                <button type="button" class="btn btn-primary" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">Add New Branch</button>

                                <!-- </div> -->
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="table table-striped" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Branch Code</th>
                                                <th>Name</th>
                                                <th>Location</th>
                                                <th>Staff</th>
                                                <th>Clients</th>

                                                <th>Opening Date</th>
                                                <th>Action</th>
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
        <div class="modal fade bd-example-modal-lg3" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Branch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $user[0]['bankId']; ?>">

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="text-label form-label">Branch's Name*
                                </label>
                                <input type="text" name="name" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Location*
                                </label>
                                <input type="text" name="location" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Branch Code (e.g 101,102, etc)*
                                </label>
                                <input type="text" name="bcode" class="form-control" placeholder="">
                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="submit" name="submit" class="btn btn-primary">Create Branch</button>
                        </div>
                    </form>

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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_branches.php?id=<?php echo $_SESSION['session_user']['bankId']; ?>',
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
                    "data": "bcode"
                }, {
                    "data": "name"
                }, {
                    "data": "location"
                }, {
                    "data": "staffs"
                }, {
                    "data": "clients"
                }, {
                    "data": "openingdate"
                }, {
                    "data": "actions",
                }]
            })

        }
    </script>

</body>

</html>