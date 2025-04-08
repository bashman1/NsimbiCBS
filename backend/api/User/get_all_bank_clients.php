<?php
require_once __DIR__.'../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';

$ApiResponse = new ApiResponser();

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$item->branch = $_GET['branch'];
$item->bank = $_GET['bank'];
$stmt = $item->bank == '' ? $item->getBranchClients() : $item->getBankClients();
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {
    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $record = array(
            "image" => '<img class="rounded-circle" width="35" src="' . $profilePhoto . '" onerror="this.onerror=null; this.src=images/account.png" alt="">',
            "accno" => $membership_no == 0 ? '' : $membership_no,
            "actype" => $membership_no > 0 ? 'Member' : 'Non-Member',
            "userId" => $userId,
            "branchName" => is_null($branchId) ? '' : $item->getBranchName($branchId),

            "name" => $firstName . ' ' . $lastName.$shared_name,
            "gender" => $gender,
            "acc_balance" => $acc_balance,
            "loan_wallet" => $loan_wallet,
            "accbalance" => '<a class="text-primary" href="member_statement_range.php?id=' . $userId . '">' . number_format($loan_wallet + $acc_balance) . '</a>',
            "freezed" => $freezed_amount,
            "contact" => $primaryCellPhone . ' / ' . $secondaryCellPhone,
            "status" => $status == "ACTIVE" ? '<span class="badge badge-rounded badge-primary">Active</span>' : '<span class="badge badge-rounded badge-danger">' . $status . '</span>',
            "openingdate" => date('d-m-Y', strtotime($ccreatedat)),
            "actions" => '
                <div class="d-flex">
                <a href="client_profile_page.php?id=' . $userId . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-eye"></i></a>
                    <a href="client_profile_page.php?id=' . $userId . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                    
                </div>
                ',

        );


        array_push($data, $record);
    }
    echo $ApiResponse::SuccessResponse($data);
} else {
    echo $ApiResponse::ErrorResponse("No Clients found !");
}
