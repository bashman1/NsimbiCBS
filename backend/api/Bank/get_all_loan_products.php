<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->createdById = $_GET['bank'];
$item->branchId = $_GET['branch'];
$stmt = $_GET['branch'] == '' ? $item->getAllLoanProducts() : $item->getAllBranchLoanProducts();
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
            "id" => $type_id,
            "name" => $type_name,
            "method" => $interestmethod,
            "rate" => $interestrate . ' / ' . $frequency,
            "frequency" => $frequency,
            "fees" => $item->getFeesDetails($type_id),
            "hasfee" => $item->hasFee($type_id) > 0 ? '<span class="badge light badge-primary">Yes</span>' : '<span class="badge light badge-danger">No</span>',
            "haspenalty" => $penalty ? '<span class="badge light badge-primary">Yes</span>' : '<span class="badge light badge-danger">No</span>',
            "penaltyrate" => $penaltyinterestrate,
            "penaltyfixed" => $penaltyfixedamount,
            "status" => $status == 1 ? '<span class="badge light badge-primary">ACTIVE</span>' : '<span class="badge light badge-danger">DEACTIVATED</span>',
            "graceperiod" =>  $numberofgraceperioddays,
            "maxdays" =>  $maxnumberofpenaltydays,
            "loans" =>  $item->getTypeLoansCount($type_id),

            "actions" => ' <div class="d-flex">
              
                    <a href="edit_loan_product.php?id=' . $type_id . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-pencil-alt"></i></a>
                            <a href="delete_loan_product.php?id=' . $type_id . '" class="btn btn-danger shadow btn-xs sharp me-1"><i
                            class="fas fa-trash"></i></a>
                    
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
    $userArr['message'] = "No Loan Product found !";
    http_response_code(200);
    echo json_encode($userArr);
}
