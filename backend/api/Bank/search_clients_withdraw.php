<?php
require_once __DIR__ . '../../RequestHeaders.php';

include_once '../../config/database.php';
include_once '../../config/handler.php';
include_once '../../config/functions.php';
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
$my_str = "this.onerror=null; this.src='images/account.png'";


if ($itemCount > 0) {
    //  $i = 1;
    $return_string = '<table class="table header-border table-responsive-sm"><thead>
         <th>#</th>
         <th>Names & NIN</th> 
         <th>Account No</th>
         <th>Account Type</th>
         <th>Branch</th>
         <th class="no_print">Action</th>
         </thead>
         <tbody>';

    foreach ($stmt as $r) {
        $use_fing_file = "https://eaoug.org/" . $r['fingerprint'];


        // if ($r['fingerprint']) {
        //     $image_url = "https://dibscor.net/" . $r['fingerprint'];

        //     $file_name = basename($image_url);
        //     $use_fing_file = $file_name;
        //     file_put_contents($file_name, file_get_contents($image_url));
        // }


        $link = '';
        if ($r['client_type'] == 'individual') {
            $link = 'client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        } else if ($r['client_type'] == 'group') {
            $link = 'group_client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        } else if ($r['client_type'] == 'institution') {
            $link = 'institution_client_profile_page.php?id=' . encrypt_data($r['userId']) . '';
        }

        $return_string .= "<tr><td><img class='rounded-circle' width='30'
             src='https://eaoug.org/" . $r['profilePhoto'] . "' alt='' onerror='this.onerror=null; this.src='images/account.png'></td>
             <td><a href='" . $link . "' title='View Client's profile' class='load_via_ajax'>  " . ($r['firstName'] ? strtoupper($r['firstName'] . " " . $r['lastName']) : strtoupper($r['shared_name'])) . " : NIN - " . $r['nin'] . "</a></td> 
            <td>" . ($r['membership_no'] == 0 ? '' : $r['membership_no'] . ' ( ' . $r['sname'] . ' )') . "</td>
             <td>" . ($r['membership_no'] > 0 ? 'Member - ' : 'Non-Member - ') . strtoupper($r['client_type']) . "  : Bal. " . number_format($r['acc_balance'] ?? 0) . "</td>
             <td>" . $r['bname'] . "</td>";

        $return_string .= '<td class="text-center no_print"><a class="btn btn-sm btn-danger load_supplement_ajax" data-bs-toggle="modal" data-bs-target="#biometrics_images_' . $r['userId'] . '"> Withdraw  </a></td>
    <div class="modal fade" id="biometrics_images_' . $r['userId'] . '">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Client Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted mb-3">Branch: ' . $r['bname'] . '</p>
                                                         <p class="text-muted mb-3">Names &NIN: ' . ($r['firstName'] ? strtoupper($r['firstName'] . " " . $r['lastName']) : strtoupper($r['shared_name'])) . " : NIN - " . $r['nin'] . '</p>
                                                           <p class="text-muted mb-3">A/C: ' .  ($r['membership_no'] == 0 ? '' : $r['membership_no'] . ' ( ' . $r['sname'] . ' )') . '</p>
                                                         <p class="text-muted mb-3">Contacts: ' . $r['primaryCellPhone'] . ' / ' . $r['secondaryCellPhone'] . '</p>
                                                          <p class="text-muted mb-3">Balance:  UGX ' . number_format($r['acc_balance'] ?? 0) . '</p>
                                                          <p class="text-muted mb-3">Attachments</p>
                                                         <div class="row">
                                    <div class="col  ps-0 pt-3">
                                    <div id="lightgallery" class="row lightgallery">
                                        <a href="https://eaoug.org/' . $r['profilePhoto'] . '" data-exthumbimage="https://eaoug.org/' . $r['profilePhoto'] . '" data-src="https://eaoug.org/' . $r['profilePhoto'] . '" class="col-md-4 mb-4">
                                            <img class="rounded-circle" src="https://eaoug.org/' . $r['profilePhoto'] . '" alt="" width="80" height="80" onerror="' . $my_str . '" />
                                        </a>

                                        <a href="https://eaoug.org/' . $r['sign'] . '" data-exthumbimage="https://eaoug.org/' . $r['sign'] . '" data-src="https://eaoug.org/' . $r['sign'] . '" class="col-md-4 mb-4">

                                            <img class="rounded-circle" src="https://eaoug.org/' . $r['sign'] . '" alt="" width="80" height="80" onerror="' . $my_str . '" />
                                        </a>

                                        <a href="https://eaoug.org/' . $r['other_attachments'] . '" data-exthumbimage="https://eaoug.org/' . $r['other_attachments'] . '" data-src="https://eaoug.org/' . $r['other_attachments'] . '" class="col-md-4 mb-4">

                                            <img class="rounded-circle" src="https://eaoug.org/' . $r['other_attachments'] . '" alt="" width="80" height="80" onerror="' . $my_str . '" />
                                            
                                        </a>
                                    </div>
                                </div>
                                </div>
                                  <div class="row">
                                  <div class="col-md-4">
                            <a class="btn btn-warning light btn-xs mb-1 me-2 download-btn" data-bs-toggle="modal" data-bs-target="#biometrics_images_final_' . $r['userId'] . '"
                                 >Verify Finger-Print</a>
 <div class="modal fade" id="biometrics_images_final_' . $r['userId'] . '">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Fingerprint Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted mb-3">Download the Fingerprint attachments and do the verification before you continue</p>
                                                       
                                        
                                  <div class="row">
                                  <div class="col-md-4">
                                 <a target="_blank" class="btn light btn-xs mb-1 me-2 download-btn" href="' . $use_fing_file . '" download="fingerprint" style="background-color: #4287f5 !important; color: #fff !important;"
                                 >Download <i class="fa fa-download"></i></a>

                                  </div> 
                                   </div> 
                                   
                                   <form class="form-horizontal" method="post" action="add_withdraw.php?t=' . encrypt_data($r['userId']) . '&verify=1"
                                <div class="row">
                                  <div class="col-md-12">
                                        <div class="mb-3">
                                                <div class="form-check custom-checkbox mb-3">
                                                    <input type="checkbox" class="form-check-input" id="customCheckBox1" name="verify_with" checked>
                                                    <label class="form-check-label" for="customCheckBox1">I have Verified Biometrics</label>
                                                    <p class="text-muted mb-3">If un-checked system will consider transaction as un-verified</p>
                                                </div>
                                            </div>
                                    </div> 
                                    <div class="col-md-4">          
                                 <a type="submit" name="verify_fing" class="btn btn-primary light btn-xs mb-1 me-2" href="add_withdraw.php?t=' . encrypt_data($r['userId']) . '&verify=1">Confirm</a>
                                    </div> 
                                   </div> 
                                   </form>

                                 
                                              
                                                        
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                  </div> 
                                   <div class="col-md-4">
                                 <a class="btn btn-warning light btn-xs mb-1 me-2" href="' . $link . '">View Profile</a>
                                  </div> 
                                             <div class="col-md-4">          
                                 <a class="btn btn-primary light btn-xs mb-1 me-2" href="add_withdraw.php?t=' . encrypt_data($r['userId']) . '">Skip & Continue</a>
                                                         </div> 
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
