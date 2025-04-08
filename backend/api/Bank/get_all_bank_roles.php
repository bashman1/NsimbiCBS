<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$item->id = $_GET['id'];
$stmt = $item->getAllBankRoles();
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
        if ($rolestatus == 2) {
            $btn = '   <a class="dropdown-item text-danger"
                        href="activate_role.php?id=' . $rid . '">Activate Role</a>';
        } else {
            $btn = '   <a class="dropdown-item text-danger"
                        href="deactivate_role.php?id=' . $rid . '">Deactivate Role</a>';
        }
        $u = array(
            "id" => $count,
            "rid" => $rid,
            "name" => $rname,
            "branch" => is_null($branchId) ? '' : $item->getBranchName($branchId),
            "description" => $description,
            "status" => $rolestatus == 1 ? '<span class="badge badge-rounded badge-primary">Active</span>' : '<span class="badge badge-rounded badge-danger">Deactivated</span>',
            "createdAt" =>is_null($createdAt)?'': date('d-m-Y', strtotime($createdAt)),
            "permissions" => '
                <div class="d-flex">
              
                    <a href="" class="btn btn-primary shadow btn-xs sharp me-1" aria-expanded="false"
                    data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg3"><i
                            class="fas fa-eye"></i></a>
                            <div class="modal fade bd-example-modal-lg3" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">' . $name . ' Permissions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                        <div class="modal-body">
                            <label class="text-label form-label"> </label>
                            
                               
                         

                </div>
            </div>
        </div>

                    
                </div>
                ',
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
                        href="edit_role.php?id=' . $rid . '">Edit Role</a>
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
    $userArr['message'] = "No Roles found !";
    http_response_code(200);
    echo json_encode($userArr);
}
