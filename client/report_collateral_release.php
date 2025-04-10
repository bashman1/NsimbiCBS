<?php
require_once('includes/response.php');
require_once('includes/ReportService.php');
require_once('includes/reports_css.php');
require_once('includes/reports_page_css.php');


$reponse = new Response();

?>

<?php
require_once('includes/report_header.php');
?>

<section class="report-section">
    <table class="main-header">
        <tr>
            <td> <strong> COLLATERAL RELEASE FORM </strong> </td>
        </tr>
        <tr>
            I, ...................................., hereby acknowledge that I have fulfilled all obligations under the loan agreement with ..............................................................., and as a result, I am entitled to the release of the collateral provided for the loan.
        </tr>

        <tr>
            <td> <strong>Loan Details </strong> </td>
        </tr>
        <tr>
            <td>Loan No.: </td>
            <td></td>
            <td>Loan Amount: </td>
            <td></td>
            <td>Loan Start Date: </td>
            <td></td>
            <td>Loan End Date: </td>
            <td></td>
        </tr>
        <tr>
            <td> <strong>Collateral Details </strong> </td>
        </tr>
        <tr>
            <td>Description of Collateral: </td>
            <td></td>
            <td>Estimated Value of Collateral: </td>
            <td></td>
            <td>Date of Collateral Deposit: </td>
            <td></td>

        </tr>
        <tr>
            <td> <strong>Release of Collateral </strong> </td>
        </tr>
        <tr>
            <td>
                I hereby request the release of the aforementioned collateral and confirm that I have no outstanding obligations to ........................................................... I understand that upon the release of the collateral, ................................................... will no longer hold any claim to it.
            </td>
        </tr>



        <tr>
            <td> <strong>Acknowledgment: </strong> </td>
        </tr>
        <tr>
            <td>
                I acknowledge that I have received all original documents related to the collateral and that I am solely responsible for any further dealings or transactions involving it.
            </td>
        </tr>

        <tr>
            <td>Signature of Borrower: </td>
            <td></td>
            <td>Date: </td>
            <td></td>

        </tr>
        <tr>
            <td>Signature of SACCO Representative: </td>
            <td></td>
            <td>Date: </td>
            <td></td>

        </tr>


    </table>
    <hr>


</section>