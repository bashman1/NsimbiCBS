<?php
    require_once __DIR__.'../../RequestHeaders.php';

    include_once '../../config/database.php';
    include_once '../../config/handler.php';
    include_once '../../models/Loan.php';

    $database = new Database();
    $db = $database->connect();

    $handler = new Handler();

    $item = new Loan($db);
    $item->branchId = $_GET['branch'];
    $item->createdById = $_GET['bank'];
    if($_GET['branch']!=''){
        $stmt = $item->getAllBranchSMSOutbox();
    }else if($_GET['bank']!=''){
        $stmt = $item->getAllBankSMSOutbox();
    }else{
        $stmt = $item->getAllSystemSMSOutbox();
    }


    $itemCount = $stmt->rowCount();

    if($itemCount > 0)
    {
        
         $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = true;
         $userArr['statusCode']="200";
         $userArr['count']=$itemCount;
   
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
       $status_badge = '';
       if($sent_status == 'pending'){
        $status_badge = '<span class="badge light badge-danger">Pending</span>';
       }else if($sent_status =='sent' ){
        $status_badge = '<span class="badge light badge-primary">Successful</span>';
       }else if($sent_status == 'cancelled'){
        $status_badge = '<span class="badge light badge-danger">Cancelled</span>';
       }else{
        $status_badge = '<span class="badge light badge-danger">'.$sent_status.'</span>';
       }
            $u = array(
                "id" => $sms_id,
                "branchname" => $branchname,
                "bname" => $item->getBankName($bankId, 'bank'),
            "reason" => $failed_reason??'',
                "charge" => number_format($charge),
                "phone" => $phone,
                "body" => $msg_body,
                "key" => $msg_key,
                "senderid" => $sender_id==''?'Default': $sender_id,
                "gen" => $issysgen==0?'<span class="badge light badge-danger">No</span>':'<span class="badge light badge-primary">Yes</span>',
                "status" => $status_badge,
             
                
                "dateCreated" =>date('d-m-Y', strtotime($date_created)),
                
                "actions" => ( $sent_status=='pending')?'
                <div class="dropdown custom-dropdown mb-0">
                <div class="btn sharp btn-primary tp-btn"
                    data-bs-toggle="dropdown">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="18px"
                        height="18px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none"
                            fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <circle fill="#000000" cx="12" cy="5" r="2">
                            </circle>
                            <circle fill="#000000" cx="12" cy="12" r="2">
                            </circle>
                            <circle fill="#000000" cx="12" cy="19" r="2">
                            </circle>
                        </g>
                    </svg>
                </div>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item text-primary"
                        href="resend_single_sms.php?id='. $sms_id.'">Resend this SMS</a>
                        <a class="dropdown-item text-primary"
                        href="resend_all_sms.php?id='. $sms_id.'">Resend All failed SMS with similar Key</a>
                    <a class="dropdown-item text-danger"
                        href="decline_purchase.php?id='. $sms_id.'">Cancel SMS</a>
                </div>
            </div>
                ':'',
               
            );
      


            array_push($userArr['data'], $u);
          
            // array_push($userArr['sub'], $u2);
        }
        http_response_code(200);
        echo json_encode($userArr);
    }else{
        $userArr = array();
        $userArr["data"] = array();
         $userArr["success"] = false;
         $userArr['statusCode']="400";
         $userArr['message']="No SMS found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
