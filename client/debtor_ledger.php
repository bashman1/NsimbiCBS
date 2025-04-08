<?php
include('../backend/config/session.php');

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    exit();
}


require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
?>
<?php
$title = 'DEBTOR LEDGER';
require_once('includes/head_tag.php');
?>
<?php
include_once('includes/response.php');
$response = new Response();

$details = $response->getDebtorDetails($_GET['id'])[0];
// print_r($details);
// exit;
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
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Debtor Ledger
                                </h4>

                                <!-- <div class="btn-group" role="group"> -->
                                <button type="button" class="btn btn-primary"> <a href="register_receivable.php?id=<?= @$_REQUEST['id'] ?>" style="color:#fff;">Register Receivable</a></button>

                            </div>
                            <div class="card-body">
                                <div class="col-md-5">
                                    <h4 class="mt-0 header-title">Debtor Details</h4>
                                    <p class="text-muted mb-3"></p>

                                    <ul class="list-unstyled pricing-content-2 text-left py-1 border-0 mb-3">
                                        <li><b>NAMES </b> : <?= @$details['name'] ?></li>
                                         <li><b>CHART ACCOUNT </b> : <?= @$details['chart'] ?></li>
                                        <li><b>CREATED ON </b> : <?= normal_date(@$details['date_created']) ?></li>
                                        <li><b>Total Payable </b> : <?= @$details['receivable'] ?></li>
                                        <li><b>Total Paid </b> : <?= @$details['paid'] ?></li>
                                        <li><b>Oustanding </b> : <?= @$details['oustanding'] ?></li> 
                                    </ul>
                                </div>
                                <hr>
                                <div class="table-responsive recentOrderTable">
                                    <table id="roles" class="table table-striped" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Record Date</th>
                                                <th scope="col">Maturity Date</th>
                                                <th scope="col">Branch</th>
                                                <th scope="col">Pay Method</th>
                                                <th scope="col">Paid Back</th>
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
                url: '<?= BACKEND_BASE_URL ?>/Bank/get_all_debtor_receivables.php?id=<?= $_GET['id'] ?>',
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
                    "data": "amount"
                }, {
                    "data": "description"
                }, {
                    "data": "rdate"
                }, {
                    "data": "mdate"
                }, {
                    "data": "branch"
                }, {
                    "data": "pmeth"
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