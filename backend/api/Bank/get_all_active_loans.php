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
    if($_GET['bank']==''){
        $stmt = $item->getAllBranchLoansActive();
    }else{
        $stmt = $item->getAllBankLoansActive();
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
        $stt = '';
        if($lstatus==2){
            $stt = '<span class="badge light badge-primary">ACTIVE - ON TIME</span>';

        }else  if($lstatus==3){
            $stt = '<span class="badge light badge-warning">ACTIVE - LATE</span>';

        }else  if($lstatus==4){
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
                "name" => $firstName.' '.$lastName,
                "acno" => $membership_no,
                "principal" => number_format($principal),
                "rate" => number_format($interest_amount),
                "duration" =>$approved_loan_duration.' '.$ftype,
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
              
                    <a href="loan_details_page?id='.$loan_no.'" class="btn btn-primary shadow btn-xs sharp me-1"><i
                            class="fas fa-eye"></i></a>
                    
                </div>',
               
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
         $userArr['message']="No Loans found !";
        http_response_code(200);
        echo json_encode($userArr);
      
    }
