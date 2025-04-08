<?php
require_once('Fee.php');
class Transaction
{
  // DB stuff
  private $conn;
  private $db_table = 'transactions';

  //   table columns
  public $tid;
  public $lid;
  public $is_verified;
  public $amount;
  public $outstanding_amount_total;
  public $outstanding_amount;
  public $loan_interest;
  public $outstanding_interest;
  public $outstanding_interest_total;
  public $left_balance;
  public $t_type;
  public $date_created;
  public $description;
  public $message;
  public $_authorizedby;
  public $_actionby;
  public $_actionbyphone;
  public $phone_number;
  public $_status;
  public $transaction_error;
  public $acc_name;
  public $mid;
  public $loan_id;
  public $approvedby;
  public $channel;
  public $mm_tid;
  public $_branch;
  public $external_ref;
  public $orig_acid;

  public $pay_method;

  public $bacid;
  public $cheque_no;

  public $cash_acc;
  public $send_sms;
  public $make_charges;
  public $said;

  public $parent_name;
  public $parent_phone;
  public $student_name;
  public $student_class;
  public $send_sms_school;
  public $send_sms_parent;
  public $term;



  // Constructor with DB
  public function __construct($db)
  {
    $this->conn = $db;
    date_default_timezone_set("Africa/Kampala");
  }
  public function createDepositFees()
  {
    $charges = 0;

    $cid = 0;

    $rec = 'fees';


    // update cash or bank account balance used to deposit
    if ($this->pay_method == 'cash') {
      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->cash_acc);
      $stmt->bindParam(':amount', $this->amount);
      $stmt->execute();
    } else if ($this->pay_method == 'cheque') {
      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->bacid);
      $stmt->bindParam(':amount', $this->amount);
      $stmt->execute();
    } else if ($this->pay_method == 'mobilemoney') {
      $acode = '1-01-20-06';
      $sqlQuery = 'SELECT id FROM  public."Account"  WHERE account_code_used=:id AND "branchId"=:bid ORDER BY "createdAt" ASC LIMIT 1';

      $transactions = $this->conn->prepare($sqlQuery);
      $transactions->bindParam(':id', $acode);
      $transactions->bindParam(':bid', $this->_branch);
      $transactions->execute();
      $rowx = $transactions->fetch();

      if ($rowx) {
        $this->bacid  = $rowx['id'];
      }
    }


    //  get bank id from branch
    $sqlQuery = 'select "bankId" from  public."Branch" where id=:id';
    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->_branch);
    $stmt->execute();
    $branch = $stmt->fetch();

    // check whether branch -- bank has deposit charges 
    $sqlQuery = 'select * from  public."transaction_charges" where bankid=:id and c_status=1 and c_application=:appln';
    $appln = 'school_pay';
    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $branch['bankId']);
    $stmt->bindParam(':appln', $appln);
    $stmt->execute();

    $count = $stmt->rowCount();

    if (
      $count > 0
    ) {


      foreach ($stmt as $use) {
        if ($use['c_type'] == 'general') {
          if ($use['charge_mode'] == 'fixed') {
            $charges = $use['charge'];
          } else {
            $charges = ($use['charge'] / 100) * $this->amount;
          }

          $cid = $use['c_id'];
        } else {
          if (($use['min_amount'] <= $this->amount) && ($this->amount <= $use['max_amount'])) {
            if ($use['charge_mode'] == 'fixed') {
              $charges = $use['charge'];
            } else {
              $charges = ($use['charge'] / 100) * $this->amount;
            }
            $cid = $use['c_id'];
          }
        }
      }
    }


    // get client names,left balance
    $sqlQuery = 'SELECT *, public."Client".id AS client_id FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->mid);
    $stmt->execute();
    $row = $stmt->fetch();
    $client = $row;

    $aid = 0;
    if ($row['membership_no'] > 0) {
      $this->left_balance = $row['acc_balance'] + $this->amount;


      $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".said=:id AND type=:atype ORDER BY "createdAt" ASC LIMIT 1';

      $stmt = $this->conn->prepare($sqlQuery);
      $atyp = 'LIABILITIES';
      $stmt->bindParam(':id', $row['actype']);
      $stmt->bindParam(':atype', $atyp);
      $stmt->execute();
      $rown  = $stmt->fetch();
      $aid = $rown['id'] ?? null;
    } else {
      $this->left_balance = $row['loan_wallet'] + $this->amount;
    }
    $this->acc_name = $row['firstName'] . ' ' . $row['lastName'] . ' ' . $row['shared_name'];
    $this->t_type = 'D';

    $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created,charges,trxn_rec,sno,parent_phone,parent_name,sname,sclass,send_sms_school,send_sms_parent,sterm,trxn_ref) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created,:charges,:rec,:sno,:pphone,:pname,:sname,:sclass,:sssch,:sspar,:sterm,:trxn_ref)';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':amount', $this->amount);
    $stmt->bindParam(':trxn_ref', $this->external_ref);
    $stmt->bindParam(':sterm', $this->term);
    $stmt->bindParam(':descri', $this->description);
    $stmt->bindParam(':autho', $this->_authorizedby);
    $stmt->bindParam(':actby', $this->_actionby);
    $stmt->bindParam(':accname', $this->acc_name);
    $stmt->bindParam(':mid', $this->mid);
    $stmt->bindParam(':approv', $this->_authorizedby);
    $stmt->bindParam(':branc', $this->_branch);
    $stmt->bindParam(':leftbal', $this->left_balance);
    $stmt->bindParam(':ttype', $this->t_type);
    $stmt->bindParam(':acid', $aid);
    $stmt->bindParam(':pay_method', $this->pay_method);
    $stmt->bindParam(':bacid', $this->bacid);
    $stmt->bindParam(':cheque', $this->cheque_no);
    $stmt->bindParam(':cash_acc', $this->cash_acc);
    // $stmt->bindParam(':send_sms', $this->send_sms);
    $stmt->bindParam(':date_created', $this->date_created);
    $stmt->bindParam(':charges', $charges);
    $stmt->bindParam(':rec', $rec);
    $stmt->bindParam(':sno', $this->send_sms);
    $stmt->bindParam(':sclass', $this->student_class);
    $stmt->bindParam(':sname', $this->student_name);
    $stmt->bindParam(':pname', $this->parent_name);
    $stmt->bindParam(':pphone', $this->parent_phone);
    $stmt->bindParam(':sssch', $this->send_sms_school);
    $stmt->bindParam(':sspar', $this->send_sms_parent);


    $stmt->execute();
    $this->lid = $this->conn->lastInsertId();

    if (
      $charges > 0
    ) {

      $sqlQuery = 'SELECT * FROM public."Account" WHERE feeid=:id AND "branchId"=:bid ORDER BY id DESC LIMIT 1';
      $stmt = $this->conn->prepare($sqlQuery);
      $stmt->bindParam(':id', $cid);
      $stmt->bindParam(':bid', $this->_branch);
      $stmt->execute();
      $rowfx  = $stmt->fetch();



      $cid_take = $rowfx['id'] ?? 0;




      $this->t_type = 'I';
      $desc = 'School Fees Payment Charges';
      $pmethod = 'saving';
      $this->left_balance = $this->left_balance - $charges;

      $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created,charges) VALUES
          (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created,:charges)';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':amount', $charges);
      $stmt->bindParam(':descri', $desc);
      $stmt->bindParam(':autho', $this->_authorizedby);
      $stmt->bindParam(':actby', $this->_actionby);
      $stmt->bindParam(':accname', $this->acc_name);
      $stmt->bindParam(':mid', $this->mid);
      $stmt->bindParam(':approv', $this->_authorizedby);
      $stmt->bindParam(':branc', $this->_branch);
      $stmt->bindParam(':leftbal', $this->left_balance);
      $stmt->bindParam(':ttype', $this->t_type);
      $stmt->bindParam(':acid', $cid_take);
      $stmt->bindParam(':pay_method', $pmethod);
      $stmt->bindParam(':bacid', $this->bacid);
      $stmt->bindParam(':cheque', $this->cheque_no);
      $stmt->bindParam(':cash_acc', $this->cash_acc);
      // $stmt->bindParam(':send_sms', $this->send_sms);
      $stmt->bindParam(':date_created', $this->date_created);
      $stmt->bindParam(':charges', $charges);


      $stmt->execute();
    }

    try {
      $fee = new Fee($this->conn);
      $fee->_authorizedby = $this->_authorizedby;
      $fee->_actionby = $this->_actionby;
      $fee->acc_name = $this->acc_name;
      $fee->mid = $this->mid;
      $fee->_branch = $this->_branch;
      $fee->cash_acc = $this->cash_acc;
      $fee->pmethod = $this->pay_method;
      $fee->date_created = $this->date_created;

      $balance = $this->amount - $charges;
      $fee->computeDepositTransaction($this->mid, $balance);
    } catch (\Throwable $th) {
      return $th->getMessage();
      // return false;
    }



    return
      $this->lid;;
  }

  public function editDeposit()
  {
    $charges = 0;

    $cid = 0;

    if ($this->amount > $this->make_charges) {
      $amount_diff = $this->amount - $this->make_charges;

      if ($this->said) {
        // update account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->said);
        $stmt->bindParam(':amount', $amount_diff);
        $stmt->execute();
      }


      // update cash or bank account balance used to deposit

      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->orig_acid);
      $stmt->bindParam(':amount', $amount_diff);
      $stmt->execute();
    } else if ($this->make_charges > $this->amount) {
      $amount_diff = $this->make_charges - $this->amount;
      if ($this->said) {
        // update account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->said);
        $stmt->bindParam(':amount', $amount_diff);
        $stmt->execute();
      }
      // update cash or bank account balance used to deposit

      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->orig_acid);
      $stmt->bindParam(':amount', $amount_diff);
      $stmt->execute();
    }




    // get client names,left balance
    $sqlQuery = 'SELECT *, public."Client".id AS client_id FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->mid);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($this->amount > $this->make_charges) {
      $diff = $this->amount - $this->make_charges;
      if ($row['membership_no'] > 0) {

        // update account balance
        $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      } else {
        $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet+:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      }
    } else if ($this->make_charges > $this->amount) {
      $diff = $this->make_charges - $this->amount;
      if ($row['membership_no'] > 0) {

        // update account balance
        $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      } else {
        $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet-:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      }
    }



    $sqlQuery = 'UPDATE public."transactions" SET amount=:amount,description=:descri,_actionby=:actby,cheque_no=:cheque,date_created=:date_created WHERE tid=:tid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':amount', $this->amount);
    $stmt->bindParam(':tid', $this->send_sms);
    $stmt->bindParam(':descri', $this->description);
    $stmt->bindParam(':actby', $this->_actionby);
    $stmt->bindParam(':cheque', $this->cheque_no);
    $stmt->bindParam(':date_created', $this->date_created);


    $stmt->execute();

    return true;
  }

  public function editWithdraw()
  {
    $charges = 0;

    $cid = 0;

    if ($this->amount > $this->make_charges) {
      $amount_diff = $this->amount - $this->make_charges;

      if ($this->said) {
        // update account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->said);
        $stmt->bindParam(':amount', $amount_diff);
        $stmt->execute();
      }


      // update cash or bank account balance used to deposit

      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->orig_acid);
      $stmt->bindParam(':amount', $amount_diff);
      $stmt->execute();
    } else if ($this->make_charges > $this->amount) {
      $amount_diff = $this->make_charges - $this->amount;
      if ($this->said) {
        // update account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->said);
        $stmt->bindParam(':amount', $amount_diff);
        $stmt->execute();
      }
      // update cash or bank account balance used to deposit

      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->orig_acid);
      $stmt->bindParam(':amount', $amount_diff);
      $stmt->execute();
    }




    // get client names,left balance
    $sqlQuery = 'SELECT *, public."Client".id AS client_id FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->mid);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($this->amount > $this->make_charges) {
      $diff = $this->amount - $this->make_charges;
      if ($row['membership_no'] > 0) {

        // update account balance
        $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      } else {
        $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet-:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      }
    } else if ($this->make_charges > $this->amount) {
      $diff = $this->make_charges - $this->amount;
      if ($row['membership_no'] > 0) {

        // update account balance
        $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      } else {
        $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet+:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':amount', $diff);
        $stmt->execute();
      }
    }



    $sqlQuery = 'UPDATE public."transactions" SET amount=:amount,description=:descri,_actionby=:actby,cheque_no=:cheque,date_created=:date_created WHERE tid=:tid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':amount', $this->amount);
    $stmt->bindParam(':tid', $this->send_sms);
    $stmt->bindParam(':descri', $this->description);
    $stmt->bindParam(':actby', $this->_actionby);
    $stmt->bindParam(':cheque', $this->cheque_no);
    $stmt->bindParam(':date_created', $this->date_created);


    $stmt->execute();

    return true;
  }
  public function createDeposit()
  {
    $charges = 0;


    $cid = 0;

    // update cash or bank account balance used to deposit
    if ($this->pay_method == 'cash') {
      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->cash_acc);
      $stmt->bindParam(':amount', $this->amount);
      $stmt->execute();
    } else if ($this->pay_method == 'cheque') {
      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->bacid);
      $stmt->bindParam(':amount', $this->amount);
      $stmt->execute();
    }

    //  get bank id from branch
    $sqlQuery = 'select "bankId" from  public."Branch" where id=:id';
    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->_branch);
    $stmt->execute();
    $branch = $stmt->fetch();

    // check whether branch -- bank has deposit charges 
    $sqlQuery = 'SELECT * from  public."transaction_charges" LEFT JOIN public."Account" ON public."transaction_charges".c_id=public."Account".txn_charge  where bankid=:id and c_status=1 AND c_application=:appln';
    $appln = 'deposit';
    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $branch['bankId']);
    $stmt->bindParam(':appln', $appln);
    $stmt->execute();

    $count = $stmt->rowCount();

    if ($count > 0) {


      foreach ($stmt as $use) {
        if ($use['c_type'] == 'general') {
          if ($use['charge_mode'] == 'fixed') {
            $charges = $use['charge'];
          } else {
            $charges = ($use['charge'] / 100) * $this->amount;
          }

          $cid = $use['id'];
        } else {
          if (($use['min_amount'] <= $this->amount) && ($this->amount <= $use['max_amount'])) {
            if ($use['charge_mode'] == 'fixed') {
              $charges = $use['charge'];
            } else {
              $charges = ($use['charge'] / 100) * $this->amount;
            }
            $cid = $use['id'];
          }
        }
      }
    }


    // get client names,left balance
    $sqlQuery = 'SELECT *, public."Client".id AS client_id FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->mid);
    $stmt->execute();
    $row = $stmt->fetch();
    $client = $row;

    $aid = 0;
    if ($row['membership_no'] > 0) {
      $this->left_balance = $row['acc_balance'] + $this->amount;


      $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".said=:id  ORDER BY "createdAt" ASC LIMIT 1';

      $stmt = $this->conn->prepare($sqlQuery);
      // $atyp = 'LIABILITIES';
      $stmt->bindParam(':id', $row['actype']);
      // $stmt->bindParam(':atype', $atyp);
      $stmt->execute();
      $rown  = $stmt->fetch();
      $aid = $rown['id'] ?? null;

      if ($aid) {
        // update account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $aid);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->execute();
      }
    } else {
      $this->left_balance = $row['loan_wallet'] + $this->amount;
    }
    $this->acc_name = $row['firstName'] . ' ' . $row['lastName'] . ' ' . $row['shared_name'];
    $this->t_type = 'D';

    $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created,charges) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created,:charges)';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':amount', $this->amount);
    $stmt->bindParam(':descri', $this->description);
    $stmt->bindParam(':autho', $this->_authorizedby);
    $stmt->bindParam(':actby', $this->_actionby);
    $stmt->bindParam(':accname', $this->acc_name);
    $stmt->bindParam(':mid', $this->mid);
    $stmt->bindParam(':approv', $this->_authorizedby);
    $stmt->bindParam(':branc', $this->_branch);
    $stmt->bindParam(':leftbal', $this->left_balance);
    $stmt->bindParam(':ttype', $this->t_type);
    $stmt->bindParam(':acid', $aid);
    $stmt->bindParam(':pay_method', $this->pay_method);
    $stmt->bindParam(':bacid', $this->bacid);
    $stmt->bindParam(':cheque', $this->cheque_no);
    $stmt->bindParam(':cash_acc', $this->cash_acc);
    // $stmt->bindParam(':send_sms', $this->send_sms);
    $stmt->bindParam(':date_created', $this->date_created);
    $stmt->bindParam(':charges', $charges);


    $stmt->execute();

    $this->lid = $this->conn->lastInsertId();

    if ($charges > 0) {
      $this->t_type = 'I';
      $desc = 'Deposit Fees';
      $pmethod = 'saving';
      $this->left_balance = $this->left_balance - $charges;

      $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created,charges) VALUES
          (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created,:charges)';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':amount', $charges);
      $stmt->bindParam(':descri', $desc);
      $stmt->bindParam(':autho', $this->_authorizedby);
      $stmt->bindParam(':actby', $this->_actionby);
      $stmt->bindParam(':accname', $this->acc_name);
      $stmt->bindParam(':mid', $this->mid);
      $stmt->bindParam(':approv', $this->_authorizedby);
      $stmt->bindParam(':branc', $this->_branch);
      $stmt->bindParam(':leftbal', $this->left_balance);
      $stmt->bindParam(':ttype', $this->t_type);
      $stmt->bindParam(':acid', $cid);
      $stmt->bindParam(':pay_method', $pmethod);
      $stmt->bindParam(':bacid', $this->bacid);
      $stmt->bindParam(':cheque', $this->cheque_no);
      $stmt->bindParam(':cash_acc', $this->cash_acc);
      // $stmt->bindParam(':send_sms', $this->send_sms);
      $stmt->bindParam(':date_created', $this->date_created);
      $stmt->bindParam(':charges', $charges);


      $stmt->execute();


      $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id AND "branchId"=:bid';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $cid);
      $stmt->bindParam(':bid', $this->_branch);
      $stmt->bindParam(':amount', $charges);
      $stmt->execute();
    }

    try {
      $fee = new Fee($this->conn);
      $fee->_authorizedby = $this->_authorizedby;
      $fee->_actionby = $this->_actionby;
      $fee->acc_name = $this->acc_name;
      $fee->mid = $this->mid;
      $fee->_branch = $this->_branch;
      $fee->cash_acc = $this->cash_acc;
      $fee->pmethod = $pmethod ?? 'saving';
      $fee->date_created = $this->date_created;

      $balance = $this->amount - $charges;
      $fee->computeDepositTransaction($this->mid, $balance);
    } catch (\Throwable $th) {
      return $th->getMessage();
      // return false;
    }


    // if ($row['membership_no'] > 0) {
    //   $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

    //   $stmt = $this->conn->prepare($sqlQuery);

    //   $amount_subtract = $this->amount - $charges;

    //   $stmt->bindParam(':id', $this->mid);
    //   $stmt->bindParam(':ac', $amount_subtract);
    //   $stmt->execute();
    // } else {
    //   $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet+:ac WHERE public."Client"."userId"=:id';

    //   $stmt = $this->conn->prepare($sqlQuery);
    //   $amount_subtract = $this->amount - $charges;
    //   $stmt->bindParam(':id', $this->mid);
    //   $stmt->bindParam(':ac', $amount_subtract);
    //   $stmt->execute();
    // }


    return $this->lid;
    // return true;
  }

  public function scheduleSMS()
  {
    $sqlQuery = 'INSERT INTO public.scheduled_sms(
	 s_key, s_type, s_body, s_savingid, s_date, branch_id, sender_id, sms_charge,scheduled_by)
	VALUES (:skey,:stype,:sbody,:sid,:sdate,:bid,:senderid,:charge,:scheduled_by)';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':skey', $this->mid['unique_key']);
    $stmt->bindParam(':scheduled_by', $this->mid['user']);
    $stmt->bindParam(':stype', $this->mid['type']);
    $stmt->bindParam(':sbody', $this->mid['sms_text']);
    $stmt->bindParam(':sid', $this->mid['actype']);
    $stmt->bindParam(':sdate', $this->mid['date']);
    $stmt->bindParam(':bid', $this->mid['branch']);
    $stmt->bindParam(':senderid', $this->mid['senderid']);
    $stmt->bindParam(':charge', $this->mid['charge']);
    $stmt->execute();

    return true;
  }

  public function checkBranchSMSBalance($id)
  {
    $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $row = $stmt->fetch();
    return $row['sms_balance'] ?? 0;
  }

  public function getBranchClientsContacts($id)
  {
    $sqlQuery = 'SELECT "userId" AS uid FROM public."Client"  WHERE "branchId"=:id AND sent_message=0';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt;
  }

  public function updateClientSMSStatus($id)
  {
    $sqlQuery = 'UPDATE public."Client" SET sent_message=1  WHERE "userId"=:id ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return true;
  }

  public function checkBankSMSPrice($id)
  {
    $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $row = $stmt->fetch();

    $sqlQuery = 'SELECT * FROM public."Bank" WHERE id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $row['bankId']);
    $stmt->execute();

    $row1 = $stmt->fetch();
    return $row1;
  }

  public function checkBankSenderid($sid)
  {


    $sqlQuery = 'SELECT * FROM public."senderids" WHERE sid=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $sid);
    $stmt->execute();

    $row1 = $stmt->fetch();
    return $row1['sname'];
  }

  public function getBranchSenderid($branch)
  {
    $sqlQuery = 'SELECT * FROM public."senderids" LEFT JOIN public."Branch" ON public."senderids".bankid=public."Branch"."bankId" WHERE public."Branch".id=:id ORDER BY public."senderids".sid ASC LIMIT 1';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $branch);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      $row1 = $stmt->fetch();
      return $row1['sname'];
    }
    return '';
  }

  public function getClientSMSConsent($id)
  {
    $sqlQuery = 'SELECT message_consent FROM public."Client" WHERE "userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $row = $stmt->fetch();
    return $row['message_consent'] ?? 0;
  }
  public function getClientPhone($id, $phone)
  {
    $sms_nos = [];

    $sqlQuery = 'SELECT "primaryCellPhone","secondaryCellPhone",sms_phone_numbers FROM public."User" WHERE id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $row1 = $stmt->fetch();

    if (!$row1['primaryCellPhone'] && strlen($row1['primaryCellPhone'] ?? '') < 9 && $phone) {
      $sqlQuery = 'UPDATE "User" SET "primaryCellPhone"=:pp WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $id);
      $stmt->bindParam(':pp', $phone);
      $stmt->execute();




      $sqlQuery = 'SELECT "primaryCellPhone","secondaryCellPhone",sms_phone_numbers FROM public."User" WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $id);
      $stmt->execute();

      $row1 = $stmt->fetch();
    }


    $row1['sms_phone_numbers'] = json_decode($row1['sms_phone_numbers'] ?? '[]');
    if ($row1['sms_phone_numbers'] && count($row1['sms_phone_numbers']) > 0) {
      foreach ($row1['sms_phone_numbers'] as $value) {
        array_push($sms_nos, $value);
      }
    } else {
      if ($row1['primaryCellPhone'] && strlen($row1['primaryCellPhone']) >= 8) {
        array_push($sms_nos, $row1['primaryCellPhone']);
      }
      if ($row1['secondaryCellPhone'] && strlen($row1['secondaryCellPhone']) >= 8) {
        array_push($sms_nos, $row1['secondaryCellPhone']);
      }
    }

    return count($sms_nos) > 0 ? $sms_nos : null;
  }
  public function chargeBranchSMS($charge, $bid)
  {


    $sqlQuery = 'UPDATE public."Branch" SET sms_balance=sms_balance-:ac,sms_used_count=sms_used_count+1,sms_amount_spent=sms_amount_spent+:ac WHERE id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $bid);
    $stmt->bindParam(':ac', $charge);
    $stmt->execute();

    return true;
  }

  public function getBranchSMSChargesAcc($bid)
  {

    $sqlQuery = 'SELECT * FROM  public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId" = public."Bank".id  WHERE public."Branch".id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $bid);
    $stmt->execute();

    $rown = $stmt->fetch();

    $sqlQuery = 'SELECT * FROM  public."Account"  WHERE public."Account".id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $rown['sms_income_acid']);
    $stmt->execute();

    $rowx = $stmt->fetch();

    $sqlQuery = 'SELECT * FROM  public."Account"  WHERE account_code_used=:id AND "branchId"=:bid';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $rowx['account_code_used']);
    $stmt->bindParam(':bid', $bid);
    $stmt->execute();

    $my = $stmt->fetch();

    return $my['id'] ?? 0;
  }

  public function insertSMSOutBox($phone, $sms_text, $senderid, $mid, $amount, $status, $issys, $branch, $reason)
  {


    $sqlQuery = 'INSERT INTO public."sms_outbox" (phone, msg_body, branchid, issysgen, sender_id, sent_status, charge, failed_reason,cid)VALUES
    (:phone,:msg,:bid,:issys,:sid,:status,:charge,:reason,:cid)
    ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':msg', $sms_text);
    $stmt->bindParam(':bid', $branch);
    $stmt->bindParam(':issys', $issys);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':charge', $amount);
    $stmt->bindParam(':reason', $reason);
    $stmt->bindParam(':cid', $mid);
    $stmt->bindParam(':sid', $senderid);
    $stmt->execute();

    return true;
  }

  public function createSMSChargeTrxn($amount, $leftbal, $ttype, $descri, $auth, $actby, $phone, $acname, $mid, $app, $bid, $issys, $acid, $paymeth, $sendsms)
  {

    $sqlQuery = 'INSERT INTO public."transactions" (
      amount, left_balance, t_type, description, _authorizedby, _actionby, phone_number, acc_name, mid, approvedby, _branch, issystemgenerated, acid, pay_method, send_sms
    )VALUES
    (
      :amount,:leftbal,:ttype,:descri,:auth,:actby,:phone,:acname,:mid,:app,:bid,:issys,:acid,:paymeth,:sendsms
    )
    ';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':leftbal', $leftbal);
    $stmt->bindParam(':ttype', $ttype);
    $stmt->bindParam(':descri', $descri);
    $stmt->bindParam(':auth', $auth);
    $stmt->bindParam(':actby', $actby);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':acname', $acname);
    $stmt->bindParam(':mid', $mid);
    $stmt->bindParam(':app', $app);
    $stmt->bindParam(':bid', $bid);
    $stmt->bindParam(':issys', $issys);
    $stmt->bindParam(':acid', $acid);
    $stmt->bindParam(':paymeth', $paymeth);
    $stmt->bindParam(':sendsms', $sendsms);
    $stmt->execute();



    $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE id=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $acid);
    $stmt->bindParam(':bal', $amount);
    $stmt->execute();


    return true;
  }

  public function chargeClientSMS($mid, $charge)
  {
    $sqlQuery = 'SELECT * FROM public."Client" WHERE "userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $mid);
    $stmt->execute();

    $row = $stmt->fetch();

    if ($row['actype'] > 0) {
      $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac WHERE "userId"=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $mid);
      $stmt->bindParam(':ac', $charge);
      $stmt->execute();
      return true;
    } else {
      $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet-:ac WHERE "userId"=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $mid);
      $stmt->bindParam(':ac', $charge);
      $stmt->execute();
      return true;
    }

    return true;
  }
  public function SendSMS($sender, $number, $message)
  {
    $username = 'ucscucbs';
    $password = 'smsmanage';

    $url = "www.egosms.co/api/v1/plain/?";

    $parameters = "number=[number]&message=[message]&username=[username]&password=[password]&sender=[sender]";
    $parameters = str_replace("[message]", urlencode($message), $parameters);
    $parameters = str_replace("[sender]", urlencode($sender), $parameters);
    $parameters = str_replace("[number]", urlencode($number), $parameters);
    $parameters = str_replace("[username]", urlencode($username), $parameters);
    $parameters = str_replace("[password]", urlencode($password), $parameters);
    $live_url = "https://" . $url . $parameters;
    $parse_url = file($live_url);
    $response = $parse_url[0];
    return $response;
  }

  public function SendOneSMS($sender, $number, $message)
  {

    $data = array(
      'method' => 'SendSms',
      'userdata' => array(
        'username' => 'ucscucbs', //  Username
        'password' => 'smsmanage',  // password
        'msgdata' => array(
          array(
            'number' => $number,
            'message' => $message,
            'senderid' => $sender
          ),
        )
      )
    );

    //encode the array into json
    $json_builder = json_encode($data);
    //use curl to post the the json encoded information
    $ch = curl_init('https://www.egosms.co/api/v1/json/');

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_builder);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ch_result = curl_exec($ch);
    curl_close($ch);
    //print an array that is json decoded
    // print_r(json_decode($ch_result, true));
    return json_decode($ch_result, true);
  }


  public function sendSingleSMS()
  {

    if ($this->send_sms == 'sub') {
      $sqlQuery = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->mid);
      $stmt->execute();
      $row = $stmt->fetch();

      if ($row['primaryCellPhone'] == '') {
        $this->_actionby = $row['secondaryCellPhone'];
      } else {
        $this->_actionby = $row['primaryCellPhone'];
      }
    } else {
    }

    // incomplete
    // // get client names,left balance
    // $sqlQuery = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

    // $stmt = $this->conn->prepare($sqlQuery);

    // $stmt->bindParam(':id', $this->mid);
    // $stmt->execute();
    //         $row = $stmt->fetch();

    //         $aid = 0;
    //         if($row['membership_no']>0){
    //           $this->left_balance = $row['acc_balance']+$this->amount;


    //           $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".said=:id AND type=:atype';

    // $stmt = $this->conn->prepare($sqlQuery);
    // $atyp = 'LIABILITIES';
    // $stmt->bindParam(':id', $row['actype']);
    // $stmt->bindParam(':atype', $atyp);
    // $stmt->execute();
    // $rown  = $stmt->fetch();
    // $aid = $rown['id'];


    //         }else{
    //           $this->left_balance = $row['loan_wallet']+$this->amount;

    //         }
    //         $this->acc_name = $row['firstName'].' '.$row['lastName'];
    //         $this->t_type = 'I';

    //         $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
    //       acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created) VALUES
    //         (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created)';

    //         $stmt = $this->conn->prepare($sqlQuery);

    //         $stmt->bindParam(':amount', $this->amount);
    //         $stmt->bindParam(':descri', $this->description);
    //         $stmt->bindParam(':autho', $this->_authorizedby);
    //         $stmt->bindParam(':actby', $this->_actionby);
    //         $stmt->bindParam(':accname', $this->acc_name);
    //         $stmt->bindParam(':mid', $this->mid);
    //         $stmt->bindParam(':approv', $this->_authorizedby);
    //         $stmt->bindParam(':branc', $this->_branch);
    //         $stmt->bindParam(':leftbal', $this->left_balance);
    //         $stmt->bindParam(':ttype', $this->t_type);
    //         $stmt->bindParam(':acid', $aid);
    //         $stmt->bindParam(':pay_method', $this->pay_method);
    //         $stmt->bindParam(':bacid', $this->bacid);
    //         $stmt->bindParam(':cheque', $this->cheque_no);
    //         $stmt->bindParam(':cash_acc', $this->cash_acc);
    //         // $stmt->bindParam(':send_sms', $this->send_sms);
    //         $stmt->bindParam(':date_created', $this->date_created);


    //         $stmt->execute();

    //         if($row['membership_no']>0){
    //           $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

    //           $stmt = $this->conn->prepare($sqlQuery);

    //           $stmt->bindParam(':id', $this->mid);
    //           $stmt->bindParam(':ac', $this->amount);
    //           $stmt->execute();
    //         }else{
    //           $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet+:ac WHERE public."Client"."userId"=:id';

    //           $stmt = $this->conn->prepare($sqlQuery);

    //           $stmt->bindParam(':id', $this->mid);
    //           $stmt->bindParam(':ac', $this->amount);
    //           $stmt->execute();
    //         }



    return true;
  }

  public function createWithdraw()
  {

    $charges = 0;

    $cid = 0;

    // update cash or bank account balance used to deposit
    if ($this->pay_method == 'cash') {
      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->cash_acc);
      $stmt->bindParam(':amount', $this->amount);
      $stmt->execute();
    } else if ($this->pay_method == 'cheque') {
      // update account balance
      $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':id', $this->bacid);
      $stmt->bindParam(':amount', $this->amount);
      $stmt->execute();
    }

    //  get bank id from branch
    $sqlQuery = 'select "bankId" from  public."Branch" where id=:id';
    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->_branch);
    $stmt->execute();
    $branch = $stmt->fetch();

    // check whether branch -- bank has deposit charges 
    $sqlQuery = 'SELECT * from  public."transaction_charges" LEFT JOIN public."Account" ON public."transaction_charges".c_id=public."Account".txn_charge  where bankid=:id and c_status=1 AND c_application=:appln ';
    $appln = 'withdraw';
    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $branch['bankId']);
    $stmt->bindParam(':appln', $appln);
    $stmt->execute();

    $count = $stmt->rowCount();

    if (
      $count > 0
    ) {


      foreach ($stmt as $use) {
        if ($use['c_type'] == 'general') {
          if ($use['charge_mode'] == 'fixed') {
            $charges = $use['charge'];
          } else {
            $charges = ($use['charge'] / 100) * $this->amount;
          }

          $cid = $use['id'];
        } else {
          if (($use['min_amount'] <= $this->amount) && ($this->amount <= $use['max_amount'])) {
            if ($use['charge_mode'] == 'fixed') {
              $charges = $use['charge'];
            } else {
              $charges = ($use['charge'] / 100) * $this->amount;
            }
            $cid = $use['id'];
          }
        }
      }
    }

    // get client names,left balance
    $sqlQuery = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
