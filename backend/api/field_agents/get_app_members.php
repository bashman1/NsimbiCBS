<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../models/FieldAgents.php';

$database = new Database();
$db = $database->connect();



$item = new FieldAgents($db);


$stmt = $item->getAppMembers(@$_GET['branch']);
$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    // $userArr["data"] = array();
    //  $userArr["success"] = true;
    //  $userArr['statusCode']="200";
    //  $userArr['count']=$itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);
        $u = array(
            "MID- " => strval($useid),
            "A/C-No- " => strval($membership_no),
            "NAMES- " => $firstName . ' ' . $lastName . ' ' . $shared_name . ' - ' . $accname,


        );


        array_push($userArr, $u);
        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    // $userArr["data"] = array();
    // $userArr["sub"] = array();
    //  $userArr["success"] = false;
    //  $userArr['statusCode']="204";
    //  $userArr['message']="No Members found !";

    http_response_code(200);
    echo json_encode($userArr);
}
