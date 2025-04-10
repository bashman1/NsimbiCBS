<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);
$item->id = $_GET['id'];
if ($item->id == '') {
    $stmt = $item->getAllSystemBranches();
} else {
    $stmt = $item->getAllBranches();
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
        $staffs = $item->getTotalBranchStaffs($id);
        $clients = $item->getTotalBranchClients($id);
        $clients_today = $item->getTotalBranchClientsToday($id);

        $btn =   ($staffs == 0 && $clients == 0) ? ' <a href="trash_branch.php?id=' . $id . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fa fa-trash"></i></a>' : '';
        $u = array(
            "count" => $count,
            "id" => $id,
            "name" => $name,
            "is_main" => $is_main ?? 0,
            "bcode" => $bcode,
            "location" => $location,
            "openingdate" => date('d-m-Y', strtotime($createdAt)),
            "staffs" =>  $staffs,
            "clients" => $clients,
            "clients_today" => $clients_today,
            "actions" => ' <div class="d-flex">
              
                    <a href="edit_branch.php?name=' . $name . '&location=' . $location . '&id=' . $id . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
' . $btn . '
                    
                </div>',

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
    $userArr['message'] = "No Branch found !";
    http_response_code(200);
    echo json_encode($userArr);
}
