<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['branch'];
$item->createdById = $_GET['bank'];
if ($_GET['bank'] == '' && $_GET['branch'] != '') {
    $stmt = $item->getAllBranchSMSTypes();
} else if ($_GET['branch'] == '' && $_GET['bank'] != '')  {
    $stmt = $item->getAllBankSMSTypes();
}else{
    $stmt = $item->getAllSMSTypes();
}


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;
$count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
       
        $u = array(
            "count" => $count++,
            "id" => $st_id,
            "charge_to" => $charged_to,
            "charge" => $charge,
            "sms_on" => $sms_sent_on,
            "act_name" => $action_name,
            "bank" => is_null($name)?'':$name,
            "status" => $s_status==1?'<a class="ajax_delete" href="unsubscribe_sms_type.php?t='.$st_id.'"> <i class="fa fa-toggle-on" style="color: #0ad40a;font-size: 20px;"></i> </a>':
            '<a class="ajax_delete" href="subscribe_sms_type.php?t=' . $st_id . '"> <i class="fa fa-toggle-off" style="color: red;font-size: 20px;"></i> </a>',
            "action" => $s_status == 1? '<a class="load_via_ajax" href="sms_type_settings.php?t='.$st_id.'"><i class="ti-settings"></i> Settings </a>': '<i class="ti-na"></i> Disabled',
            "trash" => '
            <a href="trash_sms_type.php?t=' . $st_id . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fa fa-trash"></i></a>
            ',

        );



        array_push($userArr['data'], $u);

        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No SMS Types found !";
    http_response_code(200);
    echo json_encode($userArr);
}
