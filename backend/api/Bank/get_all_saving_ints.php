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
if ($_GET['branch'] != '') {
    $stmt = $item->getAllBranchSavingInits();
} else if ($_GET['bank'] != '') {
    $stmt = $item->getAllBankSavingInits();
}


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);


        $stt = '';
        if ($_status == 0) {

            $stt = '<span class="badge badge-rounded badge-danger">Pending</span>';
        } else {
            $stt = '<span class="badge badge-rounded badge-success">Completed</span>';
        }

        $sttn = '';
        if ($send_sms == 0) {

            $sttn = '<span class="badge badge-rounded badge-danger">No</span>';
        } else {
            $sttn = '<span class="badge badge-rounded badge-success">Yes</span>';
        }

        $u = array(
            "id" => $init_id,
            "branch" => $bname,
            "name" => $sname,
            "spdt" => $pname,
            "amount" => number_format($min_bal ?? 0),

            "status" => $stt,
            "sms" => $sttn,
            "int_rate" => $int_rate,
            "wht_rate" => $wht_rate,
            "exec_date" => normal_date($set_date),
            "open_date" => normal_date($created_at),
            "actions" => '
           
             <a type="button" href="details_save_init.php?id=' . $init_id . '" class="btn btn-primary light btn-xs mb-1"><i class="fa fa-eye"></i>Details </a>
              <a type="button" href="delete_save_init.php?id=' . $init_id . '" class="btn btn-danger light btn-xs mb-1"><i class="fa fa-trash"></i> Delete </a>
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
    $userArr['message'] = "No trxns found !";
    http_response_code(200);
    echo json_encode($userArr);
}
