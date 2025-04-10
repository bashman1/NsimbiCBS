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
$stmt = $item->getLoanAttachments($_GET['id']);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {
    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $record = array(
            "name" => $attach_name,
            "link" => $attach_link,
            "lid" => $loan_id,
        );


        array_push($data, $record);
    }
    echo $ApiResponse::SuccessResponse($data);
} else {
    echo $ApiResponse::ErrorResponse("No Members found !");
}
