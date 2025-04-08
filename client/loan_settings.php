<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'LOAN PRODUCTS';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();


// var_dump($bank_details);
// exit;
if (isset($_POST['submit'])) {
    $res = $response->addCollateralCategory($_POST['id'], $_POST['name'], $_POST['description']);
    if ($res) {
        setSessionMessage(true, 'Collateral Category Created Successfully!');
        header('location:loan_settings.php');
    } else {
        setSessionMessage(false, 'Something went wrong! Try again to create the Category');
        header('location:loan_settings.php');
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
                                    Loan Settings
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#products" data-bs-toggle="tab" class="nav-link  <?= !in_array(@$_REQUEST['current_tab'], ['collateral']) ? 'active' : '' ?> ">Loan Products</a>
                                        </li>

                                        <li class="nav-item"><a href="#collateral" data-bs-toggle="tab" class="nav-link ">Collateral Categories</a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">
                                        <div id="products" class="tab-pane fade <?= !in_array(@$_REQUEST['current_tab'], ['collateral']) ? 'show active' : '' ?>" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Loan Products</h4>
                                                    <a href="add_loan_product.php" class="btn btn-primary light btn-xs mb-1">Add New Loan Product</a>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="loan_products_table" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>No. of Loans</th>
                                                                    <th>Interest Method</th>
                                                                    <th>Rate/Frequency</th>
                                                                    <th>Has Fees</th>
                                                                    <th>Fees Details</th>
                                                                    <th>Has Penalty</th>
                                                                    <th>Penalty Interest Rate</th>
                                                                    <th>Penalty Fixed Amount</th>
                                                                    <th>Grace Period</th>
                                                                    <th>Max. Penalty Days</th>
                                                                    <th>Status</th>
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

                                        <div id="collateral" class="tab-pane fade ">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Collateral Categories</h4>
                                                    <a class="btn btn-primary light btn-xs mb-1" type="button" aria-expanded="false" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3">Add New Collateral Category</a>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="collateral_table" class="display dataTable">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Description</th>
                                                                    <th>Total Collaterals</th>

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
                            <h5 class="modal-title">Add New Collateral Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>


                        <form method="POST">
                            <input type="hidden" name="id" class="form-control" placeholder="" value="<?php echo $user[0]['bankId']; ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="text-label form-label">Category's Name*
                                    </label>
                                    <input type="text" name="name" class="form-control" placeholder="">
                                </div>
                                <div class="mb-3">
                                    <label class="text-label form-label">Description*
                                    </label>
                                    <input type="text" name="description" class="form-control" placeholder="">
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="submit" name="submit" class="btn btn-primary">Create Category</button>
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

        <script>
            $('.is-back-btn').each(function() {
                $(this).addClass('hide');
                if (history.length) {
                    $(this).removeClass('hide');
                }
            });

            $('body').on('click', '.is-back-btn', function(event) {
                event.preventDefault();
                history.back();
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_collateral_categories.php?branch=<?php echo $user[0]['branchId']; ?>&bank=<?php echo $user[0]['bankId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        getCollateralCategories(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function getCollateralCategories(data) {

                var table = $('#collateral_table').dataTable({
                    destroy: true,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                            previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                        }
                    },

                    "aaData": data,

                    "columns": [{
                        "data": "_catid"
                    }, {
                        "data": "_catname"
                    }, {
                        "data": "_catdesc"
                    }, {
                        "data": "tot"
                    }, {
                        "data": "actions",
                    }]
                })

            }
        </script>

        <script type="text/javascript">
            $(document).ready(function() {

                $.ajax({
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_all_loan_products.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        getLoanProducts(data.data);
                        // console.log(data.data);
                    }
                });

            });

            function getLoanProducts(data) {

                var table = $('#loan_products_table').dataTable({
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
                        "data": "loans"
                    }, {
                        "data": "method"
                    }, {
                        "data": "rate"
                    }, {
                        "data": "hasfee"
                    }, {
                        "data": "fees"
                    }, {
                        "data": "haspenalty"
                    }, {
                        "data": "penaltyrate"
                    }, {
                        "data": "penaltyfixed"
                    }, {
                        "data": "graceperiod"
                    }, {
                        "data": "maxdays",
                    }, {
                        "data": "status",
                    }, {
                        "data": "actions",
                    }]
                })

            }
        </script>

</body>

</html>