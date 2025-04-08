<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
// $item->lno = $data->id;

$stmt = ($_GET['branch']=='')? $item->getBankCollateralCategories($_GET['bank']):$item->getBranchCollateralCategories($_GET['branch']);
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
            "_catname" => $_catname,
            "_datecreated" => $_datecreated,
            "_catdesc" => $_catdesc,
            "_catid" => $_catid,
            "bankid" => $bankid,
            "tot" => $item->getCategoryCollaterals($_catid),
            "actions" => ' <div class="d-flex">
            <a href="category_collaterals.php?id=' . $_catid . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                        class="fas fa-eye"></i></a>
                <a href="edit_collateral_category.php?id=' . $_catid . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                        class="fas fa-pencil-alt"></i></a>
                
            </div>',

          




        );



        array_push($userArr['data'], $u);

        // array_push($userArr['sub'], $u2);
    }
    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = false;
    $userArr['statusCode'] = "400";
    $userArr['message'] = "No schedule found !";
    http_response_code(200);
    echo json_encode($userArr);
}
