<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();
// $collates = $reponse->getLoanSchedule($_GET['id']);
// $details = $reponse->getLoanDetails($_GET['id']);

// $repays = $reponse->getLoanSchedule($_GET['id']); 
// $loan_details = @$details[0]['loan'];

$freq = '';
$dur = '';
?>

<?php
require_once('includes/report_header.php');
?>



<section class="report-section">
    <table class="main-header">
        <tr>
            <td> <strong> Report Summary </strong> </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    </table>
    <hr>
    <table class="main-header">
        <tr>
            <td> <strong> ACTIVE LOANS REPORT </strong> </td>
        </tr>
    </table>

    <table class="report_table">
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
                <th rowspan="2">STATUS:</th>
                <th colspan="4">AMOUNT PAID:</th>

                <th colspan="4">OUSTANDING BALANCE:</th>

                <th colspan="4">DUE:</th>

                <th colspan="4">ARREARS:</th>

                <th colspan="3">WAIVED:</th>






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

            </tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot>

        </tfoot>
    </table>

</section>