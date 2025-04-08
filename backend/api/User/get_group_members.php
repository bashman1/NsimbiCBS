<?php
require_once __DIR__ . '../../RequestHeaders.php';

require_once '../../config/database.php';
require_once '../../config/handler.php';
require_once '../../models/Bank.php';
require_once '../ApiResponser.php';

$ApiResponse = new ApiResponser();

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$stmt = $item->getGroupMembers($_GET['id']);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {
    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $record = array(
            "name" => $gm_name,
            "id" => $gmid,
            "member" => $gm_is_member==0 ? '<span class="badge badge-rounded badge-danger">No</span>' : '<span class="badge badge-rounded badge-success">Yes</span>',
            "phone" => $gm_phone,
            "address" => $gm_address,
            "role" => $gm_role,
            "actions" => '
                <div class="d-flex">
                <a href="edit_group_member.php?id=' . $gmid . '&name='.$gm_name.'" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                    <a href="delete_group_member.php?id=' . $gmid . '&name='.$gm_name.'" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-trash"></i></a>
                    
                </div>
                ',

        );


        array_push($data, $record);
    }
    echo $ApiResponse::SuccessResponse($data);
} else {
    echo $ApiResponse::ErrorResponse("No Members found !");
}
