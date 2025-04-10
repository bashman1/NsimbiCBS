<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
// $item->lno = $data->id;

$stmt = ($_GET['branch']=='')? $item->getBankCollaterals($_GET['bank']):$item->getBranchCollaterals($_GET['branch']);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $u = array(
            "_cid" => $_cid,
            "loanid" => $loanid,
            "_status" =>'<span
            class="badge light '.($_status==0?'badge-success':'badge-danger').'">'.($_status==0?'Active':'Released').'</span>
   ',
            "_collateral" => $_collateral,
            "_mvalue" => number_format($_mvalue),
            "_date_created" =>date('Y-m-d',strtotime($_date_created)),
            "_attachment" => '<a href="'.$_attachment.'" class="text-primary">View</a>',
            "cat" => $cat,
            "_receivedby" => $firstName.' '.$lastName,
            "_releasedby" => $_releasedby,
            "_fvalue" => number_format($_fvalue),
            "_location" => $_location,
            "_catname" => $_catname,
            "actions" => ' <div class="d-flex">
            <a href="edit_collateral.php?id=' . $_cid . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                        class="fas fa-pencil-alt"></i></a>
                
            </div>',

          




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
    $userArr['message'] = "No schedule found !";
    http_response_code(200);
    echo json_encode($userArr);
}
