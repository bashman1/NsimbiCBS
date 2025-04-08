<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);


$item->bank = $_GET['bank'];
$item->branch = $_GET['branch'];


$stmt = $item->bank=='' ? $item->getBranchClients() : $item->getBankClients();
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
            "id" => $count,
            "image" => '<img class="rounded-circle" width="35"
            src="'.$profilePhoto.'" alt="">',
            "accno" => $membership_no==0?'':$membership_no,
            "actype" => $membership_no>0 ? 'Member' : 'Non-Member',
            "userId" => $userId,
            "branchName" => is_null($branchId) ? '' : $item->getBranchName($branchId),
            
            "name" => $firstName.' '.$lastName,
            "gender" => $gender,
            "acc_balance" => $acc_balance,
            "loan_wallet" => $loan_wallet,
            "accbalance" =>'<a class="text-primary" href="member_statement_range.php?id='.$userId.'">'. number_format($loan_wallet + $acc_balance).'</a>',
            "freezed" => $freezed_amount,
            "contact" => $primaryCellPhone.' / '.$secondaryCellPhone,
            "status" => $status=="ACTIVE"?'<span class="badge badge-rounded badge-primary">Active</span>':'<span class="badge badge-rounded badge-danger">'.$status.'</span>',
            "openingdate" =>date('d-m-Y', strtotime($ccreatedat)),
            "actions" => '
                <div class="d-flex">
                <a href="client_profile_page.php?id=' . $userId . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-eye"></i></a>
                    <a href="client_profile_page.php?id=' . $userId . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                    
                </div>
                ',

        );


        array_push($userArr['data'], $u);
        // array_push($userArr['sub'], $u2);
        $count++;
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    // $userArr["sub"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = '204';
    $userArr['message'] = "No Clients found !";

    http_response_code(200);
    echo json_encode($userArr);
}
