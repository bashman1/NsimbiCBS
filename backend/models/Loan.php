<?php
require_once __DIR__ . '../../config/functions.php';
require_once __DIR__ . '../../api/DatatableSearchHelper.php';
require_once __DIR__ . '../../config/DbHandler.php';
class Loan
{
  // DB stuff
  private $conn;
  private $db_table = 'ClientLoan';

  //   table columns
  public $data_array;
  public $bank_object;
  public $lno;
  public $requestedAmount;
  public $denialReason;
  public $branchId;
  public $bankId;
  public $numberOfRepayments;
  public $status;
  public $notes;
  public $approvedAmount;
  public $approvedLoanTerm;
  public $disbursedAmount;
  public $createdById;
  public $transaction_type;
  public $updatedById;
  public $reviewedById;
  public $requestedDisbursementDate;
  public $approvedInterestRate;
  public $requestedDuration;
  public $approvedDuration;
  public $applicationDate;
  public $isInRepayment;
  public $isPerforming;
  public $termStartingDate;
  public $estimatedTermClosingDate;
  public $actualTermClosingDate;
  public $arrearsBeginDate;
  public $firstPenaltyAccrualDate;
  public $lastPenaltyAccrualDate;
  public $installmentAmount;
  public $isApproved;
  public $filter_loan_amount;
  public $clientid;
  public $loanproductid;
  public $interestRate;
  public $penalty;
  public $numberOfGracePeriodDays;
  public $penaltyInterestRate;
  public $penaltyFixedAmount;
  public $with_payment_totals;


  public $filter_branch_id;
  public $filter_sub_account_id;
  public $filter_deposit_method;
  public $filter_transaction_method;
  public $filter_withdraw_method;
  public $filter_approved_by_id;
  public $filter_transaction_start_date;
  public $filter_transaction_end_date;
  public $filter_loan_status;
  public $filter_loan_product_id;
  public $filter_collateral_type_id;
  public $filter_next_due_date;
  public $filter_frequency;
  public $filter_disbursement_start_date;
  public $filter_disbursement_end_date;
  public $filter_disbursement_date;

  public $filter_application_start_date;
  public $filter_application_end_date;

  public $filter_declined_start_date;
  public $filter_declined_end_date;

  public $filter_closing_start_date;
  public $filter_received_end_date;

  public $filter_received_start_date;
  public $filter_closing_end_date;

  public $filter_search_string;
  public $filter_per_page;
  public $filter_page;



