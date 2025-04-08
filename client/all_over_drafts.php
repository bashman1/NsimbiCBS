<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once './includes/functions.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
$title = 'OVER-DRAFTS';
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
                                    Over-Drafts
                                </h5>
                                <!-- <h5 class="m-0 subtitle">Accounting</h5> -->
                            </div>
                            <div class="card-body">
                                <div class="default-tab">
                                    <!-- <div class="default-tab"> -->

                                    <ul class="nav nav-tabs" role="tablist">

                                        <li class="nav-item"><a href="#products" data-bs-toggle="tab" class="nav-link active ">Over-Drafts</a>
                                        </li>



                                    </ul>
                                    <div class="tab-content">
                                        <div id="products" class="tab-pane fade show active" role="tabpanel">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-primary mb-4">All Over Drafts</h4>
                                                    <a href="search_client_overdraft.php" class="btn btn-primary light btn-xs mb-1">Apply for Over-Draft</a>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="loan_products_table" class="table table-striped" style="min-width: 845px">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th colspan="2" style="align-items:center !important;">Principal</th>
                                                                    <th colspan="2" style="align-items:center !important;">Interest / Charge</th>
                                                                    <th colspan="3" style="align-items:center !important;">Penalty</th>
                                                                    <th colspan="2" style="align-items:center !important;">Date</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>A/C No.</th>
                                                                    <th>Amount</th>
                                                                    <th>Period</th>
                                                                    <th>Over-Draft Product</th>
                                                                    <th>Status</th>
                                                                    <th>Offered</th>
                                                                    <th>Balance</th>
                                                                    <th>Total</th>
                                                                    <th>Balance</th>
                                                                    <th>Days in Arrears</th>
                                                                    <th>Total Penalty</th>
                                                                    <th>Penalty Balance</th>
                                                                    <th>Application Date</th>
                                                                    <th>Approval Date</th>
                                                                    <th>Maturity Date</th>
                                                                    <th>Branch</th>
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
                    url: '<?= BACKEND_BASE_URL ?>Bank/get_over_drafts.php?bank=<?php echo $user[0]['bankId']; ?>&branch=<?= $user[0]['bankId'] == '' ? $user[0]['branchId'] : '' ?>',
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
                        "data": "client_name"
                    }, {
                        "data": "acno"
                    }, {
                        "data": "amount"
                    }, {
                        "data": "period"
                    }, {
                        "data": "product"
                    }, {
                        "data": "status"
                    }, {
                        "data": "amount"
                    }, {
                        "data": "princ_balance"
                    }, {
                        "data": "interest"
                    }, {
                        "data": "interest_bal"
                    }, {
                        "data": "arrears_days"
                    }, {
                        "data": "penalty_total"
                    }, {
                        "data": "penalty_balance"
                    }, {
                        "data": "appln_date"
                    }, {
                        "data": "approval_date"
                    }, {
                        "data": "maturity_date"
                    }, {
                        "data": "branch"
                    }, {
                        "data": "actions"
                    }]
                })

            }
        </script>

</body>

</html>