LEFT JOIN public."savingaccounts" ON public."Client".actype = public."savingaccounts".id
 WHERE public."Client"."userId"=:id';

    $stmt = $this->conn->prepare($sqlQuery);

    $stmt->bindParam(':id', $this->mid);
    $stmt->execute();
    $row = $stmt->fetch();

    if (($row['acc_balance'] - $this->amount) >= $row['min_balance']) {
      $aid = 0;
      if ($row['membership_no'] > 0) {
        $this->left_balance = $row['acc_balance'] - $this->amount;


        $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".said=:id ORDER BY "createdAt" ASC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);
        // $atyp = 'EXPENSES';
        $stmt->bindParam(':id', $row['actype']);
        // $stmt->bindParam(':atype', $atyp);
        $stmt->execute();
        $rown  = $stmt->fetch();
        $aid = $rown['id'] ?? null;

        if ($aid) {
          // update account balance
          $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

          $stmt = $this->conn->prepare($sqlQuery);

          $stmt->bindParam(':id', $aid);
          $stmt->bindParam(':amount', $this->amount);
          $stmt->execute();
        }
      } else {
        $this->left_balance = $row['loan_wallet'] - $this->amount;
      }
      $this->acc_name = $row['firstName'] . ' ' . $row['lastName'];
      $this->t_type = 'W';

      $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created,charges,verify) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created,:charges,:verify)';

      $stmt = $this->conn->prepare($sqlQuery);

      $stmt->bindParam(':amount', $this->amount);
      $stmt->bindParam(':verify', $this->is_verified);
      $stmt->bindParam(':descri', $this->description);
      $stmt->bindParam(':autho', $this->_authorizedby);
      $stmt->bindParam(':actby', $this->_actionby);
      $stmt->bindParam(':accname', $this->acc_name);
      $stmt->bindParam(':mid', $this->mid);
      $stmt->bindParam(':approv', $this->_authorizedby);
      $stmt->bindParam(':branc', $this->_branch);
      $stmt->bindParam(':leftbal', $this->left_balance);
      $stmt->bindParam(':ttype', $this->t_type);
      $stmt->bindParam(':acid', $aid);
      $stmt->bindParam(':pay_method', $this->pay_method);
      $stmt->bindParam(':bacid', $this->bacid);
      $stmt->bindParam(':cheque', $this->cheque_no);
      $stmt->bindParam(':cash_acc', $this->cash_acc);
      // $stmt->bindParam(':send_sms', $this->send_sms);
      $stmt->bindParam(':date_created', $this->date_created);
      $stmt->bindParam(':charges', $charges);


      $stmt->execute();

      $this->lid = $this->conn->lastInsertId();

      if (
        $charges > 0
      ) {
        $this->t_type = 'I';
        $desc = 'Withdraw Fees';
        $pmethod = 'saving';
        $this->left_balance = $this->left_balance - $charges;

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,date_created,charges) VALUES
          (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:date_created,:charges)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $charges);
        $stmt->bindParam(':descri', $desc);
        $stmt->bindParam(':autho', $this->_authorizedby);
        $stmt->bindParam(':actby', $this->_actionby);
        $stmt->bindParam(':accname', $this->acc_name);
        $stmt->bindParam(':mid', $this->mid);
        $stmt->bindParam(':approv', $this->_authorizedby);
        $stmt->bindParam(':branc', $this->_branch);
        $stmt->bindParam(':leftbal', $this->left_balance);
        $stmt->bindParam(':ttype', $this->t_type);
        $stmt->bindParam(':acid', $cid);
        $stmt->bindParam(':pay_method', $pmethod);
        $stmt->bindParam(':bacid', $this->bacid);
        $stmt->bindParam(':cheque', $this->cheque_no);
        // $stmt->bindParam(':cash_acc', $this->cash_acc);
        // $stmt->bindParam(':send_sms', $this->send_sms);
        $stmt->bindParam(':date_created', $this->date_created);
        $stmt->bindParam(':charges', $charges);


        $stmt->execute();


        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id AND "branchId"=:bid';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $cid);
        $stmt->bindParam(':bid', $this->_branch);
        $stmt->bindParam(':amount', $charges);
        $stmt->execute();
      }


      if ($row['membership_no'] > 0) {
        $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac WHERE public."Client"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $amount_subtract = $this->amount + $charges;
        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':ac', $amount_subtract);
        $stmt->execute();
      } else {
        $sqlQuery = 'UPDATE public."Client" SET loan_wallet=loan_wallet-:ac WHERE public."Client"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $amount_subtract = $this->amount + $charges;
        $stmt->bindParam(':id', $this->mid);
        $stmt->bindParam(':ac', $amount_subtract);
        $stmt->execute();
      }
      return $this->lid;
    }

    return 0;
  }
  public function getByTransactionReference($id)
  {
    $sqlQuery = 'SELECT * FROM transactions WHERE trxn_ref=:trxn_ref ';
    $stmt = $this->conn->prepare($sqlQuery);
    $stmt->bindParam(':trxn_ref', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
