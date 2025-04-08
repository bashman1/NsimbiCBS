<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login');
    // exit();
}

?>
<?php
include_once('includes/response.php');
$response = new Response();
$branches = $response->getBankBranches($user[0]['bankId']);

if (isset($_POST['submit'])) {
    $res = $response->addBankAccount($_POST['name'], $_POST['id'], $_POST['bname'], $_POST['acno'], $_POST['branch']);
    if ($res) {
        setSessionMessage(true, 'Bank Account Created Successfully!');
        header('location:manage_bank_accounts.php');
        exit;
    } else {
        setSessionMessage(false, 'Bank Account not Created');
        header('location:manage_bank_accounts.php');
        exit;
    }

    // header('location:all_banks.php');
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

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    All Your Bank Accounts
                                </h4>

                              
                                <button type="button" class="btn btn-primary" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">Add New Bank Account</button>

                                <!-- </div> -->
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example3" class="display" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Associated Bank</th>
                                                <th>Account Name</th>
                                                <th>A/C No</th>
                                                <th>A/C Balance</th>
                                                <th>Branch</th>

                                                <th>Status</th>
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
                        <h5 class="modal-title">Add New Bank Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $user[0]['bankId']; ?>">

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="text-label form-label">Associated Bank Name*
                                </label>
                                <input type="text" name="bname" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Account Belongs to Which Branch*
                                </label>
                                <?php
                                echo '
                                <select id="single-select" data-select2-id="single-select" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true" name="branch">
    <option value="0">None</option>
        ';
                                if ($branches !== '') {
                                    foreach ($branches as $row) {
                                        echo '
    <option value="' . $row['id'] . '">' . $row['name'] . ' - ' . $row['location'] . '</option>
    
    ';
                                    }
                                } else {
                                    echo '
    <option readonly>No Branches Added yet</option>
    ';
                                }

                                echo
                                '

    </select>
                                ';
                                ?>

                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">Account Name*
                                </label>
                                <input type="text" name="name" class="form-control" placeholder="">
                            </div>
                            <div class="mb-3">
                                <label class="text-label form-label">A/C No*
                                </label>
                                <input type="text" name="acno" class="form-control" placeholder="">
                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="submit" name="submit" class="btn btn-primary">Create Account</button>
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_bank_bank_accounts.php?bank=<?php echo $user[0]['bankId']; ?>',
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
                    "data": "bank"
                }, {
                    "data": "acname"
                }, {
                    "data": "acno"
                }, {
                    "data": "acc_balance"
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