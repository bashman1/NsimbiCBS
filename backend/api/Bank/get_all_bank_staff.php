<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
if ($_GET['branch'] == '') {
    $item->id = $_GET['id'];
    $stmt = $item->getAllBankStaffs();
} else {

    $item->id = $_GET['branch'];
    $stmt = $item->getAllBranchStaffs();
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

        if ($sstatus == 'INACTIVE') {
            $btn = ' <a class="dropdown-item text-danger"
                        href="activate_staff.php?id=' . $suserid . '">Activate Staff</a>';
        } else {
            $btn = ' <a class="dropdown-item text-danger"
                        href="deactivate_staff.php?id=' . $suserid . '">Deactivate Staff</a>';
        }
        $u = array(
            "count" => $count,
            "id" => $idx,
            "email" => $email,
            "phone" => $primaryCellPhone,
            "name" => $firstName . ' ' . $lastName,
            "branch" => is_null($branchId) ? '' : $item->getBranchName($branchId),
            "position" => $positionTitle,
            "status" => $sstatus == 'ACTIVE' ? '<span class="badge badge-rounded badge-primary">ACTIVE</span>' : '<span class="badge badge-rounded badge-danger">' . $sstatus . '</span>',
            "actions" => '
                <div class="dropdown custom-dropdown mb-0">
                <div class="btn sharp btn-primary tp-btn"
                    data-bs-toggle="dropdown">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                        height="18px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none"
                            fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <circle fill="#000000" cx="12" cy="5" r="2">
                            </circle>
                            <circle fill="#000000" cx="12" cy="12" r="2">
                            </circle>
                            <circle fill="#000000" cx="12" cy="19" r="2">
                            </circle>
                        </g>
                    </svg>
                </div>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item"
                        href="edit_staff.php?id=' . $suserid . '">Edit Staff</a>
                        <a class="dropdown-item"
                        href="edit_password.php?id=' . $suserid . '">Edit Password</a>
                        <a class="dropdown-item"
                        href="staff_permissions.php?id=' . $suserid . '">Permissions</a>
                        <a class="dropdown-item text-primary"
                        href="resend_email.php?id=' . $suserid . '&email=' . $email . '">Resend Set Password Email</a>
                    ' . $btn . '
                     
                </div>
            </div>
                ',

        );


        array_push($userArr['data'], $u);
        $count++;
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Staff found !";
    http_response_code(200);
    echo json_encode($userArr);
}
