<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/functions.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['branch'];
$item->createdById = $_GET['bank'];
if ($_GET['bank'] == '') {
    $stmt = $item->getBranchTypeClients($_GET['term'],$_GET['to']);
} else {
    $stmt = $item->getBankTypeClients($_GET['term'],$_GET['to']);
}


$itemCount = $stmt->rowCount();
$return_string = 'No Results Found';



if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>#</th>
         <th>Names & NIN</th> 
         <th>Account No</th>
         <th>Account Type</th>
         <th>Branch</th>
         <th class="no_print">Action</th>
         </thead>
         <tbody>';

    foreach ($stmt as $r) {

        $btn = '';

        if($_GET['to']==1){
            $btn = '
            <a class="btn btn-sm btn-primary load_supplement_ajax delete-record" href="convert_client.php?id=' . $r['userId'] . '&to=1">Convert to Individual</a>
            ';
        }
        if ($_GET['to'] == 2) {
            $btn = '<a class="btn btn-sm btn-primary load_supplement_ajax delete-record" href="convert_client.php?id=' . $r['userId'] . '&to=2">Convert to Group</a>';
        }
        if ($_GET['to'] == 3) {
            $btn = '<a class="btn btn-sm btn-primary load_supplement_ajax delete-record" href="convert_client.php?id=' . $r['userId'] . '&to=3">Convert to Institution</a>';
        }


        $return_string .= "<tr><td><img class='rounded-circle' width='30'
             src='" . $r['profilePhoto'] . "' alt='' onerror='this.onerror=null; this.src='images/account.png'></td>
             <td><a href='client_profile_page.php?id=" . encrypt_data($r['userId']) . "' title='View Client's profile' class='load_via_ajax'> " . strtoupper($r['firstName'] . " " . $r['lastName'] . $r['shared_name']) . " : NIN - " . $r['nin'] . "</a></td> 
             <td>" . ($r['membership_no'] == 0 ? '' : $r['membership_no'] . ' ( ' . $r['sname'] . ' )') . "</td>
             <td>" . ($r['membership_no'] > 0 ? 'Member - ' : 'Non-Member - ') . strtoupper($r['client_type']) . "</td>
             <td>" . $r['bname'] . "</td>";

        $return_string .= '<td class="text-center no_print">'.$btn.'</td></tr>';

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
