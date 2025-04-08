<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'OVER-DRAFT PRODUCTS';
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
                                    Over-Draft Products
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#products" data-bs-toggle="tab" class="nav-link active ">Products</a>
                                        </li>



                                    </ul>
                                    <div class="tab-content">
                                        <div id="products" class="tab-pane fade show active" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Over Draft Products</h4>
                                                    <a href="add_over_draft_product.php" class="btn btn-primary light btn-xs mb-1">Add New Product</a>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="loan_products_table" class="display" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Period Type</th>
                                                                    <th>Max. Amount</th>
                                                                    <th>Max. Period</th>
                                                                    <th>Interest</th>
                                                                    <th>Penalty</th>
                                                                    <th>Affected Interest Income A/C</th>
                                                                    <th>Affected Penalty Income A/C</th>
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
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_over_draft_products.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?php echo $user[0]['branchId']; ?>',
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
                        "data": "period_type"
                    }, {
                        "data": "max_amount"
                    }, {
                        "data": "max_period"
                    }, {
                        "data": "interest"
                    }, {
                        "data": "penalty"
                    }, {
                        "data": "int_acc"
                    }, {
                        "data": "pen_acc"
                    }, {
                        "data": "actions"
                    }]
                })

            }
        </script>

</body>

</html>