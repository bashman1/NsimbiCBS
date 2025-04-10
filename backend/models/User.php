<?php
require_once('AuditTrail.php');
require_once('Fee.php');
require_once __DIR__ . '../../config/DbHandler.php';
class User
{
    // DB stuff
    private $conn;
    private $db_table = 'User';

    //   table columns
    public $firstName;
    public $is_supervisor;
    public $supervisor_level;
    public $supervisor_bankid;
    public $id;
    public $lastName;
    public $region;
    public $email;
    public $income;
    public $education_level;
    public $password;
    public $gender;
    public $country;
    public $addressLine1;
    public $addressLine2;
    public $village;
    public $marital;
    public $parish;
    public $subcounty;
    public $district;
    public $primaryCellPhone;
    public $secondaryCellPhone;
    public $dateOfBirth;
    public $notes;
    public $is_registered;
    public $confirmed;
    public $spouseName;
    public $spouseCell;
    public $createdAt;
    public $updatedAt;
    public $deletedAt;
    public $status;
    public $nin;
    public $by_tid;
    public $by_date;
    public $spouseNin;
    public $userId;
    public $branchId;
    public $bankId;
    public $roleId;
    public $positionTitle;
    public $serialNumber;
    public $identificationNumber;
    public $freezed_amount;
    public $loan_wallet;
    public $savings_officer_id;
    public $membership_fee;
    public $old_membership_no;
    public $other_attachments;
    public $message_consent;
    public $sign;
    public $profilePhoto;
    public $actype;
    public $account_id;
    public $fingerprint;
    public $bname;
    public $baddress;
    public $baddress2;
    public $bcity;
    public $bcountry;
    public $bregno;
    public $btype;
    public $registration_status;
    public $krelationship;
    public $kaddress;
    public $clientId;
    public $mno;
    public $profession;
    public $details;
    public $acc_balance;
    public $sid;
    public $entry_chanel;
    public $entered_by;

    public $occupation_type_id;
    public $business_type;
    public $business_type_other;
    public $client_type;
    public $business_nature_description;
    public $name;
    public $otherCellPhone;
    public $number_of_members;
    public $sms_phone_numbers;
    public $is_data_importer_client;

    public $disability_desc;
    public $disability_status;
    public $disability_cat;
    public $disability_others;


    public $occupation_category;
    public $occupation_sub_category;
    public $occupation_sector;
    public $other_cat;
    public $other_sub_cat;
    public $other_sect;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function updateClientPassword()
    {
        $sqlQuery = 'UPDATE public."Client" SET 
        mpin=:passx  WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':passx', $this->password);

        $stmt->bindParam(':id', $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function createInterBranchTransfer()
    {
        // sanitize amount to remove commas

        $amount = str_replace(",", "", $this->details['amount']);

        // create interbranch request trxn with status 0 ---pending

        $sqlQuery = 'INSERT INTO public.inter_branch_requests(
             req_amount, from_branch, to_branch, req_pay_mode, date_created)
            VALUES (:amount,:fr,:tr,:pmode,:datec)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':fr', $this->details['fr']);
        $stmt->bindParam(':tr', $this->details['tr']);
        $stmt->bindParam(':pmode', $this->details['payment_mode']);
        $stmt->bindParam(':datec', $this->details['trxn_date']);

        $stmt->execute();

