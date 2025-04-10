<?php
include('../backend/config/session.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}

require_once './includes/constants.php';
require('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
// if (!$permissions->IsBankStuff()) {
//     return $permissions->isNotPermitted(true);
// }
?>
<?php
$title  = 'SMS PURCHASES';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

$branches = $response->getBankBranches($user[0]['bankId']);

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
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Savings</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">All Deposits</a></li>
                    </ol>
                </div> -->
                <!-- row -->


                <!-- <div class="row">

                    <div class="col-12"> -->
                <div class="card">
                    <div class="card-body">

                        <p class="text-muted mb-3">Filters</p>

                        <form class="ajax_results_form" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="text-label form-label">Branch *</label>
                                        <select class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" name="branch" style="display: none;" required>
                                            <?php

                                            if ($user[0]['branchId']) {
                                                echo '
                                <option value="' . $user[0]['branchId'] . '" selected>' . $user[0]['branchName'] . '</option>
                                ';
                                            } else {
                                                if ($branches !== '') {
                                                    foreach ($branches as $row) {
                                                        echo '
                                  <option value="' . $row['id'] . '">' . $row['name'] . '</option>
                                  
                                  ';
                                                    }
                                                }
                                            }

                                            ?>

                                        </select>




                                    </div>
                                </div>



                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="text-label form-label">Payment Method *</label>

                                        <select name="method" class="me-sm-2 default-select form-control wide" id="inlineFormCustomSelect" style="display: none;" required>
                                            <option value="all" selected> All </option>
                                            <option value="cash">Cash</option>
                                            <option value="bank">Cheque/Bank Account/Mobile Money</option>
                                            <option value="online">Online Payment Methods</option>
                                        </select>
                                    </div>
                                </div>


                            </div><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail3">Start
                                            Date *</label>
                                        <input type="date" class="form-control" name="from_date" value="<?php echo date('Y-m-d'); ?>" id="exampleInputEmail3" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label" for="exampleInputEmail4">End
                                            Date *</label>
                                        <input type="date" class="form-control" name="end_date" value="<?php echo date('Y-m-d'); ?>" id="exampleInputEmail4" placeholder="End Date">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-label form-label"> </label>
                                        <button type="submit" class="btn btn-primary form-control">Fetch
                                            Entries</button>
                                    </div>
                                </div>

                            </div>

                        </form>


                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            All SMS Purchase Transactions
                        </h4>

                        <?php
                        if (isset($_GET['success'])) {
                            echo '<script type="text/javascript">
                    mySuccess();
                   </script>';
                            // unset($_SESSION['success']);
                        }
                        if (isset($_GET['error'])) {
                            echo '<script type="text/javascript">
                                    myError();
                                   </script>';
                        }

                        // unset($_SESSION['error']);

                        ?>
                        <!-- <div class="btn-group" role="group"> -->


                        <!-- </div> -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example3" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Bank</th>
                                        <th>Branch</th>
                                        <th>Date Created</th>
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



            <!-- </div>
            </div> -->
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
                url: '<?= BACKEND_BASE_URL ?>Bank/get_all_sms_purchases.php?branch=<?php echo $user[0]['branchId']; ?>&bank=<?php echo $user[0]['bankId']; ?>',
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
                    "data": "amount"
                }, {
                    "data": "status"
                }, {
                    "data": "pay_method"
                }, {
                    "data": "bname"
                }, {
                    "data": "branchname"
                }, {
                    "data": "dateCreated"
                }, {
                    "data": "actions",
                }]
            })

        }
    </script>

</body>

</html>