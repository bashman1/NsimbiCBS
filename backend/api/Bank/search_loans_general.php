<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/functions.php';
include_once '../../config/handler.php';
include_once '../../models/Loan.php';

$database = new Database();
$db = $database->connect();

$handler = new Handler();

$item = new Loan($db);
$item->branchId = $_GET['branch'];
$item->createdById = $_GET['bank'];
if ($_GET['bank'] == '') {
    $stmt = $item->getBranchClientLoans(preg_replace("/\s+/", "", $_GET['term']));
} else {
    $stmt = $item->getBankClientLoans(preg_replace("/\s+/", "", $_GET['term']));
}


$itemCount = $stmt->rowCount();
$return_string = 'No Results Found';



if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>Mem No:</th>
         <th>Names & TEL</th>
         <th>Savings Balance</th>
         <th>Loan Amount</th>
         <th>Princ. Balance</th>
         <th>Int. Balance</th>
         <th>Status</th>
         <th class="no_print">Actions</th>
         </thead>
         <tbody>';

    foreach ($stmt as $r) {
        $lno = $r['loan_no'];


        $status_label = '';
        if ($r['lstatus'] == 2) {
            $status_label = '<span class="badge light badge-primary">ACTIVE - ON TIME</span>';
        } else if ($r['lstatus'] == 3) {
            $status_label = '<span class="badge light badge-warning">ACTIVE - DUE</span>';
        } else if ($r['lstatus'] == 4) {
            $status_label = '<span class="badge light badge-danger">ACTIVE - OVERDUE</span>';
        } else if ($r['lstatus'] == 5) {
            $status_label = '<span class="badge light badge-success">CLEARED</span>';
        } else if ($r['lstatus'] == 0) {
            $status_label = '<span class="badge light badge-warning">Awaiting Approval</span>';
        } else if ($r['lstatus'] == 1) {
            $status_label = '<span class="badge light badge-warning">Awaiting Disbursement</span>';
        }

        $link = '';
        if ($r['client_type'] == 'individual') {
            $link = 'client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        } else if ($r['client_type'] == 'group') {
            $link = 'group_client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        } else if ($r['client_type'] == 'institution') {
            $link = 'institution_client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        }
        $return_string .= "<tr> <td>" .  ($r['membership_no'] == 0 ? '' : $r['membership_no']) . "</td>
             <td><a href='" . $link . "' title='View Client's profile' class='load_via_ajax'> " . strtoupper($r['firstName'] . " " . $r['lastName'] . $r['shared_name']) . " : TEL - " . $r['primaryCellPhone'] . "</a></td> 
              <td><a href='member_statement_range.php?id=" . $r['userId'] . "'>" . number_format($r['acc_balance'] ?? 0) . "</a></td>
             <td>" . number_format($r['total_loan_amount'] - $r['interest_amount']) . "</td>
             <td>" . number_format($r['principal_balance'] ?? 0) . "</td>
             <td>" . number_format($r['interest_balance'] ?? 0) . "</td>
            
             <td>" . $status_label . "</td>";

        $return_string .= '<td class="text-center no_print">
       
         <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#stmtsModal_' . $lno . '">Actions</a>
                                       </td>
                                        <div class="modal fade" id="stmtsModal_' . $lno . '">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Select an option you need</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <a href="loan_details_page.php?id=' . $lno . '" class="list-group-item load_via_ajax">View Details</a>
                                                            <a href="loan_statement.php?id=' . $lno . '" class="list-group-item load_via_ajax">Loan Statement</a>
                                                             <a href="repayment_schedule.php?id=' . $lno . '" class="list-group-item load_via_ajax">Repayment Schedule</a>
                                                            <a href="collect_payment.php?id=' . $lno . '" class="list-group-item load_via_ajax">Make Payment</a>
                                                             <a href="collect_payment_2.php?id=' . $lno . '" class="list-group-item load_via_ajax">Make Payment(Specify Principal & Interest Paid)</a>
                                                           
                                                            <a href="waive_penalty.php?id=' . $lno . '" class="list-group-item load_via_ajax">Waive Penalty</a>
                                                            <a href="waive_interest.php?id=' . $lno . '" class="list-group-item load_via_ajax">Waive Interest</a>
                                                            <a href="top_up_loan.php?id=' . $lno . '" class="list-group-item load_via_ajax">Make Top-Up</a>
                                                              <a href="write_off_loan.php?id=' . $lno . '" class="list-group-item load_via_ajax">Write-Off Loan</a>
                                                             <a href="rectify_loan_balances.php?id=' . $lno . '" class="list-group-item load_via_ajax">Rectify Balances</a>
                                                             <a href="send_to_cleared.php?id=' . $lno . '" class="list-group-item load_via_ajax">Send to Cleared</a>
                                                           

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
        
        
        </tr>';

        $return_string .= '</tr>';
    }

    $return_string .= '</table>';

    $userArr = array();
    $userArr["data"] = array();
    $userArr["message"] = '';
    $userArr['redirect'] = $itemCount;
    array_push($userArr['data'], $return_string);

    http_response_code(200);
    echo json_encode($userArr);
} else {
    $userArr = array();
    $userArr["data"] = array();
    $userArr["message"] = $return_string;
    $userArr['redirect'] = $itemCount;
    echo json_encode($userArr);
}
