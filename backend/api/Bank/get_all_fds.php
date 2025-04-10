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
    $stmt = $item->getAllBranchFDs();
} else if ($_GET['bank'] != '') {
    $stmt = $item->getAllBankFDs();
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
        $dtype = '';
        $dtype1 = '';
        if ($duration_type == 'y') {
            $dtype = 'Years';
        } else if ($duration_type == 'm') {
            $dtype = 'Months';
        } else if ($duration_type == 'd') {
            $dtype = 'Days';
        }


        if ($compound_freq == 'y') {
            $dtype1 = 'Annually';
        } else if ($compound_freq == 'm') {
            $dtype1 = 'Monthly';
        } else if ($compound_freq == 'q') {
            $dtype1 = 'Quarterly';
        } else if ($compound_freq == 'h') {
            $dtype1 = 'Half Yearly';
        }

        $stt = '';
        if ($fd_status == 0) {
            $currentDate = strtotime(date('Y-m-d'));
            $startDate = strtotime(date('Y-m-d', strtotime($fd_maturity_date)));


            if ($startDate <= $currentDate) {
                $stt = '<span class="badge badge-rounded badge-warning">Due</span>';
            } else {
                $stt = '<span class="badge badge-rounded badge-danger">Running</span>';
            }
        } else {
            $stt = '<span class="badge badge-rounded badge-success">Closed</span>';
        }

        $u = array(
            "id" => $fd_id,
            "branch" => $name,
            "client" => $mno . ' : ' . $firstName . ' ' . $lastName.''.$shared_name,

            "amount" => number_format($fd_amount ?? 0),
            "period" => number_format($fd_duration ?? 0) . ' ' . $dtype,
            "freq" => $dtype1,
            "status" => $stt,
            "open_date" => normal_date($fd_date),
            "close_date" => normal_date($fd_maturity_date),
            "actions" => '
           
             <a type="button" href="fixed_deposit_details.php?id=' . $fd_id . '" class="btn btn-primary light btn-xs mb-1"><i class="fa fa-eye"></i> view </a>
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
