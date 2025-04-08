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
    $stmt = $item->getBranchClients($_GET['term']);
} else {
    $stmt = $item->getBankClients($_GET['term']);
}


$itemCount = $stmt->rowCount();
$return_string = 'No Results Found';



if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>Names</th> 
         <th>Account No</th>
         <th class="no_print">Action</th>
         </thead>
         <tbody>';

    foreach ($stmt as $r) {

        $return_string .= "<tr>
             <td><a href='client_profile_page?id=" . encrypt_data($r['userId']) . "' title='View Client's profile' class='load_via_ajax'> " . strtoupper($r['firstName'] . " " . $r['lastName'] . $r['shared_name']) . "</a></td> 
             <td>" . ($r['membership_no'] == 0 ? '' : $r['membership_no'] . ' ( ' . $r['sname'] . ' )') . "</td>";

        $return_string .= '<td class="text-center no_print"><a class="text-primary" account-no="' . $r['membership_no'] . '" account-id="' . $r['userId'] . '" account-name="' . strtoupper($r['firstName'] . " " . $r['lastName'] . $r['shared_name']) . '" mem-no="' . $r['userId'] . '"  onClick="bulk_transfer_populateTableRows($(this));"><i class="fa fa-plus-circle"></i></a></td>';

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
