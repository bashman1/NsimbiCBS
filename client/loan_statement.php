<?php
include('../backend/config/session.php');
?>
<?php

if (!isset($_SESSION['user']) && $_SESSION['user'] == "") {
    header('location: login.php');
    // exit();
}
$title = 'LOAN STATEMENT';
require_once('includes/head_tag.php');
include_once('includes/response.php');
$response = new Response();
$details = $response->getLoanDetails($_GET['id']);
$repays = $response->getLoanRepayments($_GET['id']);

?>

<?php
if ($details[0]['loan']['repay_cycle_id'] == 1) {
    $ftype = 'DAYS';
} else if ($details[0]['loan']['repay_cycle_id']  == 2) {
    $ftype = 'WEEKS';
} else if ($details[0]['loan']['repay_cycle_id']  == 3) {
    $ftype = 'MONTHS';
} else if ($details[0]['loan']['repay_cycle_id']  == 4) {
    $ftype = 'DAYS';
} else if ($details[0]['loan']['repay_cycle_id']  == 5) {
    $ftype = 'YEARS';
}

$dur = number_format(@$details[0]['loan']['approved_loan_duration']) . ' ' . $ftype;
?>

<?php

if ($details[0]['loan']['repay_cycle_id'] == 1) {
    $freq = '  DAILY';
} else if ($details[0]['loan']['repay_cycle_id'] == 2) {
    $freq = '  WEEKLY';
} else if ($details[0]['loan']['repay_cycle_id'] == 3) {
    $freq = '  MONTHLY';
} else if ($details[0]['loan']['repay_cycle_id'] == 4) {
    $freq =  '  DAILY';
} else if ($details[0]['loan']['repay_cycle_id'] == 5) {
    $freq = '  ANNUALLY';
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
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-primary">
                            <a href="javascript:;" class="btn btn-primary light btn-xs mb-1 is-back-btn hide"><i class="fa fa-arrow-left"></i> Back</a>
                            Loan Statement
                        </h4>

                        <a class="btn btn-primary light btn-xs mb-1" href="export_report.php?exportFile=export_loan_statement&useFile=1&id=<?= @$details[0]['loan']['loan_no'] ?>" target="_blank">Print</a>
                    </div>
                    <div class="card-body" id="exreportn">
                        <!--<div class="table-responsive">-->

                        <br />
                        <table class="table table-striped" style="min-width: 845px">
                            <thead>
                                <tr colspan="8">
                                    <th colspan="8" style="text-align: center !important;"> LOAN DETAILS</th>
                                </tr>
                            </thead>
                            <tbody>


                                <tr>
                                    <td>Client Name: </td>
                                    <td><?= $details[0]['client']['firstName'] . ' ' . $details[0]['client']['lastName']  ?></td>
                                    <td>A/C No.: </td>
                                    <td><?= $details[0]['client']['membership_no']; ?></td>
                                </tr>
                                <tr>
                                    <td>Contacts: </td>
                                    <td><?= @$details[0]['client']['primaryCellPhone'] . ' / ' . @$details[0]['client']['secondaryCellPhone']; ?></td>
                                    <td>Physical Address: </td>
                                    <td><?= @$details[0]['client']['addressLine1'] . ' , ' . @$details[0]['client']['addressLine2'] . ' , ' . @$details[0]['client']['country'] ?></td>
                                </tr>

                                <tr>
                                    <td>Loan Amount: </td>
                                    <td><?php echo number_format($details[0]['loan']['principal']); ?></td>
                                    <td>Total Interest: </td>
                                    <td><?= number_format(($details[0]['loan']['interest_amount'] ?? 0) + ($details[0]['loan']['int_waivered'] ?? 0))  ?> Waived Interest: <?= number_format($details[0]['loan']['int_waivered'] ?? 0)  ?></td>
                                 

                                </tr>
                                <tr>
                                    <td>Disbursed Net Amount: </td>
                                    <td>UGX <?php echo number_format($details[0]['loan']['principal'] - ($details[0]['loan']['deductions'] ?? 0)); ?></td>
                                    <td>Total Deductions: </td>
                                    <td>UGX <a href="loan_attached_fees.php?id=<?= $details[0]['loan']['loan_no'] ?>"><?= number_format($details[0]['loan']['deductions'] ?? 0)  ?></a></td>

                                </tr>
                                <tr>
                                    <td>Duration: </td>
                                    <td><?php echo $dur; ?></td>
                                    <td>Loan No.: </td>
                                    <td><?php echo $details[0]['loan']['loan_no']; ?></td>

                                </tr>
                                <tr>
                                    <td>Loan Product: </td>
                                    <td><?php echo $details[0]['product']['type_name']; ?></td>
                                    <td>Interest Rate: </td>
                                    <td><?php echo $details[0]['loan']['monthly_interest_rate'] . '% PER ANNUM'; ?></td>

                                </tr>
                                <tr>
                                    <td>Frequency: </td>
                                    <td><?php echo $freq ?></td>
                                    <td>Grace Period: </td>
                                    <td><?php echo $details[0]['loan']['num_grace_periods'] ?? 0; ?> Days</td>

                                </tr>
                                <tr>
                                    <td>Application Date: </td>
                                    <td><?php echo normal_date($details[0]['loan']['application_date']); ?></td>
                                    <td>Approval Date: </td>
                                    <td><?php echo normal_date($details[0]['loan']['date_disbursed']); ?></td>

                                </tr>
                                <tr>
                                    <td>Disbursement Date: </td>
                                    <td><?php echo normal_date($details[0]['loan']['date_disbursed']); ?></td>
                                    <td>Maturity Date: </td>
                                    <td><?php echo normal_date(date('Y-m-d', strtotime("+" . $dur, strtotime(date('Y-m-d', strtotime("+ 1" . $ftype, strtotime($details[0]['loan']['requesteddisbursementdate']))))))); ?></td>

                                </tr>

                                <tr>
                                    <td>Next Due Date: </td>
                                    <td><?php echo normal_date($details[0]['loan']['date_of_next_pay']); ?></td>
                                    <td>Credit Officer: </td>
                                    <td><?php echo @$details[0]['staff']['firstName'] . ' ' . @$details[0]['staff']['lastName'] . ' - ' . @$details[0]['staff']['positionTitle']; ?></td>

                                </tr>
                                <tr>
                                    <td>Amount Due: </td>
                                    <td><?php echo number_format($details[0]['loan']['principal_due'] + $details[0]['loan']['interest_due']); ?></td>
                                    <td>Amount in Arrears: </td>
                                    <td><?php echo number_format($details[0]['loan']['principal_arrears'] + $details[0]['loan']['interest_arrears']); ?></td>

                                </tr>



                            </tbody>
                        </table>
                        <br />


                        <table class="table table-striped" style="min-width: 845px" id="example3">
                            <thead>

                                <tr>
                                    <th colspan="7" style="text-align:center;">
                                        <h4 class="page-title">LOAN REPAYMENTS</h4>
                                    </th>
                                </tr>

                                <tr style="text-align: center !important;">
                                    <th>#</th>
                                    <th>Repayment Date</th>
                                    <th>Amount Paid</th>
                                    <th>Principal Paid</th>
                                    <th>Interest Paid</th>
                                    <th>Penalty Paid</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody><?php
                                    $i = 1;
                                    $total_amount = 0;
                                    $total_principal = 0;
                                    $total_interest = 0;
                                    $total_penalty = 0;

                                    $total_ob = $details[0]['loan']['approvedamount'] + $details[0]['loan']['interest_amount'] + $details[0]['loan']['penalty_balance'] + $details[0]['loan']['int_waivered'];
                                    if ($repays != '') {
                                        $usetotal = $total_ob;
                                        foreach ($repays as $row) {
                                            $total_amount = $total_amount + $row['amount'];
                                            // $usetotal = $usetotal - $total_amount;
                                            $total_principal = $total_principal + $row['principal'];
                                            $total_interest = $total_interest + $row['interest'];
                                            $total_penalty = $total_penalty + $row['penalty'];

                                            echo '
                                                    <tr style="text-align: center !important;">
                                       <td class="no_print clickable_ref_no" ref-no="' . $row['ref'] . ' " tid="' . $row['id'] . '">' . $row['ref'] . '</td>
                                        <td>' . $row['date_created'] . '</td>
                                       
                                          <td>' . number_format($row['amount'] ?? 0) . '</td>
                                        <td>' . number_format($row['principal'] ?? 0) . '</td>
                                        <td> ' . number_format($row['interest'] ?? 0) . '</td>
                                        <td> ' . number_format($row['penalty'] ?? 0) . '</td>
                                        <td>' . number_format($total_ob - $total_amount) . '</td>
                                  
                                        </tr>

                                        ';

                                            $i++;
                                        }
                                    }

                                    ?>

                            </tbody>

                            <tfoot>

                                <?php
                                echo '
                <tr style="text-align: center !important;">
                <td></td>
                <td><strong>Total</strong></td>
                <td> <strong>' . number_format($total_amount) . '</strong></td>
                <td> <strong>' . number_format($total_principal) . '</strong></td>
                <td><strong> ' . number_format($total_interest) . '</strong></td>
                <td><strong> ' . number_format($total_penalty) . '</strong></td>
                <td><strong> ' . number_format($total_ob - $total_amount) . '</strong></td>
              
                </tr>
                ';
                                ?>

                            </tfoot>

                        </table>


                        <!--</div>-->
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
        <div class="modal fade" id="pageGeneralModal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>

                </div>
            </div>
        </div>
        <!--**********************************
        Scripts
    ***********************************-->
        <!-- Required vendors -->
        <?php
        include('includes/bottom_scripts.php');
        ?>





</body>

</html>