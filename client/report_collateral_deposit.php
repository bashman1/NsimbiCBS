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
            <td> <strong> COLLATERAL DEPOSIT FORM </strong> </td>
        </tr>
        <tr>
            <td> <strong> Borrower's Information </strong> </td>
        </tr>
        <tr>
            <td>Full Name: </td>
            <td></td>
            <td>Membership No.: </td>
            <td></td>
            <td>Address: </td>
            <td></td>
            <td>Contacts: </td>
            <td></td>
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
            <td> <strong>Collateral Acknowledgment </strong> </td>
        </tr>
        <tr>
            <td>
                I, the undersigned, hereby acknowledge that I am depositing the aforementioned collateral with .............................................................................................. as security for the loan referenced above. I understand and agree that .............................................................................................. has the right to hold and manage the collateral until the loan is fully repaid.
            </td>
        </tr>

        <tr>
            <td> <strong>Condition of Collateral: </strong> </td>
        </tr>
        <tr>
            <td>
                I confirm that the collateral is in good condition and free from any encumbrances or claims by third parties. I understand that any damages to or diminishment of the collateral's value may affect the terms of the loan agreement.
            </td>
        </tr>

        <tr>
            <td> <strong>Acknowledgment: </strong> </td>
        </tr>
        <tr>
            <td>
                I acknowledge that I have received a copy of this Collateral Deposit Form for my records.
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