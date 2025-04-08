<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsSuperAdmin()) {
    return $permissions->isNotPermitted(true);
}
// $permiss
?>
<?php


require_once('includes/head_tag.php');

?>
<?php
include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {
    $res = $response->addBank($_POST['name'], $_POST['tname'], $_POST['location'], $_POST['contact'], $_POST['refered'], $_POST['auto_chart']);
    if ($res) {
        $_SESSION['success_message'] = 'Bank Created Successfully!';
        header('location:all_banks.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Something went wrong! Try again to Create the Bank';
        header('location:all_banks.php');
        exit;
    }

    // header('location:all_banks.php');
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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Banks</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Banks</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <div class="row">

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    All Banks
                                </h4>

                                <!-- <div class="btn-group" role="group"> -->
                                <button type="button" class="btn btn-primary" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">Add New Bank</button>

                                <!-- </div> -->
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Trade Name</th>
                                                <th>A/C Status</th>
                                                <th>SMS Banking</th>
                                                <th>Location</th>
                                                <th>Contact Person</th>
                                                <th>Recommender</th>
                                                <th>Branches</th>
                                                <th>Staff</th>
                                                <th>Clients</th>

                                                <th>On-Boarding Date</th>
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
                        <h5 class="modal-title">Add New Bank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <form method="POST">

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="text-label form-label">Bank's Name*
                                </label>
                                <input type="text" name="name" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Bank's Trade Name* (You can Put the same as above if the bank uses the same)
                                </label>
                                <input type="text" name="tname" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Head Office's Location of the Bank*
                                </label>
                                <input type="text" name="location" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Main Contact Person's Details (e.g Name - Contact)*
                                </label>
                                <input type="text" name="contact" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Refered by
                                </label>
                                <input type="text" name="refered" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Auto-Generate Chart Accounts
                                </label>
                                <select name="auto_chart" class="form-control">
                                    <option value="0" selected>No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">

                            <button type="submit" name="submit" class="btn btn-primary">Create Bank</button>
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_banks.php',
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
                        "data": "name"
                    }, {
                        "data": "tname"
                    },
                    {
                        "data": "status"
                    }, {
                        "data": "smsstatus"
                    },
                    {
                        "data": "location"
                    }, {
                        "data": "contact_person"
                    }, {
                        "data": "recommender"
                    }, {
                        "data": "branches"
                    }, {
                        "data": "staffs"
                    }, {
                        "data": "clients"
                    }, {
                        "data": "onboardingdate"
                    }, {
                        "data": "actions",
                    }
                ]
            })

        }
    </script>

</body>

</html>