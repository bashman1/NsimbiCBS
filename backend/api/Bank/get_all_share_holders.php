<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['branch'];
$item->createdById = $_GET['bank'];
if (@$_GET['branch'] != '') {
    $stmt = $item->getAllBranchShareHolders(@$_GET['start_date'],@$_GET['end_date']);
} else if (@$_GET['bank'] != '') {
    $stmt = $item->getAllBankShareHolders(@$_GET['start_date'], @$_GET['end_date']);
}


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $mno = $membership_no == 0 ? '' : $membership_no;
        $u = array(
            "id" => $r_id,
            "branch" => $name,
            "acc" => $mno,
            "client" => $firstName . ' ' . $lastName,
            "shares" => number_format(
                $no_shares,
                2,
                '.',
                ''
            ),
            "shareamount" => number_format($share_amount),
            "savingsdivids" => number_format($savings_dividends ?? 0),
            "sharesdivids" => number_format($shares_dividends ?? 0),
            "dateCreated" => normal_date($date_added),
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
    $userArr['message'] = "No trxns found !";
    http_response_code(200);
    echo json_encode($userArr);
}
