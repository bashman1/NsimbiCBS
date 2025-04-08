<?php
include('../backend/config/session.php');
require_once './includes/constants.php';
require_once('./middleware/PermissionMiddleware.php');
$permissions = new PermissionMiddleware();
if (!$permissions->IsBankStuff()) {
	return $permissions->isNotPermitted(true);
}

include_once('includes/response.php');
$response = new Response();

if (!empty($_GET['id']) && $_GET['id']) {
	$details = $response->getLoanDetails($_GET['id']);

	$cott = $response->getLoanCollaterals($_GET['id']);
	$gats = $response->getLoanGuarantors($_GET['id']);
}
$colats = '';
if ($cott != '') {
	foreach ($cott as $rown) {
		$colats = $colats  . $rown['_collateral'] . ' , ';
	}
}

$guarats = '';
if ($gats != '') {
	foreach ($gats as $row) {
		$guarats = $guarats  . $row['init'] . ' , ';
	}
}
$ftype = '';
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

$output = '';
$output .= '<table width="100%" border="1" cellpadding="5" cellspacing="0">
	<tr>
	<td colspan="2" align="center" style="font-size:15px"><b> '.$_GET['name'].'  - LOAN COMMITTEE REPORT </b></td>
	</tr>
	<tr>
	<td colspan="2">
	<table width="100%" cellpadding="5">
	<tr>
	<td width="65%">
	Customer\'s Name: ' . $details[0]['client']['firstName'] . ' ' . $details[0]['client']['lastName'] . '<br />
	<b> A/C No. ' . $details[0]['client']['membership_no'] . '</b><br />
	Available Balance : UGX ' . number_format($details[0]['client']['acc_balance']) . '<br /> 
	Loan Product : ' . $details[0]['product']['type_name'] . '<br />
	</td>
	<td width="35%">         
	Phone Number : ' . @$details[0]['client']['primaryCellPhone'] . ' / ' . @$details[0]['client']['secondaryCellPhone'] . '<br />
	Business Location : ' . @$details[0]['client']['addressLine1'] . ' , ' . @$details[0]['client']['addressLine2'] . ' , ' . @$details[0]['client']['country'] . '<br />
	Loan ID : ' . $details[0]['loan']['loan_no'] . '<br />
    Date of Application : ' . normal_date($details[0]['loan']['date_created']) . '<br />
	</td>
	</tr>
	</table>
	<br />
	<table width="100%" border="1" cellpadding="5" cellspacing="0">
	<tr>
	<th align="left">Item</th>
	<th align="left" >Proposed</th>
	<th align="left" >Approved</th>
	 
	</tr>';

$output .= '
	<tr>
	<td align="left">Amount</td>
	<td align="left">UGX ' . number_format($details[0]['loan']['principal']) . '</td>
  <td></td>
	</tr>
		<tr>
	<td align="left"> Duration - Repayment Cycle</td>
	<td align="left">' . $details[0]['loan']['approved_loan_duration'] . ' '.$ftype. ' - ' . $details[0]['product']['frequency'] . '</td>
  <td></td>
	</tr>
		<tr>
	<td align="left"> Total Interest</td>
	<td align="left">' . number_format($details[0]['loan']['interest_balance']) . '</td>
  <td></td>
	</tr>
		<tr>
	<td align="left"> Security</td>
	<td align="left">'.$colats. '</td>
  <td></td>
	</tr>
	<tr>
	<td align="left"> Guarantors</td>
	<td align="left">' . $guarats . '</td>
  <td></td>
	</tr>
	
	
	';


$output .= '
	</table>
	</td>
	</tr>
		<table width="100%" border="1" cellpadding="5" cellspacing="0">
		<tr>
	<th width="100%" align="centre">COMMITTEE MEMBERS PRESENT IN THE MEETING</th>
	</tr>
	<tr>
	<td width="35%">
	<br /></br>
	Name: <br /><br/>.......................................................<br>
	</td>
		<td width="35%">
	<br /></br>
	Comments : <br/><br/>..........................................................<br /> 

	</td>
	<td width="30%">   <br/></br>      
	Signature :<br /><br/>
	..........................................<br />
	</td>
	</tr>
		<tr>
	<td width="35%">
	<br /></br>
	Name: <br /><br/>.......................................................<br>
	</td>
		<td width="35%">
	<br /></br>
	Comments : <br/><br/>......................................................<br /> 

	</td>
	<td width="30%">   <br/></br>      
	Signature :<br /><br/>
	..........................................<br />
	</td>
	</tr>
		
		<tr>
	<td width="35%">
	<br /></br>
	Name: <br /><br/>.......................................................<br>
	</td>
		<td width="35%">
	<br /></br>
	Comments : <br/><br/>.....................................................<br /> 

	</td>
	<td width="30%">   <br/></br>      
	Signature :<br /><br/>
	..........................................<br />
	</td>
	</tr>
	
	</table>
	</table>';
// create pdf of invoice	
$invoiceFileName = 'Committee-Report-' . $details[0]['loan']['loan_no'] . '.pdf';
require_once 'vendor/dompdf/src/Autoloader.php';
Dompdf\Autoloader::register();

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml(html_entity_decode($output));
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($invoiceFileName, array("Attachment" => false));
