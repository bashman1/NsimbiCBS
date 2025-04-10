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
$_REQUEST['disbursement_start_date'] = $_REQUEST['disbursement_start_date'] ?? '';
$_REQUEST['disbursement_end_date'] = $_REQUEST['disbursement_end_date'] ?? '';
$_REQUEST['branchId'] = $_REQUEST['branchId'] ?? @$user[0]['branchId'];
$_REQUEST['loan_product_id'] = $_REQUEST['loan_product_id'] ?? '';
$_REQUEST['loan_officer_id'] = $_REQUEST['loan_officer_id'] ?? '';

$loans = $response->getAllActiveLoans2(@$user[0]['bankId'], @$_REQUEST['branchId'], @$_REQUEST['loan_product_id'], @$_REQUEST['loan_officer_id'], @$_REQUEST['disbursement_start_date'], @$_REQUEST['disbursement_end_date']);
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
                                    Loans Ageing Report
                                </h4>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="share_purchase_transactions2" class="table table-striped dataTable" style="min-width: 845px">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">#</th>
                                                <th colspan="4">CLIENT</th>

                                                <th rowspan="2">CREDIT OFFICER</th>
                                                <th rowspan="2">LOAN PRODUCT</th>
                                                <th rowspan="2">ENCONOMIC SECTOR</th>
                                                <th rowspan="2">HAS COLLATERAL</th>

                                                <th rowspan="2">LOAN AMOUNT:</th>

                                                <th colspan="2">INTEREST:</th>

                                                <th colspan="3">LOAN TERM:</th>

                                                <th colspan="2">LAST REPAYMENT:</th>
                                                <th colspan="3">EXPECTED PAYMENT (THIS MONTH):</th>
                                                <th colspan="3">AMOUNT PAID (THIS MONTH):</th>
                                                <th colspan="3">PREPAYMENTS (THIS MONTH):</th>
                                                <th rowspan="2">STATUS:</th>
                                                <th colspan="4">AMOUNT PAID (SINCE):</th>

                                                <th colspan="4">OUSTANDING BALANCE:</th>

                                                <th colspan="4">DUE:</th>


                                                <th colspan="4">ARREARS:</th>

                                                <th colspan="3">WAIVED:</th>
                                                <th colspan="7">AGEING OF LOAN PORTIFOLIO IN ARREARS:</th>






                                            </tr>
                                            <tr>

                                                <!-- client info -->
                                                <th>A/C No:</th>
                                                <th>NAME:</th>
                                                <th>GENDER:</th>
                                                <th>SAVINGS:</th>

                                                <!-- Loan Interest -->
                                                <th>INTEREST RATE / ANNUM:</th>
                                                <th>EXPECTED INTEREST:</th>

                                                <!-- Loan term -->
                                                <th>DISBURSEMENT DATE:</th>
                                                <th>DURATION:</th>
                                                <th>EXPIRY DATE:</th>
                                                <!-- last repayment -->
                                                <th>DATE</th>
                                                <th>AMOUNT</th>

                                                <!-- expected payment this filtered period -->
                                                <th>PRINCIPAL</th>
                                                <th>INTEREST</th>
                                                <th>TOTAL</th>

                                                <!-- payment this filtered period -->
                                                <th>PRINCIPAL</th>
                                                <th>INTEREST</th>
                                                <th>TOTAL</th>

                                                <!-- pre-payment this filtered period -->
                                                <th>PRINCIPAL</th>
                                                <th>INTEREST</th>
                                                <th>TOTAL</th>



                                                <!-- Amount Paid  -->
                                                <th>PRINCIPAL:</th>
                                                <th>INTEREST:</th>
                                                <th>PENALTY:</th>
                                                <th>TOTAL:</th>

                                                <!-- Loan Balance  -->
                                                <th>PRINCIPAL:</th>
                                                <th>INTEREST:</th>
                                                <th>PENALTY:</th>
                                                <th>TOTAL:</th>

                                                <!-- Loan Dues  -->
                                                <th>PRINCIPAL:</th>
                                                <th>INTEREST:</th>
                                                <th>TOTAL:</th>
                                                <th>DUE DATE:</th>

                                                <!-- Loan Arrears  -->
                                                <th>PRINCIPAL:</th>
                                                <th>INTEREST:</th>
                                                <th>TOTAL:</th>
                                                <th>DAYS:</th>

                                                <!-- Loan Waivers  -->
                                                <th>INTEREST:</th>
                                                <th>PENALTY:</th>
                                                <th>TOTAL:</th>

                                                <!-- ageing the principal balance -->
                                                <th>1-30 DAYS (0%):</th>
                                                <th>31-60 DAYS (15%):</th>
                                                <th>61-90 DAYS (30%):</th>
                                                <th>91-120 DAYS (45%):</th>
                                                <th>121-150 DAYS (60%):</th>
                                                <th>151-180 DAYS (75%):</th>
                                                <th>180+ (100%):</th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            $ftype = '';
                                            $st = '';
                                            $exp_date = '';
                                            $days_in_arrears = '';
                                            if (@$loans) {
                                                foreach ($loans as $row) {
                                                    $one = 0;
                                                    $two = 0;
                                                    $three = 0;
                                                    $four = 0;
                                                    $five = 0;
                                                    $six = 0;
                                                    $seven = 0;
                                                    if ($row['repay_cycle_id'] == 1) {
                                                        $ftype = 'DAYS';
                                                    } else if (
                                                        $row['repay_cycle_id']  == 2
                                                    ) {
                                                        $ftype = 'WEEKS';
                                                    } else if (
                                                        $row['repay_cycle_id']  == 3
                                                    ) {
                                                        $ftype = 'MONTHS';
                                                    } else if (
                                                        $row['repay_cycle_id']  == 4
                                                    ) {
                                                        $ftype = 'DAYS';
                                                    } else if (
                                                        $row['repay_cycle_id']  == 5
                                                    ) {
                                                        $ftype = 'YEARS';
                                                    }


                                                    if ($row['status'] == 2) {
                                                        $st = 'ACTIVE-ONTIME';
                                                    } else  if ($row['status'] == 3) {
                                                        $st = 'ACTIVE-DUE';
                                                    } else  if ($row['status'] == 4) {
                                                        $st = 'ACTIVE-OVERDUE';
                                                    }

                                                    if (@$row['arrearsbegindate'] && (@$row['principal_arrears'] + @$row['interest_arrears']) > 0) {
                                                        $now = time(); // or your date as well
                                                        $your_date = strtotime(@$row['arrearsbegindate']);
                                                        $datediff = $now - $your_date;

                                                        $days_in_arrears =  round($datediff / (60 * 60 * 24));
                                                    } else {
                                                        $days_in_arrears = '';
                                                    }

                                                    $dur = (@$row['approved_loan_duration'] . ' ' . $ftype);

                                                    $exp_date = normal_date(date('Y-m-d', strtotime("+" . $dur, strtotime(date('Y-m-d', strtotime("+ 1" . $ftype, strtotime(@$row['date_disbursed'])))))));

                                                    if ($days_in_arrears && $days_in_arrears > 0 && $days_in_arrears <= 30) {
                                                        $one = $row['principal_balance'];
                                                    }

                                                    if ($days_in_arrears && $days_in_arrears > 30 && $days_in_arrears <= 60) {
                                                        $two = $row['principal_balance'];
                                                    }

                                                    if ($days_in_arrears && $days_in_arrears > 60 && $days_in_arrears <= 90) {
                                                        $three = $row['principal_balance'];
                                                    }

                                                    if ($days_in_arrears && $days_in_arrears > 90 && $days_in_arrears <= 120) {
                                                        $four = $row['principal_balance'];
                                                    }

                                                    if ($days_in_arrears && $days_in_arrears > 120 && $days_in_arrears <= 150) {
                                                        $five = $row['principal_balance'];
                                                    }

                                                    if ($days_in_arrears &&  $days_in_arrears > 150 && $days_in_arrears <= 180) {
                                                        $six = $row['principal_balance'];
                                                    }

                                                    if ($days_in_arrears && $days_in_arrears > 180) {
                                                        $seven = $row['principal_balance'];
                                                    }


                                                    echo '
                                                    
                                                     <tr style="text-align: center !important;">
                                                     <td>' . $row['loan_no'] . '</td>
                                                     <td>' . $row['ac_no'] . '</td>
                                                     <td>' . $row['client_name'] . '</td>
                                                     <td>' . (@$row['client_gender'] ?? '') . '</td>
                                                     <td>' . number_format($row['ac_bal'] ?? 0) . '</td>
                                                     <td>' . @$row['credit_officer'] . '</td>
                                                     <td>' . $row['product_name'] . '</td>
                                                     <td>' . $row['enconomic_sector'] . '</td>
                                                     <td>' . (@$row['has_collateral'] ? 'YES' : 'NO') . '</td>
                                                     <td>' . number_format($row['principal'] ?? 0) . '</td>
                                                     <td>' . @$row['monthly_interest_rate'] . '</td>
                                                     <td>' . number_format(@$row['interest_amount'] ?? 0) . '</td>
                                                     <td>' . normal_date_short(@$row['date_disbursed']) . '</td>
                                                     <td>' . (@$row['approved_loan_duration'] . ' ' . $ftype) . '</td>
                                                     <td>' . $exp_date . '</td>
                                                     <td>' . normal_date_short(@$row['last_trxn_date']) . '</td>
                                                     <td>' . number_format(@$row['last_trxn_amount'] ?? 0) . '</td>
                                                      <td>' . number_format($row['amount_exp_month'] ?? 0) . '</td>
                                                         <td>' . number_format($row['int_exp_month'] ?? 0) . '</td>
                                                           <td>' . number_format(($row['amount_exp_month'] ?? 0) + ($row['int_exp_month'] ?? 0)) . '</td>

                                                       <td>' . number_format($row['amount_paid_month'] ?? 0) . '</td>
                                                         <td>' . number_format($row['int_paid_month'] ?? 0) . '</td>
                                                           <td>' . number_format(($row['amount_paid_month'] ?? 0) + ($row['int_paid_month'] ?? 0)) . '</td>

                                                             <td>' . number_format(max((($row['amount_paid_month'] ?? 0) - ($row['amount_exp_month'] ?? 0)), 0)) . '</td>
                                                         <td>' . number_format(max((($row['int_paid_month'] ?? 0) - ($row['int_exp_month'] ?? 0)), 0)) . '</td>
                                                           <td>' . number_format(max((($row['int_paid_month'] ?? 0) - ($row['int_exp_month'] ?? 0)), 0) + max((($row['amount_paid_month'] ?? 0) - ($row['amount_exp_month'] ?? 0)), 0)) . '</td>
                                                     <td>' . $st . '</td>
                                                     <td>' . number_format($row['principal'] - $row['principal_balance']) . '</td>
                                                     <td>' . number_format($row['interest_amount'] - $row['interest_balance']) . '</td>
                                                     <td></td>
                                                     <td>' . number_format(@$row['amount_paid'] ?? 0) . '</td>
                                                     <td>' . number_format($row['principal_balance'] ?? 0) . '</td>
                                                     <td>' . number_format($row['interest_balance'] ?? 0) . '</td>
                                                     <td>' . number_format($row['penalty_balance'] ?? 0) . '</td>
                                                     <td>' . number_format($row['current_balance'] ?? 0) . '</td>
                                                     <td>' . number_format($row['principal_due'] ?? 0) . '</td>
                                                     <td>' . number_format($row['interest_due'] ?? 0) . '</td>
                                                     <td>' . number_format($row['principal_due'] + $row['interest_due']) . '</td>
                                                     <td>' . normal_date_short($row['date_of_next_pay']) . '</td>
                                                      
                                                     <td>' . number_format($row['principal_arrears'] ?? 0) . '</td>
                                                     <td>' . number_format($row['interest_arrears'] ?? 0) . '</td>
                                                     <td>' . number_format(($row['principal_arrears'] ?? 0) + ($row['interest_arrears'] ?? 0)) . '</td>
                                                     <td>' . @$days_in_arrears . '</td>
                                                     <td>' . number_format(@$row['int_waivered'] ?? 0) . '</td>
                                                     <td>' . number_format($row['penalty_waivered'] ?? 0) . '</td>
                                                     <td>' . number_format(($row['int_waivered'] ?? 0) + ($row['penalty_waivered'] ?? 0)) . '</td>

                                                    <td>' . number_format($one) . '</td>
                                                    <td>' . number_format($two) . '</td>
                                                    <td>' . number_format($three) . '</td>
                                                    <td>' . number_format($four) . '</td>
                                                    <td>' . number_format($five) . '</td>
                                                    <td>' . number_format($six) . '</td>
                                                    <td>' . number_format($seven) . '</td>
                                                     
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