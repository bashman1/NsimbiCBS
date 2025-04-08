<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);

    $stmt = $item->getAllBanks($_GET['term']);

$itemCount = $stmt->rowCount();
$return_string = 'No Results Found';



if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>#</th>
         <th>Names</th> 
         <th>Trade Name</th>
         <th>Location</th>
         <th class="no_print">Action</th>
         </thead>
         <tbody>';
$count = 0;
    foreach ($stmt as $r) {

        $return_string .= "<tr><td>".++$count."</td>
             <td> " . strtoupper($r['name']) . "</td> 
             <td> " . strtoupper($r['trade_name']) . "</td> 
             <td> " . strtoupper($r['location']) . "</td>";

        $return_string .= '<td class="text-center no_print">' . ($r['sms_sub_status'] == 1 ? '<a class="btn btn-sm btn-danger load_supplement_ajax" href="subscribe_bank_to_sms.php?t=' . $r['id'] . '&st=0"> Un-Subscribe  </a>' : '<a class="btn btn-sm btn-primary load_supplement_ajax" href="subscribe_bank_to_sms.php?t=' . $r['id'] . '&st=1"> Subscribe  </a>') . '</td></tr>';


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
