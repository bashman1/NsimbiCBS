<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Bank.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Bank($db);


$userArr = array();
$userArr["data"] = array();
$userArr["success"] = true;
$userArr['statusCode'] = "200";

if ($_GET['type'] == 'sectors') {
    $stmt = $_GET['bank'] != '' ? $item->getBankSectors($_GET['bank']) : $item->getBranchSectors($_GET['branch']);


    $itemCount = $stmt->rowCount();

    if ($itemCount > 0) {

        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = true;
        $userArr['statusCode'] = "200";
        $userArr['count'] = $itemCount;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $u = array(
                "osid" => $osid,
                "os_name" => $os_name,

            );


            array_push($userArr['data'], $u);
        }
        http_response_code(200);
        echo json_encode($userArr);
    } else {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = false;
        $userArr['statusCode'] = "400";
        $userArr['message'] = "No Items found !";
        http_response_code(200);
        echo json_encode($userArr);
    }
}

if ($_GET['type'] == 'category') {

    $stmt = $_GET['bank'] != '' ? $item->getBankCats($_GET['bank']) : $item->getBranchCats($_GET['branch']);


    $itemCount = $stmt->rowCount();

    if ($itemCount > 0) {

        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = true;
        $userArr['statusCode'] = "200";
        $userArr['count'] = $itemCount;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $u = array(
                "oscid" => $oscid,
                "osc_name" => $osc_name,

            );


            array_push($userArr['data'], $u);
        }
        http_response_code(200);
        echo json_encode($userArr);
    } else {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = false;
        $userArr['statusCode'] = "400";
        $userArr['message'] = "No Items found !";
        http_response_code(200);
        echo json_encode($userArr);
    }
}

if ($_GET['type'] == 'subcategory') {


    $stmt = $_GET['bank'] != '' ? $item->getBankSubCats($_GET['bank']) : $item->getBranchSubCats($_GET['branch']);


    $itemCount = $stmt->rowCount();

    if ($itemCount > 0) {

        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = true;
        $userArr['statusCode'] = "200";
        $userArr['count'] = $itemCount;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $u = array(
                "ocid" => $ocid,
                "oc_name" => $oc_name,

            );


            array_push($userArr['data'], $u);
        }
        http_response_code(200);
        echo json_encode($userArr);
    } else {
        $userArr = array();
        $userArr["data"] = array();
        $userArr["success"] = false;
        $userArr['statusCode'] = "400";
        $userArr['message'] = "No Items found !";
        http_response_code(200);
        echo json_encode($userArr);
    }
}
if ($_GET['type'] == '') {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No Items found !";
    http_response_code(200);
    echo json_encode($userArr);
}
// $u = array(
//     "sectors" => $_GET['bank'] != '' ? $item->getBankSectors($_GET['bank']) : $item->getBranchSectors($_GET['branch']),
//     "category" =>
//     $_GET['bank'] != '' ? $item->getBankCats($_GET['bank']) : $item->getBranchCats($_GET['branch']),
//     "subcategory" =>
//     $_GET['bank'] != '' ? $item->getBankSubCats($_GET['bank']) : $item->getBranchSubCats($_GET['branch']),
// );

// array_push($userArr['data'], $u);

// http_response_code(200);
// echo json_encode($userArr);
