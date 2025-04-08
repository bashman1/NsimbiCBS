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
    $stmt = $item->getBranchClients(preg_replace("/\s+/", "", $_GET['term']));
} else {
    $stmt = $item->getBankClients(preg_replace("/\s+/", "", $_GET['term']));
}


$itemCount = $stmt->rowCount();
$return_string = 'No Results Found';



if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>Mem No:</th>
         <th>Names & TEL</th> 
         <th>Account No</th>
         <th>Savings Balance</th>
         <th>Freezed Balance</th>
         <th>Branch</th>
         <th class="no_print">Action</th>
         </thead>
         <tbody>';

    foreach ($stmt as $r) {
        $link = '';
        if ($r['client_type'] == 'individual') {
            $link = 'client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        } else if ($r['client_type'] == 'group') {
            $link = 'group_client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        } else if ($r['client_type'] == 'institution') {
            $link = 'institution_client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        }
        $return_string .= "<tr> <td>" . $r['userId']  . "</td>
             <td><a href='" . $link . "' title='View Client's profile' class='load_via_ajax'> " . strtoupper($r['firstName'] . " " . $r['lastName'] . $r['shared_name']) . " : TEL - " . $r['primaryCellPhone'] . "</a></td> 
             <td>" . ($r['membership_no'] == 0 ? '' : $r['membership_no'] . ' ( ' . $r['sname'] . ' )') . "</td>
             <td><a href='member_statement_range.php?id=" . $r['userId'] . "'>" . number_format($r['acc_balance'] ?? 0) . "</a></td>
              <td><a href='member_statement_range.php?id=" . $r['userId'] . "' class='text-danger'>" . number_format($r['freezed_amount'] ?? 0) . "</a></td>
             <td>" . $r['bname'] . "</td>";

        $return_string .= '<td class="text-center no_print">
       
         <a class="btn btn-primary light btn-xs mb-1 me-2" data-bs-toggle="modal" data-bs-target="#stmtsModal_'.$r['userId'].'">View</a>
                          </td>             
                                        <div class="modal fade" id="stmtsModal_'.$r['userId'].'">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Select an option you need</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <a href="' . $link . '" class="list-group-item load_via_ajax">View Profile</a>
                                                            <a href="member_statement_range.php?id=' . encrypt_data($r['userId']) . '" class="list-group-item load_via_ajax"> Account Statement</a>
                                                            <a href="saving_statement.php?id='. encrypt_data($r['userId']).'" class="list-group-item load_via_ajax"> Savings Statement</a>
                                                            <a href="user_fixed_deposits.php?id='. $r['userId'] .'" class="list-group-item load_via_ajax"> Fixed Deposit Statement</a>
                                                            <a href="share_statement.php?id='.$r['userId'].'" class="list-group-item load_via_ajax"> Shares Statement</a>
                                                            <a href="over_draft_statement.php?id='. encrypt_data($r['userId']).'" class="list-group-item load_via_ajax"> Over Drafts Statement</a>
                                                            <a href="user_loans.php?id='. $r['userId'] .'" class="list-group-item load_via_ajax"> Loans Statement</a>
                                                            <a href="user_loans.php?id='. $r['userId'] . '" class="list-group-item load_via_ajax"> Credit History</a>
                                                             <a href="rectify_user_share.php?id=' . $r['userId'] . '" class="list-group-item load_via_ajax">Rectify Share Amount</a>

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
