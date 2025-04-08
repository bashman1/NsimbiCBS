<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
    return $permissions->isNotPermitted(true);
}
// $permiss
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();

// check & set filters
$_REQUEST['disbursement_start_date'] = $_REQUEST['disbursement_start_date'] ?? date('m-01-Y');
$_REQUEST['disbursement_end_date'] = $_REQUEST['disbursement_end_date'] ?? date('m-t-Y');
$_REQUEST['branchId'] = $_REQUEST['branchId'] ?? @$user[0]['branchId'];
$_REQUEST['loan_product_id'] = $_REQUEST['loan_product_id'] ?? '';
$_REQUEST['loan_officer_id'] = $_REQUEST['loan_officer_id'] ?? '';


$loans = $response->getAllLoanDisbursements(@$user[0]['bankId'], @$_REQUEST['branchId'], @$_REQUEST['loan_product_id'], @$_REQUEST['loan_officer_id'], @$_REQUEST['disbursement_start_date'], @$_REQUEST['disbursement_end_date']);
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

                    <div class="col-md-12">

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                                    Loan Disbursement Report
                                </h4>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="share_purchase_transactions2" class="table table-striped dataTable" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Loan Amount</th>
                                                <th>A/C</th>
                                                <th>Client</th>
                                                <th>Loan Product</th>
                                                <th>Mode of Disbursement</th>

                                            </tr>

                                        </thead>
                                        <tbody>

                                            <?php

                                            if (@$loans) {
                                                foreach ($loans as $row) {

                                                    echo '
                                                    
                                                     <tr style="text-align: center !important;">
                                                     <td>' . $row['loan_no'] . '</td>
                                                     <td>' . normal_date_short($row['date_created']) . '</td>
                                                     <td>' . number_format($row['amount']) . '</td>
                                                      <td>' . @$row['ac_no'] . '</td>
                                                     <td>' . @$row['client_name'] . '</td>
                                                     <td>' . $row['product_name'] . '</td>
                                                    
                                                     <td>' . @$row['pay_method'] . '</td>
                                                    

                                                     </tr>
                                                    
                                                    
                                                    
                                                    
                                                    
                                                    ';
                                                }
                                            }


                                            ?>


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


    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#share_purchase_transactions2').DataTable({
                destroy: true,
                "pageLength": -1, // Show all rows by default
                "lengthMenu": [
                    [-1, 10, 25, 50],
                    ["All", 10, 25, 50]
                ], // Options for the length menu

                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5'
                ]
            });
        });
    </script>
</body>

</html>