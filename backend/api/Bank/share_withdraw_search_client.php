<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['branch'];
$item->createdById = $_GET['bank'];
if ($_GET['bank'] == '') {
    $stmt = $item->getShareBranchClients($_GET['term']);
} else {
    $stmt = $item->getShareBankClients($_GET['term']);
}


$itemCount = $stmt->rowCount();
$return_string = 'No Results Found';



if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>#</th>
         <th>Names & NIN</th> 
         <th>Account No</th>
         <th>Shares</th>
         <th>Branch</th>
         <th class="no_print">Action</th>
         </thead>
         <tbody>';

    foreach ($stmt as $r) {

        $return_string .= "<tr><td><img class='rounded-circle' width='30'
             src='" . @$r['profilePhoto'] . "' alt='' onerror='this.onerror=null; this.src='images/account.png'></td>
             <td><a href='client_profile_page.php?id=" . @$r['userId'] . "' title='View Client's profile' class='load_via_ajax'> " . strtoupper($r['firstName'] . " " . $r['lastName'] . $r['shared_name']) . " : NIN - " . @$r['nin'] . "</a></td> 
             <td>" . ($r['membership_no'] == 0 ? '' : $r['membership_no'] . ' ( ' . $r['sname'] . ' )') . "</td>
             <td> Shares: " . @$r['no_shares']??0 . "  |  Share Amount:  " . @$r['share_amount']??0 . "</td>
             <td>" . $r['bname'] . "</td>";

        $return_string .= '<td class="text-center no_print"><a class="btn btn-sm btn-primary load_supplement_ajax" href="share_withdraw.php?t=' . $r['userId'] . '"> Withdraw Shares  </a></td></tr>';

        $return_string .= '</tr>';
    }

    $return_string .= '</table>';

    $userArr = array();
    $userArr["data"] = array();
    $userArr["message"] = '';
    $userArr['redirect'] = $itemCount;
    array_push($userArr['data'], $return_string);

    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["message"] = $return_string;
    $userArr['redirect'] = $itemCount;
    echo json_encode($userArr);
}
