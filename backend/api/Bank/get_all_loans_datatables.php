<?php
require_once __DIR__.'../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$loan = new Loan($db);
$loan->branchId = $_GET['branch'];
$loan->bankId = $_GET['bankId'];
$loan->createdById = $_GET['bankId'];
$loan->status = $_GET['status'];


/**
 * general filters
 */
$loan->filter_branch_id = @$_REQUEST['branchId'];
$loan->filter_loan_status = @$_REQUEST['loan_status'];
$loan->filter_loan_product_id = @$_REQUEST['loan_product_id'];
$loan->filter_disbursement_start_date = @$_REQUEST['disbursement_start_date'];
$loan->filter_disbursement_end_date = @$_REQUEST['disbursement_end_date'];

/**
 * active loan filters
 */
$loan->filter_disbursement_date = @$_REQUEST['disbursement_date'];
$loan->filter_application_start_date = @$_REQUEST['application_start_date'];
$loan->filter_application_end_date = @$_REQUEST['application_end_date'];


/**
 * approved loans filters
 */
$loan->filter_next_due_date = @$_REQUEST['next_due_date'];


/**
 * declined loans filters
 */
$loan->filter_frequency = @$_REQUEST['frequency'];
$loan->filter_declined_start_date = @$_REQUEST['declined_start_date'];
$loan->filter_declined_end_date = @$_REQUEST['declined_end_date'];


/**
 * Closed loans filters
 */

$loan->filter_closing_start_date = @$_REQUEST['closing_start_date'];
$loan->filter_closing_end_date = @$_REQUEST['closing_end_date'];

/**
 * datatables filters
 */
$loan->filter_search_string = @$_REQUEST['search']['value'] ? trim(@$_REQUEST['search']['value']) : "";
$loan->filter_per_page = @$_REQUEST['length'];
$loan->filter_page = @$_REQUEST['start'];

// var_dump($loan->filter_next_due_date);
// exit;

try {

    /**
     * Handles transactions at branch level
     */
    if ($loan->bankId) {
        if ($loan->status == 'active') {
            $recordsTotal = count($loan->getAllBankLoansActive()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 0) {
            $recordsTotal = count($loan->getAllBankLoanApplications()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 1) {
            $recordsTotal = count($loan->getAllBankLoansApproved()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 5) {
            $recordsTotal = count($loan->getAllBankLoansClosed()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 6) {
            $recordsTotal = count($loan->getAllBankLoansDeclined()->fetchAll(PDO::FETCH_ASSOC));
        }
    }

    /**
     * Handles transactions at branch level
     */
    else {
        if ($loan->status == 'active') {
            $recordsTotal = count($loan->getAllBranchLoansActive()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 0) {
            $recordsTotal = count($loan->getAllBranchLoanApplications()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 1) {
            $recordsTotal = count($loan->getAllBranchLoansApproved()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 5) {
            $recordsTotal = count($loan->getAllBranchLoansClosed()->fetchAll(PDO::FETCH_ASSOC));
        } else if ($loan->status == 6) {
            $recordsTotal = count($loan->getAllBranchLoansDeclined()->fetchAll(PDO::FETCH_ASSOC));
        }
    }

    $records = $loan->getLoansDatatables();

    if ($loan->filter_search_string) {
        $recordsFiltered = count($records);
    } else {
        $recordsFiltered = $recordsTotal;
    }

    echo json_encode(['draw' => (int)@$_REQUEST['draw'], "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, "data" => $records, "input" => array("draw" => (int)@$_REQUEST['draw'], "length" => (int)@$_REQUEST['length'])]);
} catch (\Throwable $th) {
    echo json_encode($th->getMessage());
    //throw $th;
}
return;


// try {
//     $records = $item->getBankClientsDatatable()->fetchAll(PDO::FETCH_ASSOC);
//     if ($item->bank) {
//         $total_records = $item->getBankClients()->fetchAll(PDO::FETCH_ASSOC);
//     } else {
//         $total_records = $item->getBranchClients()->fetchAll(PDO::FETCH_ASSOC);
//     }
//     echo json_encode(['draw' => (int)@$_REQUEST['draw'], "recordsTotal" => count($total_records), "recordsFiltered" => (int)@$_REQUEST['length'], "data" => $records]);
// } catch (\Throwable $th) {
//     echo json_encode($th->getMessage());
//     //throw $th;
// }
// return;


$itemCount = $stmt->rowCount();

if ($itemCount > 0) {

    $userArr = array();
    $userArr["data"] = array();
    $userArr["success"] = true;
    $userArr['statusCode'] = "200";
    $userArr['count'] = $itemCount;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $stt = '';
        if ($lstatus == 2) {
            $stt = '<span class="badge light badge-primary">ACTIVE - ON TIME</span>';
        } else  if ($lstatus == 3) {
            $stt = '<span class="badge light badge-warning">ACTIVE - LATE</span>';
        } else  if ($lstatus == 4) {
            $stt = '<span class="badge light badge-danger">ACTIVE - OVERDUE</span>';
        }

        if ($repay_cycle_id == 1) {
            $ftype = 'DAYS';
        } else if ($repay_cycle_id == 2) {
            $ftype = 'WEEKS';
        } else if ($repay_cycle_id == 3) {
            $ftype = 'MONTHS';
        } else if ($repay_cycle_id == 4) {
            $ftype = 'DAYS';
        } else if ($repay_cycle_id == 5) {
            $ftype = 'YEARS';
        }
        $u = array(
            "id" => $loan_no,
            "name" => $firstName . ' ' . $lastName,
            "acno" => $membership_no,
            "principal" => number_format($principal),
            "rate" => number_format($interest_amount),
            "duration" => $approved_loan_duration . ' ' . $ftype,
            "disbursementdate" => date('d-m-Y', strtotime($requesteddisbursementdate)),
            "loanproduct" => $type_name,
            "mode_of_disb" => $mode_of_disbursement,
            "status" => $stt,
            "amountpaid" => number_format($amount_paid),
            "arrears" => number_format(0),
            "balance" => number_format($current_balance),
            "duedate" =>  date('d-m-Y', strtotime($date_of_next_pay)),
            "dateCreated" =>  date('d-m-Y', strtotime($application_date)),

            "actions" => ' <div class="d-flex">
              
                    <a href="loan_details_page?id=' . $loan_no . '" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-eye"></i></a>
                    
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
    $userArr['message'] = "No Loans found !";
    http_response_code(200);
    echo json_encode($userArr);
}
