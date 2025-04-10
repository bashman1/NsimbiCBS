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
    $stmt = $item->getBranchTrxnDetailsSearch(preg_replace("/\s+/", "", $_GET['term']));
} else {
    $stmt = $item->getBankTrxnDetailsSearch(preg_replace("/\s+/", "", $_GET['term']));
}


$itemCount = $stmt->rowCount();
$return_string = 'No Results Found';



if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>#</th>
         <th>TRXN REF</th> 
         <th>OLD TRXN REF</th> 
         <th>Amount</th> 
         <th>Description</th>
         <th>Names</th>
         <th>Branch</th>
         </thead>
         <tbody>';

    foreach ($stmt as $r) {
        $ref = $r['t_type'] . '-ref-' . $r['pay_method'] . '-' . $r['tid'] . '-' . $r['_authorizedby'];

        $return_string .= "<tr><td>" . $r['tid'] . "</td>
             <td class='no_print clickable_ref_no' ref-no='" . $ref .  "' tid='" . $r['tid'] . "'>" . $ref . "</td> 
             <td>" . $r['trxn_ref'] ?? '---' . "</td>
             <td>" . number_format($r['amount'] ?? 0) . "</td>
             <td>" . $r['description'] ?? '---' . "</td>
             <td>" . $r['acc_name'] ?? '---' . "</td>
             <td>" . $r['name'] . "</td>";

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
