<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}

include_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {
    $res = $response->addGroupMember($_POST);
    if ($res) {
        setSessionMessage(true, 'Group Member Added Successfully!');
        header('location:manage_group_members.php?id=' . $_POST['gid'] . '&name=' . $_POST['name']);
        exit;
    } else {
        setSessionMessage(false, 'Process failed. Try again!');
        header('location:manage_group_members.php?id=' . $_POST['gid'] . '&name=' . $_POST['name']);
        exit;
    }

    // header('location:all_banks');
}


$title = 'GROUP MEMBERS';
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

                <!-- row -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- <button type="button" class="btn btn-primary card-title"><span
                                        class="btn-icon-start text-primary"><i class="fa fa-arrow-left"></i>
                                    </span>Back</button> -->
                                <h5 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Group's Members
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a href="#chart_of_accounts" data-bs-toggle="tab" class="nav-link active">Group's Members</a>
                                        </li>


                                    </ul>
                                    <div class="tab-content">

                                        <div id="chart_of_accounts" class="tab-pane fade show active" role="tabpanel">
                                            <br /><br /><br />
                                            <!-- <div class="profile-personal-info"> -->


                                            <div class="card">
                                                <div class="card-header">
                                                    <!-- <h4 class="text-primary mb-4"> </h4> -->
                                                    <p class="m-0 subtitle"><?= $_GET['name'] ?></p>
                                                    <a class="btn btn-primary light btn-xs mb-1" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">Add New Group Member</a>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Export as</button>
                                                        <div class="dropdown-menu" style="margin: 0px;">
                                                            <a class="dropdown-item" onclick="exportToPDF('bankaccs','group_members_export_pdf')">PDF</a>
                                                            <a class="dropdown-item" onclick="exportToExcel('bankaccs','group_members_export_excel')">EXCEL</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="bankaccs" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Contact</th>
                                                                    <th>Role</th>
                                                                    <th>Address</th>
                                                                    <th>Has A/C</th>
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
                                    <!-- </div> -->
                                    <!-- Modal -->

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
                            <h5 class="modal-title">Add New Group Member</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="gid" class="form-control" placeholder="" value="<?php echo $_GET['id']; ?>">
                            <input type="hidden" name="name" class="form-control" placeholder="" value="<?php echo $_GET['name']; ?>">

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Name</label>
                                            <input type="text" name="field_name" class="form-control" placeholder="">

                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Contact</label>
                                            <input type="text" name="field_contact" class="form-control" placeholder="">

                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Address</label>
                                            <input type="text" name="field_address" class="form-control" placeholder="">

                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Role in Group</label>
                                            <input type="text" name="field_role" class="form-control" placeholder="">

                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-2">
                                        <div class="mb-3">
                                            <label class="text-label form-label">Has Personal A/C in SACCO</label>
                                            <select name="field_ac" class="form-control">
                                                <option value="1" selected>Yes</option>
                                                <option value="0">No</option>
                                            </select>

                                        </div>
                                    </div>


                                </div>


                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="submit" class="btn btn-primary">Add Member</button>
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
        <!-- Required vendors -->
        <?php include('includes/bottom_scripts.php'); ?>


        <script type="text/javascript">
            $(document).ready(function() {

                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>User/get_group_members.php?id=<?php echo $_GET['id']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        bindtoDatatable(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function bindtoDatatable(data) {

                var table = $('#bankaccs').dataTable({
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
                        "data": "phone"
                    }, {
                        "data": "role"
                    }, {
                        "data": "address"
                    }, {
                        "data": "member"
                    }, {
                        "data": "actions",
                    }]
                })

            }
        </script>






</body>

</html>