        return true;
    }

    public function getClientNames($id)
    {

        $clientQuery = 'SELECT * FROM public."User" LEFT JOIN public."Client" ON public."User".id=public."Client"."userId" WHERE public."Client"."userId"=:id ';
        $client = $this->conn->prepare($clientQuery);
        $client->bindParam(':id', $id);
        $client->execute();
        $row = $client->fetchAll();
        return $row['firstName'] . ' ' . $row['lastName'] . ' ' . $row['shared_name'];
    }

    public function getStaffAccNames($id)
    {

        $clientQuery = 'SELECT * FROM public."User"  WHERE public."User".id=:id ';
        $client = $this->conn->prepare($clientQuery);
        $client->bindParam(':id', $id);
        $client->execute();
        $row = $client->fetchAll();
        return $row['firstName'] . ' ' . $row['lastName'] . ' ( ' . $id . ' )';
    }


    public function getClientDetails2($id)
    {

        $clientQuery = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."User".id=public."Client"."userId" WHERE public."Client"."userId"=:id ';
        $client = $this->conn->prepare($clientQuery);
        $client->bindParam(':id', $id);
        $client->execute();
        // return true;
        return $client->fetch(PDO::FETCH_ASSOC);
    }
    public function getShareAmountDetails($uid)
    {
        $sqlQuery = 'SELECT share_amount FROM public."share_register"  WHERE "share_register".userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $uid);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            return $row['share_amount'];
        }
        return 0;
    }
    public function getShareDetails($uid)
    {
        $sqlQuery = 'SELECT no_shares FROM public."share_register"  WHERE "share_register".userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $uid);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            return $row['no_shares'];
        }
        return 0;
    }
    public function getPortalClientDetails()
    {
        $sqlQuery = 'SELECT *, public."Branch".id AS branc, public."Branch"."bankId" AS bid FROM public."Client" LEFT JOIN public."User" ON public."User".id=public."Client"."userId" LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id WHERE "Client"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);


        $stmt->execute();
        return $stmt;
    }
    public function createCashTransfer()
    {
        // sanitize amount to remove commas

        $amount = str_replace(",", "", $this->details['amount']);

        // update balances of both accounts


        // update sender balance

        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id AND balance>=:amount';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['sender']);
        $stmt->bindParam(':amount', $amount);

        if ($stmt->execute()) {
            // update receiver balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['receiver']);
            $stmt->bindParam(':amount', $amount);

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
STT,TTS,TTT,BTS,STB,BTB,IBR,BRTBR
        dr_acid = the sender
        cr_acid = the receiver
        */
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, cr_acid,dr_acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:drid)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = 0;
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['trxndate']);
            $stmt->bindParam(':ttype', $this->details['ttype']);
            $stmt->bindParam(':descri', $this->details['notes']);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);
            $stmt->bindParam(':crid', $this->details['receiver']);
            $stmt->bindParam(':drid', $this->details['sender']);

            $stmt->execute();



            // insert into audit trail

            // $auditTrail = new AuditTrail($this->conn);
            // $auditTrail->type = $this->details['notes'];
            // $auditTrail->staff_id = $this->details['user'];
            // $auditTrail->bank_id = $this->details['bank'];
            // $auditTrail->branch_id = $this->details['branch'];

            // $auditTrail->log_message = 'Cash Transfer from: ' . $this->details['sender'] . ' to ' . $this->details['receiver'];
            // $auditTrail->create();


            return true;
        }



        return false;
    }

    public function advancedJournalEntry()
    {
        // sanitize amount to remove commas

        $amount = str_replace(",", "", $this->details['amount']);

        // update balances of both accounts


        // update sender balance

        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id AND balance>=:amount';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['debit_account']);
        $stmt->bindParam(':amount', $amount);

        if ($stmt->execute()) {
            // update receiver balance

            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['credit_account']);
            $stmt->bindParam(':amount', $amount);

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
            AJE - ADVANCED JOURNAL ENTRY
        }

        dr_acid = the sender
        cr_acid = the receiver
        */
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, cr_acid,dr_acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:drid)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = 0;
            $ttype = 'AJE';
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['date_of_p']);
            $stmt->bindParam(':ttype', $ttype);
            $stmt->bindParam(':descri', $this->details['heading']);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);
            $stmt->bindParam(':crid', $this->details['credit_account']);
            $stmt->bindParam(':drid', $this->details['debit_account']);

            $stmt->execute();



            // insert into audit trail

            $auditTrail = new AuditTrail($this->conn);
            $auditTrail->type = $this->details['heading'];
            $auditTrail->staff_id = $this->details['user'];
            $auditTrail->bank_id = $this->details['bank'];
            $auditTrail->branch_id = $this->details['branch'];

            $auditTrail->log_message = 'Advanced Journal Entry from: ' . $this->details['debit_account'] . ' to ' . $this->details['credit_account'];
            $auditTrail->create();


            return true;
        }



        return false;
    }

    public function createOverDraftProduct()
    {

        $sqlQuery = 'SELECT * FROM  public."Branch" WHERE id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details['branch']);
        $stmt->execute();
        $row = $stmt->fetch();

        $bid = $row['bankId'];

        $sqlQuery = 'INSERT INTO public.over_draft_products(
	 odpname, max_amount, max_period, penalty_value, penalty_type, penalty_grace_period_days, interest_value, interest_type, period_type, affected_interest_acc, affected_penalty_acc, bankid, withdraw_allowance_period_days)
	VALUES (:name, :max_amount, :max_period, :penalty_value, :penalty_type, :penalty_grace_period_days, :interest_value, :interest_type, :period_type, :affected_interest_acc, :affected_penalty_acc, :bankid, :withdraw_allowance_period_days)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':name', $this->details['name']);
        $stmt->bindParam(':max_amount', $this->details['max_amt']);
        $stmt->bindParam(':max_period', $this->details['max_period']);
        $stmt->bindParam(':penalty_rate', $this->details['penalty_rate']);
        $stmt->bindParam(':penalty_type', $this->details['penalty_type']);
        $stmt->bindParam(':penalty_grace_period_days', $this->details['p_grace_period']);
        $stmt->bindParam(':interest_value', $this->details['charge']);
        $stmt->bindParam(':interest_type', $this->details['charge_type']);
        $stmt->bindParam(':affected_interest_acc', $this->details['interest_income_acc']);
        $stmt->bindParam(':affected_penalty_acc', $this->details['penalty_income_acc']);
        $stmt->bindParam(':bankid', $bid);
        $stmt->bindParam(':withdraw_allowance_period_days', $this->details['withdraw_allowance_period']);
        $stmt->bindParam(':period_type', $this->details['freq']);

        $stmt->execute();

        return true;
    }

    public function createOverDraft()
    {
        $amount = str_replace(",", "", $this->details['amount']);


        $sqlQuery = 'UPDATE public."Client" SET over_draft=over_draft+:amount, acc_balance=acc_balance+:amount WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['client']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['main_acc']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();



        $descri = 'Over-Draft ( of ' . $this->details['period'] . ' Days) Disbursement ' . @$this->details['comment'];

        $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch,pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pmethod, :acid)
            ';

        $stmt = $this->conn->prepare($sqlQuery);
        $mid = $this->details['client'];
        $ty = 'D';
        $pm = 'saving';
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':dc', $this->details['record_date']);
        $stmt->bindParam(':ttype', $ty);
        $stmt->bindParam(':pmethod', $pm);
        $stmt->bindParam(':descri', $descri);
        $stmt->bindParam(':_auth', $this->details['user']);
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':acid', $this->details['main_acc']);
        $stmt->bindParam(':apby', $this->details['user']);
        $stmt->bindParam(':bran', $this->details['branch']);

        $stmt->execute();

        $crid = 'debit';

        $sqlQuery = 'INSERT INTO public.over_drafts(
	 uid, amount, duration, duration_type, product, branch, trxn_date, notes, approval_date, status, authby,daily_rate,acc_id_affected,income_acid)
	VALUES (:uidd, :amount, :duration, :duration_type, :product,:branch, :trxn_date, :notes, :approval_date, :status, :authby,:daily_rate,:acc_id_affected,:income_acid)
            ';

        $stmt = $this->conn->prepare($sqlQuery);
        $pm = 'days';
        $product = 0;
        $st = 1;
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':uidd', $this->details['client']);
        $stmt->bindParam(':daily_rate', $this->details['daily_rate']);
        $stmt->bindParam(':duration', $this->details['period']);
        $stmt->bindParam(':duration_type', $pm);
        $stmt->bindParam(':product', $product);
        $stmt->bindParam(':branch', $this->details['branch']);
        $stmt->bindParam(':trxn_date', $this->details['record_date']);
        $stmt->bindParam(':notes', $this->details['comments']);
        $stmt->bindParam(':approval_date', $this->details['record_date']);
        $stmt->bindParam(':authby', $this->details['user']);
        $stmt->bindParam(':acc_id_affected', $this->details['main_acc']);
        $stmt->bindParam(':income_acid', $this->details['income_acid']);
        $stmt->bindParam(':status', $st);

        $stmt->execute();

        return true;
    }
    public function registerFixedDeposit()
    {
        $amount = str_replace(",", "", $this->details['amount']);
        $acc_used = 0;
        // check cash , bank , or savings account balance
        if ($this->details['pay_method'] == 'cash' || $this->details['pay_method'] == 'cheque' || $this->details['pay_method'] == 'mobile') {
            $acc_used = 0;
            if ($this->details['pay_method'] == 'cash') {
                $acc_used = $this->details['cash_acc'];
            }
            if ($this->details['pay_method'] == 'cheque' || $this->details['mobile']) {
                $acc_used = $this->details['bank_acc'];
            }

            $sqlQuery = 'SELECT * FROM public."Account"  WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acc_used);
            $stmt->execute();

            $rown = $stmt->fetch();

            $used_balance = $rown['balance'] ?? 0;
            if ($used_balance < $amount) {
                return false;
            } else {

                // update acc balance and reduce them before continuing
                $sqlQuery = 'UPDATE  public."Account" SET balance=balance-:bal  WHERE public."Account".id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $acc_used);
                $stmt->bindParam(
                    ':bal',
                    $amount
                );
                $stmt->execute();
            }
        }


        // deduct money from savings if it's offset from savings ---payment method
        if ($this->details['pay_method'] == 'saving') {

            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->details['client']);
            $stmt->bindParam(':ac',  $amount);
            $stmt->execute();


            $tt_type = 'W';
            $descri = 'Fixed Deposit Using Savings';

            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount',  $amount);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':autho', $this->details['user']);
            $stmt->bindParam(':actby', $this->details['client']);
            $stmt->bindParam(':accname', $this->details['client']);
            $stmt->bindParam(':mid', $this->details['client']);
            $stmt->bindParam(':approv', $this->details['user']);
            $stmt->bindParam(':branc', $this->details['branch']);
            $stmt->bindParam(':ttype', $tt_type);
            $stmt->bindParam(':pay_method', $this->details['pay_method']);
            $stmt->bindParam(':date_created', $this->details['record_date']);


            $stmt->execute();
        }

        $maturity_date = '';

        if ($this->details['period_type'] == 'm') {
            $maturity_date = date('Y-m-d', strtotime("+" . $this->details['fd_period'] . " months", strtotime($this->details['record_date'])));
        } else if ($this->details['period_type'] == 'd') {
            $maturity_date = date('Y-m-d', strtotime("+" . $this->details['fd_period'] . " days", strtotime($this->details['record_date'])));
        } else if ($this->details['period_type'] == 'y') {
            $maturity_date = date('Y-m-d', strtotime("+" . $this->details['fd_period'] . " years", strtotime($this->details['record_date'])));
        }

        //  insert into fixed_deposits
        $sqlQuery = 'INSERT INTO public.fixed_deposits(
	 user_id, mem_no, fd_amount, auto_pay, auto_close, fd_date, fd_duration, duration_type, int_rate, wht, compound_freq, fd_branch, fd_notes, fd_maturity_date,fd_acc)
	VALUES (:uid, :mno, :amount, :auto_pay, :auto_close, :fd_date, :dur, :dur_type, :int_rate, :wht, :freq, :branch, :notes, :matur,:fd_acc)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':uid', $this->details['client']);
        $stmt->bindParam(':fd_acc', $acc_used);
        $stmt->bindParam(':mno', $this->details['mno']);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':auto_pay', $this->details['auto_payments']);
        $stmt->bindParam(':auto_close', $this->details['auto_close']);
        $stmt->bindParam(':fd_date', $this->details['record_date']);
        $stmt->bindParam(':dur', $this->details['fd_period']);
        $stmt->bindParam(':dur_type', $this->details['period_type']);
        $stmt->bindParam(':int_rate', $this->details['rate']);
        $stmt->bindParam(':wht', $this->details['wht']);
        $stmt->bindParam(':freq', $this->details['freq']);
        $stmt->bindParam(':branch', $this->details['branch']);
        $stmt->bindParam(':notes', $this->details['comment']);
        // $stmt->bindParam(':acc', $this->details['lid']);
        $stmt->bindParam(':matur', $maturity_date);
        $stmt->execute();
        // generate interest distribution schedule & insert it


        // update balance of affected accounts --- cash ---bank --- savings  & the general fixed deposit a/c

        $sqlQuery = 'SELECT * FROM  public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details['branch']);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row['fd_princ_acid']) {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['fd_princ_acid']);
            $stmt->bindParam(':ac',  $amount);
            $stmt->execute();
        }


        return true;
    }
    public function updateInterestChartAcc($amount, $wht, $bid)
    {
        $sqlQuery = 'SELECT * FROM  public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $bid);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row['fd_int_acid']) {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['fd_princ_acid']);
            $stmt->bindParam(':ac',  $amount);
            $stmt->execute();
        }
        if ($row['fd_wht_acid']) {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['fd_princ_acid']);
            $stmt->bindParam(':ac',  $wht);
            $stmt->execute();
        }
        return true;
    }

    public function updateFixedDeposit()
    {
        $amount = str_replace(",", "", $this->details['amount']);

        $maturity_date = '';

        if ($this->details['period_type'] == 'm') {
            $maturity_date = date('Y-m-d', strtotime("+" . $this->details['fd_period'] . " months", strtotime($this->details['record_date'])));
        } else if ($this->details['period_type'] == 'd') {
            $maturity_date = date('Y-m-d', strtotime("+" . $this->details['fd_period'] . " days", strtotime($this->details['record_date'])));
        } else if ($this->details['period_type'] == 'y') {
            $maturity_date = date('Y-m-d', strtotime("+" . $this->details['fd_period'] . " years", strtotime($this->details['record_date'])));
        }

        //  insert into fixed_deposits
        $sqlQuery = 'UPDATE public.fixed_deposits SET 
	  fd_amount=:amount, auto_pay=:auto_pay, auto_close=:auto_close, fd_date=:fd_date, fd_duration=:dur, duration_type=:dur_type, int_rate=:int_rate, wht=:wht, compound_freq=:freq, fd_branch=:branch, fd_notes=:notes, fd_maturity_date=:matur WHERE fd_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['fid']);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':auto_pay', $this->details['auto_payments']);
        $stmt->bindParam(':auto_close', $this->details['auto_close']);
        $stmt->bindParam(':fd_date', $this->details['record_date']);
        $stmt->bindParam(':dur', $this->details['fd_period']);
        $stmt->bindParam(':dur_type', $this->details['period_type']);
        $stmt->bindParam(':int_rate', $this->details['rate']);
        $stmt->bindParam(':wht', $this->details['wht']);
        $stmt->bindParam(':freq', $this->details['freq']);
        $stmt->bindParam(':branch', $this->details['branch']);
        $stmt->bindParam(':notes', $this->details['comment']);
        // $stmt->bindParam(':acc', $this->details['lid']);
        $stmt->bindParam(':matur', $maturity_date);
        $stmt->execute();


        if ($this->details['old_amount'] != $amount) {
            $ch_amount = 0;
            $pay_meth = 'saving';
            if ($this->details['old_amount'] > $amount) {
                $ch_amount = $this->details['old_amount'] - $amount;
                // create reversal deposit trxn
                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $this->details['client']);
                $stmt->bindParam(':ac',  $ch_amount);
                $stmt->execute();


                $tt_type = 'D';
                $descri = 'Fixed Deposit Via Savings - Excess Reversal after Update';


                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created)';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount',  $ch_amount);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':autho', $this->details['user']);
                $stmt->bindParam(':actby', $this->details['client']);
                $stmt->bindParam(':accname', $this->details['client']);
                $stmt->bindParam(':mid', $this->details['client']);
                $stmt->bindParam(':approv', $this->details['user']);
                $stmt->bindParam(':branc', $this->details['branch']);
                $stmt->bindParam(':ttype', $tt_type);
                $stmt->bindParam(':pay_method', $pay_meth);
                $stmt->bindParam(':date_created', $this->details['record_date']);


                $stmt->execute();

                //  TODO: check if amount is greater or less than old amount and update fixed_deposit a/c on trial balance 
                $sqlQuery = 'SELECT * FROM  public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $this->details['branch']);
                $stmt->execute();
                $row = $stmt->fetch();

                if ($row['fd_princ_acid']) {
                    $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $row['fd_princ_acid']);
                    $stmt->bindParam(':ac',  $ch_amount);
                    $stmt->execute();
                }
            }
            if ($this->details['old_amount'] < $amount) {
                $ch_amount =  $amount - $this->details['old_amount'];
                // create another withdraw trxn

                $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac WHERE public."Client"."userId"=:id';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $this->details['client']);
                $stmt->bindParam(':ac',  $ch_amount);
                $stmt->execute();


                $tt_type = 'W';
                $descri = 'Fixed Deposit Using Savings';


                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created)';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount',  $ch_amount);
                $stmt->bindParam(':descri', $descri);
                $stmt->bindParam(':autho', $this->details['user']);
                $stmt->bindParam(':actby', $this->details['client']);
                $stmt->bindParam(':accname', $this->details['client']);
                $stmt->bindParam(':mid', $this->details['client']);
                $stmt->bindParam(':approv', $this->details['user']);
                $stmt->bindParam(':branc', $this->details['branch']);
                $stmt->bindParam(':ttype', $tt_type);
                $stmt->bindParam(':pay_method', $pay_meth);
                $stmt->bindParam(':date_created', $this->details['record_date']);


                $stmt->execute();


                //  TODO: check if amount is greater or less than old amount and update fixed_deposit a/c on trial balance 
                $sqlQuery = 'SELECT * FROM  public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $this->details['branch']);
                $stmt->execute();
                $row = $stmt->fetch();

                if ($row['fd_princ_acid']) {
                    $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $row['fd_princ_acid']);
                    $stmt->bindParam(':ac',  $ch_amount);
                    $stmt->execute();
                }
            }
        }


        return true;
    }

    public function updateCreditOfficer()
    {

        $sqlQuery = 'UPDATE public."loan" SET  loan_officer=:lo , officer_change_reason=:reas  WHERE loan_no=:lno';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':lo', $this->details['officer']);
        $stmt->bindParam(':reas', $this->details['reason']);
        $stmt->bindParam(':lno', $this->details['lid']);
        $stmt->execute();
        return true;
    }


    public function updateLoanBranch()
    {

        $sqlQuery = 'UPDATE public."loan" SET  branchid=:lo , branch_change_reason=:reas  WHERE loan_no=:lno';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':lo', $this->details['officer']);
        $stmt->bindParam(':reas', $this->details['reason']);
        $stmt->bindParam(':lno', $this->details['lid']);
        $stmt->execute();
        return true;
    }

    public function addGroupMember()
    {

        $is_mem = $this->details['field_ac'] ?? 0;
        $sqlQuery = 'INSERT INTO public.group_members(
	 gm_name, gm_phone, gm_role, gm_uid, gm_address, gm_is_member)
	VALUES (:name,:phone,:roles,:uidd,:address,:is_member)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':name', $this->details['field_name']);
        $stmt->bindParam(':phone', $this->details['field_contact']);
        $stmt->bindParam(':roles', $this->details['field_role']);
        $stmt->bindParam(':uidd', $this->details['gid']);
        $stmt->bindParam(':address', $this->details['field_address']);
        $stmt->bindParam(':is_member', $is_mem);
        $stmt->execute();
        return true;
    }

    public function updateScheduleDate()
    {
        $sqlQuery = 'UPDATE public."loan_schedule" SET date_of_payment=:dop, edited_date=:ed  WHERE schedule_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $dop = date('Y-m-d', strtotime($this->details['sdate']));
        $ed = date('Y-m-d', strtotime($this->details['date_orig']));
        $stmt->bindParam(':id', $this->details['sid']);
        $stmt->bindParam(':dop', $dop);
        $stmt->bindParam(':ed', $ed);

        $stmt->execute();
        return true;
    }
    public function updateLoanSavingAcc()
    {
        $sqlQuery = 'UPDATE public."loan" SET account_id=:acid  WHERE loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['id']);
        $stmt->bindParam(':acid', $this->details['clientacc']);

        $stmt->execute();

        $sqlQuery = 'UPDATE public."transactions" SET mid=:acid  WHERE loan_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['id']);
        $stmt->bindParam(':acid', $this->details['clientacc']);

        $stmt->execute();
        return true;
    }

    public function updateImporterLoanSavingAcc()
    {
        $sqlQuery = 'UPDATE public."data_importer_loan_batch_records" SET client_id=:acid  WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['id']);
        $stmt->bindParam(':acid', $this->details['clientacc']);

        $stmt->execute();

        // $sqlQuery = 'UPDATE public."transactions" SET mid=:acid  WHERE loan_id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $this->details['id']);
        // $stmt->bindParam(':acid', $this->details['clientacc']);

        // $stmt->execute();
        return true;
    }

    public function UpdateClientDetails()
    {
        /**
         * check if client exists
         */
        $this->clientId = $this->details['cid'];
        $client = $this->getClientDetails()->fetch(PDO::FETCH_ASSOC);
        if (!@$client) return false;
        $message_consent = $this->details['message'] ?? $client['message_consent'];
        /**
         * update user
         */
        $sqlQuery = 'UPDATE public."User" SET 
        "firstName"=:fname,"lastName"=:lname,"email"=:email,"gender"=:gender,"country"=:country,"addressLine1"=:address1,"addressLine2"=:address2,
        "village"=:village,"parish"=:parish,notes=:notes,shared_name=:shared_name,
        "subcounty"=:subcounty,"district"=:district,profession=:prof,kaddress=:kaddress,krelationship=:krelationship,
        "primaryCellPhone"=:phone,"secondaryCellPhone"=:other_phone,"dateOfBirth"=:dob,"spouseName"=:sname,"spouseCell"=:sphone,"nin"=:nin,"spouseNin"=:snin,sms_phone_numbers=:sms_phone_numbers,entered_by=:saving_officer, marital_status=:marital, region=:region, expected_income=:income, occupation_category=:oc,occupation_sub_category=:osc, occupation_sector=:os, disability_status=:disability_status,disability_cat=:disability_cat,disability_other=:disability_other,disability_desc=:disability_desc
         WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $sms_phone_numbers = [];
        $marital_staus = '';

        if ($client['client_type'] == 'individual') {
            if (@$this->details["primaryCellPhone"]) {
                $sms_phone_numbers = [$this->details["primaryCellPhone"]];
            } else if (@$this->details["secondaryCellPhone"]) {
                $sms_phone_numbers = [$this->details["secondaryCellPhone"]];
            }
            $marital_staus = $this->details['marital'];
        } else {
            if (@$this->details["phone_1_send_sms"] && @$this->details["primaryCellPhone"]) {
                array_push($sms_phone_numbers, $this->details["primaryCellPhone"]);
            }

            if (@$this->details["phone_2_send_sms"] && @$this->details["secondaryCellPhone"]) {
                array_push($sms_phone_numbers, $this->details["secondaryCellPhone"]);
            }

            if (@$this->details["phone_3_send_sms"] && @$this->details["otherCellPhone"]) {
                array_push($sms_phone_numbers, $this->details["otherCellPhone"]);
            }

            $marital_staus = '';
        }


        if ($this->details['message'] == "0") {
            $sms_phone_numbers = [];
        }

        $sms_phone_numbers = json_encode($sms_phone_numbers);

        if (@$this->details['name']) {
            $this->details['fname'] = @$this->details['name'];
        }

        $this->details['income']  = str_replace(",", "",   $this->details['income']);

        $stmt->bindParam(':region', $this->details['region']);
        $stmt->bindParam(':disability_other', $this->details['disability_other']);
        $stmt->bindParam(':disability_cat', $this->details['disability_cat']);
        $stmt->bindParam(':disability_status', $this->details['disability_desc']);
        $stmt->bindParam(':disability_desc', $this->details['disability_status']);
        $stmt->bindParam(':income', $this->details['income']);
        $stmt->bindParam(':oc', $this->details['ocategory']);
        $stmt->bindParam(':osc', $this->details['oscategory']);
        $stmt->bindParam(':os', $this->details['ocsector']);

        $stmt->bindParam(':fname', $this->details['fname']);
        $stmt->bindParam(':lname', $this->details['lname']);
        $stmt->bindParam(':email', $this->details['email']);
        $stmt->bindParam(':gender', $this->details['gender']);
        $stmt->bindParam(':country', $this->details['country']);
        $stmt->bindParam(':address1', $this->details['address']);
        $stmt->bindParam(':address2', $this->details['address2']);
        $stmt->bindParam(':village', $this->details['village']);
        $stmt->bindParam(':parish', $this->details['parish']);
        $stmt->bindParam(':subcounty', $this->details['subcounty']);
        $stmt->bindParam(':district', $this->details['district']);
        $stmt->bindParam(':phone', $this->details['phone']);
        $stmt->bindParam(':other_phone', $this->details['other_phone']);
        $stmt->bindParam(':sname', $this->details['kname']);
        $stmt->bindParam(':sphone', $this->details['kphone']);
        $stmt->bindParam(':nin', $this->details['nin']);
        $stmt->bindParam(':snin', $this->details['knin']);
        $stmt->bindParam(':id', $this->details['uid']);
        $stmt->bindParam(':dob', $this->details['dob']);
        $stmt->bindParam(':prof', $this->details['prof']);
        $stmt->bindParam(':kaddress', $this->details['paddress']);
        $stmt->bindParam(':krelationship', $this->details['relationship']);
        $stmt->bindParam(':notes', $this->details['notes']);
        $stmt->bindParam(':shared_name', $this->details['name']);
        $stmt->bindParam(':saving_officer', $this->details['saving_officer']);
        $stmt->bindParam(':sms_phone_numbers', $sms_phone_numbers);
        $stmt->bindParam(':marital', $marital_staus);
        $stmt->execute();

        /**
         * update user as client
         */
        if (@$this->details['mpin'] == '') {
            $this->details['mpin'] = 0;
        }
        $sqlQuery = 'UPDATE public."Client" SET message_consent=:message_consent, "branchId"=:bid, old_membership_no=:mem_no, membership_no=:mem_no WHERE id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':message_consent', $message_consent);
        $stmt->bindParam(':id', $this->details['cid']);
        // $stmt->bindParam(':mpin', $this->details['mpin']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(':mem_no', $this->details['old_mem']);
        $stmt->execute();

        /**
         * update business details
         */

        $db_handler = new DbHandler();
        $business_details = $db_handler->fetch('Business', 'clientId', $this->details['cid']);

        $business_name = @$this->details['bname'] ?? @$this->details['name'];
        $registration_number = @$this->details['businessreg'] ?? null;

        $this->details['is_registered'] = @$this->details['is_registered'] ? true : false;

        $data_array = [
            'name' => $business_name,
            "addressLine1" => $this->details['baddress'],
            "addressLine2" => $this->details['baddress2'],
            "city" => $this->details['businessCity'],
            "country" => @$this->details['businesscountry'],
            "is_registered" => @$this->details['is_registered'],
            "registrationNumber" => $registration_number,
            "registration_status" => @$this->details['is_registered'],
            "clientId" => $this->clientId,
            "business_type_other" => @$this->details['business_type_other'],
            "number_of_members" => (int)@$this->details['number_of_members'],
            "business_type" => @$this->details['business_type'],
            "business_nature_description" => @$this->details['business_nature_description'],
        ];

        if ($business_details) {
            $db_handler->update('Business', $data_array, 'clientId', $this->clientId);
        } else {
            $db_handler->insert('Business', $data_array);
        }

        /* check for change in actype */

        if (@$this->details['current_actype'] != @$this->details['saving_product']) {
            // generate the new account number
            // get client banch id
            $sqlQuery = 'SELECT * FROM  public."Client" WHERE id=:id';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->details['cid']);
            $stmt->execute();
            $row = $stmt->fetch();
            // get account number length && filler character in the account number of the bank
            $getAccValues = $this->getBankAccLength($row['branchId']);


            // separate the return merge separated by / , i.e acc-length and filler character
            $myArray = explode('/', $getAccValues);
            $accLength = (int)$myArray[0];
            $paddValue = $myArray[1];

            // get the saving product code 
            $accCode = $this->getAccountCode($this->details['saving_product']);
            $codelength = strlen($accCode['ucode']);
            $uselength = $accLength - $codelength;

            $rett = $this->details['uid'];

            // generate the account number now
            $take = $rett;
            $padd = sprintf('%' . $paddValue . '' . $uselength . 'd', $take);
            $acc_use_no = $accCode['ucode'] . $padd;
            // update client and set the new acno

            $sqlQuery = 'UPDATE public."Client" SET membership_no=:mno, actype=:actype, old_membership_no=:omno WHERE id=:id';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':mno', $acc_use_no);
            $stmt->bindParam(':actype', $this->details['saving_product']);
            $stmt->bindParam(':id', $this->details['cid']);
            $stmt->bindParam(':omno', $row['membership_no']);


            $stmt->execute();
        }

        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Update Client Details';
        $auditTrail->staff_id = $this->details['auth_id'];
        $auditTrail->bank_id = $this->details['bank_id'];
        $auditTrail->branch_id = $this->details['branch'];

        $auditTrail->log_message = 'Update Client Details for: ' . $this->details['fname'] . ' ' . $this->details['lname'];
        $auditTrail->create();

        return true;
    }

    public function getClientRangeTransactionsFees()
    {
        $rec = 'fees';
        $tt = 'D';
        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id LEFT JOIN public."User" ON 
        public."transactions".mid=public."User".id LEFT JOIN public."Client" ON public."transactions".mid=public."Client"."userId" WHERE public."transactions".t_type=:tt AND  public."transactions"._status=1 AND public."transactions".mid=:id AND public."transactions".trxn_rec=:rec  ORDER BY public."transactions".tid ASC';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':rec', $rec);
        $stmt->bindParam(':tt', $tt);
        // $stmt->bindParam(':start', $this->createdAt);
        // $stmt->bindParam(':end', $this->updatedAt);


        $stmt->execute();
        return $stmt;
    }


    public function getBalance($mid)
    {
        $sqlQuery = 'SELECT acc_balance, loan_wallet FROM public."Client" WHERE public."Client"."userId"=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $mid);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['acc_balance'] + $row['loan_wallet'];
    }
    public function getTotalFeesCollected($uid)
    {
        $rec = 'fees';

        $sqlQueryn = 'SELECT SUM(amount) AS tot FROM public."transactions"  WHERE mid =:id AND trxn_rec=:rec';
        $stmtn = $this->conn->prepare($sqlQueryn);
        $stmtn->bindParam(':id', $uid);
        $stmtn->bindParam(':rec', $rec);
        $stmtn->execute();
        $row = $stmtn->fetch();
        return $row['tot'] ?? 0;
    }


    public function registerPayable()
    {
        $amount  = str_replace(",", "", $this->details['amount']);

        // update account balance foor both credit and debit
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['chartid']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        $sqlQuery = 'UPDATE public."creditors" SET tot_payable=tot_payable+:amount,tot_bal=tot_bal+:amount WHERE cred_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['id']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();



        $acid = 0;

        if ($this->details['pay_method'] == 'cash') {
            $acid = $this->details['cash_acc'];

            // $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            // $stmt = $this->conn->prepare($sqlQuery);

            // $stmt->bindParam(':id', $this->details['cash_acc']);
            // $stmt->bindParam(':amount', $amount);
            // $stmt->execute();
        } else if ($this->details['pay_method'] == 'cheque') {
            $acid = $this->details['bank_acc'];
            // $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            // $stmt = $this->conn->prepare($sqlQuery);

            // $stmt->bindParam(':id', $this->details['bank_acc']);
            // $stmt->bindParam(':amount', $amount);
            // $stmt->execute();
        } else if ($this->details['pay_method'] == 'on_credit') {
            $acid = $this->details['exp_account'];
            // $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            // $stmt = $this->conn->prepare($sqlQuery);

            // $stmt->bindParam(':id', $this->details['exp_account']);
            // $stmt->bindParam(':amount', $amount);
            // $stmt->execute();
        }

        // create payable 
        $sqlQuery = 'INSERT INTO public.payables(
	 p_branch_id, p_creditor, p_descri, p_amount, p_pay_method, pay_method_chart_acc, pay_trxn_date, maturity_date, p_comments)
	VALUES (:bid,:pcred,:pdesc,:amount,:pay_meth,:acid,:trdate,:matdate,:notes)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':pcred', $this->details['id']);
        $stmt->bindParam(':pdesc', $this->details['heading']);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':pay_meth', $this->details['pay_method']);
        $stmt->bindParam(':acid', $acid);
        $stmt->bindParam(':trdate', $this->details['record_date']);
        $stmt->bindParam(':matdate', $this->details['maturity_date']);
        $stmt->bindParam(':notes', $this->details['comment']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->execute();


        // create cash transfer trxn 



        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Registered Payable on Creditor: -' . $this->details['id'];
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['bid'];

        $auditTrail->log_message = 'Registered Payable on Creditor: -' . $this->details['id'] . ' of ' . $amount;
        $auditTrail->create();

        return true;
    }

    public function payReceivable()
    {
        $amount  = str_replace(",", "", $this->details['amount']);

        $sqlQuery = 'SELECT * FROM  public."receivables" WHERE p_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['rid']);
        $stmt->execute();

        $row = $stmt->fetch();

        if ($amount >= $row['p_amount']) {
            // cleared
            $sqlQuery = 'UPDATE  public."receivables" SET pay_status=2,p_amount_paid=p_amount_paid+:amount WHERE p_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['rid']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
        } else {
            // partially paid
            $sqlQuery = 'UPDATE  public."receivables" SET pay_status=1,p_amount_paid=p_amount_paid+:amount WHERE p_id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['rid']);
            $stmt->bindParam(':amount', $amount);

            $stmt->execute();
        }


        // update balance of the detor acid
        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $row['deb_acid']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();


        // update debtor details of balance
        $sqlQuery = 'UPDATE public."debtors" SET tot_receivable=tot_receivable-:amount,tot_bal=tot_bal-:amount WHERE deb_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $row['p_creditor']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        // create payment transaction

        $mid = 0;
        $acid = '';
        if ($this->details['pay_method'] == 'cash') {
            $acid = $this->details['cash_acc'];
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acid);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
            // get acid for the cash account used
        }
        if ($this->details['pay_method'] == 'saving') {
            $mid = $this->details['account_id'];

            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:amount WHERE "userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $mid);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
            $descri = 'Payment of Receivable: - ' . @$this->details['comment'];
            $ttypee = 'W';

            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch,pay_method)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pmethod)
            ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['record_date']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':pmethod', $this->details['pay_method']);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            // $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $row['p_branch_id']);
            // $stmt->bindParam(':crid', $row['deb_acid']);

            $stmt->execute();
        }
        if ($this->details['pay_method'] == 'cheque') {
            $acid = $this->details['bank_acc'];

            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acid);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
        }

        // transaction
        $descri = 'Payment of Receivable: - ' . @$this->details['comment'];
        $ttypee = 'ASS';
        if ($acid) {
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, dr_acid,pay_method, acid,cr_dr)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pmethod, :acid,:cr_dr)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $cr_dr = 'debit';
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['record_date']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':cr_dr', $cr_dr);
            $stmt->bindParam(':pmethod', $this->details['pay_method']);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $row['p_branch_id']);
            $stmt->bindParam(':crid', $row['deb_acid']);

            $stmt->execute();
        } else {
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, dr_acid,pay_method,cr_dr)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pmethod,:cr_dr)
            ';

            $stmt = $this->conn->prepare($sqlQuery);
            $cr_dr = 'debit';
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['record_date']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':cr_dr', $cr_dr);
            $stmt->bindParam(':pmethod', $this->details['pay_method']);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            // $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $row['p_branch_id']);
            $stmt->bindParam(':crid', $row['deb_acid']);

            $stmt->execute();
        }


        return true;
    }


    public function registerReceivable()
    {
        $amount  = str_replace(",", "", $this->details['amount']);

        // update account balance foor both credit and debit
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['chartid']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        $sqlQuery = 'UPDATE public."debtors" SET tot_receivable=tot_receivable+:amount,tot_bal=tot_bal+:amount WHERE deb_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['id']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();



        $acid = '';
        $mid = 0;
        $pm = 'cash';
        $pm_acid = 0;
        $meth =
            $this->details['pay_method'];

        if ($this->details['pay_method'] == 'cash') {
            $acid = $this->details['cash_acc'];
            $pm_acid = $acid;
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acid);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
            // get acid for the cash account used
        }
        if ($this->details['pay_method'] == 'saving') {
            $mid = $this->details['account_id'];
            $pm_acid = $mid;

            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:amount WHERE "userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $mid);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();

            $descri = 'Receivable registered against a debtor: - ' . @$this->details['heading'];
            $ttypee = 'D';

            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch,pay_method)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:pmethod)
            ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $this->details['record_date']);
            $stmt->bindParam(':ttype', $ttypee);
            $stmt->bindParam(':pmethod', $meth);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':_auth', $this->details['user']);
            $stmt->bindParam(':mid', $mid);
            // $stmt->bindParam(':acid', $this->details['chartid']);
            $stmt->bindParam(':apby', $this->details['user']);
            $stmt->bindParam(':bran', $this->details['branch']);
            // $stmt->bindParam(':crid', $acid);

            $stmt->execute();
        }
        if ($this->details['pay_method'] == 'cheque') {
            $acid = $this->details['bank_acc'];
            $pm_acid = $acid;

            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acid);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();
        }

        // create receivable record 
        $sqlQuery = 'INSERT INTO public.receivables(
	 p_branch_id, p_creditor, p_descri, p_amount, p_pay_method, pay_method_chart_acc, pay_trxn_date, maturity_date, p_comments, deb_acid)
	VALUES (:bid,:pcred,:pdesc,:amount,:pay_meth,:acid,:trdate,:matdate,:notes, :deb_acid)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':pcred', $this->details['id']);
        $stmt->bindParam(':deb_acid', $this->details['chartid']);
        $stmt->bindParam(':pdesc', $this->details['heading']);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':pay_meth', $meth);
        $stmt->bindParam(':acid', $pm_acid);
        $stmt->bindParam(':trdate', $this->details['record_date']);
        $stmt->bindParam(':matdate', $this->details['maturity_date']);
        $stmt->bindParam(':notes', $this->details['comment']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->execute();


        // create asset registration for the debtor
        $descri = 'Receivable registered against a debtor: - ' . @$this->details['heading'];
        $ttypee = 'ASS';

        $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, date_created, description, _authorizedby, mid, approvedby, _branch, dr_acid,pay_method, acid)
	VALUES (:amount,:ttype,:dc,:descri,:_auth,:mid,:apby,:bran,:crid,:pmethod, :acid)
            ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':dc', $this->details['record_date']);
        $stmt->bindParam(':ttype', $ttypee);
        $stmt->bindParam(':pmethod', $meth);
        $stmt->bindParam(':descri', $descri);
        $stmt->bindParam(':_auth', $this->details['user']);
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':acid', $this->details['chartid']);
        $stmt->bindParam(':apby', $this->details['user']);
        $stmt->bindParam(':bran', $this->details['branch']);
        $stmt->bindParam(':crid', $acid);

        $stmt->execute();




        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Registered Receivable on Debtor: -' . $this->details['id'];
        $auditTrail->staff_id = @$this->details['user'];
        $auditTrail->bank_id = @$this->details['bank'];
        $auditTrail->branch_id = @$this->details['bid'];

        $auditTrail->log_message = 'Registered Receivable on Debtor: -' . @$this->details['id'] . ' of ' . $amount;
        $auditTrail->create();

        return true;
    }



    public function registerCreditor()
    {

        $acid = 0;
        $chartid = 0;

        if ($this->details['create_chart_account'] == 'yes') {
            // branch payable account
            $sqlQuery = 'INSERT INTO public."Account"(
            type, "branchId",name, description, "isSystemGenerated",is_payable)
           VALUES (:typee,:bid,:nname,:descr,:isgen,:pay )';
            $atype = 'LIABILITIES';
            $nname = strtoupper($this->details['cr_name']) . ' - CREDITOR ACCOUNT';
            $descr = 'This account holds creditor of ' . strtolower($this->details['cr_name']);
            $isgen = true;
            $pay = 1;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':typee', $atype);
            $stmt->bindParam(':bid', $this->details['branch']);
            $stmt->bindParam(':nname', $nname);
            $stmt->bindParam(':descr', $descr);
            $stmt->bindParam(':isgen', $isgen);
            $stmt->bindParam(':pay', $pay);

            $stmt->execute();

            // get created account details since it's uuid
            $sqlQuery = 'SELECT id FROM  public."Account"  WHERE name LIKE :nname AND type=:tt ORDER BY acc_id DESC LIMIT 1';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':tt', $atype);
            $stmt->bindParam(':nname', $nname);
            $stmt->execute();
            $row = $stmt->fetch();

            $chartid = $row['id'];
        } else if ($this->details['create_chart_account'] == 'existing') {
            $chartid = $this->details['account_code'];
        }

        // create payable 
        $sqlQuery = 'INSERT INTO public.creditors(
	 cred_name, cred_chart_acc, tot_payable, tot_paid, tot_bal, cred_desc, branch_id)
	VALUES (:name,:chart,:payab,:paid,:bal,:cdesc,:bid)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':name', $this->details['cr_name']);
        $stmt->bindParam(':chart', $chartid);
        $stmt->bindParam(':payab', $acid);
        $stmt->bindParam(':paid', $acid);
        $stmt->bindParam(':bal', $acid);
        $stmt->bindParam(':cdesc', $this->details['descr']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->execute();




        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Registered Creditor: -' . $this->details['cr_name'];
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['bid'];

        $auditTrail->log_message = 'Registered Creditor: -' . $this->details['cr_name'];
        $auditTrail->create();

        return true;
    }


    public function registerDebtor()
    {

        $acid = 0;
        $chartid = 0;

        if ($this->details['create_chart_account'] == 'yes') {
            // branch payable account
            $sqlQuery = 'INSERT INTO public."Account"(
            type, "branchId",name, description, "isSystemGenerated",is_receivable)
           VALUES (:typee,:bid,:nname,:descr,:isgen,:pay )';
            $atype = 'ASSETS';
            $nname = strtoupper($this->details['cr_name']) . ' - DEBTOR ACCOUNT';
            $descr = 'This account holds debtor of ' . strtolower($this->details['cr_name']);
            $isgen = true;
            $pay = 1;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':typee', $atype);
            $stmt->bindParam(':bid', $this->details['branch']);
            $stmt->bindParam(':nname', $nname);
            $stmt->bindParam(':descr', $descr);
            $stmt->bindParam(':isgen', $isgen);
            $stmt->bindParam(':pay', $pay);

            $stmt->execute();

            // get created account details since it's uuid
            $sqlQuery = 'SELECT id FROM  public."Account"  WHERE name LIKE :nname AND type=:tt ORDER BY acc_id DESC LIMIT 1';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':tt', $atype);
            $stmt->bindParam(':nname', $nname);
            $stmt->execute();
            $row = $stmt->fetch();

            $chartid = $row['id'];
        } else if ($this->details['create_chart_account'] == 'existing') {
            $chartid = $this->details['account_code'];
        }

        // create payable 
        $sqlQuery = 'INSERT INTO public.debtors(
	 deb_name, deb_chart_acc, tot_receivable, tot_paid, tot_bal, deb_desc, branch_id)
	VALUES (:name,:chart,:payab,:paid,:bal,:cdesc,:bid)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':name', $this->details['cr_name']);
        $stmt->bindParam(':chart', $chartid);
        $stmt->bindParam(':payab', $acid);
        $stmt->bindParam(':paid', $acid);
        $stmt->bindParam(':bal', $acid);
        $stmt->bindParam(':cdesc', $this->details['descr']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->execute();




        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Registered Debtor: -' . @$this->details['cr_name'];
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = @$this->details['bank'];
        $auditTrail->branch_id = @$this->details['bid'];

        $auditTrail->log_message = 'Registered Debtor: -' . @$this->details['cr_name'];
        $auditTrail->create();

        return true;
    }

    public function freezeAccount()
    {
        $amount = str_replace(",", "", $this->details['amount']);

        // check if balance  has enough money to freeze
        $sqlQuery = 'SELECT acc_balance FROM  public."Client"  WHERE "userId"=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['client']);
        $stmt->execute();
        $row = $stmt->fetch();

        $amount2 = $amount + 10000;

        if ($row['acc_balance'] >= $amount2) {
            $sqlQuery = 'UPDATE  public."Client"  SET acc_balance=acc_balance-:bal, freezed_amount=freezed_amount+:bal, freeze_reason=:fr, freeze_cat=:fc WHERE "userId"=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['client']);
            $stmt->bindParam(':fr', $this->details['reason']);
            $stmt->bindParam(':fc', $this->details['fr_cat']);
            $stmt->bindParam(':bal', $amount);
            $stmt->execute();



            // insert into audit trail
            $auditTrail = new AuditTrail($this->conn);
            $auditTrail->type = 'Freezed Account Savings Balance: of -' . $this->details['client'];
            $auditTrail->staff_id = $this->details['user'];
            $auditTrail->bank_id = $this->details['bank'];
            $auditTrail->branch_id = $this->details['branch'];

            $auditTrail->log_message = 'Freezed Account Savings Balance: of -' . $this->details['client'];
            $auditTrail->create();

            return true;
        }

        return false;
    }

    public function registerExcess()
    {
        $amount = str_replace(",", "", $this->details['amount']);

        //  add amount to cashier cash account
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['cash_acc']);
        $stmt->bindParam(':ac', $amount);
        $stmt->execute();

        // add excess to excess journal account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['main_acc']);
        $stmt->bindParam(':ac', $amount);
        $stmt->execute();

        // register excess trxn
        $sqlQuery = 'INSERT INTO  public."staff_excess" (staff_id,narration,amount,date_created,_branch,affected_cash_acid,affected_acid) values (:staff,:narr,:amount,:dc,:bid,:cash,:acid)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':staff', $this->details['staff']);
        $stmt->bindParam(':narr', $this->details['heading']);
        $stmt->bindParam(':dc', $this->details['date_of_p']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(':cash', $this->details['cash_acc']);
        $stmt->bindParam(':acid', $this->details['main_acc']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Excess Registered by' . $this->details['user'] . ' for staff ID - ' . $this->details['staff'];
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['branchid'];

        $auditTrail->log_message = 'Excess Registered of Amount -' . $amount . ' for Staff ID - ' . $this->details['staff'];
        $auditTrail->create();
        return true;
    }


    public function registerShortfall()
    {
        $amount = str_replace(",", "", $this->details['amount']);

        //  clear amount from cashier cash account
        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['cash_acc']);
        $stmt->bindParam(':ac', $amount);
        $stmt->execute();

        // add shortfall to shortfall journal account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['main_acc']);
        $stmt->bindParam(':ac', $amount);
        $stmt->execute();

        // register shortfall trxn
        $sqlQuery = 'INSERT INTO  public."staff_shortfalls" (staff_id,narration,amount,date_created,_branch,affected_cash_acid,affected_acid) values (:staff,:narr,:amount,:dc,:bid,:cash,:acid)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':staff', $this->details['staff']);
        $stmt->bindParam(':narr', $this->details['heading']);
        $stmt->bindParam(':dc', $this->details['date_of_p']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(':cash', $this->details['cash_acc']);
        $stmt->bindParam(':acid', $this->details['main_acc']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        // insert into audit trail
        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Shortfall Registered by' . $this->details['user'] . ' for staff ID - ' . $this->details['staff'];
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['branchid'];

        $auditTrail->log_message = 'Shortfall Registered of Amount -' . $amount . ' for Staff ID - ' . $this->details['staff'];
        $auditTrail->create();
        return true;
    }


    public function clearShortfall()
    {
        $amount = str_replace(",", "", $this->details['amount']);

        $sqlQuery = 'SELECT * FROM public."staff_shortfalls" WHERE ss_id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details['ssid']);

        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row  = $stmt->fetch();

            //  clear amount from cashier cash account
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $row['affected_cash_acid']);
            $stmt->bindParam(':ac', $amount);
            $stmt->execute();

            // add shortfall to shortfall journal account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $row['affected_acid']);
            $stmt->bindParam(':ac', $amount);
            $stmt->execute();

            if ($amount == $row['amount']) {
                $sqlQuery = 'UPDATE  public."staff_shortfalls" SET amount_paid=amount_paid+:ac, status=2 WHERE ss_id=:id';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $row['ss_id']);
                $stmt->bindParam(':ac', $amount);

                $stmt->execute();
            } else {
                // update the trxn entry
                $sqlQuery = 'UPDATE  public."staff_shortfalls" SET amount_paid=amount_paid+:ac, status=4 WHERE ss_id=:id';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $row['ss_id']);
                $stmt->bindParam(':ac', $amount);

                $stmt->execute();
            }

            // insert into audit trail
            $auditTrail = new AuditTrail($this->conn);
            $auditTrail->type = 'Shortfall Clearance by' . $this->details['user'] . ' for staff ID - ' . $row['staff_id'];
            $auditTrail->staff_id = $this->details['user'];
            $auditTrail->bank_id = $this->details['bank'];
            $auditTrail->branch_id = $this->details['branchid'];

            $auditTrail->log_message = 'Shortfall Clearance of Amount -' . $amount . ' for Staff ID - ' . $row['staff_id'];
            $auditTrail->create();

            return true;
        }
        return false;
    }




    public function oneToOneTransfer()
    {

        $amount = str_replace(",", "", $this->details['amount']);
        // check if sender has enough money
        $sqlQuery = 'SELECT acc_balance FROM  public."Client"  WHERE "userId"=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['sender']);
        $stmt->execute();
        $row = $stmt->fetch();




        if ($row['acc_balance'] >= $amount) {

            // get sender memeber no
            $sqlQuery = 'SELECT membership_no FROM  public."Client"  WHERE "userId"=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['sender']);
            $stmt->execute();
            $rows = $stmt->fetch();

            $sqlQuery = 'SELECT membership_no FROM  public."Client"  WHERE "userId"=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['receiver']);
            $stmt->execute();
            $rowr = $stmt->fetch();


            // update balances of sender and receiver
            $sqlQuery = 'UPDATE  public."Client" SET acc_balance=acc_balance-:bal  WHERE "userId"=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['sender']);
            $stmt->bindParam(':bal', $amount);
            $stmt->execute();

            $sqlQuery = 'UPDATE  public."Client" SET acc_balance=acc_balance+:bal  WHERE "userId"=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['receiver']);
            $stmt->bindParam(':bal', $amount);
            $stmt->execute();
            // create transfer trxn   --- two trxns for both sending and receiving
            // receiving --- Deposit

            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
    acc_name,mid,approvedby,_branch,left_balance,t_type,transaction_error,charges,is_transfer) VALUES
      (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:charges,:is_transfer)';

            $stmt = $this->conn->prepare($sqlQuery);
            $ttype = 'D';
            $leftbal = 0;
            $is_transfer = 1;
            $descr = 'Transfered from ' . $rows['membership_no'] . ' ( ' . $this->details['reason'] . ' )';
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':descri', $descr);
            $stmt->bindParam(':autho', $this->details['user']);
            $stmt->bindParam(':actby', $this->details['receiver']);
            $stmt->bindParam(':accname', $this->details['receiver']);
            $stmt->bindParam(':mid', $this->details['receiver']);
            $stmt->bindParam(':approv', $this->details['user']);
            $stmt->bindParam(':branc', $this->details['branch']);
            $stmt->bindParam(':leftbal', $leftbal);
            $stmt->bindParam(':ttype', $ttype);
            $stmt->bindParam(':acid', $this->details['sender']);
            $stmt->bindParam(':charges', $leftbal);
            $stmt->bindParam(':is_transfer', $is_transfer);


            $stmt->execute();

            //   sender trxn ---- Withdraw
            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