  // Constructor with DB
  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function getMemberLoans($uid)
  {
    $sqlQuery = 'SELECT  * FROM public."loan" WHERE account_id=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $uid);
    $transactions->execute();

    return $transactions;
  }
  public function getClientSaccoDetails()
  {

    $sqlQuery = 'SELECT  * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Client".id=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->lno);
    $transactions->execute();
    // $row = $transactions->fetch();

    // $sqlQuery = 'SELECT  public."Bank".name AS bname FROM public."Bank" WHERE public."Bank".id=:id';

    // $transactions = $this->conn->prepare($sqlQuery);
    // $transactions->bindParam(':id', $row['bankId']);
    // $transactions->execute();
    return $transactions;
  }
  public function getClientPortalFeesTrxns($uid, $start_date, $next_date)
  {

    $sqlQuery = 'SELECT * FROM public."transactions"  WHERE public."transactions".mid=:id AND public."transactions".trxn_rec=\'fees\' AND  DATE(public."transactions".date_created) >= :st_date AND DATE(public."transactions".date_created) <= :end_date';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $uid);
    $transactions->bindParam(':st_date', $start_date);
    $transactions->bindParam(':end_date', $next_date);
    $transactions->execute();
    return $transactions;
  }
  public function getClientPortalTrxns($uid, $start_date, $next_date)
  {

    $sqlQuery = 'SELECT * FROM public."transactions"  WHERE public."transactions".mid=:id AND  DATE(public."transactions".date_created) >= :st_date AND DATE(public."transactions".date_created) <= :end_date';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $uid);
    $transactions->bindParam(':st_date', $start_date);
    $transactions->bindParam(':end_date', $next_date);
    $transactions->execute();
    return $transactions;
  }
  public function getPhoneDetails()
  {

    $sqlQuery = 'SELECT  *, public."User".id AS uid, public."Client".id AS cid FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id WHERE public."User"."primaryCellPhone"=:id OR public."User"."secondaryCellPhone"=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->lno);
    $transactions->execute();
    return $transactions;
  }
  public function getBranchClients($term)
  {
    $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);

    $stmtn->execute();
    $row = $stmtn->fetch();
    $likk = '%' . strtolower($term) . '%';
    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname, public."Client"."createdAt" AS ccreatedat FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
      LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype 
       WHERE public."Branch"."bankId"=:bid AND public."User".status<>:stt AND replace(lower(CONCAT(
          COALESCE(public."User"."firstName",\'\'),
					COALESCE(public."User"."lastName",\'\'),
					COALESCE(public."User".shared_name,\'\'),
					COALESCE(public."Client".membership_no,\'\'),
					COALESCE(public."Client".old_membership_no,\'\'),
					COALESCE(public."User"."primaryCellPhone",\'\'),
					COALESCE(public."User"."secondaryCellPhone",\'\'))),\' \',\'\') LIKE :lik ORDER BY public."Client"."userId" DESC';

    // || COALESCE(public."User"."lastName",:cs) || COALESCE(public."User".shared_name,:cs) || COALESCE(public."Client".membership_no,:cs) || COALESCE(public."Client".old_membership_no,:cs) || COALESCE(public."User"."primaryCellPhone",:cs)  || COALESCE(public."User"."secondaryCellPhone",:cs)
    $stmt = $this->conn->prepare($sqlQuery);
    $cs = '';
    $stt = 'INACTIVE';
    $stmt->bindParam(':bid', $row['bankId']);
    $stmt->bindParam(':lik', $likk);
    // $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    $stmt->execute();
    return $stmt;
  }


  public function getBranchClientLoans($term)
  {
    $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);

    $stmtn->execute();
    $row = $stmtn->fetch();
    $likk = '%' . strtolower($term) . '%';
    $sqlQuery = 'SELECT *,public."Branch".name AS bname, public."Client"."createdAt" AS ccreatedat, public."loan".status AS lstatus FROM public."loan" LEFT JOIN public."Client" ON public."Client"."userId"=public."loan".account_id LEFT JOIN public."User" ON public."loan".account_id=public."User".id
      LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id 
       WHERE public."Branch"."bankId"=:bid AND public."User".status<>:stt AND replace(lower(CONCAT(
          COALESCE(public."User"."firstName",\'\'),
					COALESCE(public."User"."lastName",\'\'),
					COALESCE(public."User".shared_name,\'\'),
					COALESCE(public."Client".membership_no,\'\'),
					COALESCE(public."Client".old_membership_no,\'\'),
					COALESCE(public."User"."primaryCellPhone",\'\'),
					COALESCE(public."User"."secondaryCellPhone",\'\'))),\' \',\'\') LIKE :lik ORDER BY public."Client"."userId" DESC';

    // || COALESCE(public."User"."lastName",:cs) || COALESCE(public."User".shared_name,:cs) || COALESCE(public."Client".membership_no,:cs) || COALESCE(public."Client".old_membership_no,:cs) || COALESCE(public."User"."primaryCellPhone",:cs)  || COALESCE(public."User"."secondaryCellPhone",:cs)
    $stmt = $this->conn->prepare($sqlQuery);
    $cs = '';
    $stt = 'INACTIVE';
    $stmt->bindParam(':bid', $row['bankId']);
    $stmt->bindParam(':lik', $likk);
    // $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    $stmt->execute();
    return $stmt;
  }

  public function getBranchTrxnDetailsSearch($term)
  {

    $likk = '%' . strtolower($term) . '%';
    $sqlQuery = 'SELECT * FROM public."transactions"  WHERE _branch=:bid AND  lower(CONCAT(COALESCE(public."transactions".tid,:cs),COALESCE(public."transactions".acc_name,:cs))) LIKE :lik ORDER BY public."transactions".tid DESC';

    $stmt = $this->conn->prepare($sqlQuery);
    $cs = '';
    $stmt->bindParam(':bid', $this->branchId);
    $stmt->bindParam(':lik', $likk);
    $stmt->bindParam(':cs', $cs);

    $stmt->execute();
    return $stmt;
  }

  public function getBankTrxnDetailsSearch($term)
  {

    $likk = '%' . strtolower($term) . '%';
    $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id WHERE public."Branch"."bankId"=:bid AND  lower(CONCAT(COALESCE(public."transactions".tid,:cs),COALESCE(public."transactions".acc_name,:cs))) LIKE :lik ORDER BY public."transactions".tid DESC';

    $stmt = $this->conn->prepare($sqlQuery);
    $cs = '';
    $stmt->bindParam(':bid', $this->createdById);
    $stmt->bindParam(':lik', $likk);
    $stmt->bindParam(':cs', $cs);

    $stmt->execute();
    return $stmt;
  }

  public function getAllBranchBDs()
  {

    $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);

    $stmtn->execute();
    $row = $stmtn->fetch();

    $sqlQueryn = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"= public."User".id LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE DATE_PART(\'day\', public."User"."dateOfBirth") = date_part(\'day\', CURRENT_DATE) AND DATE_PART(\'month\', public."User"."dateOfBirth") = date_part(\'month\', CURRENT_DATE) AND public."Branch"."bankId"=:bid';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':bid', $row['bankId']);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankBDs()
  {

    $sqlQueryn = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"= public."User".id LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE DATE_PART(\'day\', public."User"."dateOfBirth") = date_part(\'day\', CURRENT_DATE) AND DATE_PART(\'month\', public."User"."dateOfBirth") = date_part(\'month\', CURRENT_DATE) AND public."Branch"."bankId"=:bid';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':bid', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getBranchTypeClients($term, $type)
  {
    // $ctype = '';
    $cquery = '';

    if ($type == 1) {
      // $ctype = '\'group\',\'institution\'';

      $cquery = 'public."Client".client_type IN (\'group\',\'institution\') AND ';
    }
    if ($type == 2) {
      // $ctype = '\'individual\',\'institution\'';
      $cquery = 'public."Client".client_type IN (\'individual\',\'institution\') AND ';
    }
    if ($type == 3) {
      // $ctype = '\'individual\',\'group\'';
      $cquery = 'public."Client".client_type IN (\'individual\',\'group\') AND ';
    }

    $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);

    $stmtn->execute();
    $row = $stmtn->fetch();
    $likk = '%' . strtolower($term) . '%';
    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
      LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype 
       WHERE ' . $cquery . ' public."Branch"."bankId"=:bid AND public."User".status<>:stt AND lower(COALESCE(public."User"."firstName",:cs) || COALESCE(public."User"."lastName",:cs) || COALESCE(public."User".shared_name,:cs) || COALESCE(public."Client".membership_no,:cs) || COALESCE(public."User"."primaryCellPhone",:cs)  || COALESCE(public."User"."secondaryCellPhone",:cs)) LIKE :lik ORDER BY public."Client"."createdAt" ASC';
    $stmt = $this->conn->prepare($sqlQuery);
    $cs = '';
    $stt = 'INACTIVE';
    $stmt->bindParam(':bid', $row['bankId']);
    $stmt->bindParam(':lik', $likk);
    $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);
    // $stmt->bindParam(':ct', $ctype);

    $stmt->execute();
    return $stmt;
  }
  public function getShareBranchClients($term)
  {
    $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);

    $stmtn->execute();
    $row = $stmtn->fetch();
    $likk = '%' . strtolower($term) . '%';
    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
      LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype LEFT JOIN public."share_register" ON public."share_register".userid = public."Client"."userId" 
       WHERE public."Branch"."bankId"=:bid AND public."User".status=:stt AND lower(COALESCE(public."User"."firstName",:cs) || COALESCE(public."User"."lastName",:cs) || COALESCE(public."User".shared_name,:cs)) LIKE :lik ORDER BY public."Client"."createdAt" ASC';
    $stmt = $this->conn->prepare($sqlQuery);
    $cs = '';
    $stt = 'ACTIVE';
    $stmt->bindParam(':bid', $row['bankId']);
    $stmt->bindParam(':lik', $likk);
    $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    $stmt->execute();
    return $stmt;
  }
  public function getAllBanks($term)
  {
    $sqlQueryn = 'SELECT * FROM public."Bank" WHERE lower(COALESCE(public."Bank".name,:cs) || COALESCE(public."Bank".trade_name,:cs)) LIKE :lik ORDER BY public."Bank"."createdAt" ASC';
    $stmtn = $this->conn->prepare($sqlQueryn);

    $likk = '%' . strtolower($term) . '%';

    $cs = '';
    $stmtn->bindParam(':lik', $likk);
    $stmtn->bindParam(':cs', $cs);
    $stmtn->execute();

    return $stmtn;
  }

  public function getsmstaskaudience($sid)
  {
    $sqlQueryn = 'SELECT s_type,s_savingid FROM public."scheduled_sms" WHERE s_id=:id';
    $stmtn = $this->conn->prepare($sqlQueryn);

    $stmtn->bindParam(':id', $sid);
    $stmtn->execute();

    if ($stmtn->rowCount()) {
      $row = $stmtn->fetch();
      if ($row['s_type'] == 'all') {
        return 'All Clients';
      } else if ($row['s_type'] == 'sp') {
        $sqlQueryn = 'SELECT name FROM public."savingaccounts" WHERE id=:id';
        $stmtn = $this->conn->prepare($sqlQueryn);

        $stmtn->bindParam(':id', $row['s_savingid']);
        $stmtn->execute();

        if ($stmtn->rowCount()) {
          $rown = $stmtn->fetch();

          return $rown['name'];
        } else {
          return '';
        }
      }
    }

    return '';
  }

  public function getBankName($id, $type)
  {
    if ($type == 'branch') {
      $sqlQueryn = 'SELECT public."Branch".name AS brname, public."Bank".name AS bname  FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';
      $stmtn = $this->conn->prepare($sqlQueryn);
      $stmtn->bindParam(':id', $id);
      $stmtn->execute();

      $row = $stmtn->fetch();

      return $row['brname'] . ' - ' . $row['bname'];
    } else if ($type == 'bank') {
      $sqlQueryn = 'SELECT name  FROM public."Bank"  WHERE public."Bank".id=:id ';
      $stmtn = $this->conn->prepare($sqlQueryn);
      $stmtn->bindParam(':id', $id);
      $stmtn->execute();

      $row = $stmtn->fetch();

      return $row['name'];
    }


    return '';
  }
  public function getBankName2($id, $type)
  {
    if (
      $type == 'branch'
    ) {
      $sqlQueryn = 'SELECT  public."Bank".trade_name AS bname  FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';
      $stmtn = $this->conn->prepare($sqlQueryn);
      $stmtn->bindParam(':id', $id);
      $stmtn->execute();

      $row = $stmtn->fetch();

      return $row['bname'];
    } else if ($type == 'bank') {
      $sqlQueryn = 'SELECT name  FROM public."Bank"  WHERE public."Bank".id=:id ';
      $stmtn = $this->conn->prepare($sqlQueryn);
      $stmtn->bindParam(':id', $id);
      $stmtn->execute();

      $row = $stmtn->fetch();

      return $row['name'];
    }


    return '';
  }



  public function getBankTradeName($id)
  {

    $sqlQueryn = 'SELECT name  FROM public."Bank"  WHERE public."Bank".id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $id);
    $stmtn->execute();

    $row = $stmtn->fetch();

    return $row['name'] ?? '';
  }

  public function getAllBranchSMSTasks()
  {

    $sqlQueryn = 'SELECT *  FROM public."scheduled_sms"  WHERE branch_id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getUserNames($uid)
  {

    $sqlQueryn = 'SELECT *  FROM public."User" LEFT JOIN public."Client" ON public."User".id=public."Client"."userId" WHERE public."User".id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $uid);
    $stmtn->execute();

    $row = $stmtn->fetch();
    return $row['firstName'] . ' ' . $row['lastName'];
  }
  public function getMemberNames($uid)
  {

    $sqlQueryn = 'SELECT *  FROM public."User"  LEFT JOIN public."Client" ON public."User".id=public."Client"."userId"  WHERE public."User".id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $uid);
    $stmtn->execute();

    $row = $stmtn->fetch();
    return $row['membership_no'] . ' : ' . $row['firstName'] . ' ' . $row['lastName'] . $row['shared_name'];
  }

  public function getStaffNames($uid)
  {

    $sqlQueryn = 'SELECT *  FROM public."User"  LEFT JOIN public."Staff" ON public."User".id=public."Staff"."userId"  WHERE public."User".id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $uid);
    $stmtn->execute();

    $row = $stmtn->fetch();
    return $row['positionTitle'] . ' : ' . $row['firstName'] . ' ' . $row['lastName'];
  }

  public function getAllBranchShareHolders($start_date, $end_date)
  {
    $binding_array = [];
    $sqlQueryn = 'SELECT * FROM public."share_register" LEFT JOIN public."User" ON public."share_register".userid= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."share_register".userid LEFT JOIN public."Branch" ON public."share_register".branch_id=public."Branch".id  WHERE public."share_register".branch_id=:id ';

    $binding_array[':id'] = $this->branchId;

    if (@$start_date && @$end_date) {
      $sqlQueryn .= ' AND DATE(public."share_register".date_added) >= :filter_transaction_start_date AND DATE(public."share_register".date_added) <= :filter_transaction_end_date ';
      $binding_array[':filter_transaction_start_date'] = @$start_date;
      $binding_array[':filter_transaction_end_date'] = @$end_date;
    }

    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->execute($binding_array);



    return $stmtn;
  }

  public function getAllBranchFDs()
  {

    $sqlQueryn = 'SELECT * FROM public."fixed_deposits" LEFT JOIN public."User" ON public."fixed_deposits".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."fixed_deposits".user_id LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id  WHERE public."fixed_deposits".fd_branch=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBranchSavingInits()
  {

    $sqlQueryn = 'SELECT *, public."Branch".name AS bname,public."saving_interest_initiations".name AS sname,  public."savingaccounts".name AS pname   FROM public."saving_interest_initiations" LEFT JOIN public."savingaccounts" ON public."savingaccounts".id= public."saving_interest_initiations".save_pdt LEFT JOIN public."Branch" ON public."Branch"."branchId"=public."saving_interest_initiations".branch LEFT JOIN public."User" ON public."User".id=public."saving_interest_initiations".auth_by  WHERE public."saving_interest_initiations".branch=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBranchFeesInits()
  {

    $sqlQueryn = 'SELECT *, public."Branch".name AS bname,public."general_fees_initiations".fees_name AS sname,  public."savingaccounts".name AS pname   FROM public."general_fees_initiations" LEFT JOIN public."savingaccounts" ON public."savingaccounts".id= public."general_fees_initiations".sid LEFT JOIN public."Branch" ON public."Branch".id=public."general_fees_initiations"._branch LEFT JOIN public."User" ON public."User".id=public."general_fees_initiations".auth_by  LEFT JOIN public."Fee" ON public."Fee".id=public."general_fees_initiations".fee_id  WHERE public."general_fees_initiations".branch=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankFeesInits()
  {

    $sqlQueryn = 'SELECT *, public."Branch".name AS bname,public."general_fees_initiations".fees_name AS sname,  public."savingaccounts".name AS pname   FROM public."general_fees_initiations" LEFT JOIN public."savingaccounts" ON public."savingaccounts".id= public."general_fees_initiations".sid LEFT JOIN public."Branch" ON public."Branch".id=public."general_fees_initiations"._branch LEFT JOIN public."User" ON public."User".id=public."general_fees_initiations".auth_by LEFT JOIN public."Fee" ON public."Fee".id=public."general_fees_initiations".fee_id  WHERE public."Branch"."bankId"=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBranchSavingDisburse()
  {

    $sqlQueryn = 'SELECT * FROM saving_int_disbursements WHERE _branch=:id';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankSavingDisburse()
  {

    $sqlQueryn = 'SELECT * FROM public."saving_int_disbursements" LEFT JOIN public."Branch" ON public."saving_int_disbursements"._branch=public."Branch".id WHERE public."Branch"."bankId"=:id';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankSavingInits()
  {

    $sqlQueryn = 'SELECT *, public."Branch".name AS bname,public."saving_interest_initiations".name AS sname, public."savingaccounts".name AS pname   FROM public."saving_interest_initiations" LEFT JOIN public."savingaccounts" ON public."savingaccounts".id= public."saving_interest_initiations".save_pdt LEFT JOIN public."Branch" ON public."Branch".id=public."saving_interest_initiations".branch LEFT JOIN public."User" ON public."User".id=public."saving_interest_initiations".auth_by  WHERE public."Branch"."bankId"=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllUserFDs($id)
  {

    $sqlQueryn = 'SELECT * FROM public."fixed_deposits" LEFT JOIN public."User" ON public."fixed_deposits".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."fixed_deposits".user_id LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id  WHERE public."fixed_deposits".user_id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $id);
    $stmtn->execute();

    return $stmtn;
  }

  public function getFixedDepDetails()
  {

    $sqlQueryn = 'SELECT * FROM public."fixed_deposits" LEFT JOIN public."User" ON public."fixed_deposits".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."fixed_deposits".user_id LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id  WHERE public."fixed_deposits".fd_id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }




  public function setFDSchedule($fid, $fd_amount, $fd_rate, $fd_period, $fd_period_type, $fd_freq, $fd_date, $fd_wht)
  {

    // calc

    // get form info
    $amount = $fd_amount;
    $interest_rate = $fd_rate;
    $period = $fd_period;
    $period_type = $fd_period_type;
    $frequency = $fd_freq;

    $interest = 0;
    $freq_days = 1;
    $total_period_interest = 0;
    $inst_interest = 0;
    $no_times = 0;


    if ($period_type == 'y') {
      $period_no_days = $period * 360;
      $daily_rate  = $interest_rate / 36000;
      $daily_interest = $amount * $daily_rate;
      $total_period_interest = round($daily_interest * $period_no_days);

      if ($frequency == 12) {
        $freq_days = 30;
      } else  if ($frequency == 4) {
        $freq_days = 90;
      } else  if ($frequency == 2) {
        $freq_days = 180;
      } else  if ($frequency == 1) {
        $freq_days = 360;
      }

      $no_times = round($period_no_days / $freq_days);

      $inst_interest = round($total_period_interest / $no_times);
    } else if ($period_type == 'm') {
      $period_no_days = $period * 30;
      $daily_rate  = $interest_rate / 36000;
      $daily_interest = $amount * $daily_rate;
      $total_period_interest = round($daily_interest * $period_no_days);

      if ($frequency == 12) {
        $freq_days = 30;
      } else  if ($frequency == 4) {
        $freq_days = 90;
      } else  if ($frequency == 2) {
        $freq_days = 180;
      } else  if ($frequency == 1) {
        $freq_days = 360;
      }

      $no_times = round($period_no_days / $freq_days);

      $inst_interest = round($total_period_interest / $no_times);
    } else if ($period_type == 'd') {
      $period_no_days = $period;
      $daily_rate  = $interest_rate / 36000;
      $daily_interest = $amount * $daily_rate;
      $total_period_interest = round($daily_interest * $period_no_days);

      if ($frequency == 12) {
        $freq_days = 30;
      } else  if ($frequency == 4) {
        $freq_days = 90;
      } else  if ($frequency == 2) {
        $freq_days = 180;
      } else  if ($frequency == 1) {
        $freq_days = 360;
      }

      $no_times = round($period_no_days / $freq_days);

      $inst_interest = round($total_period_interest / $no_times);
    }



    $open_bal = $amount;
    $int = $inst_interest;

    $next_date = date('Y-m-d', strtotime($fd_date));
    while ($no_times) {

      $cb = $open_bal + $int;

      $whh = ($fd_wht / 100) * $int;
      $sqlQueryn = 'INSERT INTO public.fixed_deposit_schedule(f_id, opening_bal, interest, wht_amount, closing_balance, sch_date)
	VALUES (:fid, :ob,:inter,:wht, :cb,:schdate)';
      $stmtn = $this->conn->prepare($sqlQueryn);
      $stmtn->bindParam(':fid', $fid);
      $stmtn->bindParam(':ob', $open_bal);
      $stmtn->bindParam(':inter', $int);
      $stmtn->bindParam(':wht', $whh);
      $stmtn->bindParam(':cb', $cb);
      $stmtn->bindParam(':schdate', $next_date);
      $stmtn->execute();

      $open_bal = $open_bal + $int;
      $no_times--;

      $next_date =   date('Y-m-d', strtotime($next_date . ' + ' . $freq_days . ' DAYS'));
    }
    return true;
  }

  public function getAllLoanAttachedFeesTrxns()
  {


    $sqlQueryn = 'SELECT * FROM public."transactions" LEFT JOIN public."User" ON public."transactions"._authorizedby= public."User".id  WHERE public."transactions".loan_id=:id AND public."transactions".t_type NOT IN(\'A\',\'WLI\',\'WLP\',\'L\') ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankFDs()
  {


    $sqlQueryn = 'SELECT * FROM public."fixed_deposits" LEFT JOIN public."User" ON public."fixed_deposits".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."fixed_deposits".user_id LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id  WHERE public."Branch"."bankId"=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankShareHolders($start_date, $end_date)
  {
    $binding_array = [];
    $sqlQueryn = 'SELECT * FROM public."share_register" LEFT JOIN public."User" ON public."share_register".userid= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."share_register".userid LEFT JOIN public."Branch" ON public."share_register".branch_id=public."Branch".id  WHERE public."Branch"."bankId"=:id';
    $binding_array[':id'] = $this->createdById;

    if (@$start_date && @$end_date) {
      $sqlQueryn .= ' AND DATE(public."share_register".date_added) >= :filter_transaction_start_date AND DATE(public."share_register".date_added) <= :filter_transaction_end_date ';
      $binding_array[':filter_transaction_start_date'] = @$start_date;
      $binding_array[':filter_transaction_end_date'] = @$end_date;
    }

    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->execute($binding_array);

    return $stmtn;
  }

  public function getAllBranchSharePurchaseTrxns()
  {

    $sqlQueryn = 'SELECT * , public."share_purchases".id AS tid FROM public."share_purchases" LEFT JOIN public."User" ON public."share_purchases".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."share_purchases".user_id  WHERE public."share_purchases".branch_id=:id AND public."share_purchases".t_type=\'purchase\'';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBranchShareWithdrawTrxns()
  {

    $sqlQueryn = 'SELECT * , public."share_purchases".id AS tid FROM public."share_purchases" LEFT JOIN public."User" ON public."share_purchases".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."share_purchases".user_id  WHERE public."share_purchases".branch_id=:id  AND public."share_purchases".t_type=\'withdraw\'';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBranchShareTransferTrxns()
  {

    $sqlQueryn = 'SELECT *  FROM public."share_transfers"  WHERE public."share_transfers".branch_id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankSharePurchaseTrxns()
  {

    // $bids = array();

    // $sqlQueryn = 'SELECT *  FROM public."Bank"  WHERE id=:id ';
    // $stmtn = $this->conn->prepare($sqlQueryn);
    // $stmtn->bindParam(':id', $this->createdById);
    // $stmtn->execute();


    // foreach ($stmtn as $row) {
    //   array_push($bids, $row['id']);
    // }

    // $ubids = "'" . implode("','", $bids) . "'";



    $sqlQueryn = 'SELECT *, public."share_purchases".id AS tid  FROM public."share_purchases" LEFT JOIN public."User" ON public."share_purchases".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."share_purchases".user_id LEFT JOIN public."Branch" ON public."share_purchases".branch_id=public."Branch".id  WHERE public."Branch"."bankId" =:id AND public."share_purchases".t_type=\'purchase\'';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankShareWithdrawTrxns()
  {



    $sqlQueryn = 'SELECT *, public."share_purchases".id AS tid  FROM public."share_purchases" LEFT JOIN public."User" ON public."share_purchases".user_id= public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."share_purchases".user_id LEFT JOIN public."Branch" ON public."share_purchases".branch_id=public."Branch".id  WHERE public."Branch"."bankId" =:id AND public."share_purchases".t_type=\'withdraw\'';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllBankShareTransferTrxns()
  {

    // $bids = array();

    // $sqlQueryn = 'SELECT *  FROM public."Bank"  WHERE id=:id ';
    // $stmtn = $this->conn->prepare($sqlQueryn);
    // $stmtn->bindParam(':id', $this->createdById);
    // $stmtn->execute();


    // foreach ($stmtn as $row) {
    //   array_push($bids, $row['id']);
    // }

    // $ubids = "'" . implode("','", $bids) . "'";



    $sqlQueryn = 'SELECT *  FROM public."share_transfers"  LEFT JOIN public."Branch" ON public."share_transfers".branch_id=public."Branch".id  WHERE public."Branch"."bankId" =:id';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }


  public function getAllBankSMSTasks()
  {

    //     $bids = array();
    //     $sqlQueryn = 'SELECT *  FROM public."Branch"  WHERE "bankId"=:id ';
    //     $stmtn = $this->conn->prepare($sqlQueryn);
    //     $stmtn->bindParam(':id', $this->createdById);
    //     $stmtn->execute();
    // foreach($stmtn as $row){
    // array_push($bids,$row['id']);
    // }

    //  $ubids ="'" . implode("','", $bids) . "'";

    $sqlQueryn = 'SELECT *  FROM public."scheduled_sms" LEFT JOIN public."Branch" ON public."scheduled_sms".branch_id=public."Branch".id  WHERE public."Branch"."bankId" =:id';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->createdById);
    $stmtn->execute();

    return $stmtn;
  }

  public function getAllSystemSMSTasks()
  {

    $sqlQueryn = 'SELECT *  FROM public."scheduled_sms" ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->execute();

    return $stmtn;
  }

  public function getLoanProductDetails()
  {
    $sqlQueryn = 'SELECT * FROM public."loantypes" WHERE type_id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);
    $stmtn->execute();

    return $stmtn;
  }
  public function getLoanProductFees($id)
  {
    $sqlQueryn = 'SELECT * FROM public."loanproducttofee" LEFT JOIN public."Fee" ON public."loanproducttofee".fee_id=public."Fee".id WHERE lp_id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $id);
    $stmtn->execute();

    $take = array();
    foreach ($stmtn as $row) {
      $u = array(
        'id' => $row['id'],
        'name' => $row['name']
      );

      array_push($take, $u);
    }
    return $take;
  }
  public function getBankClients($term)
  {
    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname, public."Client"."createdAt" AS ccreatedat FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
       LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype 
       WHERE public."Branch"."bankId"=:bid AND public."User".status<>:stt AND 
       replace(lower(CONCAT(
          COALESCE(public."User"."firstName",\'\'),
					COALESCE(public."User"."lastName",\'\'),
					COALESCE(public."User".shared_name,\'\'),
					COALESCE(public."Client".membership_no,\'\'),
					COALESCE(public."Client".old_membership_no,\'\'),
					COALESCE(public."User"."primaryCellPhone",\'\'),
					COALESCE(public."User"."secondaryCellPhone",\'\'))),\' \',\'\') LIKE :lik
        ORDER BY public."Client"."userId" ASC';
    $stmt = $this->conn->prepare($sqlQuery);

    $likk = '%' . strtolower($term) . '%';
    $stt = 'INACTIVE';

    $cs = '';

    $stmt->bindParam(':bid', $this->createdById);
    $stmt->bindParam(':lik', $likk);
    // $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    // $stmt->bindParam(':bid', $bidd);

    $stmt->execute();
    return $stmt;
  }


  public function getBankClientLoans($term)
  {
    $sqlQuery = 'SELECT *,public."Branch".name AS bname, public."Client"."createdAt" AS ccreatedat, public."loan".status AS lstatus FROM public."loan" LEFT JOIN public."Client" ON public."Client"."userId"=public."loan".account_id LEFT JOIN public."User" ON public."loan".account_id=public."User".id
      LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id 
       WHERE public."Branch"."bankId"=:bid AND public."User".status<>:stt AND 
       replace(lower(CONCAT(
          COALESCE(public."User"."firstName",\'\'),
					COALESCE(public."User"."lastName",\'\'),
					COALESCE(public."User".shared_name,\'\'),
					COALESCE(public."Client".membership_no,\'\'),
					COALESCE(public."Client".old_membership_no,\'\'),
					COALESCE(public."User"."primaryCellPhone",\'\'),
					COALESCE(public."User"."secondaryCellPhone",\'\'))),\' \',\'\') LIKE :lik
        ORDER BY public."Client"."userId" ASC';
    $stmt = $this->conn->prepare($sqlQuery);

    $likk = '%' . strtolower($term) . '%';
    $stt = 'INACTIVE';

    $cs = '';

    $stmt->bindParam(':bid', $this->createdById);
    $stmt->bindParam(':lik', $likk);
    // $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    // $stmt->bindParam(':bid', $bidd);

    $stmt->execute();
    return $stmt;
  }

  public function getBankTypeClients($term, $type)
  {
    // $ctype = '';
    $cquery = '';

    if ($type == 1) {
      // $ctype = '\'group\',\'institution\'';

      $cquery = 'public."Client".client_type IN (\'group\',\'institution\') AND ';
    }
    if ($type == 2) {
      // $ctype = '\'individual\',\'institution\'';
      $cquery = 'public."Client".client_type IN (\'individual\',\'institution\') AND ';
    }
    if ($type == 3) {
      // $ctype = '\'individual\',\'group\'';
      $cquery = 'public."Client".client_type IN (\'individual\',\'group\') AND ';
    }

    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
       LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype 
       WHERE ' . $cquery . ' public."Branch"."bankId"=:bid AND public."User".status<>:stt AND lower(COALESCE(public."User"."firstName",:cs) || COALESCE(public."User"."lastName",:cs) || COALESCE(public."User".shared_name,:cs) || COALESCE(public."Client".membership_no,:cs) || COALESCE(public."User"."primaryCellPhone",:cs)  || COALESCE(public."User"."secondaryCellPhone",:cs)) LIKE :lik ORDER BY public."Client"."createdAt" ASC';
    $stmt = $this->conn->prepare($sqlQuery);

    $likk = '%' . strtolower($term) . '%';
    $stt = 'INACTIVE';

    $cs = '';

    $stmt->bindParam(':bid', $this->createdById);
    $stmt->bindParam(':lik', $likk);
    $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);
    // $stmt->bindParam(':ct', $ctype);

    // $stmt->bindParam(':bid', $bidd);

    $stmt->execute();
    return $stmt;
  }
  public function getShareBankClients($term)
  {
    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
       LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype LEFT JOIN public."share_register" ON public."share_register".userid = public."Client"."userId" 
       WHERE public."Branch"."bankId"=:bid AND public."User".status=:stt AND lower(COALESCE(public."User"."firstName",:cs) || COALESCE(public."User"."lastName",:cs) || COALESCE(public."User".shared_name,:cs)) LIKE :lik ORDER BY public."Client"."createdAt" ASC';
    $stmt = $this->conn->prepare($sqlQuery);

    $likk = '%' . strtolower($term) . '%';
    $stt = 'ACTIVE';

    $cs = '';

    $stmt->bindParam(':bid', $this->createdById);
    $stmt->bindParam(':lik', $likk);
    $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    // $stmt->bindParam(':bid', $bidd);

    $stmt->execute();
    return $stmt;
  }
  public function getAllBranchLoanApplications()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
 WHERE public."loan".status=:st AND public."loan".branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 0;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchSMSTypes()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."sms_types" LEFT JOIN public."Bank" ON public."sms_types".bank_id=public."Bank".id 
 WHERE public."sms_types".branchid=:id AND deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBankSMSTypes()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."sms_types" LEFT JOIN public."Bank" ON public."sms_types".bank_id=public."Bank".id 
 WHERE public."sms_types".bank_id=:id AND deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();



    return $stmt;
  }
  public function getAllSMSTypes()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."sms_types" WHERE bank_id IS NULL AND deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->execute();



    return $stmt;
  }

  public function getAllSMSTypeDetails($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."sms_types" WHERE st_id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();



    return $stmt;
  }

  public function getAllSMSPurchaseDetails($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * , public."sms_topup_transactions".id AS tid, public."Branch".name AS branchname, public."Bank".name AS bname, public."Branch".id AS bid, public."Bank".id AS baid FROM public."sms_topup_transactions" 
    LEFT JOIN
public."Bank" ON public."sms_topup_transactions".bankid = public."Bank".id
LEFT JOIN public."Branch" ON public."sms_topup_transactions".branchid = public."Branch".id
 WHERE public."sms_topup_transactions".id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();



    return $stmt;
  }

  public function getLoanRepayments()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" WHERE t_type IN(\'L\',\'WLI\') AND _status=1 AND loan_id=:id ORDER BY tid ASC';

    $stmt = $this->conn->prepare($sqlQuery);
    // $this->status = 'L';
    $stmt->bindParam(':id', $this->lno);
    // $stmt->bindParam(':ttype', $this->status);
    $stmt->execute();



    return $stmt;
  }

  public function getLoansDatatables()
  {
    $dataTableSearchHelper = new DatatableSearchHelper();
    $binding_array = [];
    $selected_branch = null;
    if (@$this->branchId) {
      $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
      $brunchStmt = $this->conn->prepare($brunchQuery);
      $brunchStmt->bindParam(':branch_id_1', $this->branchId);
      $brunchStmt->execute();
      $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
      $selected_branch = $result;
      $this->bankId = $result['bankId'];
    }

    $sqlQuery = 'SELECT *,public."loan".status AS lstatus, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) as client_names ';

    // if ($this->with_payment_totals) {
    $sqlQuery .= ',  COALESCE((SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
      WHERE transaction.loan_id=public."loan".loan_no AND  transaction.t_type IN (\'L\') ), 0)  AS total_principal_paid  ';

    $sqlQuery .= ',  (SELECT transaction.date_created FROM public.transactions AS transaction 
      WHERE transaction.loan_id=public."loan".loan_no AND  transaction.t_type IN (\'L\') ORDER BY transaction.tid DESC LIMIT 1 )  AS last_pay_d  ';

    $sqlQuery .= ',  COALESCE((SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
      WHERE transaction.loan_id=public."loan".loan_no AND  transaction.t_type IN (\'L\') AND DATE(transaction.date_created) >= :trxn_start_date AND DATE(transaction.date_created) <= :trxn_end_date  ), 0)  AS fil_principal_paid  ';

    $sqlQuery .= ',  COALESCE((SELECT SUM(transaction.loan_interest) FROM public.transactions AS transaction 
      WHERE transaction.loan_id=public."loan".loan_no AND  transaction.t_type IN (\'L\') AND DATE(transaction.date_created) >= :trxn_start_date AND DATE(transaction.date_created) <= :trxn_end_date  ), 0)  AS fil_interest_paid  ';

    $binding_array[':trxn_start_date'] = $this->filter_transaction_start_date;
    $binding_array[':trxn_end_date'] = $this->filter_transaction_end_date;

    $sqlQuery .= ',  COALESCE((SELECT SUM(transaction.loan_interest) FROM public.transactions AS transaction 
      WHERE transaction.loan_id=public."loan".loan_no AND  transaction.t_type IN (\'L\') ), 0)  AS total_interest_paid  ';



    $sqlQuery .= ',  COALESCE((SELECT SUM(transaction.loan_penalty) FROM public.transactions AS transaction 
      WHERE transaction.loan_id=public."loan".loan_no AND  transaction.t_type IN (\'L\') ), 0)  AS total_loan_penalty  ';
    // }

    $sqlQuery .= ' FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
    public."User" ON public."loan".account_id = public."User".id
    LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
    LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id ';


    if ($this->bankId) {
      $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
      $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
      $binding_array[':bank_id'] = $this->bankId;
    }

    if (@$this->branchId) {
      $sqlQuery .= ' AND public."loan".branchid = :branch ';
      $binding_array[':branch'] = $selected_branch['id'];
    } else {
      if (@$this->filter_branch_id) {
        $sqlQuery .= ' AND public."loan"."branchid" = :branch_id ';
        $binding_array[':branch_id'] = $this->filter_branch_id;
      }
    }

    if (@$this->filter_loan_status && @$this->filter_loan_status != 9) {
      if (@$this->filter_loan_status == 'active') {
        $sqlQuery .= ' AND public."loan".status IN(2,3,4) ';
      } else {
        $sqlQuery .= ' AND public."loan".status = :loan_status ';
        $binding_array[':loan_status'] = $this->filter_loan_status;
      }
    }

    if (@$this->filter_loan_product_id) {
      $sqlQuery .= ' AND public."loan".loan_type = :loan_type_id ';
      $binding_array[':loan_type_id'] = $this->filter_loan_product_id;
    }

    if ($this->filter_next_due_date) {
      $sqlQuery .= ' AND DATE(public."loan".date_of_next_pay) = :next_due_date ';
      $binding_array[':next_due_date'] = $this->filter_next_due_date;
    }


    /**
     * Active loans filters
     */
    if ($this->filter_disbursement_start_date && $this->filter_disbursement_end_date) {
      $sqlQuery .= ' AND DATE(public."loan".date_disbursed) >= :disbursement_start_date AND DATE(public."loan".date_disbursed) <= :disbursement_end_date ';
      $binding_array[':disbursement_start_date'] = $this->filter_disbursement_start_date;
      $binding_array[':disbursement_end_date'] = $this->filter_disbursement_end_date;
    }

    /**
     * Approved loans filters
     */
    if ($this->filter_disbursement_date) {
      $sqlQuery .= ' AND DATE(public."loan".date_disbursed) = :filter_disbursement_date ';
      $binding_array[':filter_disbursement_date'] = $this->filter_disbursement_date;
    }

    if ($this->filter_application_start_date && $this->filter_application_end_date) {
      $sqlQuery .= ' AND DATE(public."loan".application_date) >= :filter_application_start_date AND DATE(public."loan".application_date) <= :filter_application_end_date ';
      $binding_array[':filter_application_start_date'] = $this->filter_application_start_date;
      $binding_array[':filter_application_end_date'] = $this->filter_application_end_date;
    }

    /**
     * declined loan filters
     */
    if (@$this->filter_frequency) {
      $sqlQuery .= ' AND public."loan".repay_cycle_id = :filter_frequency ';
      $binding_array[':filter_frequency'] = $this->filter_frequency;
    }

    if ($this->filter_declined_start_date && $this->filter_declined_end_date) {
      $sqlQuery .= ' AND DATE(public."loan".application_date) >= :filter_declined_start_date AND DATE(public."loan".application_date) <= :filter_declined_end_date ';
      $binding_array[':filter_declined_start_date'] = $this->filter_declined_start_date;
      $binding_array[':filter_declined_end_date'] = $this->filter_declined_end_date;
    }

    /**
     * closed loans filters
     */
    if ($this->filter_closing_start_date && $this->filter_closing_end_date) {
      $sqlQuery .= ' AND DATE(public."loan".application_date) >= :filter_closing_start_date AND DATE(public."loan".application_date) <= :filter_closing_end_date ';
      $binding_array[':filter_closing_start_date'] = $this->filter_closing_start_date;
      $binding_array[':filter_closing_end_date'] = $this->filter_closing_end_date;
    }


    if ($this->status) {
      if ($this->status  == 'active') {
        $sqlQuery .= ' AND public."loan".status IN(2,3,4)';
      } else {
        $sqlQuery .= ' AND public."loan".status =:loan_status ';
        $binding_array[':loan_status'] = $this->status;
      }
    } else {
      $sqlQuery .= ' AND public."loan".status =:loan_status ';
      $binding_array[':loan_status'] = 0;
    }

    /**
     * Handle/Filter client related data while user is searching
     */
    $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
    $sqlQuery .= $clientSearch['query'];
    $binding_array = array_merge($binding_array, $clientSearch['binding_array']);

    if ($this->filter_search_string) {
      // $sqlQuery .= ' AND (';
      // $sqlQuery .= ' (public."Bank".id=:bank_id_1 AND public."loan".loan_no ILIKE :loan_no) ';
      // $sqlQuery .= ' 0R (public."loan".account_id ILIKE :account_id) ';
      // $sqlQuery .= ')';
      // $binding_array[':loan_no'] = '%' . @$this->filter_search_string . '%';
      // $binding_array[':bank_id_1'] = $this->bankId;
      // $binding_array[':account_id'] = '%' . @$this->filter_search_string . '%';
    }

    $sqlQuery .= ' ORDER BY public."loan".loan_no DESC ';


    $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
    $binding_array[':limit'] = $this->filter_per_page;
    $binding_array[':offset'] = $this->filter_page;

    $loans = $this->conn->prepare($sqlQuery);
    // $stmt->bindParam(':st', $this->status);
    // $stmt->bindParam(':id', $this->createdById);
    $loans->execute($binding_array);
    return $loans->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getAllBankLoanApplications()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
    public."User" ON public."loan".account_id = public."User".id
    LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
    LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id
    WHERE public."loan".status=:st AND public."Branch"."bankId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 0;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();

    return $stmt;
  }
  public function getAllBankLoansDeclined()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id
 WHERE public."loan".status=:st AND public."Branch"."bankId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 6;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchLoansDeclined()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
 WHERE public."loan".status=:st AND public."loan".branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 6;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }


  public function getAllBankLoansApproved()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id
 WHERE public."loan".status=:st AND public."Branch"."bankId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 1;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchLoansApproved()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
 WHERE public."loan".status=:st AND public."loan".branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 1;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBankLoansActive()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT *,public."loan".status AS lstatus, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) as client_names FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
    public."User" ON public."loan".account_id = public."User".id
    LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
    LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id
    WHERE public."loan".status IN(2,3,4) AND public."Branch"."bankId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    // $this->status = '2,3,4';
    // $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchLoansActive()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT *,public."loan".status AS lstatus FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
 WHERE public."loan".status IN(2,3,4) AND public."loan".branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    // $this->status = '2,3,4';
    // $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllUserLoans($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT *,public."loan".status AS lstatus FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
 WHERE  public."loan".account_id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    // $this->status = '2,3,4';
    // $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $id);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBankLoansClosed()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id
 WHERE public."loan".status=:st AND public."Branch"."bankId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 5;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchLoansClosed()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN
public."User" ON public."loan".account_id = public."User".id
LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
 WHERE public."loan".status=:st AND public."loan".branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $this->status = 5;
    $stmt->bindParam(':st', $this->status);
    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getBankCashTransfersDatatables()
  {
    $dataTableSearchHelper = new DatatableSearchHelper();
    //  as authorized_by 
    $this->status = 'D';
    $binding_array = [];
    $selected_branch = null;
    if (@$this->branchId) {
      $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
      $brunchStmt = $this->conn->prepare($brunchQuery);
      $brunchStmt->bindParam(':branch_id_1', $this->branchId);
      $brunchStmt->execute();
      $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
      $selected_branch = $result;
      $this->bankId = $result['bankId'];
    }

    $sqlQuery = 'SELECT *, 
    public."Branch".name AS branch_name,
    (SELECT CONCAT(public."User"."firstName", \' \', public."User"."lastName") FROM public."User" 
    WHERE public."transactions"._authorizedby=public."User".id) AS authorized_by,

    (SELECT public."Staff"."positionTitle" FROM public."Staff" WHERE public."Staff"."userId"=public."transactions"._authorizedby) AS authorized_by_position,
    (SELECT public."Account".name FROM public."Account" WHERE public."Account".id=public."transactions".cr_acid) AS receivername,
    (SELECT public."Account".name FROM public."Account" WHERE public."Account".id=public."transactions".dr_acid) AS sendername

    FROM public."transactions" 

    LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
    LEFT JOIN public."Account" ON public."transactions".cr_acid=public."Account".cr_acid
    LEFT JOIN public."Staff" ON public."Staff"."userId" = public."User".id ';

    $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
    $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
    $binding_array[':bank_id'] = $this->bankId;

    // $sqlQuery .= " AND ";

    if ($this->transaction_type) {
      $sqlQuery .= ' AND public."transactions".t_type=:transaction_type ';
      $binding_array[':transaction_type'] = $this->transaction_type;
    }


    if (@$this->branchId) {
      $sqlQuery .= ' AND public."transactions"._branch = :branch ';
      $binding_array[':branch'] = $selected_branch['id'];
    } else {
      if (@$this->filter_branch_id) {
        $sqlQuery .= ' AND public."transactions"."_branch" = :branch_id ';
        $binding_array[':branch_id'] = $this->filter_branch_id;
      }
    }



    if (@$this->filter_approved_by_id) {
      $sqlQuery .= ' AND public."transactions".approvedby = :filter_approved_by_id ';
      $binding_array[':filter_approved_by_id'] = $this->filter_approved_by_id;
    }



    if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
      $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date ';
      $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
      $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
    }

    /**
     * Handle/Filter client related data while user is searching
     */
    $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
    $sqlQuery .= $clientSearch['query'];
    $binding_array = array_merge($binding_array, $clientSearch['binding_array']);

    $sqlQuery .= ' ORDER BY public."transactions".date_created DESC ';
    $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
    $binding_array[':limit'] = $this->filter_per_page;
    $binding_array[':offset'] = $this->filter_page;

    $transactions = $this->conn->prepare($sqlQuery);
    // $transactions->bindParam(':id', $this->createdById);
    // $transactions->bindParam(':tt', $this->status);
    $transactions->execute($binding_array);


    return $transactions->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getAgentDepositsAll()
  {
    // $dataTableSearchHelper = new DatatableSearchHelper();
    //  as authorized_by 
    $dataTableSearchHelper = new DatatableSearchHelper();
    $this->status = 'D';


    $sqlQuery = 'SELECT *, 
    public."Branch".name AS branch_name,
    (SELECT CONCAT(public."User"."firstName", \' \', public."User"."lastName") FROM public."User" 
    WHERE public."transactions"._authorizedby=public."User".id) AS authorized_by,

    (SELECT public."Staff"."positionTitle" FROM public."Staff" WHERE public."Staff"."userId"=public."transactions"._authorizedby) AS authorized_by_position

    FROM public."transactions" 

    LEFT JOIN public."User" ON public."transactions".mid = public."User".id
    LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
    LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
    LEFT JOIN public."Staff" ON public."Staff"."userId" = public."User".id ';

    $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
    $sqlQuery .= ' WHERE public."transactions"._status=0 ';

    // $sqlQuery .= " AND ";
    $this->transaction_type = 'D';
    $sqlQuery .= ' AND public."transactions".t_type=:transaction_type ';
    $binding_array[':transaction_type'] = $this->transaction_type;




    if (@$this->filter_approved_by_id) {
      $sqlQuery .= ' AND public."transactions"._authorizedby = :filter_approved_by_id ';
      $binding_array[':filter_approved_by_id'] = $this->filter_approved_by_id;
    }




    /**
     * Handle/Filter client related data while user is searching
     */
    $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
    $sqlQuery .= $clientSearch['query'];
    $binding_array = array_merge($binding_array, $clientSearch['binding_array']);

    if ($this->filter_search_string) {
      $sqlQuery .= ' OR public.transactions.trxn_ref ILIKE :transaction_reference ';
      $binding_array[':transaction_reference'] = '%' . @$this->filter_sub_account_id . '%';
    }

    $sqlQuery .= ' ORDER BY public."transactions".date_created DESC ';
    $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
    $binding_array[':limit'] = $this->filter_per_page;
    $binding_array[':offset'] = $this->filter_page;

    $transactions = $this->conn->prepare($sqlQuery);
    // $transactions->bindParam(':id', $this->createdById);
    // $transactions->bindParam(':tt', $this->status);
    $transactions->execute($binding_array);


    return $transactions->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getBankTransactionsDatatables()
  {
    $dataTableSearchHelper = new DatatableSearchHelper();
    //  as authorized_by 
    $this->status = 'D';
    $binding_array = [];
    $selected_branch = null;
    if (@$this->branchId) {
      $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
      $brunchStmt = $this->conn->prepare($brunchQuery);
      $brunchStmt->bindParam(':branch_id_1', $this->branchId);
      $brunchStmt->execute();
      $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
      $selected_branch = $result;
      $this->bankId = $result['bankId'];
    }

    $sqlQuery = 'SELECT *, 
    public."Branch".name AS branch_name, public."Client".membership_no AS m_no,
    (SELECT CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name) FROM public."User" 
    WHERE public."transactions"._authorizedby=public."User".id) AS authorized_by,

    (SELECT public."Staff"."positionTitle" FROM public."Staff" WHERE public."Staff"."userId"=public."transactions"._authorizedby) AS authorized_by_position

    FROM public."transactions" 

    LEFT JOIN public."User" ON public."transactions".mid = public."User".id
    LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
    LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
    LEFT JOIN public."Staff" ON public."Staff"."userId" = public."User".id ';

    $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
    $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
    $binding_array[':bank_id'] = $this->bankId;

    // $sqlQuery .= " AND ";

    if ($this->transaction_type) {
      $sqlQuery .= ' AND public."transactions".t_type=:transaction_type ';
      $binding_array[':transaction_type'] = $this->transaction_type;
    }

    if (@$this->filter_loan_amount) {

      if ($this->filter_loan_amount == 0) {
      }
      if ($this->filter_loan_amount == 1) {
        $from_v = 100000;
        $to_v = 500000;
        $sqlQuery .= ' AND public."transactions".amount>=:st AND public."transactions".amount<=:enn ';
        $binding_array[':st'] = $from_v;
        $binding_array[':enn'] = $to_v;
      }
      if ($this->filter_loan_amount == 2) {
        $from_v = 500001;
        $to_v = 1000000;
        $sqlQuery .= ' AND public."transactions".amount>=:st AND public."transactions".amount<=:enn ';
        $binding_array[':st'] = $from_v;
        $binding_array[':enn'] = $to_v;
      }
      if ($this->filter_loan_amount == 3) {
        $from_v = 1000001;
        $to_v = 5000000;
        $sqlQuery .= ' AND public."transactions".amount>=:st AND public."transactions".amount<=:enn ';
        $binding_array[':st'] = $from_v;
        $binding_array[':enn'] = $to_v;
      }
      if ($this->filter_loan_amount == 4) {
        $from_v = 5000001;
        $to_v = 10000000;
        $sqlQuery .= ' AND public."transactions".amount>=:st AND public."transactions".amount<=:enn ';
        $binding_array[':st'] = $from_v;
        $binding_array[':enn'] = $to_v;
      }
      if ($this->filter_loan_amount == 5) {
        $from_v = 10000001;
        $to_v = 100000000;
        $sqlQuery .= ' AND public."transactions".amount>=:st AND public."transactions".amount<=:enn ';
        $binding_array[':st'] = $from_v;
        $binding_array[':enn'] = $to_v;
      }
      if ($this->filter_loan_amount == 6) {
        $from_v = 100000001;
        $to_v = 300000000;
        $sqlQuery .= ' AND public."transactions".amount>=:st AND public."transactions".amount<=:enn ';
        $binding_array[':st'] = $from_v;
        $binding_array[':enn'] = $to_v;
      }
      if ($this->filter_loan_amount == 7) {
        $to_v = 300000000;
        $sqlQuery .= ' AND public."transactions".amount>:st ';
        $binding_array[':st'] = $from_v;
      }
    }


    if (@$this->branchId) {
      $sqlQuery .= ' AND public."transactions"._branch = :branch ';
      $binding_array[':branch'] = $selected_branch['id'];
    } else {
      if (@$this->filter_branch_id) {
        $sqlQuery .= ' AND public."transactions"."_branch" = :branch_id ';
        $binding_array[':branch_id'] = $this->filter_branch_id;
      }
    }


    if (@$this->filter_deposit_method) {
      $sqlQuery .= ' AND public."transactions".pay_method = :deposit_method ';
      $binding_array[':deposit_method'] = $this->filter_deposit_method;
    }

    if (@$this->filter_withdraw_method) {
      $sqlQuery .= ' AND public."transactions".pay_method = :filter_withdraw_method ';
      $binding_array[':filter_withdraw_method'] = $this->filter_withdraw_method;
    }

    if (@$this->filter_transaction_method) {
      $sqlQuery .= ' AND public."transactions".pay_method = :filter_transaction_method ';
      $binding_array[':filter_transaction_method'] = $this->filter_transaction_method;
    }

    if (@$this->filter_approved_by_id) {
      $sqlQuery .= ' AND public."transactions"._authorizedby = :filter_approved_by_id ';
      $binding_array[':filter_approved_by_id'] = $this->filter_approved_by_id;
    }

    if (@$this->filter_sub_account_id) {
      $sqlQuery .= ' AND public."transactions".acid = :filter_sub_account_id ';
      $binding_array[':filter_sub_account_id'] = $this->filter_sub_account_id;
    }


    if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
      $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date ';
      $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
      $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
    }

    /**
     * Handle/Filter client related data while user is searching
     */
    $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
    $sqlQuery .= $clientSearch['query'];
    $binding_array = array_merge($binding_array, $clientSearch['binding_array']);

    if ($this->filter_search_string) {
      $sqlQuery .= ' OR public.transactions.trxn_ref ILIKE :transaction_reference ';
      $binding_array[':transaction_reference'] = '%' . @$this->filter_sub_account_id . '%';
    }

    $sqlQuery .= ' ORDER BY public."transactions".date_created DESC ';
    $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
    $binding_array[':limit'] = $this->filter_per_page;
    $binding_array[':offset'] = $this->filter_page;

    $transactions = $this->conn->prepare($sqlQuery);
    // $transactions->bindParam(':id', $this->createdById);
    // $transactions->bindParam(':tt', $this->status);
    $transactions->execute($binding_array);


    return $transactions->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getBankJournalEntriesDatatables()
  {
    $dataTableSearchHelper = new DatatableSearchHelper();
    //  as authorized_by 
    $this->status = '';

    $acc_name  = null;
    if (@$this->filter_sub_account_id) {
      $sqlQuery = 'SELECT * FROM public."Account" WHERE  public."Account".id=:id ';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->filter_sub_account_id);

      $stmt->execute();
      $row = $stmt->fetch();

      $acc_name = $row['name'] ?? '';
    }

    $binding_array = [];


    $sqlQuery = 'SELECT *, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ) as authorized_by_names, (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \',public."User".shared_name)) FROM public."User" WHERE public."User".id=transaction.mid ) as client_names FROM public."transactions" AS transaction LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

    $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';

    if ($this->branchId) {
      $sqlQuery .= ' WHERE  transaction._branch = :bid ';
      $binding_array[':bid'] = $this->branchId;
    } else {
      $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
      $binding_array[':bank_id'] = $this->bankId;
    }


    if (@$this->filter_approved_by_id) {
      $sqlQuery .= ' AND transaction._authorizedby = :filter_approved_by_id ';
      $binding_array[':filter_approved_by_id'] = $this->filter_approved_by_id;
    }

    if (@$this->filter_sub_account_id) {

      if (!@$this->branchId && @$this->bankId) {
        $sqlQuery .= ' AND (transaction.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name ILIKE \'' . @$acc_name . '\') OR transaction.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name ILIKE \'' . @$acc_name . '\') OR transaction.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name ILIKE \'' . @$acc_name . '\')) ';
        $binding_array[':bk'] = $this->bankId;
      } else {
        $sqlQuery .= ' AND (transaction.acid::text = :filter_acid OR transaction.cr_acid=:filter_acid OR transaction.dr_acid=:filter_acid) ';
        $binding_array[':filter_acid'] = $this->filter_sub_account_id;
      }
    }


    if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
      $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
      $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
      $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
    }

    /**
     * Handle/Filter client related data while user is searching
     */
    $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
    $sqlQuery .= $clientSearch['query'];
    $binding_array = array_merge($binding_array, $clientSearch['binding_array']);

    if ($this->filter_search_string) {
      $sqlQuery .= ' OR transaction.tid ILIKE :transaction_reference ';
      $binding_array[':transaction_reference'] = '%' . @$this->filter_sub_account_id . '%';
    }

    $sqlQuery .= ' ORDER BY transaction.tid DESC ';
    $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
    $binding_array[':limit'] = $this->filter_per_page;
    $binding_array[':offset'] = $this->filter_page;

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->execute($binding_array);


    return $transactions->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getBankTransactionsDatatablesFees()
  {
    $dataTableSearchHelper = new DatatableSearchHelper();
    //  as authorized_by 
    $this->status = 'D';
    $binding_array = [];
    $selected_branch = null;
    if (@$this->branchId) {
      $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
      $brunchStmt = $this->conn->prepare($brunchQuery);
      $brunchStmt->bindParam(':branch_id_1', $this->branchId);
      $brunchStmt->execute();
      $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
      $selected_branch = $result;
      $this->bankId = $result['bankId'];
    }

    $sqlQuery = 'SELECT *, 
    public."Branch".name AS branch_name,
    (SELECT CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name) FROM public."User" 
    WHERE public."transactions"._authorizedby=public."User".id) AS authorized_by,

    (SELECT public."Staff"."positionTitle" FROM public."Staff" WHERE public."Staff"."userId"=public."transactions"._authorizedby) AS authorized_by_position

    FROM public."transactions" 

    LEFT JOIN public."User" ON public."transactions".mid = public."User".id
    LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
    LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
    LEFT JOIN public."Staff" ON public."Staff"."userId" = public."User".id ';

    $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
    $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
    $binding_array[':bank_id'] = $this->bankId;

    // $sqlQuery .= " AND ";

    if ($this->transaction_type) {
      $sqlQuery .= ' AND public."transactions".t_type=:transaction_type AND public."transactions".trxn_rec=\'fees\' ';
      $binding_array[':transaction_type'] = $this->transaction_type;
    }


    if (@$this->branchId) {
      $sqlQuery .= ' AND public."transactions"._branch = :branch ';
      $binding_array[':branch'] = $selected_branch['id'];
    } else {
      if (@$this->filter_branch_id) {
        $sqlQuery .= ' AND public."transactions"."_branch" = :branch_id ';
        $binding_array[':branch_id'] = $this->filter_branch_id;
      }
    }


    if (@$this->filter_deposit_method) {
      $sqlQuery .= ' AND public."transactions".pay_method = :deposit_method ';
      $binding_array[':deposit_method'] = $this->filter_deposit_method;
    }

    if (@$this->filter_withdraw_method) {
      $sqlQuery .= ' AND public."transactions".pay_method = :filter_withdraw_method ';
      $binding_array[':filter_withdraw_method'] = $this->filter_withdraw_method;
    }

    if (@$this->filter_transaction_method) {
      $sqlQuery .= ' AND public."transactions".pay_method = :filter_transaction_method ';
      $binding_array[':filter_transaction_method'] = $this->filter_transaction_method;
    }

    if (@$this->filter_approved_by_id) {
      $sqlQuery .= ' AND public."transactions"._authorizedby = :filter_approved_by_id ';
      $binding_array[':filter_approved_by_id'] = $this->filter_approved_by_id;
    }

    if (@$this->filter_sub_account_id) {
      $sqlQuery .= ' AND public."transactions".acid = :filter_sub_account_id ';
      $binding_array[':filter_sub_account_id'] = $this->filter_sub_account_id;
    }


    if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
      $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date ';
      $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
      $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
    }

    /**
     * Handle/Filter client related data while user is searching
     */
    $clientSearch = $dataTableSearchHelper->search_client($this->filter_search_string);
    $sqlQuery .= $clientSearch['query'];
    $binding_array = array_merge($binding_array, $clientSearch['binding_array']);

    if ($this->filter_search_string) {
      $sqlQuery .= ' OR public.transactions.trxn_ref ILIKE :transaction_reference ';
      $binding_array[':transaction_reference'] = '%' . @$this->filter_sub_account_id . '%';
    }

    $sqlQuery .= ' ORDER BY public."transactions".date_created DESC ';
    $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
    $binding_array[':limit'] = $this->filter_per_page;
    $binding_array[':offset'] = $this->filter_page;

    $transactions = $this->conn->prepare($sqlQuery);
    // $transactions->bindParam(':id', $this->createdById);
    // $transactions->bindParam(':tt', $this->status);
    $transactions->execute($binding_array);


    return $transactions->fetchAll(PDO::FETCH_ASSOC);
  }



  public function getAllBranchRequests()
  {

    $sqlQuery = 'SELECT  * FROM public.inter_branch_requests WHERE from_branch=:id OR to_branch=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->bankId);
    $transactions->execute();
    return $transactions;
  }

  public function getAllBankBranchRequests()
  {

    $sqlQuery = 'SELECT  * FROM public.inter_branch_requests LEFT JOIN public."Branch" ON public."Branch".id = public.inter_branch_requests.from_branch WHERE public."Branch"."bankId"=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->bankId);
    $transactions->execute();
    return $transactions;
  }

  public function getAllBankCashTransfers()
  {
    $binding_array = [];
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
    WHERE public."transactions".t_type IN (\'STS\',\'STT\',\'TTS\',\'TTT\',\'TTB\',\'BTB\',\'BTS\',\'STB\',\'BRTBR\') AND public."Branch"."bankId"=:bank_id ';
    $binding_array[':bank_id'] = $this->bankId;

    // $tt = "'STS','STT','TTS','TTT','TTB','BTB','BTS','STB','BRTBR'";

    if (@$this->createdById && @$this->loanproductid) {
      $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :transaction_start_date AND DATE(public."transactions".date_created) <= :transaction_end_date ';
      $binding_array[':transaction_start_date'] = @$this->createdById;
      $binding_array[':transaction_end_date'] = @$this->loanproductid;
    }



    $sqlQuery .= ' ORDER BY public."transactions".tid DESC LIMIT 5000 ';

    $transactions = $this->conn->prepare($sqlQuery);
    // $transactions->bindParam(':bank_id', $this->bankId);
    // $transactions->bindParam(':tt', $tt);
    $transactions->execute($binding_array);
    return $transactions;
  }

  public function getAllBranchCashTransfers()
  {
    $binding_array = [];
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id 
    WHERE public."transactions".t_type IN (\'STS\',\'STT\',\'TTS\',\'TTT\',\'TTB\',\'BTB\',\'BTS\',\'STB\',\'BRTBR\') AND public."transactions"._branch=:baranch_id';

    $binding_array[':baranch_id'] = $this->branchId;


    if (@$this->createdById && @$this->loanproductid) {
      $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :transaction_start_date AND DATE(public."transactions".date_created) <= :transaction_end_date ';
      $binding_array[':transaction_start_date'] = @$this->createdById;
      $binding_array[':transaction_end_date'] = @$this->loanproductid;
    }

    $sqlQuery .= ' ORDER BY public."transactions" DESC LIMIT 5000 ';

    // $tt = "'STS','STT','TTS','TTT','TTB','BTB','BTS','STB','BRTBR'";
    $transactions = $this->conn->prepare($sqlQuery);
    // $transactions->bindParam(':baranch_id', $this->branchId);
    // $transactions->bindParam(':tt', $tt);
    $transactions->execute($binding_array);

    // $transactions->execute();
    return $transactions;
  }

  public function getAllBankTransactions()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
    public."User" ON public."transactions".mid = public."User".id
    LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
    LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
    WHERE public."Branch"."bankId"=:bank_id AND public."transactions".t_type=:ty ';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':bank_id', $this->bankId);
    $transactions->bindParam(':ty', $this->transaction_type);
    $transactions->execute();
    return $transactions->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllBranchTransactions()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
    public."User" ON public."transactions".mid = public."User".id
    LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
    WHERE public."transactions"._branch=:baranch_id AND public."transactions".t_type=:ty';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':baranch_id', $this->branchId);
    $transactions->bindParam(
      ':ty',
      $this->transaction_type
    );
    $transactions->execute();
    return $transactions->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllBankDeposits()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
 WHERE public."transactions".t_type=:tt AND public."Branch"."bankId"=:id ORDER BY public."transactions".tid DESC LIMIT 5000';

    $stmt = $this->conn->prepare($sqlQuery);
    $this->status = 'D';
    $stmt->bindParam(':id', $this->createdById);
    $stmt->bindParam(':tt', $this->status);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBankDepositsFees()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
 WHERE public."transactions".t_type=:tt AND public."transactions".trxn_rec=\'fees\' AND public."Branch"."bankId"=:id ORDER BY public."transactions".tid DESC LIMIT 5000';

    $stmt = $this->conn->prepare($sqlQuery);
    $this->status = 'D';
    $stmt->bindParam(':id', $this->createdById);
    $stmt->bindParam(':tt', $this->status);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBankWithdraws()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
 WHERE public."transactions".t_type=:tt AND public."Branch"."bankId"=:id ORDER BY public."transactions".date_created DESC LIMIT 5000';

    $stmt = $this->conn->prepare($sqlQuery);
    $this->status = 'W';
    $stmt->bindParam(':id', $this->createdById);
    $stmt->bindParam(':tt', $this->status);
    $stmt->execute();



    return $stmt;
  }

  public function getStaffDetails($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Staff" 
    LEFT JOIN
public."User" ON public."Staff"."userId" = public."User".id
 WHERE  public."Staff"."userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();



    return $row['firstName'] . ' ' . $row['lastName'] . ' - ' . $row['positionTitle'];
  }

  public function getUserActype($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Client" 
    LEFT JOIN
public."savingaccounts" ON public."Client".actype = public."savingaccounts".id
 WHERE  public."Client"."userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();



    return $row['name'] ?? ' ';
  }
  public function getAccountDetails($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Account" WHERE  public."Account".id=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();



    return $row['name'] ?? '';
  }

  public function getBranchDetails($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Branch" 
 WHERE  public."Branch".id=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();



    return $row['name'];
  }

  public function getAllBranchCashAccounts($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Account" 
 WHERE  public."Account"."branchId"=:id AND public."Account".is_cash_account>0';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();



    return $row ?? '';
  }

  public function getBranchInterBranchAcc($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Account" 
 WHERE  public."Account"."branchId"=:id AND public."Account".is_inter_branch>0';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      $row = $stmt->fetch();
      return $row['id'];
    } else {

      // inter-branch account
      $sqlQuery = 'INSERT INTO public."Account"(
            type, "branchId",name, description, "isSystemGenerated",is_inter_branch)
           VALUES (:typee,:bid,:nname,:descr,:isgen,:isinterbranch )';
      $atype = 'ASSETS';
      $nname = 'Inter-Branch Transactions A/C';
      $descr = 'This account is used for all inter-branch transactions for ' . strtolower($id);
      $isgen = true;
      $isinterbranch = 1;
      $stmt = $this->conn->prepare($sqlQuery);
      $stmt->bindParam(':typee', $atype);
      $stmt->bindParam(':bid', $id);
      $stmt->bindParam(':nname', $nname);
      $stmt->bindParam(':descr', $descr);
      $stmt->bindParam(':isgen', $isgen);
      $stmt->bindParam(':isinterbranch', $isinterbranch);

      $stmt->execute();


      // get the account id
      $sqlQuery = 'SELECT * FROM public."Account" 
 WHERE  public."Account"."branchId"=:id AND public."Account".is_inter_branch>0';

      $stmt = $this->conn->prepare($sqlQuery);
      $stmt->bindParam(':id', $id);
      $stmt->execute();

      $row = $stmt->fetch();
      return $row['id'];
    }

    return '';
  }

  public function getBankDetails($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Branch" 
 WHERE  public."Branch".id=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();

    $sqlQueryn = 'SELECT * FROM public."Bank" 
 WHERE  public."Bank".id=:id';

    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $row['bankId']);
    $stmtn->execute();
    $rown = $stmtn->fetch();



    return $rown['trade_name'];
  }
  public function getAllBranchDepositsFees()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
 WHERE public."transactions".t_type=:tt AND public."transactions".trxn_rec=\'fees\' AND  public."transactions"._branch=:id ORDER BY public."transactions".tid DESC ';

    $stmt = $this->conn->prepare($sqlQuery);
    $this->status = 'D';

    $stmt->bindParam(':id', $this->branchId);
    $stmt->bindParam(':tt', $this->status);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchJournalEntries()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" AS transaction
   
 WHERE (transaction.acid::text = :filter_acid OR transaction.cr_acid=:filter_acid OR transaction.dr_acid=:filter_acid) AND  transaction._branch=:id ORDER BY transaction.tid DESC';

    $stmt = $this->conn->prepare($sqlQuery);


    $stmt->bindParam(':filter_acid', $this->filter_sub_account_id);
    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }


  public function getAllBankJournalEntries()
  {
    $acc_name  = null;
    if (@$this->filter_sub_account_id) {
      $sqlQuery = 'SELECT * FROM public."Account" WHERE  public."Account".id=:id ';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->filter_sub_account_id);

      $stmt->execute();
      $row = $stmt->fetch();

      $acc_name = $row['name'] ?? '';
    }
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" AS transaction
   
 WHERE ';

    $sqlQuery .= ' (transaction.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name ILIKE \'' . @$acc_name . '\') OR transaction.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name ILIKE \'' . @$acc_name . '\') OR transaction.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name ILIKE \'' . @$acc_name . '\')) ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bk', $this->bankId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchDeposits()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
 WHERE public."transactions".t_type=:tt AND  public."transactions"._branch=:id ORDER BY public."transactions".tid DESC LIMIT 5000';

    $stmt = $this->conn->prepare($sqlQuery);
    $this->status = 'D';

    $stmt->bindParam(':id', $this->branchId);
    $stmt->bindParam(':tt', $this->status);
    $stmt->execute();



    return $stmt;
  }

  public function getAllAgentDeposits()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
 WHERE public."transactions".t_type=:tt AND  public."transactions"._authorizedby=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $this->status = 'D';

    $stmt->bindParam(':id', $this->filter_approved_by_id);
    $stmt->bindParam(':tt', $this->status);
    $stmt->execute();

    return $stmt;
  }



  public function getAllBranchSMSPurchases()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * , public."sms_topup_transactions".id AS tid, public."Branch".name AS branchname, public."Bank".name AS bname FROM public."sms_topup_transactions" 
    LEFT JOIN
public."Bank" ON public."sms_topup_transactions".bankid = public."Bank".id
LEFT JOIN public."Branch" ON public."sms_topup_transactions".branchid = public."Branch".id
 WHERE public."sms_topup_transactions".branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchSMSOutbox()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * , public."Branch".name AS branchname FROM public."sms_outbox" LEFT JOIN public."Branch" ON public."sms_outbox".branchid = public."Branch".id
 WHERE public."sms_outbox".branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBankSMSPurchases()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * , public."sms_topup_transactions".id AS tid, public."Branch".name AS branchname, public."Bank".name AS bname FROM public."sms_topup_transactions" 
    LEFT JOIN
public."Bank" ON public."sms_topup_transactions".bankid = public."Bank".id
LEFT JOIN public."Branch" ON public."sms_topup_transactions".branchid = public."Branch".id
 WHERE public."sms_topup_transactions".bankid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();



    return $stmt;
  }
  public function getAllBankSMSOutbox()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * , public."Branch".name AS branchname FROM public."sms_outbox" LEFT JOIN public."Branch" ON public."sms_outbox".branchid = public."Branch".id
 WHERE public."Branch"."bankId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();



    return $stmt;
  }

  public function getAllSystemSMSPurchases()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT *, public."sms_topup_transactions".id AS tid, public."Branch".name AS branchname, public."Bank".name AS bname  FROM public."sms_topup_transactions" 
    LEFT JOIN
public."Bank" ON public."sms_topup_transactions".bankid = public."Bank".id
LEFT JOIN public."Branch" ON public."sms_topup_transactions".branchid = public."Branch".id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->execute();



    return $stmt;
  }

  public function getAllSystemSMSOutbox()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT *, public."Branch".name AS branchname  FROM public."sms_outbox" 
    LEFT JOIN
public."Bank" ON public."sms_outbox".bankid = public."Bank".id
LEFT JOIN public."Branch" ON public."sms_outbox".branchid = public."Branch".id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->execute();



    return $stmt;
  }

  public function getAllSystemSMSKeys()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT DISTINCT(msg_key)  FROM public."sms_outbox"';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->execute();
    return $stmt;
  }

  public function getAllBranchSMSKeys()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT DISTINCT(msg_key)  FROM public."sms_outbox" WHERE branchid=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $this->branchId);

    $stmt->execute();
    return $stmt;
  }

  public function getAllBankSMSKeys()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT DISTINCT(msg_key)  FROM public."sms_outbox" WHERE bankid=:id';

    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $this->createdById);

    $stmt->execute();
    return $stmt;
  }

  public function getTransactionDetails()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
 WHERE  public."transactions".tid=:id';

    $stmt = $this->conn->prepare($sqlQuery);


    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getAllBranchWithdraws()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
    LEFT JOIN
public."User" ON public."transactions".mid = public."User".id
LEFT JOIN public."Client" ON public."transactions".mid = public."Client"."userId"
 WHERE public."transactions".t_type=:tt AND  public."transactions"._branch=:id ORDER BY public."transactions".date_created DESC LIMIT 5000';

    $stmt = $this->conn->prepare($sqlQuery);
    $this->status = 'W';

    $stmt->bindParam(':id', $this->branchId);
    $stmt->bindParam(':tt', $this->status);
    $stmt->execute();



    return $stmt;
  }
  public function createGuarantor()
  {

    if ((int)$this->denialReason == 0) {


      $sqlQuery = 'INSERT INTO public."guarantors"(_mid,_loanid,type,description,attachment) VALUES (:mid,:lid,:type,:descr,:attachment)';

      $stmt = $this->conn->prepare($sqlQuery);
      $typ = 'non-member';
      $this->lno = 0;
      $stmt->bindParam(':mid', $this->lno);
      $stmt->bindParam(':lid', $this->createdById);
      $stmt->bindParam(':type', $typ);
      $stmt->bindParam(':descr', $this->updatedById);
      $stmt->bindParam(':attachment', $this->interestRate);
      $stmt->execute();

      return true;
    } else {
      $sqlQuery = 'INSERT INTO public."guarantors"(_mid,_loanid,attachment) VALUES (:mid,:lid,:attachment)';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':mid', $this->lno);
      $stmt->bindParam(':lid', $this->createdById);
      $stmt->bindParam(':attachment', $this->interestRate);
      $stmt->execute();

      return true;
    }
    return true;
  }

  public function createCollateral()
  {
    // get client names,left balance
    $sqlQuery = 'INSERT INTO public."collaterals"(loanid,_collateral,_mvalue,_attachment,cat,_receivedby,_fvalue,_location) VALUES 
    (:lid,:coll,:mv,:att,:cat,:rby,:fv,:loc)';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':lid', $this->createdById);
    $stmt->bindParam(':coll', $this->updatedById);
    $stmt->bindParam(':mv', $this->applicationDate);
    $stmt->bindParam(':att', $this->interestRate);
    $stmt->bindParam(':cat', $this->lno);
    $stmt->bindParam(':rby', $this->penaltyInterestRate);
    $stmt->bindParam(':fv', $this->disbursedAmount);
    $stmt->bindParam(':loc', $this->requestedAmount);
    $stmt->execute();



    return true;
  }

  public function createCollateralCategory()
  {
    // get client names,left balance
    $sqlQuery = 'INSERT INTO public."collateral_categories"
    (_catname,_catdesc,bankid) VALUES 
    (:name,:descr,:bid)';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bid', $this->lno);
    $stmt->bindParam(':descr', $this->updatedById);
    $stmt->bindParam(':name', $this->createdById);
    $stmt->execute();



    return true;
  }
  public function getFeesDetails($lpid)
  {
    $sqlQuery = 'SELECT * FROM public."loanproducttofee" LEFT JOIN public."Fee" ON public."loanproducttofee".fee_id=public."Fee".id WHERE public."loanproducttofee".lp_id=:lpid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':lpid', $lpid);

    $stmt->execute();
    $row = $stmt->fetch();
    if ($row) {
      return $row['type'] . ' - ' . $row['name'];
    }
    return '';
  }

  //   public function getAllLoanProducts()
  //   {
  //     // get client names,left balance
  //     $sqlQuery = 'SELECT * FROM public."loantypes" 
  //  WHERE public."loantypes"."bankId"=:id ';

  //     $stmt = $this->conn->prepare($sqlQuery);

  //     $stmt->bindParam(':id', $this->branchId);
  //     $stmt->execute();



  //     return $stmt;
  //   }

  public function getAllLoanProducts()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."loantypes" WHERE public."loantypes"."bankId"=:id AND status=1';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->createdById);
    $stmt->execute();

    return $stmt;
  }

  public function getAllBranchLoanProducts()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Branch" 
 WHERE public."Branch".id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();
    $row = $stmt->fetch();


    $sqlQuery = 'SELECT * FROM public."loantypes" 
    WHERE public."loantypes"."bankId"=:id AND status=1';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $row['bankId']);
    $stmt->execute();



    return $stmt;
  }

  public function getLoanDetails($id = null)
  {
    $id = $id ?? $this->lno;

    $sqlQuery = 'SELECT loan_no,account_id,loan_type,loan_officer,branchid FROM public."loan" 
 WHERE public."loan".loan_no=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt;
  }
  public function getRequestDetails($id = null)
  {
    $id = $id ?? $this->lno;

    $sqlQuery = 'SELECT * FROM public."inter_branch_requests" 
 WHERE public."inter_branch_requests".req_id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt;
  }



  public function getMainAccounts()
  {

    $sqlQuery = 'SELECT * FROM public."system_gl_accounts" ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->execute();

    return $stmt;
  }

  public function getLoanClientDetails($cid)
  {

    $sqlQuery = 'SELECT * FROM public."Client" 
    LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."Client".actype = public."savingaccounts".id
 WHERE public."Client"."userId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row;
  }

  public function getLastUserTransaction($id)
  {
    $sqlQuery = 'SELECT * FROM public."transactions" 
         WHERE mid=:bid ORDER BY tid DESC LIMIT 1';
    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':bid', $id);
    // $stmt->bindParam(':bid', $bidd);

    $stmt->execute();
    $row = $stmt->fetch();

    if ($row) {
      return date('Y-m-d', strtotime($row['date_created'])) . '  | ( UGX: ' . number_format($row['amount']) . ' - ' . $row['t_type'] . ' )';
    }
    return 'Not Transaction yet';
  }

  public function getInterestDue($cid)
  {
    $now = date('Y-m-d');
    $sqlQuery = 'SELECT SUM(interest) AS int_tot, SUM(interest_paid) AS int_paid FROM fixed_deposit_schedule WHERE f_id=:id AND  DATE(sch_date)<= :nn';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->bindParam(':nn', $now);
    $stmt->execute();
    $row = $stmt->fetch();

    $tot_due = $row['int_tot'] - $row['int_paid'];
    return $tot_due;
  }

  public function getFDInterest($cid)
  {

    $sqlQuery = 'SELECT SUM(interest) AS int_tot FROM fixed_deposit_schedule WHERE f_id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();

    $tot_due = $row['int_tot'];
    return $tot_due;
  }

  public function getWhtDue($cid)
  {
    $now = date('Y-m-d');
    $sqlQuery = 'SELECT SUM(wht_amount) AS int_tot, SUM(wht_paid) AS int_paid FROM fixed_deposit_schedule WHERE f_id=:id AND  DATE(sch_date)<= :nn';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->bindParam(':nn', $now);
    $stmt->execute();
    $row = $stmt->fetch();

    $tot_due = $row['int_tot'] - $row['int_paid'];
    return $tot_due;
  }

  public function getWhtPaid($cid)
  {

    $sqlQuery = 'SELECT  SUM(wht_paid) AS int_paid FROM fixed_deposit_schedule WHERE f_id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();

    $tot_due =  $row['int_paid'];
    return $tot_due;
  }

  public function getInterestPaid($cid)
  {

    $sqlQuery = 'SELECT  SUM(interest_paid) AS int_paid FROM fixed_deposit_schedule WHERE f_id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();

    $tot_due =  $row['int_paid'];
    return $tot_due;
  }

  public function getFDSchedule($cid)
  {

    $sqlQuery = 'SELECT  * FROM fixed_deposit_schedule WHERE f_id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();

    return $row;
  }

  public function getLoanStaffDetails($cid)
  {

    $sqlQuery = 'SELECT * FROM public."Staff" 
    LEFT JOIN public."User" ON public."Staff"."userId"=public."User".id
 WHERE public."Staff"."userId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row;
  }
  public function getBranchName($id)
  {
    $sqlQuery = 'SELECT name FROM public."Branch" WHERE  id=:id';

    $stmt = $this->conn->prepare($sqlQuery);


    $stmt->bindParam(':id', $id);


    $stmt->execute();
    $row = $stmt->fetch();
    return $row['name'];
  }
  public function getSingleLoanDetails($cid)
  {

    $sqlQuery = 'SELECT *, (SELECT SUM(amount) FROM public."transactions" WHERE public."transactions".t_type NOT IN(\'L\',\'WLI\',\'WLP\',\'A\') AND public."transactions".loan_id= public."loan".loan_no) AS deductions, (SELECT pay_method FROM public."transactions" WHERE public."transactions".t_type IN(\'A\') AND public."transactions".loan_id= public."loan".loan_no ORDER BY public."transactions".tid ASC LIMIT 1 ) AS meth FROM public."loan" 
 WHERE public."loan".loan_no=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row;
  }



  public function getLoanPenaltyPaid($cid)
  {

    $sqlQuery = 'SELECT SUM(transaction.loan_penalty) AS tot FROM public.transactions AS transaction 
      WHERE transaction.loan_id=:id AND  transaction.t_type IN (\'L\') ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row['tot'] ?? 0;
  }
  public function getProductDetails($cid)
  {

    $sqlQuery = 'SELECT * FROM public."loanproducttofee" 
    LEFT JOIN public."loantypes" ON public."loanproducttofee".lp_id=public."loantypes".type_id
    LEFT JOIN public."Fee" ON public."loanproducttofee".fee_id=public."Fee".id
 WHERE public."loanproducttofee".lp_id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row;
  }

  public function getAllLoanFees($cid)
  {

    $sqlQuery = 'SELECT * FROM public."loanproducttofee" 
    LEFT JOIN public."loantypes" ON public."loanproducttofee".lp_id=public."loantypes".type_id
    LEFT JOIN public."Fee" ON public."loanproducttofee".fee_id=public."Fee".id
 WHERE public."loanproducttofee".lp_id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    return $stmt;
  }

  public function getLoanCollaterals($cid)
  {

    $sqlQuery = 'SELECT * FROM public."collaterals" 
    LEFT JOIN public."collateral_categories" ON public."collaterals".cat=public."collateral_categories"._catid
    LEFT JOIN public."User" ON public."collaterals"._receivedby=public."User".id
 WHERE public."collaterals".loanid=:id  AND public."collaterals" .deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }


    // return '';
  }

  public function getLoanIncomeSources($cid)
  {

    $sqlQuery = 'SELECT * FROM public."loan_income_sources" 
 WHERE public."loan_income_sources".inc_lid=:id  AND public."loan_income_sources" .deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }


    // return '';
  }


  public function getBankCollateralsDatatables()
  {
    $dataTableSearchHelper = new DatatableSearchHelper();
    $search_string_array = [];
    if ($this->filter_search_string) {
      $search_string_array = StringToArray($this->filter_search_string);
    }

    $binding_array = [];

    if (@$this->branchId) {
      $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
      $brunchStmt = $this->conn->prepare($brunchQuery);
      $brunchStmt->bindParam(':branch_id_1', $this->branchId);
      $brunchStmt->execute();
      $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
      $this->bankId = $result['bankId'];
    }

    $sqlQuery = 'SELECT *, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \',public."User".shared_name,\' ( \',public."loan".principal,\' )\') FROM public."loan" LEFT JOIN public."User" ON public."loan".account_id=public."User".id WHERE public."loan".loan_no=public."collaterals".loanid) AS loan_coll FROM public."collaterals" 
    LEFT JOIN public."collateral_categories" ON public."collaterals".cat=public."collateral_categories"._catid
    LEFT JOIN public."User" ON public."collaterals"._receivedby=public."User".id ';

    $sqlQuery .= ' WHERE public."collaterals".deleted=0 AND  public."collateral_categories".bankid=:bank_id ';


    if ($this->filter_received_start_date && $this->filter_received_end_date) {
      $sqlQuery .= ' AND DATE(public."collaterals"._date_created) >= :filter_received_start_date AND DATE(public."collaterals"._date_created) <= :filter_received_end_date ';
      $binding_array[':filter_received_start_date'] = $this->filter_received_start_date;
      $binding_array[':filter_received_end_date'] = $this->filter_received_end_date;
    }

    if (@$this->filter_collateral_type_id) {
      $sqlQuery .= ' AND public."collaterals".cat = :filter_collateral_type_id ';
      $binding_array[':filter_collateral_type_id'] = $this->filter_collateral_type_id;
    }

    $dataTableSearchHelper->search_string = $this->filter_search_string;
    $clientSearch = $dataTableSearchHelper->search_collateral();
    $sqlQuery .= $clientSearch['query'];
    $binding_array = array_merge($binding_array, $clientSearch['binding_array']);

    if ($this->filter_search_string) {
      // $sqlQuery .= ' AND (
      //     (public."collateral_categories".bankid=:bank_id AND public."collaterals"."_collateral" ILIKE ANY(ARRAY[:search_string_array]) )
      //   )';
      // $binding_array[':search_string_array'] = implode("','", $search_string_array);
    }

    $binding_array[':bank_id'] = $this->bankId;

    $sqlQuery .= ' LIMIT :limit OFFSET :offset ';
    $binding_array[':limit'] = $this->filter_per_page;
    $binding_array[':offset'] = $this->filter_page;

    $collaterals = $this->conn->prepare($sqlQuery);
    $collaterals->execute($binding_array);
    return $collaterals->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getBankCollaterals($cid)
  {

    $sqlQuery = 'SELECT * FROM public."collaterals" 
    LEFT JOIN public."collateral_categories" ON public."collaterals".cat=public."collateral_categories"._catid
    LEFT JOIN public."User" ON public."collaterals"._receivedby=public."User".id
    WHERE public."collateral_categories".bankid=:id  AND public."collaterals".deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }


    // return '';
  }


  public function getBranchCollaterals($cid)
  {

    $sqlQuery = 'SELECT * FROM public."Branch" 
    WHERE public."Branch".id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();

    $sqlQuery = 'SELECT * FROM public."collaterals" 
    LEFT JOIN public."collateral_categories" ON public."collaterals".cat=public."collateral_categories"._catid
    LEFT JOIN public."User" ON public."collaterals"._receivedby=public."User".id
 WHERE public."collateral_categories".bankid=:id  AND public."collaterals".deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $row['bankId']);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }


    // return '';
  }

  public function getBankCollateralCategories($cid)
  {

    $sqlQuery = 'SELECT * FROM public."collateral_categories" 
 WHERE public."collateral_categories".bankid=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }


    // return '';
  }

  public function getBranchCollateralCategories($cid)
  {

    $sqlQuery = 'SELECT * FROM public."Branch" 
    WHERE public."Branch".id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $row = $stmt->fetch();

    $sqlQueryn = 'SELECT * FROM public."collateral_categories" 
 WHERE public."collateral_categories".bankid=:id ';

    $stmtn = $this->conn->prepare($sqlQueryn);

    $stmtn->bindParam(':id', $row['bankId']);
    $stmtn->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmtn;
    // }


    // return '';
  }

  public function getLoanGuarantors($cid)
  {

    $sqlQuery = 'SELECT *, TRIM(CONCAT(public."User"."firstName" , \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone" , \' \', public."User"."secondaryCellPhone", \' \')) AS guarantor_initials FROM public."guarantors" 
    LEFT JOIN public."User" ON public."guarantors"._mid=public."User".id
    LEFT JOIN public."Client" ON public."guarantors"._mid=public."Client"."userId"
 WHERE public."guarantors"._loanid=:id AND public."guarantors".deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }


    // return '';
  }

  public function getLoanGuarantorsInitials($cid)
  {

    $sqlQuery = 'SELECT public."guarantors".gid, TRIM(CONCAT(public."User"."firstName" , \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone" , \' \', public."User"."secondaryCellPhone", \' \')) AS guarantor_initials FROM public."guarantors" 
    LEFT JOIN public."User" ON public."guarantors"._mid=public."User".id
    LEFT JOIN public."Client" ON public."guarantors"._mid=public."Client"."userId"
 WHERE public."guarantors".deleted=0 AND public."guarantors"._loanid=:id GROUP BY public."guarantors".gid, public."User"."firstName","User"."lastName", public."User"."primaryCellPhone", public."User"."secondaryCellPhone" ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }


    // return '';
  }

  public function getLoanGuarantorsText($loan_id)
  {
    $guarantors = $this->getLoanGuarantorsInitials($loan_id);
    return $guarantors = $guarantors->fetchAll(PDO::FETCH_ASSOC);
    // $guarantors_string = '';
    // $guarantors_count = count($guarantors);
    // for ($i = 0; $i < $guarantors_count; $i++) {
    //   $guarantors_string .= $guarantors[$i]['guarantor_initials'];
    //   if ($i < $guarantors_count - 1) $guarantors_string .= ",";
    // }
    // return $guarantors_string;
  }

  public function getCategoryCollaterals($cid)
  {

    $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."collaterals" 
 WHERE public."collaterals".cat=:id AND public."collaterals".deleted=0';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    $row = $stmt->fetch();
    return $row['tot'];
    // }


    // return '';
  }

  public function getLoanBusinessDetails($cid)
  {
    $sqlQuery = 'SELECT * FROM public."Client" 
 WHERE public."Client"."userId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $cid);
    $stmt->execute();

    $row = $stmt->fetch();

    $sqlQuery = 'SELECT * FROM public."Business" 
 WHERE public."Business"."clientId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $row['id']);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      $row = $stmt->fetch();
      return $row;
    }


    return '';
  }

  public function getLoanSchedule($cid)
  {

    $sqlQuery = 'SELECT * FROM public."loan_schedule" 
 WHERE public."loan_schedule".loan_id=:id AND status<>:st ORDER BY date_of_payment ASC';

    $stmt = $this->conn->prepare($sqlQuery);
    $st = 'rescheduled';
    $stmt->bindParam(':id', $cid);
    $stmt->bindParam(':st', $st);
    $stmt->execute();
    // if($stmt->rowCount()>0){
    // $row = $stmt->fetch();
    return $stmt;
    // }
  }

  public function getExportableClients($bank, $branch, $st, $end, $product, $type)
  {
    $binding_array = [];

    $sqlQuery = 'SELECT  

    public."Client"."userId" AS user_id,

   public."Client".membership_no AS ac_no,

  (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."Client"."userId") AS client_name,

  public."Client"."createdAt",

  public."Client".acc_balance AS saving_balance,

 (SELECT gender FROM public."User" WHERE public."User".id=public."Client"."userId") AS client_gender,

  (SELECT name FROM public."savingaccounts" WHERE public."savingaccounts".id=public."Client".actype) AS saving_product,

(SELECT CONCAT(public."User"."primaryCellPhone",\', \', public."User"."secondaryCellPhone",\', \', public."User"."otherCellPhone") FROM public."User" WHERE public."User".id=public."Client"."userId") AS contacts,

 (SELECT name FROM public."Branch" WHERE public."Branch".id=public."Client"."branchId") AS branch

  
        FROM public."Client" ';

    if (@$bank) {
      $sqlQuery .= ' WHERE public."Client"."branchId" IN(select id from "Branch" where "bankId"=:bid) ';
      $binding_array[':bid'] = @$bank;
    }

    if (@$branch) {
      $sqlQuery .= ' WHERE public."Client"."branchId":bid ';
      $binding_array[':bid'] = @$branch;
    }


    if (@$product) {
      $sqlQuery .= ' AND public."Client".actype = :filter_actype ';
      $binding_array[':filter_actype'] = @$product;
    }

    if (@$type) {
      $sqlQuery .= ' AND public."Client".client_type = :filter_type ';
      $binding_array[':filter_type'] = @$type;
    }

    if (@$st && @$end) {

      $filter_start = @$st;
      $filter_end = @$end;

      $sqlQuery .= ' AND DATE(public."Client"."createdAt") >= :filter_transaction_start_date AND DATE(public."Client"."createdAt") <= :filter_transaction_end_date ';

      $binding_array[':filter_transaction_start_date'] = date('Y-m-d', strtotime($filter_start));
      $binding_array[':filter_transaction_end_date'] = date('Y-m-d', strtotime($filter_end));
    }

    $sqlQuery .= ' ORDER BY public."Client"."userId" DESC  ';


    $stmt = $this->conn->prepare($sqlQuery);
    // $stmt->bindParam(':bid', $bank);
    $stmt->execute($binding_array);

    // $row = $stmt->fetch();

    return $stmt;
  }


  public function getActiveLoansReportData($bank, $branch, $lpid, $officer, $st, $end)
  {
    $binding_array = [];
    if (@$bank) {

      $sqlQuery = 'SELECT *,

       ( SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName") FROM public."User" WHERE public."User".id=public."loan".loan_officer) AS credit_officer,

       (SELECT type_name FROM public."loantypes" WHERE public."loantypes".type_id=public."loan".loanproductid) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_no,

      (SELECT acc_balance FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_bal,

      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_name,

    (SELECT gender FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_gender,

    (SELECT COUNT(*) FROM public."collaterals" WHERE public."collaterals".loanid = public."loan".loan_no) AS has_collateral,

(SELECT (amount + loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_amount,

(SELECT date_created FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_date,

(SELECT SUM(amount) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS amount_paid_month,

(SELECT SUM(loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS int_paid_month,

(SELECT SUM(principal) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS amount_exp_month,

(SELECT SUM(interest) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS int_exp_month


       
       
        FROM public."loan" 
      
 WHERE public."loan".branchid IN(select id from "Branch" where "bankId"=:bid) AND status IN(2,3,4) ';
      $binding_array[':bid'] = @$bank;
      if (@$officer) {
        $sqlQuery .= ' AND public."loan".loan_officer = :filter_transaction_type ';
        $binding_array[':filter_transaction_type'] = @$officer;
      }

      if (@$lpid) {
        $sqlQuery .= ' AND public."loan".loanproductid = :filter_transaction_typen ';
        $binding_array[':filter_transaction_typen'] = @$lpid;
      }

      if (@$st && @$end) {

        $filter_start = @$st ?? date('m-01-Y');
        $filter_end = @$end ?? date('m-t-Y');

        $binding_array[':filter_transaction_start_date'] = date('Y-m-d', strtotime($filter_start));
        $binding_array[':filter_transaction_end_date'] = date('Y-m-d', strtotime($filter_end));
      }
      $sqlQuery .= ' ORDER BY public."loan".loan_no DESC LIMIT 500 ';

      $stmt = $this->conn->prepare($sqlQuery);
      // $stmt->bindParam(':bid', $bank);
      $stmt->execute($binding_array);

      // $row = $stmt->fetch();

      return $stmt;
    } else {
      $sqlQuery = 'SELECT *,
      
       ( SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName") FROM public."User" WHERE public."User".id=public."loan".loan_officer) AS credit_officer,

       (SELECT type_name FROM public."loantypes" WHERE public."loantypes".type_id=public."loan".loanproductid) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_no,

      (SELECT acc_balance FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_bal,

      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_name,

    (SELECT gender FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_gender,

    (SELECT COUNT(*) FROM public."collaterals" WHERE public."collaterals".loanid = public."loan".loan_no) AS has_collateral,

(SELECT (amount + loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_amount,

(SELECT date_created FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_date,

(SELECT SUM(amount) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS amount_paid_month,

(SELECT SUM(loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS int_paid_month,

(SELECT SUM(principal) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS amount_exp_month,

(SELECT SUM(interest) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS int_exp_month

       FROM public."loan" 
 WHERE public."loan".branchid=:bid AND status IN(2,3,4) ';
      $binding_array[':bid'] = @$branch;
      if (@$officer) {
        $sqlQuery .= ' AND public."loan".loan_officer = :filter_transaction_type ';
        $binding_array[':filter_transaction_type'] = @$officer;
      }

      if (@$st && @$end) {

        $filter_start = @$st ?? date('m-01-Y');
        $filter_end = @$end ?? date('m-t-Y');

        $binding_array[':filter_transaction_start_date'] = date('Y-m-d', strtotime($filter_start));
        $binding_array[':filter_transaction_end_date'] = date('Y-m-d', strtotime($filter_end));
      }

      if (@$lpid) {
        $sqlQuery .= ' AND public."loan".loanproductid = :filter_transaction_typen ';
        $binding_array[':filter_transaction_typen'] = @$lpid;
      }
      $sqlQuery .= ' ORDER BY public."loan".loan_no DESC LIMIT 500 ';

      $stmt = $this->conn->prepare($sqlQuery);

      // $stmt->bindParam(':bid', $branch);
      $stmt->execute($binding_array);
      // $row = $stmt->fetch();
      return $stmt;
    }
  }

  public function getLoanDisbursements($bank, $branch, $lpid, $officer, $st, $end)
  {
    $binding_array = [];
    if (@$bank) {

      $sqlQuery = 'SELECT *,

       ( SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName") FROM public."User" WHERE public."User".id=public."transactions"._authorizedby) AS credit_officer,

       (SELECT type_name FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid = public."loantypes".type_id WHERE public."loan".loan_no=public."transactions".loan_id) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."transactions".mid) AS ac_no,


      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."transactions".mid) AS client_name



        FROM public."transactions"  LEFT JOIN public."loan" ON public."transactions".loan_id=public."loan".loan_no
      
 WHERE public."transactions"._branch IN(select id from "Branch" where "bankId"=:bid) AND t_type=\'A\' ';
      $binding_array[':bid'] = @$bank;
      if (@$officer) {
        $sqlQuery .= ' AND public."transactions"._authorizedby = :filter_transaction_type ';
        $binding_array[':filter_transaction_type'] = @$officer;
      }

      if (@$lpid) {
        $sqlQuery .= ' AND public."loan".loanproductid = :filter_transaction_typen ';
        $binding_array[':filter_transaction_typen'] = @$lpid;
      }

      if (
        @$st && @$end
      ) {

        $filter_start = @$st ?? date('m-01-Y');
        $filter_end = @$end ?? date('m-t-Y');

        $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date  ';

        $binding_array[':filter_transaction_start_date'] = date('Y-m-d', strtotime($filter_start));
        $binding_array[':filter_transaction_end_date'] = date('Y-m-d', strtotime($filter_end));
      }

      $stmt = $this->conn->prepare($sqlQuery);
      // $stmt->bindParam(':bid', $bank);
      $stmt->execute($binding_array);

      // $row = $stmt->fetch();

      return $stmt;
    } else {
      $sqlQuery = 'SELECT *,
      
       ( SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName") FROM public."User" WHERE public."User".id=public."transactions"._authorizedby) AS credit_officer,

       (SELECT type_name FROM public."loan" LEFT JOIN public."loantypes" ON public."loan".loanproductid = public."loantypes".type_id WHERE public."loan".loan_no=public."transactions".loan_id) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."transactions".mid) AS ac_no,


      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."transactions".mid) AS client_name


    FROM public."transactions"  LEFT JOIN public."loan" ON public."transactions".loan_id=public."loan".loan_no
       
 WHERE public."transactions"._branch=:bid AND t_type=\'A\' ';
      $binding_array[':bid'] = @$branch;
      if (@$officer) {
        $sqlQuery .= ' AND public."transactions"._authorizedby = :filter_transaction_type ';
        $binding_array[':filter_transaction_type'] = @$officer;
      }

      if (
        @$st && @$end
      ) {
        $filter_start = @$st ?? date('m-01-Y');
        $filter_end = @$end ?? date('m-t-Y');

        $sqlQuery .= ' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date  ';

        $binding_array[':filter_transaction_start_date'] = date('Y-m-d', strtotime($filter_start));
        $binding_array[':filter_transaction_end_date'] = date('Y-m-d', strtotime($filter_end));
      }

      if (@$lpid) {
        $sqlQuery .= ' AND public."loan".loanproductid = :filter_transaction_typen ';
        $binding_array[':filter_transaction_typen'] = @$lpid;
      }

      $stmt = $this->conn->prepare($sqlQuery);

      // $stmt->bindParam(':bid', $branch);
      $stmt->execute($binding_array);
      // $row = $stmt->fetch();
      return $stmt;
    }
  }



  public function getActiveLoansReportData2($bank, $branch, $lpid, $officer, $st, $end)
  {
    $binding_array = [];
    if (@$bank && @$branch == '') {

      $sqlQuery = 'SELECT *,

       ( SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName") FROM public."User" WHERE public."User".id=public."loan".loan_officer) AS credit_officer,

       (SELECT type_name FROM public."loantypes" WHERE public."loantypes".type_id=public."loan".loanproductid) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_no,

      (SELECT acc_balance FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_bal,

      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_name,

    (SELECT gender FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_gender,

    (SELECT COUNT(*) FROM public."collaterals" WHERE public."collaterals".loanid = public."loan".loan_no) AS has_collateral,

(SELECT (amount + loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_amount,

(SELECT date_created FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_date,

(SELECT SUM(amount) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS amount_paid_month,

(SELECT SUM(loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS int_paid_month,

(SELECT SUM(principal) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS amount_exp_month,

(SELECT SUM(interest) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS int_exp_month


       
       
        FROM public."loan" 
      
 WHERE public."loan".branchid IN(select id from "Branch" where "bankId"=:bid) AND status IN(2,3,4)  AND (principal_arrears>0 OR interest_arrears>0) ';
      $binding_array[':bid'] = @$bank;
      if (@$officer) {
        $sqlQuery .= ' AND public."loan".loan_officer = :filter_transaction_type ';
        $binding_array[':filter_transaction_type'] = @$officer;
      }

      if (@$lpid) {
        $sqlQuery .= ' AND public."loan".loanproductid = :filter_transaction_typen ';
        $binding_array[':filter_transaction_typen'] = @$lpid;
      }

      if (
        @$st && @$end
      ) {

        $filter_start = @$st ?? date('m-01-Y');
        $filter_end = @$end ?? date('m-t-Y');

        $binding_array[':filter_transaction_start_date'] = date('Y-m-d', strtotime($filter_start));
        $binding_array[':filter_transaction_end_date'] = date('Y-m-d', strtotime($filter_end));
      }
      $sqlQuery .= ' ORDER BY public."loan".loan_no DESC LIMIT 500 ';

      $stmt = $this->conn->prepare($sqlQuery);
      // $stmt->bindParam(':bid', $bank);
      $stmt->execute($binding_array);

      // $row = $stmt->fetch();

      return $stmt;
    } else {
      $sqlQuery = 'SELECT *,
      
       ( SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName") FROM public."User" WHERE public."User".id=public."loan".loan_officer) AS credit_officer,

       (SELECT type_name FROM public."loantypes" WHERE public."loantypes".type_id=public."loan".loanproductid) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_no,

      (SELECT acc_balance FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_bal,

      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_name,

    (SELECT gender FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_gender,

    (SELECT COUNT(*) FROM public."collaterals" WHERE public."collaterals".loanid = public."loan".loan_no) AS has_collateral,

(SELECT (amount + loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_amount,

(SELECT date_created FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' ORDER BY public."transactions".tid DESC LIMIT 1) AS last_trxn_date,

(SELECT SUM(amount) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS amount_paid_month,

(SELECT SUM(loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :filter_transaction_start_date AND DATE(public."transactions".date_created) <= :filter_transaction_end_date) AS int_paid_month,

(SELECT SUM(principal) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS amount_exp_month,

(SELECT SUM(interest) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no AND DATE(public."loan_schedule".date_of_payment) >= :filter_transaction_start_date AND DATE(public."loan_schedule".date_of_payment) <= :filter_transaction_end_date) AS int_exp_month

       FROM public."loan" 
 WHERE public."loan".branchid=:bid AND status IN(2,3,4)  AND (principal_arrears>0 OR interest_arrears>0) ';
      $binding_array[':bid'] = @$branch;
      if (@$officer) {
        $sqlQuery .= ' AND public."loan".loan_officer = :filter_transaction_type ';
        $binding_array[':filter_transaction_type'] = @$officer;
      }

      if (
        @$st && @$end
      ) {

        $filter_start = @$st ?? date('m-01-Y');
        $filter_end = @$end ?? date('m-t-Y');

        $binding_array[':filter_transaction_start_date'] = date('Y-m-d', strtotime($filter_start));
        $binding_array[':filter_transaction_end_date'] = date('Y-m-d', strtotime($filter_end));
      }

      if (@$lpid) {
        $sqlQuery .= ' AND public."loan".loanproductid = :filter_transaction_typen ';
        $binding_array[':filter_transaction_typen'] = @$lpid;
      }
      $sqlQuery .= ' ORDER BY public."loan".loan_no DESC LIMIT 500 ';

      $stmt = $this->conn->prepare($sqlQuery);

      // $stmt->bindParam(':bid', $branch);
      $stmt->execute($binding_array);
      // $row = $stmt->fetch();
      return $stmt;
    }
  }



  public function getAllBankFees()
  {
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Fee" 
 WHERE public."Fee"."bankId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getBranchTransfers()
  {
    $tt = 'D';
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" 
 WHERE public."transactions".is_transfer=1 AND public."transactions".t_type=:tt AND public."transactions"._branch=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':tt', $tt);
    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getBranchFreezedAccounts()
  {

    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Client" 
 WHERE public."Client".freezed_amount>0 AND  public."Client"."branchId"=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }
  public function getBranchStaffShortfalls()
  {

    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."staff_shortfalls" 
 WHERE  public."staff_shortfalls"._branch=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getBranchStaffExcess()
  {

    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."staff_excess" 
 WHERE  public."staff_excess"._branch=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getBankFreezedAccounts()
  {

    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Client"  LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id
 WHERE public."Client".freezed_amount>0 AND  public."Branch"."bankId"=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getBankStaffShortfalls()
  {

    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."staff_shortfalls"  LEFT JOIN public."Branch" ON public."staff_shortfalls"._branch=public."Branch".id
 WHERE  public."Branch"."bankId"=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }
  public function subscribeSchoolPay()
  {
    $sqlQuery = 'UPDATE  public."Client"  SET school_pay=1 WHERE public."Client"."userId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();

    return true;
  }
  public function getBankClientsFees($term)
  {
    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
       LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype 
       WHERE public."Branch"."bankId"=:bid AND public."Client".client_type=\'institution\' AND public."User".status<>:stt AND 
       
  replace(lower(CONCAT(
          COALESCE(public."User"."firstName",\'\'),
					COALESCE(public."User"."lastName",\'\'),
					COALESCE(public."User".shared_name,\'\'),
					COALESCE(public."Client".membership_no,\'\'),
					COALESCE(public."Client".old_membership_no,\'\'),
					COALESCE(public."User"."primaryCellPhone",\'\'),
					COALESCE(public."User"."secondaryCellPhone",\'\'))),\' \',\'\') LIKE :lik
       
       
       ORDER BY public."Client"."createdAt" ASC';
    $stmt = $this->conn->prepare($sqlQuery);

    $likk = '%' . strtolower($term) . '%';
    $stt = 'INACTIVE';

    $cs = '';

    $stmt->bindParam(':bid', $this->createdById);
    $stmt->bindParam(':lik', $likk);
    // $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    // $stmt->bindParam(':bid', $bidd);

    $stmt->execute();
    return $stmt;
  }

  public function getBranchClientsInst($term)
  {
    $sqlQueryn = 'SELECT * FROM public."Branch" WHERE id=:id ';
    $stmtn = $this->conn->prepare($sqlQueryn);
    $stmtn->bindParam(':id', $this->branchId);

    $stmtn->execute();
    $row = $stmtn->fetch();
    $likk = '%' . strtolower($term) . '%';
    $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
      LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype 
       WHERE public."Branch"."bankId"=:bid AND public."Client".client_type=\'institution\' AND public."User".status<>:stt AND
       

        replace(lower(CONCAT(
          COALESCE(public."User"."firstName",\'\'),
					COALESCE(public."User"."lastName",\'\'),
					COALESCE(public."User".shared_name,\'\'),
					COALESCE(public."Client".membership_no,\'\'),
					COALESCE(public."Client".old_membership_no,\'\'),
					COALESCE(public."User"."primaryCellPhone",\'\'),
					COALESCE(public."User"."secondaryCellPhone",\'\'))),\' \',\'\') LIKE :lik
        
        
        ORDER BY public."Client"."createdAt" ASC';
    $stmt = $this->conn->prepare($sqlQuery);
    $cs = '';
    $stt = 'INACTIVE';
    $stmt->bindParam(':bid', $row['bankId']);
    $stmt->bindParam(':lik', $likk);
    // $stmt->bindParam(':cs', $cs);
    $stmt->bindParam(':stt', $stt);

    $stmt->execute();
    return $stmt;
  }
  public function unsubscribeSchoolPay()
  {
    $sqlQuery = 'UPDATE  public."Client"  SET school_pay=0 WHERE public."Client"."userId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->branchId);
    $stmt->execute();

    return true;
  }
  public function getBranchInstitutions()
  {

    $sqlQuery = 'SELECT  *, public."User".id AS uid FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id WHERE public."Client".client_type=\'institution\' AND public."Client"."branchId"=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->branchId);
    $transactions->execute();
    return $transactions;
  }

  public function getBranchMMLogs()
  {

    $sqlQuery = 'SELECT  * FROM public.mm_logs WHERE log_branch_id=:id ORDER BY public.mm_logs.log_id DESC';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->branchId);
    $transactions->execute();
    return $transactions;
  }
  public function getBankMMLogs()
  {

    $sqlQuery = 'SELECT  * FROM public.mm_logs LEFT JOIN public."Branch" ON public.mm_logs.log_branch_id = public."Branch".id WHERE public."Branch"."bankId"=:id ORDER BY public.mm_logs.log_id DESC';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->createdById);
    $transactions->execute();
    return $transactions;
  }
  public function getTotalFeesCollected($id)
  {

    $sqlQuery = 'SELECT  SUM(amount) AS tot FROM public."transactions"  WHERE public."transactions".mid=:id AND  public."transactions".trxn_rec=\'fees\'';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $id);
    $transactions->execute();
    $row = $transactions->fetch();
    return $row['tot'] ?? 0;
  }
  public function getAllSchoolPaySchools()
  {
    $sqlQuery = 'SELECT  *, public."User".id AS uid FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id LEFT JOIN public."Branch" ON public."Branch".id= public."Client"."branchId" WHERE public."Client".client_type=\'institution\' AND public."Client".school_pay=1';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->execute();
    return $transactions;
  }

  public function updateStatusMmLog($tid, $st)
  {
    $sqlQuery = 'UPDATE mm_logs SET  log_status=:st  WHERE log_ext_ref_no=:tid';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':tid', $tid);
    $transactions->bindParam(':st', $st);
    $transactions->execute();


    $sqlQuery = 'SELECT * FROM  mm_logs  WHERE log_ext_ref_no=:tid';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':tid', $tid);
    $transactions->execute();
    $row = $transactions->fetch();

    $sqlQuery = 'SELECT * FROM  public."User"  WHERE id=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $row['log_uid']);
    $transactions->execute();
    $rown = $transactions->fetch();

    // 1 - 01 - 20 - 05
    $acode = '1-01-20-06';
    $sqlQuery = 'SELECT id FROM  public."Account"  WHERE account_code_used=:id AND "branchId"=:bid ORDER BY "createdAt" ASC LIMIT 1';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $acode);
    $transactions->bindParam(':bid', $row['log_branch_id']);
    $transactions->execute();
    $rowx = $transactions->fetch();

    if ($rowx) {
      $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE id=:id';

      $transactions = $this->conn->prepare($sqlQuery);
      $transactions->bindParam(':id', $rowx['id']);
      $transactions->bindParam(':bal', $row['log_trxn_amount']);
      $transactions->execute();
    }


    $acc_name = $rown['firstName'] . ' ' . $rown['lastName'] . ' ' . $rown['shared_name'];
    $t_type = 'D';
    $des = 'MM Deposit: ' . $row['log_description'];
    $auth = 0;
    $aid = $rowx['id'] ?? '';
    $meth = 'mobile_money';
    $leftbal = 0;
    $charges = 0;

    $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,cr_acid,pay_method,bacid,cheque_no,date_created,charges,mm_tid) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:date_created,:charges,:mm_tid)';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':amount', $row['log_trxn_amount']);
    $stmt->bindParam(':descri', $des);
    $stmt->bindParam(':autho', $auth);
    $stmt->bindParam(':actby', $row['log_acc_name']);
    $stmt->bindParam(':accname', $acc_name);
    $stmt->bindParam(':mid', $row['log_uid']);
    $stmt->bindParam(':approv', $auth);
    $stmt->bindParam(':branc', $row['log_branch_id']);
    $stmt->bindParam(':leftbal', $leftbal);
    $stmt->bindParam(':ttype', $t_type);
    $stmt->bindParam(':acid', $aid);
    $stmt->bindParam(':pay_method', $meth);
    $stmt->bindParam(':bacid', $aid);
    $stmt->bindParam(':cheque', $row['log_ext_ref_no']);
    $stmt->bindParam(':date_created', $row['log_date_created']);
    $stmt->bindParam(':charges', $charges);
    $stmt->bindParam(':mm_tid', $tid);



    $stmt->execute();

    $sqlQuery = 'UPDATE  public."Client" SET acc_balance=acc_balance+:bal  WHERE "userId"=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $row['log_uid']);
    $transactions->bindParam(':bal', $row['log_trxn_amount']);
    $transactions->execute();

    return true;
  }
  public function updateStatusMmFeesLog($tid, $st)
  {
    $sqlQuery = 'UPDATE mm_logs SET  log_status=:st  WHERE log_ext_ref_no=:tid';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':tid', $tid);
    $transactions->bindParam(':st', $st);
    $transactions->execute();


    $sqlQuery = 'SELECT * FROM  mm_logs  WHERE log_ext_ref_no=:tid';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':tid', $tid);
    $transactions->execute();
    $row = $transactions->fetch();

    $sqlQuery = 'SELECT * FROM  public."User"  WHERE id=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $row['log_uid']);
    $transactions->execute();
    $rown = $transactions->fetch();

    // 1 - 01 - 20 - 05
    $acode = '1-01-20-06';
    $sqlQuery = 'SELECT id FROM  public."Account"  WHERE account_code_used=:id AND "branchId"=:bid ORDER BY "createdAt" ASC LIMIT 1';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $acode);
    $transactions->bindParam(':bid', $row['log_branch_id']);
    $transactions->execute();
    $rowx = $transactions->fetch();

    if ($rowx) {
      $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE id=:id';

      $transactions = $this->conn->prepare($sqlQuery);
      $transactions->bindParam(':id', $rowx['id']);
      $transactions->bindParam(':bal', $row['log_trxn_amount']);
      $transactions->execute();
    }

    return true;
  }



  public function updateStatusMmLogWithdraw($tid, $st)
  {
    $sqlQuery = 'UPDATE mm_logs SET  log_status=:st  WHERE log_ext_ref_no=:tid';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':tid', $tid);
    $transactions->bindParam(':st', $st);
    $transactions->execute();


    $sqlQuery = 'SELECT * FROM  mm_logs  WHERE log_ext_ref_no=:tid';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':tid', $tid);
    $transactions->execute();
    $row = $transactions->fetch();

    $sqlQuery = 'SELECT * FROM  public."User"  WHERE id=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $row['log_uid']);
    $transactions->execute();
    $rown = $transactions->fetch();

    // 1 - 01 - 20 - 05
    $acode = '1-01-20-06';
    $sqlQuery = 'SELECT id FROM  public."Account"  WHERE account_code_used=:id AND "branchId"=:bid ORDER BY "createdAt" ASC LIMIT 1';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $acode);
    $transactions->bindParam(':bid', $row['log_branch_id']);
    $transactions->execute();
    $rowx = $transactions->fetch();

    if ($rowx) {
      $sqlQuery = 'UPDATE  public."Account" SET balance=balance-:bal  WHERE id=:id';

      $transactions = $this->conn->prepare($sqlQuery);
      $transactions->bindParam(':id', $rowx['id']);
      $transactions->bindParam(':bal', $row['log_trxn_amount']);
      $transactions->execute();
    }




    $acc_name = $rown['firstName'] . ' ' . $rown['lastName'] . ' ' . $rown['shared_name'];
    $t_type = 'W';
    $des = 'MM Withdraw: ' . $row['log_description'];
    $auth = 10170;
    $aid = $rowx['id'] ?? '';
    $meth = 'mobile_money';
    $leftbal = 0;
    $charges = 0;

    $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,cr_acid,pay_method,bacid,cheque_no,date_created,charges,mm_tid) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:date_created,:charges,:mm_tid)';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':amount', $row['log_trxn_amount']);
    $stmt->bindParam(':descri', $des);
    $stmt->bindParam(':autho', $auth);
    $stmt->bindParam(':actby', $row['log_acc_name']);
    $stmt->bindParam(':accname', $acc_name);
    $stmt->bindParam(':mid', $row['log_uid']);
    $stmt->bindParam(':approv', $auth);
    $stmt->bindParam(':branc', $row['log_branch_id']);
    $stmt->bindParam(':leftbal', $leftbal);
    $stmt->bindParam(':ttype', $t_type);
    $stmt->bindParam(':acid', $aid);
    $stmt->bindParam(':pay_method', $meth);
    $stmt->bindParam(':bacid', $aid);
    $stmt->bindParam(':cheque', $row['log_ext_ref_no']);
    $stmt->bindParam(':date_created', $row['log_date_created']);
    $stmt->bindParam(':charges', $charges);
    $stmt->bindParam(':mm_tid', $tid);


    $stmt->execute();


    $sqlQuery = 'UPDATE  public."Client" SET acc_balance=acc_balance-:bal  WHERE "userId"=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $row['log_uid']);
    $transactions->bindParam(':bal', $row['log_trxn_amount']);
    $transactions->execute();

    return true;
  }


  public function InsertMmTrxnLog($acname, $acno, $uid, $eref, $des, $st, $amount, $mess, $branch, $phone, $ext_ref, $log_t_type)
  {
    $sqlQuery = 'INSERT INTO mm_logs( log_acc_name, log_acc_no, log_uid, log_ext_ref_no, log_description,
					 log_status ,log_trxn_amount, log_message,log_branch_id,log_phone,log_set_ref,log_t_type) VALUES(:acname,:acno,:cuid,:eref,:descr,:st,:amount,:mess,:branch,:phone,:extr,:log_t_type)';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':acname', $acname);
    $transactions->bindParam(':log_t_type', $log_t_type);
    $transactions->bindParam(':extr', $ext_ref);
    $transactions->bindParam(':acno', $acno);
    $transactions->bindParam(':cuid', $uid);
    $transactions->bindParam(':phone', $phone);
    $transactions->bindParam(':eref', $eref);
    $transactions->bindParam(':descr', $des);
    $transactions->bindParam(':st', $st);
    $transactions->bindParam(':amount', $amount);
    $transactions->bindParam(':mess', $mess);
    $transactions->bindParam(':branch', $branch);
    $transactions->execute();
    return true;
  }

  public function InsertMmTrxnLogSMS($acname, $acno, $uid, $eref, $des, $st, $amount, $mess, $branch, $phone, $ext_ref, $bank, $tty)
  {

    $sqlQuery = 'SELECT id, name FROM  public."Account"  WHERE account_code_used=:id AND "branchId"=:bid ORDER BY "createdAt" ASC LIMIT 1';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $acname);
    $transactions->bindParam(':bid', $branch);
    $transactions->execute();
    $rowx = $transactions->fetch();

    $nn = $rowx['name'] ?? '';
    $noo = $rowx['id'] ?? '';


    $sqlQuery = 'INSERT INTO mm_logs( log_acc_name, log_acc_no, log_uid, log_ext_ref_no, log_description,
					 log_status ,log_trxn_amount, log_message,log_branch_id,log_phone,log_set_ref,log_bank_id,log_t_type) VALUES(:acname,:acno,:cuid,:eref,:descr,:st,:amount,:mess,:branch,:phone,:extr,:bank,:tty)';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':acname', $nn);
    $transactions->bindParam(':bank', $bank);
    $transactions->bindParam(':tty', $tty);
    $transactions->bindParam(':extr', $ext_ref);
    $transactions->bindParam(':acno', $noo);
    $transactions->bindParam(':cuid', $uid);
    $transactions->bindParam(':phone', $phone);
    $transactions->bindParam(':eref', $eref);
    $transactions->bindParam(':descr', $des);
    $transactions->bindParam(':st', $st);
    $transactions->bindParam(':amount', $amount);
    $transactions->bindParam(':mess', $mess);
    $transactions->bindParam(':branch', $branch);
    $transactions->execute();
    return true;
  }

  public function InsertMmTrxnLogUssd($phone, $counter, $sid, $eref, $des, $st, $amount, $mess, $ext_ref, $phone_used, $log_t_type)
  {

    $sqlQuery = 'SELECT * FROM public."Bank" WHERE mobile_wallet_code=:id ';
    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':id', $sid);
    $stmt->execute();
    $row = $stmt->fetch();

    $sqlQuery = 'SELECT  *, public."User".id AS uid, public."Client".id AS cid, public."savingaccounts".name AS sname, public."Branch".id AS bid FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id LEFT JOIN public."Branch" ON public."Branch".id = public."Client"."branchId" LEFT JOIN public."savingaccounts" ON public."savingaccounts".id = public."Client".actype WHERE (public."User"."primaryCellPhone" IN(:p1,:p2,:p3,:p4,:p5) OR public."User"."secondaryCellPhone" IN(:p1,:p2,:p3,:p4,:p5)) AND public."Branch"."bankId"=:biid ORDER BY public."User".id ASC';

    $p1 = '256' . $phone;
    $p2 = '+256' . $phone;
    $p3 = '256' . substr($phone, 1);
    $p4 = '+256' . substr($phone, 1);

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':p1', $p1);
    $transactions->bindParam(':p2', $p2);
    $transactions->bindParam(':p3', $p3);
    $transactions->bindParam(':p4', $p4);
    $transactions->bindParam(':p5', $phone);
    $transactions->bindParam(':biid', $row['id']);
    $transactions->execute();
    $rown = $transactions->fetch();

    // $acname = $rown[$counter - 1]['firstName'] . ' ' . $rown[$counter - 1]['lastName'] . ' ' . $rown[$counter - 1]['shared_name'];
    // $acno = $rown[$counter - 1]['membership_no'] ?? '';
    // $uid = $rown[$counter - 1]['uid'] ?? '';
    // $branch = $rown[$counter - 1]['bid'] ?? '';

    $acname = @$rown['firstName'] . ' ' . @$rown['lastName'] . ' ' . @$rown['shared_name'];
    $acno = $rown['membership_no'] ?? '';
    $uid = $rown['uid'] ?? '';
    $branch = $rown['bid'] ?? '';

    $sqlQuery = 'INSERT INTO mm_logs( log_acc_name, log_acc_no, log_uid, log_ext_ref_no, log_description,
					 log_status ,log_trxn_amount, log_message,log_branch_id,log_phone,log_set_ref,phone_used,log_t_type) VALUES(:acname,:acno,:cuid,:eref,:descr,:st,:amount,:mess,:branch,:phone,:log_set_ref,:phone_used,:log_t_type)';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':acname', $acname);
    $transactions->bindParam(':log_t_type', $log_t_type);
    $transactions->bindParam(':phone_used', $phone_used);
    $transactions->bindParam(':log_set_ref', $ext_ref);
    $transactions->bindParam(':acno', $acno);
    $transactions->bindParam(':cuid', $uid);
    $transactions->bindParam(':eref', $eref);
    $transactions->bindParam(':descr', $des);
    $transactions->bindParam(':st', $st);
    $transactions->bindParam(':amount', $amount);
    $transactions->bindParam(':mess', $mess);
    $transactions->bindParam(':branch', $branch);
    $transactions->bindParam(':phone', $phone_used);
    $transactions->execute();
    return true;
  }

  public function getAllSchoolPaySchoolsSpecificSACCO($bank_id)
  {
    $sqlQuery = 'SELECT  *, public."User".id AS uid FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id LEFT JOIN public."Branch" ON public."Branch".id= public."Client"."branchId" WHERE public."Client".client_type=\'institution\' AND public."Client".school_pay=1 AND public."Branch"."bankId"=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $bank_id);
    $transactions->execute();
    return $transactions;
  }
  public function getBankInstitutions()
  {

    $sqlQuery = 'SELECT  *, public."User".id AS uid FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id LEFT JOIN public."Branch" ON public."Branch".id= public."Client"."branchId" WHERE public."Client".client_type=\'institution\' AND public."Branch"."bankId"=:id';

    $transactions = $this->conn->prepare($sqlQuery);
    $transactions->bindParam(':id', $this->createdById);
    $transactions->execute();
    return $transactions;
  }
  public function getBankStaffExcess()
  {

    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."staff_excess"  LEFT JOIN public."Branch" ON public."staff_excess"._branch=public."Branch".id
 WHERE  public."Branch"."bankId"=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getBankTransfers()
  {
    $tt = 'D';
    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id
 WHERE public."transactions".is_transfer=1 AND public."transactions".t_type=:tt AND public."Branch"."bankId"=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':tt', $tt);
    $stmt->bindParam(':bid', $this->branchId);
    $stmt->execute();



    return $stmt;
  }

  public function getTypeLoansCount($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."loan" 
 WHERE public."loan".loanproductid=:id AND public."loan".status IN(2,3,4,5) ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();



    return $row['tot'];
  }

  public function hasFee($id)
  {
    // get client names,left balance
    $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."loanproducttofee" 
 WHERE public."loanproducttofee".lp_id=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch();



    return $row['tot'];
  }

  /**
   * process branch request
   */
  public function processBranchRequest()
  {
    // update balances of both accounts


    // update sender balance

    $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id AND balance>=:amount';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->data_array['cash_acc']);
    $stmt->bindParam(':amount', $this->data_array['amount']);

    if ($stmt->execute()) {
      // update receiver balance

      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->data_array['to_id']);
      $stmt->bindParam(':amount', $this->data_array['amount']);

      $stmt->execute();


      // update status to success
      $sqlQuery = 'UPDATE public."inter_branch_requests" SET req_status=1 WHERE req_id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->data_array['req_id']);

      $stmt->execute();

      // insert into trxns table 

      // t_type for each transfer
      /* 
        
        {
            STT - SAFE TO TELLER,
            TTS - TELLER TO SAFE,
            TTT - TELLER TO TELLER,
            BTS - BANK TO SAFE,
            STB - SAFE TO BANK,
            BTB - BANK TO BANK,
            IBR - INTER BRANCH TRANSFER
            BRTBR - INTER BRANCH TRANSFER
        }

        dr_acid = the sender
        cr_acid = the receiver
        */
      $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, cr_acid,dr_acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:drid)
            ';

      $stmt = $this->conn->prepare($sqlQuery);
      $mid = 0;
      $ttype = 'BRTBR';
      $today = date('Y-m-d');
      $stmt->bindParam(':amount', $this->data_array['amount']);
      $stmt->bindParam(':dc', $today);
      $stmt->bindParam(':ttype', $ttype);
      $stmt->bindParam(':descri', $this->data_array['description']);
      $stmt->bindParam(':_auth', $this->data_array['user']);
      $stmt->bindParam(':mid', $mid);
      $stmt->bindParam(':apby', $this->data_array['user']);
      $stmt->bindParam(':bran', $this->data_array['from_id']);
      $stmt->bindParam(':crid', $this->data_array['to_id']);
      $stmt->bindParam(':drid', $this->data_array['cash_acc']);

      $stmt->execute();



      // insert into audit trail

      $auditTrail = new AuditTrail($this->conn);
      $auditTrail->type = $this->data_array['description'];
      $auditTrail->staff_id = $this->data_array['user'];
      $auditTrail->bank_id = $this->data_array['bank'];
      $auditTrail->branch_id = $this->data_array['from_id'];

      $auditTrail->log_message = 'Inter-Branch Cash Transfer from: Cash A/C -' . $this->data_array['cash_acc'] . ' to ' . $this->data_array['to_id'];
      $auditTrail->create();


      return true;
    }



    return false;
  }


  /**
   * waive loan penalty
   */
  public function waivePenalty()
  {
    $loan_id = $this->data_array['loan_id'];
    $loan = $this->conn->fetch('loan', 'loan_no', $loan_id);
    if (!$loan) return "Loan not found";

    $client = $this->conn->fetch('Client', 'userId', $loan['account_id']);
    if (!$client) return "Member not found";

    $client_user = $this->conn->fetch('User', 'id', $client['userId']);
    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];

    $amount = $this->data_array['amount'] ?? 0;
    $penalty_balance = $loan['penalty_balance'];

    if ($amount == 0) return "Please enter amount";
    if ($penalty_balance <= 0) return "Loan has no penalty";

    $balance = $penalty_balance - $amount;
    if ($balance < 0) return "Amount is more than required";

    /**
     * update loan
     */
    $this->conn->update('loan', ['penalty_balance' => $balance, 'penalty_waivered' => $amount], 'loan_no', $loan_id);

    /**
     * create waiver transaction
     */
    $this->conn->insert('transactions', [
      'amount' => $amount,
      'description' => $this->data_array['description'] ?? 'Waive Loan penalty',
      '_authorizedby' => $this->data_array['auth_id'],
      '_actionby' => $this->data_array['auth_id'],
      'acc_name' => $client_names,
      'mid' => $client['userId'],
      'approvedby' => $this->data_array['auth_id'],
      '_branch' => $client['branchId'],
      't_type' => 'WLP',
      'loan_id' => $loan_id,
      'date_created' => $this->data_array['date_of_waiver'],
      'outstanding_amount' => $loan['principal_balance'],
      'outstanding_amount_total' => $loan['principal_balance'],
      'loan_interest' => $loan['interest_amount'],
      'outstanding_interest' => $loan['interest_balance'],
      'outstanding_interest_total' => $loan['interest_balance'],
      'pay_method' => 'cash',
      'loan_penalty' => $balance
    ]);

    return true;
  }

  public function writeOffLoan()
  {
    $loan_id = $this->data_array['loan_id'];

    $loan = $this->conn->fetch('loan', 'loan_no', $loan_id);
    if (!$loan) return "Loan not found";

    $client = $this->conn->fetch('Client', 'userId', $loan['account_id']);
    if (!$client) return "Member not found";

    $client_user = $this->conn->fetch('User', 'id', $client['userId']);
    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'] . '' . @$client_user['shared_name'];

    $interest_balance = $loan['interest_balance'];
    $principal_balance = $loan['principal_balance'];

    $total_write_off = $interest_balance + $principal_balance;

    if ($this->data_array['method'] == 'expense') {
      // register direct expense against the chart account selected
      $t_type = 'E';
      $p_pay_method = 'cash';
      $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee)';


      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':amount', $total_write_off);
      $stmt->bindParam(':descri', $this->data_array['comment']);
      $stmt->bindParam(':autho', $this->data_array['auth_id']);
      $stmt->bindParam(':actby', $this->data_array['auth_id']);
      $stmt->bindParam(':accname', $client_names);
      $stmt->bindParam(':approv', $this->data_array['auth_id']);
      $stmt->bindParam(':branc', $loan['branchid']);
      $stmt->bindParam(':ttype', $t_type);
      $stmt->bindParam(':acid', $this->data_array['debit_account']);
      $stmt->bindParam(':pay_method', $p_pay_method);
      $stmt->bindParam(':datee', $this->data_array['date']);

      $stmt->execute();

      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->data_array['debit_account']);
      $stmt->bindParam(':ac', $total_write_off);
      $stmt->execute();
    }

    if ($this->data_array['method'] == 'allowance') {
      // register asset against the chart account selected
      $t_type = 'ASS';
      $p_pay_method = 'cash';
      $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,approvedby,_branch,t_type,acid,pay_method,date_created) VALUES
          (:amount,:descri,:autho,:actby,:accname,:approv,:branc,:ttype,:acid,:pay_method,:datee)';


      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':amount', $total_write_off);
      $stmt->bindParam(':descri', $this->data_array['comment']);
      $stmt->bindParam(':autho', $this->data_array['auth_id']);
      $stmt->bindParam(':actby', $this->data_array['auth_id']);
      $stmt->bindParam(':accname', $client_names);
      $stmt->bindParam(':approv', $this->data_array['auth_id']);
      $stmt->bindParam(':branc', $loan['branchid']);
      $stmt->bindParam(':ttype', $t_type);
      $stmt->bindParam(':acid', $this->data_array['debit_account']);
      $stmt->bindParam(':pay_method', $p_pay_method);
      $stmt->bindParam(':datee', $this->data_array['date']);

      $stmt->execute();

      // reduce  account balance of the reserve 
      // since it was stored in negative(beign contra-asset) , so we do a plus to reduce it's negativeness
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->data_array['debit_account']);
      $stmt->bindParam(':ac', $total_write_off);
      $stmt->execute();
    }


    // update loan to set it as written off loan, and set write off date & amount written off
    $this->conn->update('loan', [
      'is_written_off' => 1,
      'principal_due' => 0,
      'interest_due' => 0,
      'principal_arrears' => 0,
      'interest_arrears' => 0,
      'princ_written_off' => $principal_balance,
      'int_written_off' => @$interest_balance,
      'date_written_off' => @$this->data_array['date'],
      'notes_written_off' => @$this->data_array['comment']
    ], 'loan_no', @$loan_id);

    // register loan repayment to clear off loan

    $data = array(
      "auth_id" => $this->data_array['auth_id'],
      "bank_id" => '',
      "bankId" => '',
      "branch" =>
      $loan['branchid'],
      "branchId" =>
      $loan['branchid'],

      "principal" => $principal_balance,
      "interest" => $interest_balance,
      "ac_bal" => $client['acc_balance'],
      "pay_method" => 'cash',
      "uid" => $client['userId'],
      "lno" => $loan['loan_no'],
      "clear_penalty" => 0,
      "penalty_amount" => $loan['penalty_balance'],
      "collection_date" => $this->data_array['date'],
      "cash_acc" => '',
      "notes" => $this->data_array['comment'],
      "send_sms" => 1,
    );

    $this->makeLoanPayment($data);


    return true;
  }

  private function makeLoanPayment($data)
  {

    $endpoint = BACKEND_BASE_URL . "Bank/create_loan_repay_pi.php";

    $url =  $endpoint;


    $options = array(
      'http' => array(
        'method'  => 'POST',
        'content' => json_encode($data),
        'header' =>  "Content-Type: application/json\r\n" .
          "Accept: application/json\r\n"
      )
    );

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);

    // var_dump($data);
    return $data;
  }

  /**
   * waive loan interest
   */
  public function waiveInterest()
  {
    $loan_id = $this->data_array['loan_id'];
    // $this->bank_object->updateTotalLoanAmount($loan_id);
    // return;
    $loan = $this->conn->fetch('loan', 'loan_no', $loan_id);
    if (!$loan) return "Loan not found";
    $amount = $this->data_array['amount'];
    $interest_balance = $loan['interest_balance'];

    $i_due = $loan['interest_due'] ?? 0;
    $new_i_due = max(($i_due - $amount), 0);


    $i_ar = $loan['interest_arrears'] ?? 0;
    $new_i_arr = max(($i_ar - $amount), 0);

    $client = $this->conn->fetch('Client', 'userId', $loan['account_id']);
    if (!$client) return "Member not found";

    $client_user = $this->conn->fetch('User', 'id', $client['userId']);
    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];

    if ($amount > $interest_balance) return "Interest to waiver can not be greater than " . number_format($interest_balance);



    $active_schedule = $this->conn->database->query('SELECT * FROM loan_schedule WHERE loan_id=? AND status=? AND interest >? ORDER BY date_of_payment ASC', $loan_id, 'active', 0)->fetchAll();

    $amount_balance = $amount;
    $i = 0;

    while ($amount_balance > 0 && count($active_schedule) >= $i) {
      $current_schedule = @$active_schedule[$i];
      // $amount_balance += (int)$current_schedule['interest_paid'];
      $interest_waivered = 0;
      $interest_paid = (int)@$current_schedule['interest_paid'];
      $interest_to_pay = @$current_schedule['interest'] - $interest_paid;
      $original_interest = @$current_schedule['amount'] - @$current_schedule['principal'];

      $new_interest_paid = 0;
      if ($amount_balance <= @$current_schedule['interest']) {
        $new_interest = @$current_schedule['interest'] - $amount_balance;
        $interest_waivered = $amount_balance;

        if ($interest_paid > $new_interest) {
          // $balance = $interest_paid - $new_interest;
          $new_interest_paid = $new_interest;
          $amount_balance += ($interest_paid - $new_interest);
          $outstanding_interest = 0;
        } else {
          $outstanding_interest = $new_interest - $interest_paid;
          $new_interest_paid = $interest_paid;
        }

        $amount_balance -= $interest_waivered;
      } else {
        $new_interest = 0;
        $outstanding_interest = 0;
        // $interest_waivered = $amount ;
        $balance = $amount_balance - @$current_schedule['interest'] + $interest_paid;
        $interest_waivered = $original_interest - $interest_paid - @$current_schedule['interest_waivered'];
        $amount_balance = $balance;
        $new_interest_paid = $interest_paid;
      }

      // return [
      //   'new_interest' => $new_interest,
      //   'interest_waivered' => $interest_waivered,
      //   'outstanding_interest' => $outstanding_interest,
      //   'amount_balance' => $amount_balance,
      // ];


      $this->conn->update('loan_schedule', [
        'interest' => @$new_interest,
        'interest_paid' => @$new_interest_paid,
        'outstanding_interest' => @$outstanding_interest,
        'interest_waivered' => @$current_schedule['interest_waivered'] += @$interest_waivered,
        'amount' => @$current_schedule['amount'] -= @$interest_waivered
      ], 'schedule_id', @$current_schedule['schedule_id']);

      $i++;
      // $current_schedule = $active_schedule[$i];
    }



    // $this->conn->update('loan', [
    //   'int_waivered' => ($loan['int_waivered'] ?? 0) + $amount
    // ], 'loan_no', $loan_id);


    /**
     * create waiver transaction
     */
    $this->conn->insert('transactions', [
      'amount' => $amount,
      'description' => $this->data_array['description'] ?? 'Waive Loan interest',
      '_authorizedby' => $this->data_array['auth_id'],
      '_actionby' => $this->data_array['auth_id'],
      'acc_name' => $client_names,
      'mid' => $client['userId'],
      'approvedby' => $this->data_array['auth_id'],
      '_branch' => $client['branchId'],
      't_type' => 'WLI',
      'loan_id' => $loan_id,
      'date_created' => $this->data_array['date_of_waiver'],
      'outstanding_amount' => $loan['principal_balance'],
      'outstanding_amount_total' => $loan['principal_balance'],
      'loan_interest' => $loan['interest_amount'] - $amount,
      'outstanding_interest' => $loan['interest_balance'] - $amount,
      'outstanding_interest_total' => $loan['interest_balance'] - $amount,
      'pay_method' => 'cash',
      'loan_penalty' => $loan['penalty_balance']
    ]);

    // update arrears and dues

    $this->conn->update('loan', [
      'interest_due' => @$new_i_due,
      'interest_arrears' => @$new_i_arr,
    ], 'loan_no', @$loan_id);

    $this->bank_object->updateTotalLoanAmount($loan_id);

    $this->bank_object->getScheduleAmountTotal($loan_id);
    // $tot_amount_now = $lbalnow;

    // while (count($active_schedule) >= $i) {
    //   $current_schedule = @$active_schedule[$i];
    //   $tot_amount_now = $tot_amount_now  - @$current_schedule['amount'];
    //   var_dump($tot_amount_now);

    //   // $this->conn->update('loan_schedule', [
    //   //   'balance' => @$tot_amount_now
    //   // ], 'schedule_id', @$current_schedule['schedule_id']);

    //   $i++;
    // }

    return true;
  }

  public function waiveInterest2()
  {
    $loan_id = $this->data_array['loan_id'];
    // $this->bank_object->updateTotalLoanAmount($loan_id);
    // return;
    $loan = $this->conn->fetch('loan', 'loan_no', $loan_id);
    if (!$loan) return "Loan not found";
    $amount = $this->data_array['amount'];
    $interest_balance = $loan['interest_balance'];

    $client = $this->conn->fetch('Client', 'userId', $loan['account_id']);
    if (!$client) return "Member not found";

    $client_user = $this->conn->fetch('User', 'id', $client['userId']);
    $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];

    if ($amount > $interest_balance) return "Interest to waiver can not be greater than " . number_format($interest_balance);



    $active_schedule = $this->conn->database->query('SELECT * FROM loan_schedule WHERE loan_id=? AND status=? AND interest >? ORDER BY date_of_payment ASC', $loan_id, 'active', 0)->fetchAll();

    $amount_balance = $amount;
    $i = 0;

    while ($amount_balance > 0 && count($active_schedule) >= $i) {
      $current_schedule = @$active_schedule[$i];
      // $amount_balance += (int)$current_schedule['interest_paid'];
      $interest_waivered = 0;
      $interest_paid = (int)@$current_schedule['interest_paid'];
      $interest_to_pay = @$current_schedule['interest'] - $interest_paid;
      $original_interest = @$current_schedule['amount'] - @$current_schedule['principal'];

      $new_interest_paid = 0;
      if ($amount_balance <= @$current_schedule['interest']) {
        $new_interest = @$current_schedule['interest'] - $amount_balance;
        $interest_waivered = $amount_balance;

        if ($interest_paid > $new_interest) {
          // $balance = $interest_paid - $new_interest;
          $new_interest_paid = $new_interest;
          $amount_balance += ($interest_paid - $new_interest);
          $outstanding_interest = 0;
        } else {
          $outstanding_interest = $new_interest - $interest_paid;
          $new_interest_paid = $interest_paid;
        }

        $amount_balance -= $interest_waivered;
      } else {
        $new_interest = 0;
        $outstanding_interest = 0;
        // $interest_waivered = $amount ;
        $balance = $amount_balance - @$current_schedule['interest'] + $interest_paid;
        $interest_waivered = $original_interest - $interest_paid - @$current_schedule['interest_waivered'];
        $amount_balance = $balance;
        $new_interest_paid = $interest_paid;
      }

      // return [
      //   'new_interest' => $new_interest,
      //   'interest_waivered' => $interest_waivered,
      //   'outstanding_interest' => $outstanding_interest,
      //   'amount_balance' => $amount_balance,
      // ];


      $this->conn->update('loan_schedule', [
        'interest' => @$new_interest,
        'interest_paid' => @$new_interest_paid,
        'outstanding_interest' => @$outstanding_interest,
        'interest_waivered' => @$current_schedule['interest_waivered'] += @$interest_waivered,
        'amount' => @$current_schedule['amount'] -= @$interest_waivered
      ], 'schedule_id', @$current_schedule['schedule_id']);

      $i++;
      // $current_schedule = $active_schedule[$i];
    }



    // $this->conn->update('loan', [
    //   'int_waivered' => ($loan['int_waivered'] ?? 0) + $amount
    // ], 'loan_no', $loan_id);


    /**
     * create waiver transaction
     */
    $this->conn->insert('transactions', [
      'amount' => $amount,
      'description' => $this->data_array['description'] ?? 'Waive Loan interest',
      '_authorizedby' => $this->data_array['auth_id'],
      '_actionby' => $this->data_array['auth_id'],
      'acc_name' => $client_names,
      'mid' => $client['userId'],
      'approvedby' => $this->data_array['auth_id'],
      '_branch' => $client['branchId'],
      't_type' => 'WLI',
      'loan_id' => $loan_id,
      'date_created' => $this->data_array['date_of_waiver'],
      'outstanding_amount' => $loan['principal_balance'],
      'outstanding_amount_total' => $loan['principal_balance'],
      'loan_interest' => $loan['interest_amount'] - $amount,
      'outstanding_interest' => $loan['interest_balance'] - $amount,
      'outstanding_interest_total' => $loan['interest_balance'] - $amount,
      'pay_method' => 'cash',
      'loan_penalty' => $loan['penalty_balance']
    ]);

    // $this->bank_object->updateTotalLoanAmount($loan_id);

    // $this->bank_object->getScheduleAmountTotal($loan_id);
    // $tot_amount_now = $lbalnow;

    // while (count($active_schedule) >= $i) {
    //   $current_schedule = @$active_schedule[$i];
    //   $tot_amount_now = $tot_amount_now  - @$current_schedule['amount'];
    //   var_dump($tot_amount_now);

    //   // $this->conn->update('loan_schedule', [
    //   //   'balance' => @$tot_amount_now
    //   // ], 'schedule_id', @$current_schedule['schedule_id']);

    //   $i++;
    // }

    // return true;
    return true;
  }

  function ApplyLoanPenaltySettings($loan_id)
  {
    $db_handler = new DbHandler();
    $loan = $db_handler->fetch('loan', 'loan_no', $loan_id);
    if ($loan) {
      $loan_product = $db_handler->fetch('loantypes', 'type_id', $loan['loan_type']);
      if ($loan_product) {
        $db_handler->update('loan', [
          'charge_penalty' => $loan_product['penalty'],
          'num_grace_periods' => $loan_product['numberofgraceperioddays'],
          'penalty_interest_rate' => $loan_product['penaltyinterestrate'],
          'penalty_fixed_amount' => $loan_product['penaltyfixedamount'],
          'penalty_max_days' => $loan_product['maxnumberofpenaltydays'],
          'penalty_based_on' => $loan_product['penalty_based_on'],
          'penalty_grace_type' => $loan_product['gracetype'],
          'auto_penalty' => $loan_product['auto_penalty'],
          'auto_repay_penalty' => $loan_product['auto_repay'],
          // 'charge_penalty'=> $loan_product['penalty'],
          // 'charge_penalty'=> $loan_product['penalty'],
          // 'charge_penalty'=> $loan_product['penalty'],

        ], 'loan_no', $loan['loan_no']);
      }
    }
  }
}
