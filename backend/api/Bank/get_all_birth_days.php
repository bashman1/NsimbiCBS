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
    $stmt = $item->getAllBranchBDs();
} else if ($_GET['bank'] != '') {
    $stmt = $item->getAllBankBDs();
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
        $mno = $membership_no == 0 ? '' : $membership_no;
      
        $stt = '';
        if ($status == 'ACTIVE') {
                $stt = '<span class="badge badge-rounded badge-warning">ACTIVE</span>';
            } else {
                $stt = '<span class="badge badge-rounded badge-danger">'.$status.'</span>';
            }
        

        $u = array(
            "id" => $userId,
            "branch" => $name,
            "client" => $mno . ' : ' . $firstName . ' ' . $lastName,

            "bod" => normal_date($dateOfBirth),
            "status" => $stt,
            "actions" => '
           
             <a type="button" href="birth_day_send.php?id=' . $userId . '" class="btn btn-primary light btn-xs mb-1"><i class="fa fa-eye"></i> Send Wish </a>
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
    $userArr['message'] = "No Bds found !";
    http_response_code(200);
    echo json_encode($userArr);
}