acc_name,mid,approvedby,_branch,left_balance,t_type,transaction_error,charges,is_transfer) VALUES
  (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:charges,:is_transfer)';

            $stmt = $this->conn->prepare($sqlQuery);
            $ttype = 'W';
            $leftbal = 0;
            $is_transfer = 1;
            $descr = 'Transfered to ' . $rowr['receiver'] . ' ( ' . $this->details['reason'] . ' )';
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':descri', $descr);
            $stmt->bindParam(':autho', $this->details['user']);
            $stmt->bindParam(':actby', $this->details['sender']);
            $stmt->bindParam(':accname', $this->details['sender']);
            $stmt->bindParam(':mid', $this->details['sender']);
            $stmt->bindParam(':approv', $this->details['user']);
            $stmt->bindParam(':branc', $this->details['branch']);
            $stmt->bindParam(':leftbal', $leftbal);
            $stmt->bindParam(':ttype', $ttype);
            $stmt->bindParam(':acid', $this->details['receiver']);
            $stmt->bindParam(':charges', $leftbal);
            $stmt->bindParam(':is_transfer', $is_transfer);


            $stmt->execute();

            // insert into audit trail
            $auditTrail = new AuditTrail($this->conn);
            $auditTrail->type = 'One to One Transfer: from -' . $this->details['sender'] . ' To - ' . $this->details['receiver'] . ' ( ' . $this->details['reason'] . ' )';
            $auditTrail->staff_id = $this->details['user'];
            $auditTrail->bank_id = $this->details['bank'];
            $auditTrail->branch_id = $this->details['branch'];

            $auditTrail->log_message = 'One to One Transfer: from -' . $this->details['sender'] . ' To - ' . $this->details['receiver'] . ' ( ' . $this->details['reason'] . ' )';
            $auditTrail->create();

            return true;
        } else {
            return false;
        }
    }

    public function insertAuditTrail($info)
    {

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = $info['action'];
        $auditTrail->staff_id = $info['uid'];
        $auditTrail->bank_id = $info['bank'];
        $auditTrail->branch_id = $info['branch'];
        $auditTrail->ip_address = $info['ip'];
        $auditTrail->status = $info['status'];

        $auditTrail->log_message = $info['log_desc'];

        // $auditTrail->staff_id = 1;
        // $auditTrail->branch_id = 1;
        $auditTrail->create();

        return true;
    }

    public function UpdateClientStatus()
    {
        $sqlQuery = 'UPDATE public."User" SET status=:st WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details);
        $stmt->bindParam(':st', $this->mno);
        $stmt->execute();
        return true;
    }

    public function deleteCustomerAccount()
    {

        $sqlQuery = 'DELETE FROM  "User"  WHERE id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();

        $sqlQuery = 'DELETE FROM  "Client"  WHERE "userId"=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();

        $sqlQuery = 'DELETE FROM  "transactions"  WHERE mid=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();

        $sqlQuery = 'DELETE FROM  "loan"  WHERE account_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();

        $sqlQuery = 'DELETE FROM  "fixed_deposits"  WHERE user_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();

        $sqlQuery = 'DELETE FROM  "share_purchases"  WHERE user_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();

        $sqlQuery = 'DELETE FROM  "share_register"  WHERE userid=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();

        $sqlQuery = 'DELETE FROM  "share_transfers"  WHERE from_uid=:id OR to_uid=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->details);
        $stmt->execute();
    }

    public function shareTransfer()
    {

        $sqlQuery = 'SELECT * FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['branch']);
        $stmt->execute();

        $row = $stmt->fetch();

        $shareValue = $row['share_value'];

        if ($shareValue <= 0) {
            $shareValue = 1;
        }

        $amount = str_replace(",", "", $this->details['shares']);
        // sub from sender
        $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount-:sa,no_shares=no_shares-:ns WHERE userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $shareAmount = $amount * $shareValue;

        $stmt->bindParam(':id', $this->details['send']);
        $stmt->bindParam(':sa', $shareAmount);
        $stmt->bindParam(':ns', $amount);
        $stmt->execute();

        //  check whether receiver exists -- add to shares -- else create with initial share values set to received
        $sqlQuery = 'SELECT * FROM public."share_register" WHERE userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['receive']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // add to receiver
            $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount+:sa,no_shares=no_shares+:ns WHERE userid=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['receive']);
            $stmt->bindParam(':sa', $shareAmount);
            $stmt->bindParam(':ns', $amount);
            $stmt->execute();
        } else {
            // create share holder
            $sqlQuery = 'INSERT INTO public.share_register(
	 userid, share_amount, no_shares, added_by, branch_id)
	VALUES (:uid,:sa,:ns,:adb,:bid)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':uid', $this->details['receive']);
            $stmt->bindParam(':sa',  $shareAmount);
            $stmt->bindParam(':adb', $this->details['user']);
            $stmt->bindParam(':bid', $this->details['branch']);
            $stmt->bindParam(':ns', $amount);
            $stmt->execute();
        }



        // create share transfer trxn

        $sqlQuery = 'INSERT INTO public.share_transfers(
	from_uid, to_uid, no_shares, current_share_value, record_date, notes, added_by, branch_id, description)
	VALUES (:fid,:tid,:ns,:csv,:trxndate,:notes,:adb,:bid,:descri)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':fid', $this->details['send']);
        $stmt->bindParam(':tid', $this->details['receive']);
        $stmt->bindParam(':ns', $amount);
        $stmt->bindParam(':csv', $shareValue);
        $stmt->bindParam(':trxndate', $this->details['record_date']);
        $stmt->bindParam(':notes', $this->details['notes']);
        $stmt->bindParam(':adb', $this->details['user']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(':descri', $this->details['notes']);

        $stmt->execute();
        // action
        return true;
    }


    public function sharePurchase()
    {
        $amount = str_replace(",", "", $this->details['amount']);
        $given_value = str_replace(",", "", $this->details['share_value']);
        // check cash , bank , or savings account balance
        if ($this->details['pay_method'] == 'cash' || $this->details['pay_method'] == 'cheque' || $this->details['pay_method'] == 'mobile') {
            $acc_used = 0;
            if ($this->details['pay_method'] == 'cash') {
                $acc_used = $this->details['cash_acc'];
            }
            if (@$this->details['pay_method'] == 'cheque' || @$this->details['mobile']) {
                $acc_used = @$this->details['bank_acc'];
            }

            $sqlQuery = 'SELECT * FROM public."Account"  WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acc_used);
            $stmt->execute();

            $rown = $stmt->fetch();

            $used_balance = $rown['balance'] ?? 0;
            if ($used_balance < $amount) {
                return false;
            } else {

                // update acc balance and reduce them before continuing
                $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE public."Account".id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $acc_used);
                $stmt->bindParam(
                    ':bal',
                    $amount
                );
                $stmt->execute();
            }
        }



        // get bank share value details

        $sqlQuery = 'SELECT * FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['branch']);
        $stmt->execute();

        $row = $stmt->fetch();

        // $current_share_value = $row['share_value'];
        $current_share_value = $given_value;


        if ($current_share_value <= 0) {
            $no_of_shares = $amount  / 1;
            $current_share_value = 1;
        } else {
            $no_of_shares = $amount  / $current_share_value;
        }


        $sqlQuery = 'SELECT * FROM public."share_register" WHERE userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['client']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // update existing share holder details
            $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount+:sa,no_shares=no_shares+:ns WHERE userid=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['client']);
            $stmt->bindParam(':sa',  $amount);
            $stmt->bindParam(':ns', $no_of_shares);
            $stmt->execute();
        } else {
            // create share holder
            $sqlQuery = 'INSERT INTO public.share_register(
	 userid, share_amount, no_shares, added_by, branch_id)
	VALUES (:uid,:sa,:ns,:adb,:bid)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':uid', $this->details['client']);
            $stmt->bindParam(':sa',  $amount);
            $stmt->bindParam(':adb', $this->details['user']);
            $stmt->bindParam(':bid', $this->details['branch']);
            $stmt->bindParam(':ns', $no_of_shares);
            $stmt->execute();
        }


        // deduct money from savings if it's offset from savings ---payment method
        if ($this->details['pay_method'] == 'savings') {

            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->details['client']);
            $stmt->bindParam(':ac',  $amount);
            $stmt->execute();


            $tt_type = 'W';
            $descri = 'Share Purchase Using Savings';

            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount',  $amount);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':autho', $this->details['user']);
            $stmt->bindParam(':actby', $this->details['client']);
            $stmt->bindParam(':accname', $this->details['client']);
            $stmt->bindParam(':mid', $this->details['client']);
            $stmt->bindParam(':approv', $this->details['user']);
            $stmt->bindParam(':branc', $this->details['branch']);
            $stmt->bindParam(':ttype', $tt_type);
            $stmt->bindParam(':pay_method', $this->details['pay_method']);
            $stmt->bindParam(':date_created', $this->details['record_date']);


            $stmt->execute();
        }

        if ($this->details['pay_method'] == 'cheque' || $this->details['pay_method'] == 'cash' || $this->details['pay_method'] == 'mobile') {
            $tt_type = 'CAP';
            $mid = 0;
            $descri = 'Share Purchase Using ' . $this->details['pay_method'];

            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,acid,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:acid,:branc,:ttype,:pay_method,:date_created)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount',  $amount);
            $stmt->bindParam(':acid',  $acc_used);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':autho', $this->details['user']);
            $stmt->bindParam(':actby', $this->details['client']);
            $stmt->bindParam(':accname', $this->details['client']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':approv', $this->details['user']);
            $stmt->bindParam(':branc', $this->details['branch']);
            $stmt->bindParam(':ttype', $tt_type);
            $stmt->bindParam(':pay_method', $this->details['pay_method']);
            $stmt->bindParam(':date_created', $this->details['record_date']);


            $stmt->execute();
        }


        // create share purchase trxn

        $sqlQuery = 'INSERT INTO public.share_purchases(
	user_id, decription, no_of_shares, current_share_value, amount, pay_method, notes, record_date, added_by, branch_id,pay_method_acid)
	VALUES (:uid,:descri,:ns,:csv,:sa,:paymeth,:notes,:trxndate,:adb,:bid,:acid)';

        $stmt = $this->conn->prepare($sqlQuery);
        $acid = 0;
        if ($this->details['pay_method'] == 'cash') {
            $acid = $this->details['cash_acc'];
        } else if ($this->details['pay_method'] == 'cheque') {
            $acid = $this->details['bank_acc'];
        } else if ($this->details['pay_method'] == 'mobile') {
            $acid = $this->details['mobile_acc'];
        } else if ($this->details['pay_method'] == 'savings') {
            $acid = $this->details['client'];
        }
        $stmt->bindParam(':uid', $this->details['client']);
        $stmt->bindParam(':sa',  $amount);
        $stmt->bindParam(':adb', $this->details['user']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(':ns', $no_of_shares);
        $stmt->bindParam(':csv', $current_share_value);
        $stmt->bindParam(':descri', $this->details['comment']);
        $stmt->bindParam(':notes', $this->details['comment']);
        $stmt->bindParam(':paymeth', $this->details['pay_method']);
        $stmt->bindParam(':trxndate', $this->details['record_date']);

        $stmt->bindParam(':acid', $acid);

        $stmt->execute();


        $sqlQuery = 'SELECT * FROM public."Account"  WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $row['share_acid']);
        $stmt->execute();

        $rown = $stmt->fetch();
        if ($rown) {
            $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE public."Account".account_code_used=:id AND "branchId"=:bid';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $rown['account_code_used']);
            $stmt->bindParam(':bid', $this->details['branch']);
            $stmt->bindParam(
                ':bal',
                $amount
            );
            $stmt->execute();
        }


        return true;
    }

    public function shareWithdraw()
    {
        $amount = str_replace(",", "", $this->details['amount']);
        // check cash , bank , or savings account balance
        if ($this->details['pay_method'] == 'cash' || $this->details['pay_method'] == 'cheque' || $this->details['pay_method'] == 'mobile') {
            $acc_used = 0;
            if ($this->details['pay_method'] == 'cash') {
                $acc_used = $this->details['cash_acc'];
            }
            if ($this->details['pay_method'] == 'cheque' || $this->details['mobile']) {
                $acc_used = $this->details['bank_acc'];
            }

            $sqlQuery = 'SELECT * FROM public."Account"  WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acc_used);
            $stmt->execute();

            $rown = $stmt->fetch();

            $used_balance = $rown['balance'] ?? 0;
            if ($used_balance < $amount) {
                return false;
            } else {

                // update acc balance and reduce them before continuing
                $sqlQuery = 'UPDATE  public."Account" SET balance=balance-:bal  WHERE public."Account".id=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $acc_used);
                $stmt->bindParam(
                    ':bal',
                    $amount
                );
                $stmt->execute();
            }
        }



        // get bank share value details

        $sqlQuery = 'SELECT * FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['branch']);
        $stmt->execute();

        $row = $stmt->fetch();

        $current_share_value = $row['share_value'];



        if ($current_share_value <= 0) {
            $no_of_shares = $amount  / 1;
            $current_share_value = 1;
        } else {
            $no_of_shares = $amount  / $current_share_value;
        }


        $sqlQuery = 'SELECT * FROM public."share_register" WHERE userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->details['client']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // update existing share holder details
            $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount-:sa,no_shares=no_shares-:ns WHERE userid=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->details['client']);
            $stmt->bindParam(':sa',  $amount);
            $stmt->bindParam(':ns', $no_of_shares);
            $stmt->execute();
        }


        // deduct money from savings if it's offset from savings ---payment method
        if ($this->details['pay_method'] == 'savings') {

            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->details['client']);
            $stmt->bindParam(':ac',  $amount);
            $stmt->execute();


            $tt_type = 'D';
            $descri = 'Share Withdraw Via Savings';

            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount',  $amount);
            $stmt->bindParam(':descri', $descri);
            $stmt->bindParam(':autho', $this->details['user']);
            $stmt->bindParam(':actby', $this->details['client']);
            $stmt->bindParam(':accname', $this->details['client']);
            $stmt->bindParam(':mid', $this->details['client']);
            $stmt->bindParam(':approv', $this->details['user']);
            $stmt->bindParam(':branc', $this->details['branch']);
            $stmt->bindParam(':ttype', $tt_type);
            $stmt->bindParam(':pay_method', $this->details['pay_method']);
            $stmt->bindParam(':date_created', $this->details['record_date']);


            $stmt->execute();
        }


        // create share withdraw trxn

        $sqlQuery = 'INSERT INTO public.share_purchases(
	user_id, decription, no_of_shares, current_share_value, amount, pay_method, notes, record_date, added_by, branch_id,pay_method_acid, t_type, cr_dr_type)
	VALUES (:uid,:descri,:ns,:csv,:sa,:paymeth,:notes,:trxndate,:adb,:bid,:acid, :t_type, :cr_dr_type)';

        $stmt = $this->conn->prepare($sqlQuery);
        $acid = 0;
        $ttype = 'withdraw';
        $cr_dr_type = 'dr';
        if ($this->details['pay_method'] == 'cash') {
            $acid = $this->details['cash_acc'];
        } else if ($this->details['pay_method'] == 'cheque') {
            $acid = $this->details['bank_acc'];
        } else if ($this->details['pay_method'] == 'mobile') {
            $acid = $this->details['mobile_acc'];
        } else if ($this->details['pay_method'] == 'savings') {
            $acid = $this->details['client'];
        }
        $stmt->bindParam(':uid', $this->details['client']);
        $stmt->bindParam(':sa',  $amount);
        $stmt->bindParam(':adb', $this->details['user']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(':ns', $no_of_shares);
        $stmt->bindParam(':csv', $current_share_value);
        $stmt->bindParam(':descri', $this->details['comment']);
        $stmt->bindParam(':notes', $this->details['comment']);
        $stmt->bindParam(':paymeth', $this->details['pay_method']);
        $stmt->bindParam(':trxndate', $this->details['record_date']);

        $stmt->bindParam(':acid', $acid);
        $stmt->bindParam(':t_type', $ttype);
        $stmt->bindParam(':cr_dr_type', $cr_dr_type);

        $stmt->execute();


        $sqlQuery = 'SELECT * FROM public."Account"  WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $$row['share_acid']);
        $stmt->execute();

        $rown = $stmt->fetch();

        $sqlQuery = 'UPDATE  public."Account" SET balance=balance-:bal  WHERE public."Account".account_code_used=:id AND "branchId"=:bid';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $$rown['account_code_used']);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(
            ':bal',
            $amount
        );
        $stmt->execute();

        return true;
    }



    public function getUserPermissions()
    {
        $sqlQuery = 'SELECT entity';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);


        $stmt->execute();
        return $stmt;
    }

    public function updateStaff()
    {
        $sqlQuery = 'UPDATE public."User" SET 
        "firstName"=:fname,"lastName"=:lname,"gender"=:gender,"country"=:country,"addressLine1"=:address1,"addressLine2"=:address2,
        "village"=:village,"parish"=:parish,
        "subcounty"=:subcounty,"district"=:district,
        "primaryCellPhone"=:phone,"secondaryCellPhone"=:other_phone,"dateOfBirth"=:dob,"spouseName"=:sname,"spouseCell"=:sphone,"nin"=:nin,"spouseNin"=:snin
         WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':fname', $this->firstName);
        $stmt->bindParam(':lname', $this->lastName);
        // $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address1', $this->addressLine1);
        $stmt->bindParam(':address2', $this->addressLine2);
        $stmt->bindParam(':village', $this->village);
        $stmt->bindParam(':parish', $this->parish);
        $stmt->bindParam(':subcounty', $this->subcounty);
        $stmt->bindParam(':district', $this->district);
        $stmt->bindParam(':phone', $this->primaryCellPhone);
        $stmt->bindParam(':other_phone', $this->secondaryCellPhone);
        $stmt->bindParam(':sname', $this->spouseName);
        $stmt->bindParam(':sphone', $this->spouseCell);
        $stmt->bindParam(':nin', $this->nin);
        $stmt->bindParam(':snin', $this->spouseNin);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':dob', $this->dateOfBirth);
        $stmt->bindParam(':country', $this->country);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePassword()
    {
        $sqlQuery = 'UPDATE public."User" SET 
        password=:passx  WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':passx', $this->password);

        $stmt->bindParam(':id', $this->email);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getStaffDeposits($id)
    {
        $sqlQuery = 'SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \', public."User".shared_name)) as client_name, tid,date_created,amount,pay_method,_status FROM transactions LEFT JOIN public."User" ON transactions.mid = public."User".id where _authorizedby=:id AND t_type=\'D\' ORDER BY date_created DESC LIMIT 5';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getStaffWithdraws($id)
    {
        $sqlQuery = 'SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \', public."User".shared_name)) as client_name, tid,date_created,amount,pay_method,_status FROM transactions LEFT JOIN public."User" ON transactions.mid = public."User".id where _authorizedby=:id AND t_type=\'W\' ORDER BY date_created DESC LIMIT 5';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }


    public function registerUser()
    {
        $sqlQuery = 'INSERT INTO public."User" 
        ("firstName","lastName","email",password,"gender","country","addressLine1","addressLine2","village","parish","subcounty","district",
        "primaryCellPhone","secondaryCellPhone","dateOfBirth","notes","confirmed","spouseName","spouseCell","status","nin","spouseNin"
        ) VALUES(:fname,:lname,:email,:pass,:gender,:country,:address1,:address2,:village,:parish,:subcounty,:district,:phone,:other_phone,:dob,:notes,:confirm,:sname,:sphone,
        :stat,:nin,:snin)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':fname', $this->firstName);
        $stmt->bindParam(':lname', $this->lastName);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':pass', $this->password);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address1', $this->addressLine1);
        $stmt->bindParam(':address2', $this->addressLine2);
        $stmt->bindParam(':village', $this->village);
        $stmt->bindParam(':parish', $this->parish);
        $stmt->bindParam(':subcounty', $this->subcounty);
        $stmt->bindParam(':district', $this->district);
        $stmt->bindParam(':phone', $this->primaryCellPhone);
        $stmt->bindParam(':other_phone', $this->secondaryCellPhone);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':confirm', $this->confirmed);
        $stmt->bindParam(':sname', $this->spouseName);
        $stmt->bindParam(':sphone', $this->spouseCell);
        $stmt->bindParam(':stat', $this->status);
        $stmt->bindParam(':nin', $this->nin);
        $stmt->bindParam(':snin', $this->spouseNin);

        $stmt->execute();

        $this->userId = $stmt->lastInsertId();
        $sqlQuery = 'INSERT INTO public."Staff" 
        ("positionTitle","notes","roleId","userId","bankId","branchId","identificationNumber","serialNumber"
        ) VALUES(:ptitle,:notes,:roleid,:uidd,:bid,:brid,:idno,:sno)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':ptitle', $this->positionTitle);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':roleid', $this->roleId);
        $stmt->bindParam(':uidd', $this->userId);
        $stmt->bindParam(':bid', $this->bankId);
        $stmt->bindParam(':brid', $this->branchId);
        $stmt->bindParam(':idno', $this->identificationNumber);
        $stmt->bindParam(':sno', $this->serialNumber);


        $stmt->execute();

        return true;
    }

    public function createBankStaff()
    {
        $sqlQuery = 'INSERT INTO public."User" 
        ("firstName","lastName","email","gender","country","addressLine1","addressLine2","village","parish","subcounty","district",
        "primaryCellPhone","secondaryCellPhone","dateOfBirth","confirmed","spouseName","spouseCell","status","nin","spouseNin","notes","profilePhoto"
        ) VALUES(:fname,:lname,:email,:gender,:country,:address1,:address2,:village,:parish,:subcounty,:district,:phone,:other_phone,:dob,:confirm,:sname,:sphone,
        :stat,:nin,:snin,:notes,:pic)';

        $stmt = $this->conn->prepare($sqlQuery);
        $notes = 'Staff';
        $stmt->bindParam(':fname', $this->firstName);
        $stmt->bindParam(':lname', $this->lastName);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address1', $this->addressLine1);
        $stmt->bindParam(':address2', $this->addressLine2);
        $stmt->bindParam(':village', $this->village);
        $stmt->bindParam(':parish', $this->parish);
        $stmt->bindParam(':subcounty', $this->subcounty);
        $stmt->bindParam(':district', $this->district);
        $stmt->bindParam(':phone', $this->primaryCellPhone);
        $stmt->bindParam(':other_phone', $this->secondaryCellPhone);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':confirm', $this->confirmed);
        $stmt->bindParam(':sname', $this->spouseName);
        $stmt->bindParam(':sphone', $this->spouseCell);
        $stmt->bindParam(':stat', $this->status);
        $stmt->bindParam(':nin', $this->nin);
        $stmt->bindParam(':snin', $this->spouseNin);
        $stmt->bindParam(':dob', $this->dateOfBirth);
        $stmt->bindParam(':pic', $this->profilePhoto);
        $stmt->execute();

        // $this->userId = $stmt->lastInsertId();
        $sqlQuery = 'SELECT * FROM public."User" ORDER BY id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $rown = $stmt->fetch();
        $this->userId = $rown['id'];





        $sqlQuery = 'INSERT INTO public."Staff" 
        ("positionTitle","notes","roleId","userId","branchId","identificationNumber","serialNumber"
        ) VALUES(:ptitle,:notes,:roleid,:uidd,:branch,:idno,:sno)';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':ptitle', $this->bname);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':roleid', $this->roleId);
        $stmt->bindParam(':uidd', $this->userId);
        // $stmt->bindParam(':bid', $this->bankId);
        $stmt->bindParam(':branch', $this->branchId);
        $stmt->bindParam(':idno', $this->identificationNumber);
        $stmt->bindParam(':sno', $this->serialNumber);


        $stmt->execute();

        if ($this->is_supervisor == 1) {
            $sup = true;
            $sqlQuery = 'UPDATE public."Staff"  SET is_supervisor=:sup, supervisor_bankid=:supbid, supervisor_level=:suplevel WHERE "userId"=:uidd';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':uidd', $this->userId);
            $stmt->bindParam(':sup', $sup);
            $stmt->bindParam(':supbid', $this->supervisor_bankid);
            $stmt->bindParam(':suplevel', $this->supervisor_level);


            $stmt->execute();
        }

        return $this->userId;
    }

    public function editBankStaff()
    {
        $sqlQuery = 'UPDATE public."User" SET
        "firstName"=:fname,"lastName"=:lname,"email"=:email,"gender"=:gender,"country"=:country,"addressLine1"=:address1,"addressLine2"=:address2,"village"=:village,"parish"=:parish,"subcounty"=:subcounty,"district"=:district,
        "primaryCellPhone"=:phone,"secondaryCellPhone"=:other_phone,"dateOfBirth"=:dob,"confirmed"=:confirm,"spouseName"=:sname,"spouseCell"=:sphone,"status"=:stat,"nin"=:nin,"spouseNin"=:snin,"notes"=:notes,"profilePhoto"=:pic WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $notes = 'Staff';
        $stmt->bindParam(':fname', $this->firstName);
        $stmt->bindParam(':lname', $this->lastName);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address1', $this->addressLine1);
        $stmt->bindParam(':address2', $this->addressLine2);
        $stmt->bindParam(':village', $this->village);
        $stmt->bindParam(':parish', $this->parish);
        $stmt->bindParam(':subcounty', $this->subcounty);
        $stmt->bindParam(':district', $this->district);
        $stmt->bindParam(':phone', $this->primaryCellPhone);
        $stmt->bindParam(':other_phone', $this->secondaryCellPhone);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':confirm', $this->confirmed);
        $stmt->bindParam(':sname', $this->spouseName);
        $stmt->bindParam(':sphone', $this->spouseCell);
        $stmt->bindParam(':stat', $this->status);
        $stmt->bindParam(':nin', $this->nin);
        $stmt->bindParam(':snin', $this->spouseNin);
        $stmt->bindParam(':dob', $this->dateOfBirth);
        $stmt->bindParam(':pic', $this->profilePhoto);
        $stmt->bindParam(':id', $this->userId);
        $stmt->execute();



        $sqlQuery = 'UPDATE public."Staff" SET
        "positionTitle"=:ptitle,"notes"=:notes,"roleId"=:roleid,"userId"=:uidd,"branchId"=:branch,"identificationNumber"=:idno,"serialNumber"=:sno where "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':ptitle', $this->bname);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':roleid', $this->roleId);
        $stmt->bindParam(':uidd', $this->userId);
        // $stmt->bindParam(':bid', $this->bankId);
        $stmt->bindParam(':branch', $this->branchId);
        $stmt->bindParam(':idno', $this->identificationNumber);
        $stmt->bindParam(':sno', $this->serialNumber);
        $stmt->bindParam(':id', $this->id);


        $stmt->execute();

        return $this->userId;
    }

    public function createBankAdmin()
    {
        $sqlQuery = 'INSERT INTO public."User" 
        ("firstName","lastName","email","gender","country","addressLine1","addressLine2","village","parish","subcounty","district",
        "primaryCellPhone","secondaryCellPhone","dateOfBirth","confirmed","spouseName","spouseCell","status","nin","spouseNin","notes","profilePhoto"
        ) VALUES(:fname,:lname,:email,:gender,:country,:address1,:address2,:village,:parish,:subcounty,:district,:phone,:other_phone,:dob,:confirm,:sname,:sphone,
        :stat,:nin,:snin,:notes,:pphoto)';

        $stmt = $this->conn->prepare($sqlQuery);
        $notes = 'Institution Admin Created by UCSCU CBS Super User';

        if ($this->dateOfBirth == '') {
            $this->dateOfBirth = NULL;
        }
        $stmt->bindParam(':fname', $this->firstName);
        $stmt->bindParam(':lname', $this->lastName);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address1', $this->addressLine1);
        $stmt->bindParam(':address2', $this->addressLine2);
        $stmt->bindParam(':village', $this->village);
        $stmt->bindParam(':parish', $this->parish);
        $stmt->bindParam(':subcounty', $this->subcounty);
        $stmt->bindParam(':district', $this->district);
        $stmt->bindParam(':phone', $this->primaryCellPhone);
        $stmt->bindParam(':other_phone', $this->secondaryCellPhone);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':confirm', $this->confirmed);
        $stmt->bindParam(':sname', $this->spouseName);
        $stmt->bindParam(':sphone', $this->spouseCell);
        $stmt->bindParam(':stat', $this->status);
        $stmt->bindParam(':nin', $this->nin);
        $stmt->bindParam(':snin', $this->spouseNin);
        $stmt->bindParam(':dob', $this->dateOfBirth);
        $stmt->bindParam(':pphoto', $this->profilePhoto);
        $stmt->execute();

        // $this->userId = $stmt->lastInsertId();

        $sqlQuery = 'SELECT * FROM public."User" ORDER BY id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $rown = $stmt->fetch();
        $this->userId = $rown['id'];


        $sqlQuery = 'INSERT INTO public."Role" 
        (name,description,"bankId"
        ) VALUES(:name,:descr,:bid)';

        $stmt = $this->conn->prepare($sqlQuery);
        $roleDesc = 'This is the Institution Administrator';
        $stmt->bindParam(':name', $this->bname);
        $stmt->bindParam(':descr', $roleDesc);
        $stmt->bindParam(':bid', $this->bankId);
        $stmt->execute();

        $sqlQuery = 'SELECT * FROM public."Role" ORDER BY id DESC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $row = $stmt->fetch();



        $sqlQuery = 'INSERT INTO public."Staff" 
        ("positionTitle","notes","roleId","userId","bankId","identificationNumber","serialNumber",is_admin
        ) VALUES(:ptitle,:notes,:roleid,:uidd,:bid,:idno,:sno,:isadmin)';

        $stmt = $this->conn->prepare($sqlQuery);
        $isadmin = true;
        $stmt->bindParam(':ptitle', $this->bname);
        $stmt->bindParam(':notes', $roleDesc);
        $stmt->bindParam(':roleid', $row['id']);
        $stmt->bindParam(':uidd', $this->userId);
        $stmt->bindParam(':bid', $this->bankId);
        $stmt->bindParam(':idno', $this->identificationNumber);
        $stmt->bindParam(':sno', $this->serialNumber);
        $stmt->bindParam(':isadmin', $isadmin);


        $stmt->execute();

        return $this->userId;
    }

    public function updateBankAdmin()
    {
        $sqlQuery = 'UPDATE public."User" SET "firstName"=:fname,"lastName"=:lname,"email"=:email,"gender"=:gender,"country"=:country,"addressLine1"=:address1,"addressLine2"=:address2,"village"=:village,"parish"=:parish,"subcounty"=:subcounty,"district"=:district, "primaryCellPhone"=:phone,"secondaryCellPhone"=:other_phone,"dateOfBirth"=:dob,"confirmed"=:confirm,"spouseName"=:sname,"spouseCell"=:sphone,"status"=:stat,"nin"=:nin,"spouseNin"=:snin,"notes"=:notes,"profilePhoto"=:pphoto  WHERE public."User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $notes = 'Institution Admin account Updated by UCSCU CBS Super User on ' . date('Y-m-d');
        $stmt->bindParam(':fname', $this->firstName);
        $stmt->bindParam(':id', $this->sid);
        $stmt->bindParam(':lname', $this->lastName);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address1', $this->addressLine1);
        $stmt->bindParam(':address2', $this->addressLine2);
        $stmt->bindParam(':village', $this->village);
        $stmt->bindParam(':parish', $this->parish);
        $stmt->bindParam(':subcounty', $this->subcounty);
        $stmt->bindParam(':district', $this->district);
        $stmt->bindParam(':phone', $this->primaryCellPhone);
        $stmt->bindParam(':other_phone', $this->secondaryCellPhone);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':confirm', $this->confirmed);
        $stmt->bindParam(':sname', $this->spouseName);
        $stmt->bindParam(':sphone', $this->spouseCell);
        $stmt->bindParam(':stat', $this->status);
        $stmt->bindParam(':nin', $this->nin);
        $stmt->bindParam(':snin', $this->spouseNin);
        $stmt->bindParam(':dob', $this->dateOfBirth);
        $stmt->bindParam(':pphoto', $this->profilePhoto);
        $stmt->execute();



        $sqlQuery = 'UPDATE public."Staff" SET "positionTitle"=:ptitle WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':ptitle', $this->bname);
        $stmt->bindParam(':id', $this->sid);


        $stmt->execute();

        return true;
    }

    public function sendEmail($mail_to, $mail_subject, $mail_body)
    {

        $cURL_key = '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';
        $mail_from = 'ucscucbsdev@gmail.com';

        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendgrid.com/v3/mail/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"personalizations\": [{\"to\": [{\"email\": \"$mail_to\"}]}],\"from\": {\"email\": \"$mail_from\"},\"subject\": \"$mail_subject\",\"content\": [{\"type\": \"text/html\", \"value\": \"$mail_body\"}]}",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $cURL_key",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }




    public function createClient()
    {

        // get bank id
        $sqlQuery = 'SELECT * FROM  public."Branch" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->branchId);
        $stmt->execute();
        $row = $stmt->fetch();

        $use_bid = $row['bankId'];

        if ($this->occupation_category == 0 && $this->other_cat != '') {
            $sqlQuery = 'INSERT INTO public.occupation_sub_categories(osc_name, bankid) VALUES (:name, :bid)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':name', $this->other_cat);
            $stmt->bindParam(':bid', $use_bid);
            $stmt->execute();

            $this->occupation_category = $this->conn->lastInsertId();
        }

        if ($this->occupation_sub_category == 0 && $this->other_sub_cat != '') {
            $sqlQuery = 'INSERT INTO public.occupation_categories(oc_name, bankid) VALUES (:name, :bid)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':name', $this->other_sub_cat);
            $stmt->bindParam(':bid', $use_bid);
            $stmt->execute();

            $this->occupation_sub_category = $this->conn->lastInsertId();
        }

        if ($this->occupation_sector == 0 && $this->other_sect != '') {
            $sqlQuery = 'INSERT INTO public.occupation_sector(os_name, bankid) VALUES (:name, :bid)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':name', $this->other_sect);
            $stmt->bindParam(':bid', $use_bid);
            $stmt->execute();

            $this->occupation_sector = $this->conn->lastInsertId();
        }



        $client_type = $this->client_type ?? 'individual';
        $shared_name = $this->name ?? '';

        $exp_income =   str_replace(",", "", $this->income);
        // user details
        $sqlQuery = 'INSERT INTO public."User" 
        ("firstName","lastName","email","gender","country","addressLine1","addressLine2","village","parish","subcounty","district",
        "primaryCellPhone","secondaryCellPhone","dateOfBirth","confirmed","spouseName","spouseCell","status","nin","spouseNin","krelationship","kaddress",profession,"createdAt",entry_chanel,shared_name,sms_phone_numbers,"otherCellPhone",notes,entered_by,occupation_category,occupation_sub_category,occupation_sector, marital_status, expected_income, region, disability_desc,disability_status,disability_cat,disability_other,education_level
        ) VALUES(:fname,:lname,:email,:gender,:country,:address1,:address2,:village,:parish,:subcounty,:district,:phone,:other_phone,:dob,:confirm,:sname,:sphone,
        :stat,:nin,:snin,:kr,:kadd,:prof,:createdAt,:entry_chanel,:shared_name,:sms_phone_numbers,:otherCellPhone,:notes,:entered_by,:cat,:sub_cat,:sect,:marital, :income, :region,:disability_desc,:disability_status,:disability_cat,:disability_others,:education_level)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':fname', $this->firstName);
        $stmt->bindParam(
            ':region',
            $this->region
        );
        $stmt->bindParam(
            ':income',
            $exp_income
        );
        $stmt->bindParam(':marital', $this->marital);
        $stmt->bindParam(':education_level', $this->education_level);
        $stmt->bindParam(':disability_desc', $this->disability_desc);
        $stmt->bindParam(':disability_status', $this->disability_status);
        $stmt->bindParam(':disability_cat', $this->disability_cat);
        $stmt->bindParam(':disability_others', $this->disability_others);
        $stmt->bindParam(':lname', $this->lastName);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':country', $this->country);
        $stmt->bindParam(':address1', $this->addressLine1);
        $stmt->bindParam(':address2', $this->addressLine2);
        $stmt->bindParam(':village', $this->village);
        $stmt->bindParam(':parish', $this->parish);
        $stmt->bindParam(':subcounty', $this->subcounty);
        $stmt->bindParam(':district', $this->district);
        $stmt->bindParam(':phone', $this->primaryCellPhone);
        $stmt->bindParam(':other_phone', $this->secondaryCellPhone);
        $stmt->bindParam(':confirm', $this->confirmed);
        $stmt->bindParam(':sname', $this->spouseName);
        $stmt->bindParam(':sphone', $this->spouseCell);
        $stmt->bindParam(':stat', $this->status);
        $stmt->bindParam(':nin', $this->nin);
        $stmt->bindParam(':snin', $this->spouseNin);
        $stmt->bindParam(':kr', $this->krelationship);
        $stmt->bindParam(':kadd', $this->kaddress);
        $stmt->bindParam(':dob', $this->dateOfBirth);
        $stmt->bindParam(':prof', $this->profession);

        $stmt->bindParam(':shared_name', $shared_name);
        $stmt->bindParam(':otherCellPhone', $this->otherCellPhone);
        $stmt->bindParam(':notes', $this->notes);
        $stmt->bindParam(':entered_by', $this->entered_by);
        $stmt->bindParam(':cat', $this->occupation_category);
        $stmt->bindParam(':sub_cat', $this->occupation_sub_category);
        $stmt->bindParam(':sect', $this->occupation_sector);

        $sms_phone_numbers = [];
        if ($client_type == 'individual' || $this->is_data_importer_client) {
            if ($this->primaryCellPhone) {
                $sms_phone_numbers = [$this->primaryCellPhone];
            } else if ($this->secondaryCellPhone) {
                $sms_phone_numbers = [$this->secondaryCellPhone];
            }
        } else {
            $sms_phone_numbers = $this->sms_phone_numbers ?? [];
        }

        if ($this->message_consent == '0') {
            $sms_phone_numbers = [];
        }

        $sms_phone_numbers =  json_encode($this->sms_phone_numbers);

        $stmt->bindParam(':sms_phone_numbers', $sms_phone_numbers);

        $entry_chanel = $this->entry_chanel ?? "system";
        $stmt->bindParam(':entry_chanel', $entry_chanel);

        $createdAt = $this->createdAt ?? date('Y-m-d');
        $stmt->bindParam(':createdAt', $createdAt);

        $stmt->execute();

        $this->userId = $this->conn->lastInsertId();




        // client details
        $sqlQuery = 'INSERT INTO public."Client" 
        ("userId","branchId","serialNumber",membership_no,actype,message_consent,acc_balance,freezed_amount,loan_wallet,savings_officer,membership_fee,"createdAt",entry_chanel,old_membership_no,occupation_type_id,client_type
        ) VALUES(:uid,:bid,:sno,:mno,:actype,:mc,:acc_balance,:freezed_amount,:loan_wallet,:savings_officer_id,:membership_fee,:createdAt,:entry_chanel,:old_membership_no,:occupation_type_id,:client_type)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':createdAt', $createdAt);
        $stmt->bindParam(':uid', $this->userId);
        $stmt->bindParam(':bid', $this->branchId);
        $stmt->bindParam(':sno', $this->serialNumber);
        // $stmt->bindParam(':idno', $this->identificationNumber);
        $stmt->bindParam(':mno', $this->mno);
        // $stmt->bindParam(':bank', $this->bankId);
        $stmt->bindParam(':entry_chanel', $entry_chanel);
        $account_type = (int)$this->actype ?? null;
        $stmt->bindParam(':actype', $account_type);

        $stmt->bindParam(':mc', $this->message_consent);

        $acc_balance = $this->acc_balance ?? 0;
        $stmt->bindParam(':acc_balance', $acc_balance);

        $freezed_amount = $this->freezed_amount ?? 0;
        $stmt->bindParam(':freezed_amount', $freezed_amount);

        $loan_wallet = $this->loan_wallet ?? 0;
        $stmt->bindParam(':loan_wallet', $loan_wallet);

        $savings_officer_id = $this->savings_officer_id ?? 0;
        $stmt->bindParam(':savings_officer_id', $savings_officer_id);

        $membership_fee = $this->membership_fee ?? 0;
        $stmt->bindParam(':membership_fee', $membership_fee);


        $stmt->bindParam(':client_type', $client_type);

        $old_membership_no = $this->old_membership_no ?? null;
        $stmt->bindParam(':old_membership_no', $old_membership_no);
        $stmt->bindParam(':occupation_type_id', $this->occupation_type_id);


        $stmt->execute();
        $this->clientId = $this->conn->lastInsertId();


        // register business details
        $sqlQuery = 'INSERT INTO public."Business" 
        (name,"addressLine1","addressLine2",city,country,"registrationNumber",registration_status,"clientId",business_type_other,number_of_members,business_type,business_nature_description,is_registered
        ) VALUES(:name,:addre,:addre2,:city,:bcount,:regno,:registration_status,:sno,:business_type_other,:number_of_members,:business_type,:business_nature_description,:is_registered)';

        $stmt = $this->conn->prepare($sqlQuery);

        if ($this->is_registered == 1 || $this->is_registered == true) {
            $this->is_registered = true;
        } else {
            $this->is_registered = false;
        }

        // $this->is_registered = @$this->is_registered ? true : false;

        // var_dump($this);

        $this->number_of_members = (int)@$this->number_of_members;
        $stmt->bindParam(':name', $shared_name);
        $stmt->bindParam(':addre', $this->baddress);
        $stmt->bindParam(':addre2', $this->baddress2);
        $stmt->bindParam(':city', $this->bcity);
        $stmt->bindParam(':bcount', $this->bcountry);
        $stmt->bindParam(':regno', $this->bregno);
        $stmt->bindParam(':registration_status', $this->registration_status);
        $stmt->bindParam(':is_registered', $this->is_registered, PDO::PARAM_BOOL);
        $stmt->bindParam(':business_type_other', $this->business_type_other);
        $stmt->bindParam(':business_type', $this->business_type);
        $stmt->bindParam(':number_of_members', $this->number_of_members);
        $stmt->bindParam(':business_type', $this->business_type);
        $stmt->bindParam(':business_nature_description', $this->business_nature_description);
        $stmt->bindParam(':sno', $this->clientId);
        $stmt->execute();

        /**
         * membership fees
         */


        // $client = $db_handler->fetch('Client', '');

        $sqlQuery = 'SELECT *, public."Bank".id AS bank_id, public."Branch".id AS branch_id FROM public."User" 
        LEFT JOIN public."Client" ON public."User".id=public."Client"."userId" 
        LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" 
        LEFT JOIN public."Bank" ON public."Bank".id=public."Branch"."bankId" 
        WHERE public."Client".id=:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->clientId);
        $stmt->execute();
        $client = $stmt->fetch(PDO::FETCH_ASSOC);


        if (@$client['membership_fee_required']) {
            $fee = new Fee($this->conn);
            $current_membership_fee = (int)$client['membership_fee'];
            $branch_fee = $fee->getByBranchId($client['branchId']);
            if (@$branch_fee && $current_membership_fee < $branch_fee['amount']) {
                $db_handler = new DbHandler();
                $db_handler->update('User', ['status' => 'PENDING'], 'id', $client['userId']);
            }
        }

        return $this->clientId;
    }

    public function getBankAccLength($branch)
    {
        $sqlQuery = 'SELECT public."Bank".acc_length AS al,public."Bank".padding_value AS pv FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $branch);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['al'] . '/' . $row['pv'];
    }



    public function getBankSMStypeStatus($branch, $sms_type)
    {
        $sqlQuery = 'SELECT s_status,temp_body,charge,charged_to FROM public."sms_types" LEFT JOIN public."Branch" ON public."Branch"."bankId"=public."sms_types".bank_id WHERE public."Branch".id=:id AND sms_sent_on=:son AND public."sms_types".deleted=0';

        $stmt = $this->conn->prepare($sqlQuery);

        // $son = 'account_opening';
        $stmt->bindParam(':id', $branch);
        $stmt->bindParam(':son', $sms_type);


        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            return $row;
        }
        return 0;
    }


    public function decryptSMS($temp_body, $sms_type, $client)
    {
        //  check for all supported tags and replace them with info
        if (
            $sms_type == 'account_opening'
        ) {
            // possible tags
            // [institution] , [instphone],[instemail],[fname],[lname],[acno],[actype],[branch]

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', @$client['fname'], $temp_body);
            }
            if (str_contains(
                $temp_body,
                '[lname]'
            )) {
                $temp_body =  str_replace(
                    '[lname]',
                    @$client['lname'],
                    $temp_body
                );
            }
            if (str_contains($temp_body, '[othername]')) {
                $temp_body =  str_replace(
                    '[othername]',
                    @$client['othername'],
                    $temp_body
                );
            }

            if (str_contains($temp_body, '[cphone]')) {
                $temp_body =  str_replace('[cphone]', @$client['phone'], $temp_body);
            }
            if (str_contains(
                $temp_body,
                '[date_created]'
            )) {
                $temp_body =  str_replace('[date_created]', date('Y-m-d'), $temp_body);
            }
            if (str_contains(
                $temp_body,
                '[acno]'
            )) {
                $temp_body =  str_replace(
                    '[acno]',
                    @$client['acno'],
                    $temp_body
                );
            }
            if (str_contains($temp_body, '[actype]')) {
                $temp_body =  str_replace(
                    '[actype]',
                    @$client['actype'],
                    $temp_body
                );
            }
            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else if ($sms_type == 'general_sms') {
            // possible tags
            // [institution] , [instphone],[instemail],[fname],[lname],[acno],[actype],[branch]

            if (str_contains($temp_body, '[fname]') || str_contains($temp_body, '[lname]') || str_contains($temp_body, '[othername]') || str_contains($temp_body, '[cphone]') || str_contains($temp_body, '[acno]') || str_contains($temp_body, '[actype]')) {

                // fetch
                $sqlQuery = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id  WHERE public."Client"."userId"=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['id']);


                $stmt->execute();

                $row = $stmt->fetch();


                if (str_contains($temp_body, '[fname]')) {
                    $temp_body =  str_replace('[fname]', @$row['firstName'], $temp_body);
                }
                if (str_contains(
                    $temp_body,
                    '[lname]'
                )) {
                    $temp_body =  str_replace(
                        '[lname]',
                        @$row['lastName'],
                        $temp_body
                    );
                }
                if (str_contains($temp_body, '[othername]')) {
                    $temp_body =  str_replace(
                        '[othername]',
                        @$row['other_names'],
                        $temp_body
                    );
                }

                if (str_contains(
                    $temp_body,
                    '[cphone]'
                )) {
                    $temp_body =  str_replace('[cphone]', @$row['primaryCellPhone'], $temp_body);
                }
                if (str_contains(
                    $temp_body,
                    '[date_created]'
                )) {
                    $temp_body =  str_replace(
                        '[date_created]',
                        date('Y-m-d', strtotime(@$row['createdAt'])),
                        $temp_body
                    );
                }
                if (str_contains(
                    $temp_body,
                    '[acno]'
                )) {
                    $temp_body =  str_replace(
                        '[acno]',
                        @$row['membership_no'],
                        $temp_body
                    );
                }
                if (str_contains($temp_body, '[actype]')) {
                    $temp_body =  str_replace(
                        '[actype]',
                        @$row['actype'],
                        $temp_body
                    );
                }
            }
            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else  if ($sms_type == 'on_subscribe_school_pay') {
            // possible tags
            // [institution] , [instphone],[instemail],[fname],[lname],[acno],[actype],[branch]

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $client['name'], $temp_body);
            }

            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else  if ($sms_type == 'on_internet_banking_pin_set') {
            // possible tags
            // [institution] , [instphone],[instemail],[fname],[lname],[acno],[actype],[branch]

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $client['name'], $temp_body);
            }
            if (str_contains($temp_body, '[lname]')) {
                $temp_body =  str_replace('[lname]', $client['name'], $temp_body);
            }
            if (str_contains($temp_body, '[mpin]')) {
                $temp_body =  str_replace(
                    '[mpin]',
                    $client['mpin'],
                    $temp_body
                );
            }

            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else  if ($sms_type == 'loan_apply') {
            // possible tags
            // [institution] , [instphone],[instemail],[fname],[lname],[acno],[actype],[branch],[requested_amount],[lpname]

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $client['fname'], $temp_body);
            }
            if (str_contains(
                $temp_body,
                '[lname]'
            )) {
                $temp_body =  str_replace(
                    '[lname]',
                    $client['lname'],
                    $temp_body
                );
            }
            if (str_contains($temp_body, '[requested_amount]')) {
                $temp_body =  str_replace(
                    '[requested_amount]',
                    $client['amount'],
                    $temp_body
                );
            }

            if (str_contains($temp_body, '[lpname]')) {
                $temp_body =  str_replace('[lpname]', $client['lpname'], $temp_body);
            }
            if (str_contains(
                $temp_body,
                '[date_created]'
            )) {
                $temp_body =  str_replace('[date_created]', date('Y-m-d'), $temp_body);
            }
            if (str_contains(
                $temp_body,
                '[acno]'
            )) {
                $temp_body =  str_replace(
                    '[acno]',
                    $client['acno'],
                    $temp_body
                );
            }
            if (str_contains($temp_body, '[actype]')) {
                $temp_body =  str_replace(
                    '[actype]',
                    $client['actype'],
                    $temp_body
                );
            }
            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else if ($sms_type == 'on_deposit') {

            // fetch personal info of the client
            $sqlQueryn = 'SELECT "firstName",shared_name, name,membership_no,loan_wallet,acc_balance FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id  WHERE public."Client"."userId"=:id ';

            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $client['id']);
            $stmtn->execute();

            $rown = $stmtn->fetch();

            // merge in the user personal info

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $rown['firstName'] . $rown['shared_name'], $temp_body);
            }
            if (str_contains($temp_body, '[actype]')) {
                $temp_body =  str_replace('[actype]', $rown['name'], $temp_body);
            }
            if (str_contains($temp_body, '[acno]')) {
                $temp_body =  str_replace('[acno]', $rown['membership_no'], $temp_body);
            }
            if ($rown['membership_no'] == 0) {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['loan_wallet'] - $client['charge']), $temp_body);
                }
            } else {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['acc_balance'] - $client['charge']), $temp_body);
                }
            }

            // move on to the trxn details

            if (str_contains($temp_body, '[amount]')) {
                $temp_body =  str_replace('[amount]', number_format($client['amount']), $temp_body);
            }

            if (str_contains($temp_body, '[date_created]')) {
                $temp_body =  str_replace('[date_created]', $client['date'], $temp_body);
            }
            if (str_contains($temp_body, '[pay_method]')) {
                $temp_body =  str_replace('[pay_method]', $client['method'], $temp_body);
            }

            // institution details

            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else if ($sms_type == 'on_deposit_agent') {

            // fetch personal info of the client
            $sqlQueryn = 'SELECT "firstName",shared_name, name,membership_no,loan_wallet,acc_balance FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id  WHERE public."Client"."userId"=:id ';

            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $client['id']);
            $stmtn->execute();

            $rown = $stmtn->fetch();

            // merge in the user personal info

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $rown['firstName'] . $rown['shared_name'], $temp_body);
            }
            if (str_contains($temp_body, '[actype]')) {
                $temp_body =  str_replace('[actype]', $rown['name'], $temp_body);
            }
            if (str_contains($temp_body, '[acno]')) {
                $temp_body =  str_replace('[acno]', $rown['membership_no'], $temp_body);
            }
            if ($rown['membership_no'] == 0) {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['loan_wallet'] - $client['charge']), $temp_body);
                }
            } else {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['acc_balance'] - $client['charge']), $temp_body);
                }
            }

            // move on to the trxn details

            if (str_contains($temp_body, '[amount]')) {
                $temp_body =  str_replace('[amount]', number_format($client['amount']), $temp_body);
            }

            if (str_contains($temp_body, '[wallet]')) {
                $temp_body =  str_replace('[wallet]', number_format($client['wallet']), $temp_body);
            }

            if (str_contains($temp_body, '[date_created]')) {
                $temp_body =  str_replace('[date_created]', $client['date'], $temp_body);
            }
            if (str_contains($temp_body, '[pay_method]')) {
                $temp_body =  str_replace('[pay_method]', $client['method'], $temp_body);
            }

            // institution details

            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else if ($sms_type == 'loan_repay') {

            // fetch personal info of the client
            $sqlQueryn = 'SELECT "firstName",shared_name, name,membership_no,loan_wallet,acc_balance FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id  WHERE public."Client"."userId"=:id ';

            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $client['id']);
            $stmtn->execute();

            $rown = $stmtn->fetch();

            // merge in the user personal info

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $rown['firstName'] . $rown['shared_name'], $temp_body);
            }
            if (str_contains($temp_body, '[actype]')) {
                $temp_body =  str_replace('[actype]', $rown['name'], $temp_body);
            }
            if (str_contains($temp_body, '[acno]')) {
                $temp_body =  str_replace('[acno]', $rown['membership_no'], $temp_body);
            }
            if ($rown['membership_no'] == 0) {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['loan_wallet'] - $client['charge']), $temp_body);
                }
            } else {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['acc_balance'] - $client['charge']), $temp_body);
                }
            }

            // move on to the trxn details

            if (str_contains($temp_body, '[amount]')) {
                $temp_body =  str_replace('[amount]', number_format($client['amount']), $temp_body);
            }

            if (str_contains($temp_body, '[date_created]')) {
                $temp_body =  str_replace('[date_created]', $client['date'], $temp_body);
            }
            if (str_contains($temp_body, '[pay_method]')) {
                $temp_body =  str_replace('[pay_method]', $client['method'], $temp_body);
            }

            // institution details

            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else if ($sms_type == 'on_deposit_fees_parent' || $sms_type == 'on_deposit_fees_school') {

            // fetch personal info of the client
            $sqlQueryn = 'SELECT "firstName",shared_name, name,membership_no,loan_wallet,acc_balance FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id  WHERE public."Client"."userId"=:id ';

            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $client['id']);
            $stmtn->execute();

            $rown = $stmtn->fetch();

            // merge in the user personal info

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $rown['firstName'] . $rown['shared_name'], $temp_body);
            }
            if (str_contains($temp_body, '[actype]')) {
                $temp_body =  str_replace('[actype]', $rown['name'], $temp_body);
            }
            if (str_contains($temp_body, '[acno]')) {
                $temp_body =  str_replace('[acno]', $rown['membership_no'], $temp_body);
            }
            if ($rown['membership_no'] == 0) {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['loan_wallet'] - $client['charge']), $temp_body);
                }
            } else {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['acc_balance'] - $client['charge']), $temp_body);
                }
            }

            // move on to the trxn details

            if (str_contains($temp_body, '[amount]')) {
                $temp_body =  str_replace('[amount]', number_format($client['amount']), $temp_body);
            }

            if (str_contains($temp_body, '[date_created]')) {
                $temp_body =  str_replace('[date_created]', $client['date'], $temp_body);
            }
            if (str_contains($temp_body, '[pay_method]')) {
                $temp_body =  str_replace('[pay_method]', $client['method'], $temp_body);
            }
            if (str_contains($temp_body, '[sno]')) {
                $temp_body =  str_replace('[sno]', $client['sno'], $temp_body);
            }
            if (str_contains($temp_body, '[sname]')) {
                $temp_body =  str_replace('[sname]', $client['sname'], $temp_body);
            }
            if (str_contains($temp_body, '[parent]')) {
                $temp_body =  str_replace('[parent]', $client['pname'], $temp_body);
            }
            if (str_contains($temp_body, '[parent_phone]')) {
                $temp_body =  str_replace('[parent_phone]', $client['pcontact'], $temp_body);
            }
            if (str_contains($temp_body, '[sclass]')) {
                $temp_body =  str_replace('[sclass]', $client['sclass'], $temp_body);
            }

            // institution details

            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        } else if ($sms_type == 'on_withdraw') {

            // fetch personal info of the client
            $sqlQueryn = 'SELECT "firstName",name,membership_no,loan_wallet,acc_balance FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id  WHERE public."Client"."userId"=:id ';

            $stmtn = $this->conn->prepare($sqlQueryn);
            $stmtn->bindParam(':id', $client['id']);
            $stmtn->execute();

            $rown = $stmtn->fetch();

            // merge in the user personal info

            if (str_contains($temp_body, '[fname]')) {
                $temp_body =  str_replace('[fname]', $rown['firstName'], $temp_body);
            }
            if (str_contains($temp_body, '[actype]')) {
                $temp_body =  str_replace('[actype]', $rown['name'], $temp_body);
            }
            if (str_contains($temp_body, '[acno]')) {
                $temp_body =  str_replace('[acno]', $rown['membership_no'], $temp_body);
            }
            if ($rown['membership_no'] == 0) {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['loan_wallet'] - $client['charge']), $temp_body);
                }
            } else {
                if (str_contains($temp_body, '[balance]')) {
                    $temp_body =  str_replace('[balance]', number_format($rown['acc_balance'] - $client['charge']), $temp_body);
                }
            }

            // move on to the trxn details

            if (str_contains($temp_body, '[amount]')) {
                $temp_body =  str_replace('[amount]', number_format($client['amount']), $temp_body);
            }

            if (str_contains($temp_body, '[date_created]')) {
                $temp_body =  str_replace('[date_created]', $client['date'], $temp_body);
            }
            if (str_contains($temp_body, '[pay_method]')) {
                $temp_body =  str_replace('[pay_method]', $client['method'], $temp_body);
            }

            // institution details

            if (
                str_contains($temp_body, '[institution]') ||
                str_contains($temp_body, '[instphone]') ||
                str_contains($temp_body, '[instemail]') ||
                str_contains($temp_body, '[branch]')
            ) {

                //  if there's need for institution or branch info , then run this query else don't
                $sqlQuery = 'SELECT public."Branch".name AS bname,public."Bank".name AS baname,trade_name,bankcontacts,bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id  WHERE public."Branch".id=:id ';

                $stmt = $this->conn->prepare($sqlQuery);


                $stmt->bindParam(':id', $client['branch']);


                $stmt->execute();

                $row = $stmt->fetch();

                if (str_contains($temp_body, '[branch]')) {
                    $temp_body =  str_replace('[branch]', $row['bname'], $temp_body);
                }

                if (str_contains($temp_body, '[institution]')) {
                    $temp_body =  str_replace(
                        '[institution]',
                        $row['trade_name'] ?? $row['baname'],
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instphone]')) {
                    $temp_body =  str_replace(
                        '[instphone]',
                        $row['bankcontacts'] ?? '',
                        $temp_body
                    );
                }

                if (str_contains($temp_body, '[instemail]')) {
                    $temp_body =  str_replace(
                        '[instemail]',
                        $row['bankmail'] ?? '',
                        $temp_body
                    );
                }
            }
        }
        return $temp_body;
    }


    public function getBankLogo($id)
    {
        $sqlQuery = 'SELECT * FROM public."Bank"  WHERE public."Bank".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['logo'];
    }

    public function getBankContacts($id)
    {
        $sqlQuery = 'SELECT * FROM public."Bank"  WHERE public."Bank".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bankcontacts'];
    }

    public function getBankEmail($id)
    {
        $sqlQuery = 'SELECT * FROM public."Bank"  WHERE public."Bank".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bankmail'];
    }

    public function getBankLocation($id)
    {
        $sqlQuery = 'SELECT * FROM public."Bank"  WHERE public."Bank".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['location'];
    }

    public function getBranchLogo($id)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['logo'] ?? '';
    }

    public function updateClientBiometrics()
    {
        $sqlQuery = 'UPDATE "User" SET fingerprint=:fing WHERE id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':fing', $this->createdAt);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return true;
    }

    public function getClientDetails()
    {
        $sqlQuery = 'SELECT *,public."User"."addressLine1" AS uaddress,public."User"."country" AS ucountry,public."Client".id AS cid,
        public."User"."addressLine2" AS uaddress2, public."Business".name AS bname,public."Business"."addressLine1" AS baddress,
        public."Business"."addressLine2" AS baddress2,public."Business".city AS bcity,public."Business".country AS bcountry,
        "Client"."createdAt" AS screatedAt,"Client"."updatedAt" AS supdatedAt,"Client"."deletedAt" AS sdeletedAt
         FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."Business" ON public."Client".id=public."Business"."clientId" LEFT JOIN public."share_register" ON public."Client"."userId"=public."share_register".userid ';

        $id = $this->id ?? $this->mno ?? $this->clientId ?? $this->old_membership_no;

        if ($this->id) {
            $sqlQuery .= ' WHERE public."Client"."userId"=:id ';
        }

        if ($this->mno) {
            $sqlQuery .= ' WHERE public."Client"."membership_no"=:id ';
        }

        if ($this->clientId) {
            $sqlQuery .= ' WHERE public."Client".id=:id ';
        }

        if ($this->old_membership_no) {
            $sqlQuery .= ' WHERE public."Client".old_membership_no=:id ';
        }

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);


        $stmt->execute();
        return $stmt;
    }

    public function createClientAccount()
    {
        $sqlQuery = 'SELECT * FROM public."User"
        LEFT JOIN public."Client" ON public."User".id=public."Client"."userId"
        WHERE public."User".id=:id 
        ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        $sqlQuery = 'INSERT INTO public."User"(
	 "firstName", "lastName", email, gender, country, "addressLine1", "addressLine2", village, parish, subcounty, district, "primaryCellPhone", "secondaryCellPhone", "dateOfBirth", notes, confirmed, "spouseName", "spouseCell", nin, "spouseNin", "profilePhoto", sign, other_attachments, krelationship, kaddress, profession,status,shared_name)
	VALUES (:fname,:lname,:email,:gender,:country,:add1,:add2,:village,:parish,:subcount,:district,:pcell,:secondcell,:dob,:notes,:confir,:sname,:scell,:nin,:snin,:pphoto,:sign,:attachn,:krelat,:kaddress,:prof,:st,:sharedname)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':fname', $user['firstName']);
        $stmt->bindParam(':sharedname', $user['shared_name']);
        $stmt->bindParam(':lname', $user['lastName']);
        $stmt->bindParam(':email', $user['email']);
        $stmt->bindParam(':gender', $user['gender']);
        $stmt->bindParam(':country', $user['country']);
        $stmt->bindParam(':add1', $user['addressLine1']);
        $stmt->bindParam(':add2', $user['addressLine2']);
        $stmt->bindParam(':village', $user['village']);
        $stmt->bindParam(':parish', $user['parish']);
        $stmt->bindParam(':subcount', $user['subcounty']);
        $stmt->bindParam(':district', $user['district']);
        $stmt->bindParam(':pcell', $user['primaryCellPhone']);
        $stmt->bindParam(':secondcell', $user['secondaryCellPhone']);
        $stmt->bindParam(':dob', $user['dateOfBirth']);
        $stmt->bindParam(':notes', $user['notes']);
        $stmt->bindParam(':confir', $user['confirmed']);
        $stmt->bindParam(':sname', $user['spouseName']);
        $stmt->bindParam(':scell', $user['spouseCell']);
        $stmt->bindParam(':nin', $user['nin']);
        $stmt->bindParam(':snin', $user['spouseNin']);
        $stmt->bindParam(':pphoto', $user['profilePhoto']);
        $stmt->bindParam(':sign', $user['sign']);
        $stmt->bindParam(':attachn', $user['other_attachments']);
        $stmt->bindParam(':krelat', $user['krelationship']);
        $stmt->bindParam(':kaddress', $user['kaddress']);
        $stmt->bindParam(':prof', $user['profession']);
        $stmt->bindParam(':st', $user['status']);

        $stmt->execute();

        $uid =  $this->conn->lastInsertId();



        // update main client entry --- aand add the new uid to the multi_uid column
        $sqlQueryn = 'UPDATE  public."Client" SET mult_uids =:muid WHERE "userId"=:id';

        $stmtn = $this->conn->prepare($sqlQueryn);

        $existing_mult_uids = $user['mult_uids'] ?? '';
        if ($existing_mult_uids == '') {
            $multi_ids = $this->id . ',' . $uid;
        } else {
            $multi_ids = $existing_mult_uids . ',' . $uid;
        }

        $stmtn->bindParam(':id', $this->id);
        $stmtn->bindParam(':muid', $multi_ids);
        $stmtn->execute();

        $sqlQuery = 'INSERT INTO public."Client" 
        ("userId","branchId","serialNumber",membership_no,actype,message_consent
        ) VALUES(:uid,:bid,:sno,:mno,:actype,:mc)';

        $stmt = $this->conn->prepare($sqlQuery);
        $multi_ids = $this->id . ',' . $uid;
        $stmt->bindParam(':uid', $uid);
        $stmt->bindParam(':bid', $user['branchId']);
        $stmt->bindParam(':sno', $this->serialNumber);
        $stmt->bindParam(':mno', $this->mno);
        $stmt->bindParam(':actype', $this->account_id);
        $stmt->bindParam(':mc', $this->message_consent);
        $stmt->execute();

        $clientId =  $this->conn->lastInsertId();

        // get & return the new client entry details
        $clientQuery = 'SELECT * FROM public."Client" WHERE public."Client".id=:id ';
        $client = $this->conn->prepare($clientQuery);
        $client->bindParam(':id', $clientId);
        $client->execute();
        // return true;
        return $client->fetch(PDO::FETCH_ASSOC);
    }

    public function checkActiveLoans()
    {
        $sqlQuery = 'SELECT COUNT(*) AS num FROM public."loan" WHERE public."loan".account_id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['num'];
    }


    public function getClientRangeTransactions()
    {

        if ($this->createdAt && $this->createdAt != '') {
            $sqlQuery = 'SELECT *, public."transactions".entry_chanel AS tchanel, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."transactions".mid) AS c_name FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id LEFT JOIN public."User" ON 
        public."transactions".mid=public."User".id LEFT JOIN public."Client" ON public."transactions".mid=public."Client"."userId" WHERE  public."transactions"._status=1 AND public."transactions".mid=:id 
        
        AND DATE(public."transactions".date_created) >= :transaction_start_date AND DATE(public."transactions".date_created) <= :transaction_end_date ';

            if (@$this->by_tid) {

                $sqlQuery .= ' ORDER BY public."transactions".tid ASC ';
            }

            if (@$this->by_date) {

                $sqlQuery .= ' ORDER BY public."transactions".date_created ASC ';
            }

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':transaction_start_date', $this->createdAt);
            $stmt->bindParam(':transaction_end_date', $this->updatedAt);
        } else {
            $sqlQuery = 'SELECT *, public."transactions".entry_chanel AS tchanel, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."transactions".mid) AS c_name FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id LEFT JOIN public."User" ON 
        public."transactions".mid=public."User".id LEFT JOIN public."Client" ON public."transactions".mid=public."Client"."userId" WHERE  public."transactions"._status=1 AND public."transactions".mid=:id ';

            if (@$this->by_tid) {

                $sqlQuery .= ' ORDER BY public."transactions".tid ASC ';
            }

            if (@$this->by_date) {

                $sqlQuery .= ' ORDER BY public."transactions".date_created ASC ';
            }

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
        }





        $stmt->execute();
        return $stmt;
    }

    public function getClientRangeShareTransactions()
    {

        if ($this->createdAt && $this->createdAt != '') {
            $sqlQuery = 'SELECT *,(SELECT share_amount FROM public.share_register WHERE public.share_register.userid=public.share_purchases.user_id) AS tot_amount,(SELECT no_shares FROM public.share_register WHERE public.share_register.userid=public.share_purchases.user_id) AS tot_sh, (SELECT name FROM public."Branch" WHERE public."Branch".id=public.share_purchases.branch_id) AS bname, (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public.share_purchases.user_id) AS memb_no, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public.share_purchases.user_id) AS c_name FROM public.share_purchases WHERE public.share_purchases.user_id=:id 
        
        AND DATE(public.share_purchases.record_date) >= :transaction_start_date AND DATE(public.share_purchases.record_date) <= :transaction_end_date

         ORDER BY public.share_purchases.id ASC';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':transaction_start_date', $this->createdAt);
            $stmt->bindParam(':transaction_end_date', $this->updatedAt);
        } else {
            $sqlQuery = 'SELECT *,(SELECT share_amount FROM public.share_register WHERE public.share_register.userid=public.share_purchases.user_id) AS tot_amount,(SELECT no_shares FROM public.share_register WHERE public.share_register.userid=public.share_purchases.user_id) AS tot_sh, (SELECT name FROM public."Branch" WHERE public."Branch".id=public.share_purchases.branch_id) AS bname, (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public.share_purchases.user_id) AS memb_no, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public.share_purchases.user_id) AS c_name FROM public.share_purchases WHERE public.share_purchases.user_id=:id ORDER BY public.share_purchases.id ASC ';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
        }





        $stmt->execute();
        return $stmt;
    }


    // get share transfers
    public function getClientRangeShareTransactions2()
    {

        if ($this->createdAt && $this->createdAt != '') {
            $sqlQuery = 'SELECT *,(SELECT share_amount FROM public.share_register WHERE public.share_register.userid=public.share_transfers.to_uid) AS tot_amount,(SELECT no_shares FROM public.share_register WHERE public.share_register.userid=public.share_transfers.to_uid) AS tot_sh, (SELECT name FROM public."Branch" WHERE public."Branch".id=public.share_transfers.branch_id) AS bname, (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public.share_transfers.to_uid) AS memb_no, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public.share_transfers.to_uid) AS c_name FROM public.share_transfers WHERE public.share_transfers.to_uid=:id 
        
        AND DATE(public.share_transfers.record_date) >= :transaction_start_date AND DATE(public.share_transfers.record_date) <= :transaction_end_date

         ORDER BY public.share_transfers.tr_id ASC';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':transaction_start_date', $this->createdAt);
            $stmt->bindParam(':transaction_end_date', $this->updatedAt);
        } else {
            $sqlQuery = 'SELECT *,(SELECT share_amount FROM public.share_register WHERE public.share_register.userid=public.share_transfers.to_uid) AS tot_amount,(SELECT no_shares FROM public.share_register WHERE public.share_register.userid=public.share_transfers.to_uid) AS tot_sh, (SELECT name FROM public."Branch" WHERE public."Branch".id=public.share_transfers.branch_id) AS bname, (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public.share_transfers.to_uid) AS memb_no, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public.share_transfers.to_uid) AS c_name FROM public.share_transfers WHERE public.share_transfers.to_uid=:id  ORDER BY public.share_transfers.tr_id ASC ';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
        }





        $stmt->execute();
        return $stmt;
    }


    public function getShareAccDetails()
    {

        $sqlQuery = 'SELECT *, public."Branch".name AS bname,CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) AS client_name FROM public.share_register

            LEFT JOIN public."User" ON public."User".id = public.share_register.userid
            LEFT JOIN public."Client" ON public."Client"."userId" = public.share_register.userid
            LEFT JOIN public."Branch" ON public."Branch".id = public.share_register.branch_id
            
             WHERE public.share_register.userid=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        return $stmt;
    }


    public function getClientRangeShareTransactions3()
    {

        if ($this->createdAt && $this->createdAt != '') {
            $sqlQuery = 'SELECT *,(SELECT share_amount FROM public.share_register WHERE public.share_register.userid=public.share_transfers.from_uid) AS tot_amount,(SELECT no_shares FROM public.share_register WHERE public.share_register.userid=public.share_transfers.from_uid) AS tot_sh, (SELECT name FROM public."Branch" WHERE public."Branch".id=public.share_transfers.branch_id) AS bname, (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public.share_transfers.from_uid) AS memb_no, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public.share_transfers.from_uid) AS c_name FROM public.share_transfers WHERE public.share_transfers.from_uid=:id 
        
        AND DATE(public.share_transfers.record_date) >= :transaction_start_date AND DATE(public.share_transfers.record_date) <= :transaction_end_date

         ORDER BY public.share_transfers.tr_id ASC';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':transaction_start_date', $this->createdAt);
            $stmt->bindParam(':transaction_end_date', $this->updatedAt);
        } else {
            $sqlQuery = 'SELECT *,(SELECT share_amount FROM public.share_register WHERE public.share_register.userid=public.share_transfers.from_uid) AS tot_amount,(SELECT no_shares FROM public.share_register WHERE public.share_register.userid=public.share_transfers.from_uid) AS tot_sh, (SELECT name FROM public."Branch" WHERE public."Branch".id=public.share_transfers.branch_id) AS bname, (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public.share_transfers.from_uid) AS memb_no, (SELECT CONCAT(public."User"."firstName",\' \',public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public.share_transfers.from_uid) AS c_name FROM public.share_transfers WHERE public.share_transfers.from_uid=:id  ORDER BY public.share_transfers.tr_id ASC ';

            $stmt = $this->conn->prepare($sqlQuery);


            $stmt->bindParam(':id', $this->id);
        }





        $stmt->execute();
        return $stmt;
    }

    public function getInstitutionShareValue()
    {


        $sqlQuery = 'SELECT share_value FROM public."Client" LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" LEFT JOIN public."Bank" ON public."Bank".id=public."Branch"."bankId" WHERE public."Client"."userId"=:id

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $row = $stmt->fetch();
        return $row['share_value'];
    }

    public function getClientSharesBF()
    {
        // imported shares

        $sqlQuery = 'SELECT no_shares FROM public.share_register where public.share_register.userid=:id

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $row = $stmt->fetch();

        $current_shares = $row['no_shares'];

        // get from share purchases
        $sqlQuery = 'SELECT SUM(no_of_shares) AS sp FROM public.share_purchases where public.share_purchases.user_id=:id

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(
            ':id',
            $this->id
        );

        $stmt->execute();

        $row = $stmt->fetch();

        $p_shares = $row['sp'];


        // get from share transfers to
        $sqlQuery = 'SELECT SUM(no_shares) AS sp FROM public.share_transfers where public.share_transfers.to_uid=:id

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(
            ':id',
            $this->id
        );

        $stmt->execute();

        $row = $stmt->fetch();

        $to_shares = $row['sp'];


        // get from share transfers to
        $sqlQuery = 'SELECT SUM(no_shares) AS sp FROM public.share_transfers where public.share_transfers.from_uid=:id

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(
            ':id',
            $this->id
        );

        $stmt->execute();

        $row = $stmt->fetch();

        $from_shares = $row['sp'];


        $balance = $current_shares - ($p_shares + $to_shares - $from_shares);

        return $balance;
    }


    public function getClientSharesFiltered()
    {

        // get from share purchases
        $sqlQuery = 'SELECT SUM(no_of_shares) AS sp FROM public.share_purchases where public.share_purchases.user_id=:id AND DATE(public."share_purchases".record_date) < :from_date  

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(
            ':id',
            $this->id
        );
        $stmt->bindParam(
            ':from_date',
            $this->createdAt
        );

        $stmt->execute();

        $row = $stmt->fetch();

        $p_shares = $row['sp'];


        // get from share transfers to
        $sqlQuery = 'SELECT SUM(no_shares) AS sp FROM public.share_transfers where public.share_transfers.to_uid=:id AND DATE(public."share_transfers".record_date) < :from_date

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(
            ':id',
            $this->id
        );
        $stmt->bindParam(
            ':from_date',
            $this->createdAt
        );

        $stmt->execute();

        $row = $stmt->fetch();

        $to_shares = $row['sp'];


        // get from share transfers to
        $sqlQuery = 'SELECT SUM(no_shares) AS sp FROM public.share_transfers where public.share_transfers.from_uid=:id AND DATE(public."share_transfers".record_date) < :from_date

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(
            ':id',
            $this->id
        );
        $stmt->bindParam(
            ':from_date',
            $this->createdAt
        );

        $stmt->execute();

        $row = $stmt->fetch();

        $from_shares = $row['sp'];


        $balance = ($p_shares + $to_shares - $from_shares);

        return $balance;
    }

    public function getClientRangeTransactionsBF()
    {


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'D\',\'A\',\'LC\',\'E\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':transaction_start_date', $this->createdAt);

        $stmt->execute();

        $row = $stmt->fetch();
        $debit = $row['tot1'] ?? 0;


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'W\',\'LE\',\'C\',\'CW\',\'CS\',\'SMS\',\'LP\',\'RC\',\'I\',\'R\',\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':transaction_start_date', $this->createdAt);

        $stmt->execute();

        $rown = $stmt->fetch();
        $credit = $rown['tot1'] ?? 0;

        $sqlQuery = 'SELECT SUM(loan_interest) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':transaction_start_date', $this->createdAt);

        $stmt->execute();

        $rowx = $stmt->fetch();
        $credit2 = $rowx['tot1'] ?? 0;

        $total = $debit - $credit - $credit2;

        return $total ?? 0;
    }

    public function getClientRangeTransactionsBFEnd()
    {


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'D\',\'A\',\'LC\',\'E\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':transaction_start_date', $this->updatedAt);

        $stmt->execute();

        $row = $stmt->fetch();
        $debit = $row['tot1'] ?? 0;


        $sqlQuery = 'SELECT SUM(amount) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'W\',\'LE\',\'C\',\'CW\',\'CS\',\'SMS\',\'LP\',\'RC\',\'I\',\'R\',\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':transaction_start_date', $this->updatedAt);

        $stmt->execute();

        $rown = $stmt->fetch();
        $credit = $rown['tot1'] ?? 0;

        $sqlQuery = 'SELECT SUM(loan_interest) AS tot1 FROM public."transactions"  WHERE  public."transactions"._status=1 AND public."transactions".t_type IN(\'L\')  AND public."transactions".mid=:id AND DATE(public."transactions".date_created) < :transaction_start_date AND  entry_chanel=\'system\'

        ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':transaction_start_date', $this->updatedAt);

        $stmt->execute();

        $rowx = $stmt->fetch();
        $credit2 = $rowx['tot1'] ?? 0;

        $total = $debit - $credit - $credit2;

        return $total ?? 0;
    }

    public function getCashAccName($id)
    {
        $sqlQuery = 'SELECT name , account_code_used FROM public."Account" WHERE  public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['name'] . ' ( ' . $row['account_code_used'] . ' )';
    }
    public function getCashAccBalance($id)
    {
        $sqlQuery = 'SELECT balance FROM public."Account" WHERE  public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['balance'] ?? 0;
    }
    public function getCashAccBranch($id)
    {
        $sqlQuery = 'SELECT public."Branch".name AS bname FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE  public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['bname'] ?? '';
    }

    public function getStaffNames($id)
    {
        $sqlQuery = 'SELECT * FROM public."Account" LEFT JOIN public."staff_cash_accounts" ON public."Account".is_cash_account=public."staff_cash_accounts".id  WHERE  public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT * FROM public."User" LEFT JOIN public."Staff" ON public."User".id=public."Staff"."userId" WHERE "User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $row['userid']);
        $stmt->execute();
        $rown = $stmt->fetch();

        if ($rown) {
            return $rown['firstName'] . ' ' . $rown['lastName'];
        }
        return '';
    }

    public function getStaffRoleName($id)
    {
        $sqlQuery = 'SELECT * FROM public."Account" LEFT JOIN public."staff_cash_accounts" ON public."Account".is_cash_account=public."staff_cash_accounts".id  WHERE  public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT * FROM public."User" LEFT JOIN public."Staff" ON public."User".id=public."Staff"."userId" WHERE "User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $row['userid']);
        $stmt->execute();
        $rown = $stmt->fetch();

        if ($rown) {
            return $rown['positionTitle'];
        }
        return '';
    }

    public function getCashAccountDetails($id)
    {
        $sqlQuery = 'SELECT * FROM public."Account" LEFT JOIN public."staff_cash_accounts" ON public."Account".is_cash_account=public."staff_cash_accounts".id  WHERE  public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT * FROM public."User" LEFT JOIN public."Staff" ON public."User".id=public."Staff"."userId" WHERE "User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $row['userid']);
        $stmt->execute();
        $rown = $stmt->fetch();

        if ($rown) {
            return $rown['firstName'] . ' ' . $rown['lastName'] . ' ' . $rown['positionTitle'] . ' ( ' . $row['acc_name'] . ' - Bal: ' . number_format($row['balance']) . ' )';
        }
        return '';
    }

    public function getAccountBf($id, $start_date)
    {
        $sqlQuery = 'SELECT SUM(amount) AS tot, SUM(loan_interest) AS tot1 FROM public."transactions" WHERE  public."transactions"._status=1 AND (public."transactions".cash_acc=:id  OR public."transactions".cr_acid=:id OR public."transactions".dr_acid=:id ) AND  (DATE(public."transactions".date_created) < :starts)  AND public."transactions".t_type IN(\'W\',\'LE\',\'C\',\'CW\',\'CS\',\'SMS\',\'LP\',\'RC\',\'E\',\'TTS\',\'L\',\'STT\')';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':starts', $start_date);

        $stmt->execute();
        $row = $stmt->fetch();
        $sqlQuery = 'SELECT SUM(amount) AS tot FROM public."transactions" WHERE  public."transactions"._status=1 AND (public."transactions".cash_acc=:id  OR public."transactions".cr_acid=:id OR public."transactions".dr_acid=:id ) AND  (DATE(public."transactions".date_created) < :starts)  AND public."transactions".t_type IN(\'D\',\'A\',\'LC\',\'I\',\'STT\',\'ASS\',\'STT\')';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':starts', $start_date);


        $stmt->execute();
        $row1 = $stmt->fetch();

        $value = $row1['tot'] - ($row['tot'] + $row['tot1']);



        return $value ?? 0;
    }

    public function getJournalAccBfs()
    {

        $sqlQuery = 'SELECT * FROM public."Account" WHERE  public."Account".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->lastName);

        $stmt->execute();
        $row = $stmt->fetch();

        // fetch trxn balance
        $binding_array = [];
        $sqlQuery = 'SELECT SUM(amount) AS tot4 from transactions  where t_type=\'I\' AND  ';

        // $binding_array[':id'] = $row['id'];
        if (@$this->firstName) {
            $sqlQuery .= ' (transactions.acid::text=:id OR transactions.cr_acid=:id OR transactions.dr_acid=:id) ';
            $binding_array[':id'] = @$this->lastName;
        } else {

            $sqlQuery .= '  (transactions.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\') OR transactions.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$row['name'] . '\')) ';
            $binding_array[':bk'] = $this->deletedAt;
        }


        if (@$this->id && @$this->createdAt) {
            $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
            $binding_array[':transaction_start_date'] = @$this->id;
            $binding_array[':transaction_end_date'] = @$this->createdAt;
        }

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($binding_array);
        $rown = $stmt->fetch();

        $trxn_interest_paid_too = @$rown['tot4'] ?? 0;


        return $trxn_interest_paid_too;
    }

    public function getJournalAccountBf($id, $start_date)
    {
        $sqlQuery = 'SELECT SUM(amount) AS tot, SUM(loan_interest) AS tot1 FROM public."transactions" WHERE  public."transactions"._status=1 AND (public."transactions".bacid=:id  OR public."transactions".cr_acid=:id OR public."transactions".dr_acid=:id OR public."transactions".acid::text=:id ) AND  (DATE(public."transactions".date_created) < :starts) ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':starts', $start_date);

        $stmt->execute();
        $row = $stmt->fetch();



        $value = $row['tot'] + $row['tot1'];



        return $value ?? 0;
    }

    public function getBankAccountBf($id, $start_date)
    {

        $sqlQuery = 'SELECT bank_account FROM public."Account" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);

        $stmt->execute();
        $row1 = $stmt->fetch();

        $sqlQuery = 'SELECT SUM(amount) AS tot, SUM(loan_interest) AS tot1 FROM public."transactions" WHERE  public."transactions"._status=1 AND  (public."transactions".bacid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac)  OR public."transactions".cr_acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) OR public."transactions".dr_acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) OR public."transactions".acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) ) AND  (DATE(public."transactions".date_created) < :starts)  AND public."transactions".t_type IN(\'W\',\'LE\',\'C\',\'CW\',\'CS\',\'SMS\',\'LP\',\'RC\',\'E\',\'TTS\',\'L\',\'STT\',\'A\',\'BTS\',\'BTB\')';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bac', $row1['bank_account']);
        $stmt->bindParam(':starts', $start_date);

        $stmt->execute();
        $row = $stmt->fetch();
        $sqlQuery = 'SELECT SUM(amount) AS tot FROM public."transactions" WHERE  public."transactions"._status=1 AND  (public."transactions".bacid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac)  OR public."transactions".cr_acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) OR public."transactions".dr_acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) OR public."transactions".acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) ) AND  (DATE(public."transactions".date_created) < :starts)  AND public."transactions".t_type IN(\'D\',\'A\',\'LC\',\'I\',\'STT\',\'ASS\',\'STT\',\'AJE\',\'BTB\',\'STB\')';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bac', $row1['bank_account']);
        $stmt->bindParam(':starts', $start_date);


        $stmt->execute();
        $row1 = $stmt->fetch();

        $value = $row1['tot'] - ($row['tot'] + $row['tot1']);



        return $value ?? 0;
    }


    public function getAccountBf2($id, $start_date)
    {


        $sqlQuery = ' SELECT SUM(amount) AS tot, SUM(loan_interest) AS tot1 FROM public."transactions" WHERE  public."transactions"._status=1 AND ( public."transactions".cr_acid=:id OR public."transactions".dr_acid=:id OR public."transactions".acid::text=:id) AND  (DATE(public."transactions".date_created) < :starts)  AND public."transactions".t_type IN(\'STT\')';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':starts', $start_date);

        $stmt->execute();
        $row = $stmt->fetch();
        $sqlQuery = 'SELECT SUM(amount) AS tot FROM public."transactions" WHERE  public."transactions"._status=1 AND ( public."transactions".cr_acid::uuid=:id OR public."transactions".dr_acid::uuid=:id OR public."transactions".acid=:id ) AND  (DATE(public."transactions".date_created) < :starts)  AND public."transactions".t_type IN(\'TTS\')';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':starts', $start_date);


        $stmt->execute();
        $row1 = $stmt->fetch();

        $value = $row1['tot'] - ($row['tot'] + $row['tot1']);



        return $value ?? 0;
    }

    public function getBankRangeTransactions()
    {

        $sqlQuery = 'SELECT bank_account FROM public."Account" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
        $row1 = $stmt->fetch();

        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id LEFT JOIN public."User" ON 
        public."transactions"._authorizedby=public."User".id LEFT JOIN public."Client" ON public."transactions".mid=public."Client"."userId" WHERE  public."transactions"._status=1 AND (public."transactions".bacid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac)  OR public."transactions".cr_acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) OR public."transactions".dr_acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) OR public."transactions".acid::text IN(SELECT id::text FROM public."Account" WHERE bank_account=:bac) ) AND pay_method IN(\'cheque\',\'cash\') AND  (DATE(public."transactions".date_created) BETWEEN :starts AND :endd) ORDER BY public."transactions".tid ASC';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':bac', $row1['bank_account']);
        $stmt->bindParam(':starts', $this->createdAt);
        $stmt->bindParam(':endd', $this->updatedAt);


        $stmt->execute();
        return $stmt;
    }


    public function getJournalRangeTransactions()
    {
        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id LEFT JOIN public."User" ON 
        public."transactions"._authorizedby=public."User".id  WHERE  public."transactions"._status=1 AND (public."transactions".bacid=:id  OR public."transactions".cr_acid=:id OR public."transactions".dr_acid=:id OR public."transactions".acid::text=:id ) AND  (DATE(public."transactions".date_created) BETWEEN :starts AND :endd) ORDER BY public."transactions".tid ASC';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':starts', $this->createdAt);
        $stmt->bindParam(':endd', $this->updatedAt);


        $stmt->execute();
        return $stmt;
    }


    public function getStaffRangeTransactions()
    {
        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id LEFT JOIN public."User" ON 
        public."transactions"._authorizedby=public."User".id LEFT JOIN public."Client" ON public."transactions".mid=public."Client"."userId" WHERE  public."transactions"._status=1 AND (public."transactions".cash_acc=:id  OR public."transactions".cr_acid=:id OR public."transactions".dr_acid=:id OR public."transactions".acid::text=:id ) AND pay_method=\'cash\' AND  (DATE(public."transactions".date_created) BETWEEN :starts AND :endd) ORDER BY public."transactions".tid ASC';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':starts', $this->createdAt);
        $stmt->bindParam(':endd', $this->updatedAt);


        $stmt->execute();
        return $stmt;
    }

    public function getSafeRangeTransactions()
    {



        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."Branch" ON public."transactions"._branch=public."Branch".id LEFT JOIN public."User" ON 
        public."transactions"._authorizedby=public."User".id LEFT JOIN public."Client" ON public."transactions".mid=public."Client"."userId" WHERE  public."transactions"._status=1 AND (public."transactions".cr_acid=:id OR public."transactions".dr_acid=:id OR public."transactions".acid::text=:id ) AND  (DATE(public."transactions".date_created) BETWEEN :starts AND :endd) ORDER BY public."transactions".tid ASC';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':starts', $this->createdAt);
        $stmt->bindParam(':endd', $this->updatedAt);


        $stmt->execute();
        return $stmt;
    }

    public function getClientAccounts()
    {
        $db_handler = new DbHandler();

        $client = $db_handler->fetch('Client', 'userId', $this->id);
        if (!@$client) return [];
        $mult_uids = $client['mult_uids'];
        $user_ids = [$this->id];
        // $user_ids = array_push($user_ids, $this->id);
        // $user_ids = array_push($user_ids, ',');

        /**
         * convert ids to array
         */
        if (@$mult_uids) {
            $user_ids = explode(',', $mult_uids);
        }


        $client_accounts = $db_handler->database->fetchAll('SELECT * FROM `Client` LEFT JOIN savingaccounts ON `Client`.actype=savingaccounts.id WHERE `userId` IN (?)', $user_ids);
        return $client_accounts;
        // $account_ids = array_column($client_accounts, 'actype');
        // return $db_handler->database->fetchAll('SELECT * FROM savingaccounts WHERE id IN (?)', $account_ids);
    }

    public function getAccountType($acid)
    {
        $sqlQuery = 'SELECT * FROM public."savingaccounts" WHERE public."savingaccounts".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $acid);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'];
    }

    public function getAccountName($acid)
    {
        $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $acid);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'] ?? '';
    }

    public function getAccountNameBalance($acid)
    {
        $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $acid);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'] . ': Balance: ' . number_format($row['balance']);
    }

    public function getJournalAccountType($acid)
    {
        $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $acid);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['type'] ?? '';
    }

    public function setClientAccountNumber($acno, $cid)
    {
        $sqlQuery = 'UPDATE public."Client" SET membership_no=:ac  WHERE id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $cid);
        $stmt->bindParam(':ac', $acno);


        $stmt->execute();
        return $acno;
    }

    public function getAccountCode($acc)
    {
        $sqlQuery = 'SELECT * FROM public."savingaccounts" WHERE id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $acc);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }

    public function getBranchCode($branch)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $branch);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bcode'];
    }

    public function loginStaff()
    {
        $stt = 'ACTIVE';
        $sqlQuery = 'SELECT id FROM public."User" WHERE "email"=:email AND password=:pass AND status=:stt';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':pass', $this->password);
        $stmt->bindParam(':stt', $stt);

        $stmt->execute();
        return $stmt;
    }

    public function getBankName($id)
    {
        $sqlQuery = 'SELECT name FROM public."Bank" WHERE  id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'];
    }
    public function getBankTradeName($id)
    {
        $sqlQuery = 'SELECT trade_name FROM public."Bank" WHERE  id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['trade_name'];
    }
    public function getBranchName($id)
    {
        $sqlQuery = 'SELECT name FROM public."Branch" WHERE  id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['name'] ?? '';
    }
    public function getBranchBankName($id)
    {
        $sqlQuery = 'SELECT public."Bank".name AS bname FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE  public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bname'];
    }

    public function getBranchBankTradeName($id)
    {
        $sqlQuery = 'SELECT public."Bank".trade_name AS bname FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE  public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bname'] ?? '';
    }

    public function getBranchContacts($id)
    {
        $sqlQuery = 'SELECT bankcontacts FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE  public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bankcontacts'] ?? '';
    }

    public function getBranchEmail($id)
    {
        $sqlQuery = 'SELECT bankmail FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE  public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['bankmail'] ?? '';
    }

    public function getBranchLocation($id)
    {
        $sqlQuery = 'SELECT public."Bank".location AS blocc FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE  public."Branch".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['blocc'] ?? '';
    }

    public function getStaffDetails()
    {
        $sqlQuery = 'SELECT *,"Staff"."createdAt" AS screatedAt,"Staff"."updatedAt" AS supdatedAt,"Staff"."deletedAt" AS sdeletedAt FROM public."User" LEFT JOIN public."Staff" ON public."User".id=public."Staff"."userId" WHERE "User".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $this->id);


        $stmt->execute();
        return $stmt;
    }



    public function uploadAttachments($body) {
    
        $files = $body['files'];
        $cid = $body['cid'];
        $uid = $body['uid'];
    
        $uploadDir = '../uploads/clients/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        $photoPath = null;
        $signPath = null;
        $fingerprintPath = null;
        $otherAttachPath = null;
    
        try {
            if (isset($files['photo']) && $files['photo']['error'] === 0) {
                $photoName = uniqid('photo_') . '_' . basename($files['photo']['name']);
                $photoPath = $uploadDir . $photoName;
                move_uploaded_file($files['photo']['tmp_name'], $photoPath);
            }
    
            if (isset($files['sign']) && $files['sign']['error'] === 0) {
                $signName = uniqid('sign_') . '_' . basename($files['sign']['name']);
                $signPath = $uploadDir . $signName;
                move_uploaded_file($files['sign']['tmp_name'], $signPath);
            }
    
            if (isset($files['fingerprint']) && $files['fingerprint']['error'] === 0) {
                $fingerName = uniqid('finger_') . '_' . basename($files['fingerprint']['name']);
                $fingerprintPath = $uploadDir . $fingerName;
                move_uploaded_file($files['fingerprint']['tmp_name'], $fingerprintPath);
            }
    
            if (isset($files['otherattach']) && $files['otherattach']['error'] === 0) {
                $otherAttachName = uniqid('other_') . '_' . basename($files['otherattach']['name']);
                $otherAttachPath = $uploadDir . $otherAttachName;
                move_uploaded_file($files['otherattach']['tmp_name'], $otherAttachPath);
            }


            $stmt = $this->conn->prepare("
                UPDATE User
                SET 
                    profilePhoto = :photo,
                    sign = :sign,
                    fingerprint = :fingerprint,
                    other_attachments = :otherattach
                WHERE id = :cid
            ");

            $stmt->bindParam(':photo', $photoPath);
            $stmt->bindParam(':sign', $signPath);
            $stmt->bindParam(':fingerprint', $fingerprintPath);
            $stmt->bindParam(':otherattach', $otherAttachPath);
            $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Success! Redirect back or show message
                return $stmt;
                // exit();
            } else {
                throw new Exception('Database update failed');
            }
    
        } catch (Exception $e) {
            error_log("Attachment upload error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    
}
