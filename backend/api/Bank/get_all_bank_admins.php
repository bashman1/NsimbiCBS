<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';
$database = new Database();
$db = $database->connect();
$item = new Bank($db);
$stmt = $item->getAllBankAdmins();
$itemCount = $stmt->rowCount();

try {
    if ($itemCount > 0) {
        $count = 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $u = array(
                "id" => $count,
                "name" => $firstName . ' ' . $lastName,
                "bank" => $item->getBankName($bankId),
                "photo" => '<img class="rounded-circle" width="35"
                src="' . $profilePhoto . '" alt="">',
                "email" => $email,
                "contact" => $primaryCellPhone,
                "onboardingdate" => date('d-m-Y', strtotime($screatedat)),
                "status" => $status == "ACTIVE" ? '<span class="badge badge-rounded badge-primary">Active</span>' : '<span class="badge badge-rounded badge-danger">' . $status . '</span>',
                "actions" => '
                <div class="d-flex">
               
                    <a href="edit_bank_admin.php?id=' . $userId . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                            <a href="edit_password_2.php?id=' . $userId . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-key"></i></a>
                    
                </div>
                ',
            );

            $userArr[] = $u;
            $count++;
        }
        echo $ApiResponser::SuccessResponse($userArr);
    } else {
        echo $ApiResponser::ErrorResponse();
    }
} catch (\Throwable $th) {
    echo $ApiResponser::ErrorResponse($th->getMessage());
}
