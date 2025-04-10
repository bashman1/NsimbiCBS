<?php
require_once('Fee.php');
require_once('AuditTrail.php');
require_once '../../config/functions.php';
class FieldAgents
{
    public $conn;
    public $bank_id;
    public $branch_id;
    public $type;
    public $with_income_totals;
    public $with_expenditure_totals;
    public $with_liabilities_totals;
    public $with_capital_totals;
    public $with_assets_totals;
    public $transaction_start_date;
    public $transaction_end_date;
    public $is_trial_balance;
    public $request;
    public $acc_name;
    public $pay_method;
    public $cash_acc;

    public $account_id;
    public $details;
    public $user_id;

    public $email;
    public $password;
    public $actby;
    public $actbyphone;

    public $auth_id;

    public $amount;
    public $uid;

    public $left_balance;
    public $t_type;
    public $createdAt;
    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function undoFixedClosure($id)
    {

        $sqlQuery = 'select * from  public."fixed_deposits" where fd_id=:id';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $trxn = $stmt->fetch();

        $sqlQuery = 'SELECT * FROM  public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $trxn['fd_branch']);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row['fd_int_acid']) {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['fd_princ_acid']);
            $stmt->bindParam(':ac',  $trxn['fd_int_paid']);
            $stmt->execute();
        }
        if ($row['fd_wht_acid']) {
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:ac WHERE public."Account".id=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['fd_princ_acid']);
            $stmt->bindParam(':ac',  $trxn['wht_paid']);
            $stmt->execute();
        }


        $sqlQuery = 'UPDATE public."fixed_deposits" SET fd_status=0, fd_int_paid=0,wht_paid=0 WHERE fd_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return true;
    }
    public function reverseTrxn()
    {
        $sqlQuery = 'select * from  public."transactions" where tid=:id';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->auth_id);
        $stmt->execute();
        $trxn = $stmt->fetch();
        if ($trxn['_status'] == 1) {
            $ttype = '';
            if ($trxn['t_type'] == 'D' || $trxn['t_type'] == 'E') {
                $ttype = 'W';
            }
            if ($trxn['t_type'] == 'W' || $trxn['t_type'] == 'I' || $trxn['t_type'] == 'L') {
                $ttype = 'D';
            }

            $amount = $trxn['amount'] + $trxn['loan_interest'];
            $is_reversal = 1;

            if (
                $trxn['t_type'] == 'W' || $trxn['t_type'] == 'D' || $trxn['t_type'] == 'I'
            ) {
                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,date_created,charges,is_reversal,reversal_tid) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:date_created,:charges,:is_reversal,:reversal_tid)';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':is_reversal', $is_reversal);
                $stmt->bindParam(':reversal_tid', $trxn['tid']);
                $stmt->bindParam(':descri', $trxn['description']);
                $stmt->bindParam(':autho', $trxn['_authorizedby']);
                $stmt->bindParam(':actby', $trxn['_actionby']);
                $stmt->bindParam(':accname', $trxn['acc_name']);
                $stmt->bindParam(':mid', $trxn['mid']);
                $stmt->bindParam(':approv', $trxn['_authorizedby']);
                $stmt->bindParam(':branc', $trxn['_branch']);
                $stmt->bindParam(':leftbal', $trxn['left_balance']);
                $stmt->bindParam(':ttype', $ttype);
                $stmt->bindParam(':acid', $trxn['acid']);
                $stmt->bindParam(':pay_method', $trxn['pay_method']);
                $stmt->bindParam(':bacid', $trxn['bacid']);
                $stmt->bindParam(':cheque', $trxn['cheque_no']);
                $stmt->bindParam(':cash_acc', $trxn['cash_acc']);
                // $stmt->bindParam(':send_sms', $this->send_sms);
                $stmt->bindParam(':date_created', $trxn['date_created']);
                $stmt->bindParam(':charges', $trxn['charges']);
                // $stmt->bindParam(':li', $trxn['loan_interest']);


                $stmt->execute();
            }
            // if savings account is involved update account balance accordingly
            if ($trxn['mid'] > 0) {
                if ($ttype == 'W') {
                    $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance-:am WHERE "userId"=:id';
                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':id', $trxn['mid']);
                    $stmt->bindParam(':am', $amount);
                    $stmt->execute();
                } else if ($ttype == 'D') {
                    $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:am WHERE "userId"=:id';
                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':id', $trxn['mid']);
                    $stmt->bindParam(':am', $amount);
                    $stmt->execute();
                }
            }

            // for loan repayment update schedule principal_paid, interest_paid, oustanding_principal, oustanding_interest
            if ($trxn['t_type'] == 'L') {
                $loan_id = $trxn['loan_id'] ?? 0;
                $princ = $trxn['amount'] ?? 0;
                $interest = $trxn['loan_interest'] ?? 0;

                if ($princ > 0) {
                    $princ_bal = $princ;

                    while ($princ_bal > 0) {
                        $sqlQuery = 'SELECT * FROM loan_schedule where loan_id=:loan_id AND principal_paid>0 ORDER BY schedule_id DESC LIMIT 1';
                        $stmt = $this->conn->prepare($sqlQuery);

                        $stmt->bindParam(':loan_id', $loan_id);
                        $stmt->execute();

                        $row = $stmt->fetch();
                        if ($row['principal_paid'] <= $princ_bal) {
                            $amount_paid = $row['principal_paid'];
                            $princ_bal = $princ_bal - $amount_paid;
                            $sqlQuery = 'UPDATE loan_schedule SET principal_paid=0, status=\'active\', outstanding_principal=outstanding_principal + :pp WHERE schedule_id=:schedule_id';
                            $stmt = $this->conn->prepare($sqlQuery);

                            $stmt->bindParam(':schedule_id', $row['schedule_id']);
                            $stmt->bindParam(':pp', $amount_paid);
                            $stmt->execute();
                        } else {
                            $amount_paid = $princ_bal;
                            $princ_bal = $princ_bal - $amount_paid;
                            $sqlQuery = 'UPDATE loan_schedule SET principal_paid=principal_paid - :pp, status=\'active\', outstanding_principal=outstanding_principal + :pp WHERE schedule_id=:schedule_id';
                            $stmt = $this->conn->prepare($sqlQuery);

                            $stmt->bindParam(':schedule_id', $row['schedule_id']);
                            $stmt->bindParam(':pp', $amount_paid);
                            $stmt->execute();
                        }
                    }
                }

                if ($interest > 0) {
                    $int_bal = $interest;

                    while ($int_bal > 0) {
                        $sqlQuery = 'SELECT * FROM loan_schedule where loan_id=:loan_id AND interest_paid>0 ORDER BY schedule_id DESC LIMIT 1';
                        $stmt = $this->conn->prepare($sqlQuery);

                        $stmt->bindParam(':loan_id', $loan_id);
                        $stmt->execute();

                        $row = $stmt->fetch();
                        if ($row['interest_paid'] <= $int_bal) {
                            $amount_paid = $row['interest_paid'];
                            $int_bal = $int_bal - $amount_paid;
                            $sqlQuery = 'UPDATE loan_schedule SET interest_paid=0, status=\'active\', outstanding_interest=outstanding_interest + :pp WHERE schedule_id=:schedule_id';
                            $stmt = $this->conn->prepare($sqlQuery);

                            $stmt->bindParam(':schedule_id', $row['schedule_id']);
                            $stmt->bindParam(':pp', $amount_paid);
                            $stmt->execute();
                        } else {
                            $amount_paid = $int_bal;
                            $int_bal = $int_bal - $amount_paid;
                            $sqlQuery = 'UPDATE loan_schedule SET interest_paid=interest_paid - :pp, status=\'active\', outstanding_interest=outstanding_interest + :pp WHERE schedule_id=:schedule_id';
                            $stmt = $this->conn->prepare($sqlQuery);

                            $stmt->bindParam(':schedule_id', $row['schedule_id']);
                            $stmt->bindParam(':pp', $amount_paid);
                            $stmt->execute();
                        }
                    }
                }

                if (
                    $trxn['t_type'] == 'L' || $trxn['t_type'] == 'E'
                ) {
                    // just delete transaction
                    $sqlQuery = 'DELETE FROM public."transactions" WHERE tid=:tid  ';
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':tid', $this->auth_id);
                    $stmt->execute();
                }

                // update loan details balances, by calling the rectify loan
                $this->sortLoanBalances($loan_id);
            }

            return true;
        } else {
            // delete entry
            $sqlQuery = 'DELETE FROM public."transactions" WHERE tid=:tid  ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':tid', $this->auth_id);
            $stmt->execute();

            return true;
        }


        return false;
    }
    public function sortLoanBalances($lid)
    {

        // $this->updateTotalLoanAmount(9475);

        $sqlQuery = 'select * from loan where loan_no=:lno';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'select SUM(amount) AS p_paid from transactions where loan_id=:lno AND t_type=\'L\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->execute();
        $rown = $stmt->fetch();
        $p_paid = $rown['p_paid'] ?? 0;

        $sqlQuery = 'select SUM(loan_interest) AS i_paid from transactions where loan_id=:lno AND t_type=\'L\'';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->execute();
        $rown = $stmt->fetch();
        $i_paid = $rown['i_paid'] ?? 0;

        $p_bal = $row['principal'] - $p_paid;
        $i_bal = $row['interest_amount'] - $i_paid;
        $t_bal = $p_bal + $i_bal;
        $t_paid = $p_paid + $i_paid;

        $sqlQuery = 'update loan set principal_balance=:p_bal, interest_balance=:i_bal,current_balance=:t_bal,amount_paid=:t_paid, status=3 where loan_no=:lno
';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lno', $lid);
        $stmt->bindParam(':p_bal', $p_bal);
        $stmt->bindParam(':i_bal', $i_bal);
        $stmt->bindParam(':t_bal', $t_bal);
        $stmt->bindParam(':t_paid', $t_paid);
        $stmt->execute();

        return true;
    }
    public function trashTrxn($trashReason, $trashedBy, $trashedDate)
    {
        // Fetch the transaction to be deleted
        $sqlQuery = 'SELECT * FROM public."transactions" WHERE tid = :id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->auth_id, PDO::PARAM_INT); // tid is an integer
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $trxn = $stmt->fetch();

        if ($trxn) {
            // Prepare columns and placeholders
            $columns = array_keys($trxn);
            $columnsList = implode(', ', array_map(fn($col) => '"' . $col . '"', $columns)) . ', "trash_reason", "trashed_by", "trash_date"';
            $placeholders = implode(', ', array_map(fn($col) => ':' . $col, $columns)) . ', :trash_reason, :trashed_by, :trash_date';

            // Prepare the INSERT query
            $sqlQuery = 'INSERT INTO public."trash_transactions" (' . $columnsList . ') VALUES (' . $placeholders . ')';
            $stmt = $this->conn->prepare($sqlQuery);

            // Bind transaction values dynamically with type checks
            foreach ($trxn as $key => $value) {
                if (is_null($value)) {
                    $stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
                } elseif (in_array($key, $this->getDoublePrecisionColumns())) {
                    $stmt->bindValue(':' . $key, (float)$value, PDO::PARAM_STR);
                } elseif (in_array($key, $this->getIntegerColumns())) {
                    $stmt->bindValue(':' . $key, (int)$value, PDO::PARAM_INT);
                } elseif (in_array($key, $this->getBooleanColumns())) {
                    $stmt->bindValue(':' . $key, $value ? true : false, PDO::PARAM_BOOL);
                } else {
                    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }

            // Bind additional trash fields
            $stmt->bindValue(':trash_reason', $trashReason, PDO::PARAM_STR);
            $stmt->bindValue(':trashed_by', $trashedBy, PDO::PARAM_INT);
            $stmt->bindValue(':trash_date', $trashedDate, PDO::PARAM_STR);

            // Execute the INSERT query
            $stmt->execute();

            // Remaining operations: Adjust balances and delete the transaction
            $this->handleBalanceAdjustments($trxn);
            $this->deleteTransaction($this->auth_id);

            return true;
        } else {
            // Transaction not found
            return false;
        }
    }

    // Helper to identify double precision columns
    private function getDoublePrecisionColumns()
    {
        return [
            'amount',
            'outstanding_amount',
            'outstanding_amount_total',
            'loan_interest',
            'outstanding_interest',
            'outstanding_interest_total',
            'left_balance',
            'charges',
            'loan_penalty'
        ];
    }

    // Helper to identify integer columns
    private function getIntegerColumns()
    {
        return [
            '_authorizedby',
            '_status',
            'mid',
            'loan_id',
            'approvedby',
            '_feeid',
            'schedule_id',
            'send_sms_parent',
            'send_sms_school',
            'is_transfer',
            'is_reversal',
            'reversal_tid',
            'agent_loan_amount',
            'loan_payment_status',
            'trashed_by'
        ];
    }

    // Helper to identify boolean columns
    private function getBooleanColumns()
    {
        return ['send_sms'];
    }

    // Adjust balances after trashing a transaction
    private function handleBalanceAdjustments($trxn)
    {
        if ($trxn['_status'] == 1) {
            // Adjust the client's account balance
            $sqlQuery = 'UPDATE public."Client" SET acc_balance = acc_balance - :amount WHERE "userId" = :id';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $trxn['mid'], PDO::PARAM_INT);
            $stmt->bindParam(':amount', $trxn['amount'], PDO::PARAM_STR);
            $stmt->execute();

            // Adjust the account balance if valid UUID
            if (!empty($trxn['acid'])) {
                $sqlQuery = 'UPDATE public."Account" SET balance = balance - :amount WHERE id = :id';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $trxn['acid'], PDO::PARAM_STR);
                $stmt->bindParam(':amount', $trxn['amount'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    // Delete the transaction
    private function deleteTransaction($transactionId)
    {
        $sqlQuery = 'DELETE FROM public."transactions" WHERE tid = :tid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':tid', $transactionId, PDO::PARAM_INT);
        $stmt->execute();
    }



    public function trashTrxnWithdraw($trashReason, $trashedBy, $trashedDate)
    {
        // Fetch the transaction to be deleted
        $sqlQuery = 'SELECT * FROM public."transactions" WHERE tid = :id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->auth_id, PDO::PARAM_INT); // tid is an integer
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $trxn = $stmt->fetch();

        if ($trxn) {
            // Prepare columns and placeholders
            $columns = array_keys($trxn);
            $columnsList = implode(', ', array_map(fn($col) => '"' . $col . '"', $columns)) . ', "trash_reason", "trashed_by", "trash_date"';
            $placeholders = implode(', ', array_map(fn($col) => ':' . $col, $columns)) . ', :trash_reason, :trashed_by, :trash_date';

            // Prepare the INSERT query
            $sqlQuery = 'INSERT INTO public."trash_transactions" (' . $columnsList . ') VALUES (' . $placeholders . ')';
            $stmt = $this->conn->prepare($sqlQuery);

            // Bind transaction values dynamically with type checks
            foreach ($trxn as $key => $value) {
                if (is_null($value)) {
                    $stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
                } elseif (in_array($key, $this->getDoublePrecisionColumns())) {
                    $stmt->bindValue(':' . $key, (float)$value, PDO::PARAM_STR);
                } elseif (in_array($key, $this->getIntegerColumns())) {
                    $stmt->bindValue(':' . $key, (int)$value, PDO::PARAM_INT);
                } elseif (in_array($key, $this->getBooleanColumns())) {
                    $stmt->bindValue(':' . $key, $value ? true : false, PDO::PARAM_BOOL);
                } else {
                    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
                }
            }

            // Bind additional trash fields
            $stmt->bindValue(':trash_reason', $trashReason, PDO::PARAM_STR);
            $stmt->bindValue(':trashed_by', $trashedBy, PDO::PARAM_INT);
            $stmt->bindValue(':trash_date', $trashedDate, PDO::PARAM_STR);

            // Execute the INSERT query
            $stmt->execute();

            // Adjust balances if the transaction status is active
            if ($trxn['_status'] == 1) {
                // Offset money from the client's account
                $sqlQuery = 'UPDATE public."Client" SET acc_balance = acc_balance + :amount WHERE "userId" = :id';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':id', $trxn['mid']);
                $stmt->bindParam(':amount', $trxn['amount']);
                $stmt->execute();

                // Offset money from the account balance
                if (@$trxn['acid'] && is_valid_uuid(@$trxn['acid'])) {
                    $sqlQuery = 'UPDATE public."Account" SET balance = balance + :amount WHERE id = :id';
                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':id', $trxn['acid']);
                    $stmt->bindParam(':amount', $trxn['amount']);
                    $stmt->execute();
                }


                // Handle cash transactions
                if ($trxn['pay_method'] == 'cash' && is_valid_uuid($trxn['cash_acc'])) {
                    if ($trxn['_authorizedby'] == $trxn['approvedby']) {
                        // Update cash account balance
                        $sqlQuery = 'UPDATE public."Account" SET balance = balance + :amount WHERE id = :id';
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $trxn['cash_acc']);
                        $stmt->bindParam(':amount', $trxn['amount']);
                        $stmt->execute();
                    } else {
                        // Fetch approvedby's cash account
                        $sqlQuery = 'SELECT public."Account".id AS aid 
                                 FROM public."staff_cash_accounts" 
                                 LEFT JOIN public."Account" ON public."staff_cash_accounts".id = public."Account".is_cash_account 
                                 WHERE public."staff_cash_accounts".userid = :id AND public."staff_cash_accounts".branchid = :bid';
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $trxn['approvedby']);
                        $stmt->bindParam(':bid', $trxn['_branch']);
                        $stmt->execute();
                        $rown = $stmt->fetch();

                        // Update cash account balance
                        $sqlQuery = 'UPDATE public."Account" SET balance = balance + :amount WHERE id = :id';
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->bindParam(':id', $rown['aid']);
                        $stmt->bindParam(':amount', $trxn['amount']);
                        $stmt->execute();
                    }
                }
            }

            // Delete the transaction from the transactions table
            $sqlQuery = 'DELETE FROM public."transactions" WHERE tid = :tid';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':tid', $this->auth_id);
            $stmt->execute();

            return true;
        } else {
            // Transaction not found
            return false;
        }
    }


    public function approveTrxn()
    {
        // get trxn details

        $sqlQuery = 'select * from  public."transactions" where tid=:id';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->auth_id);
        $stmt->execute();
        $trxn = $stmt->fetch();

        $cash_acc = 0;
        $sender = '';
        $receiver = '';

        /* agent cash account update */

        // get agent cash account id
        $sqlQuery = 'SELECT public."Account".id AS aid FROM  public."staff_cash_accounts" LEFT JOIN public."Account" ON public."staff_cash_accounts".id = public."Account".is_cash_account  WHERE public."staff_cash_accounts".userid=:id AND public."staff_cash_accounts".branchid=:bid';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $trxn['_authorizedby']);
        $stmt->bindParam(':bid', $trxn['_branch']);
        $stmt->execute();
        $rown = $stmt->fetch();

        // update cash account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $rown['aid']);
        $stmt->bindParam(':amount', $trxn['amount']);
        $stmt->execute();

        $sender = $rown['aid'];
        /* update cash account of the one approving */

        // get cashier cash account id
        $sqlQuery = 'SELECT public."Account".id AS aid FROM  public."staff_cash_accounts" LEFT JOIN public."Account" ON public."staff_cash_accounts".id = public."Account".is_cash_account  WHERE public."staff_cash_accounts".userid=:id AND public."staff_cash_accounts".branchid=:bid';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->uid);
        $stmt->bindParam(':bid', $trxn['_branch']);
        $stmt->execute();
        $rown = $stmt->fetch();

        // update cash account balance
        $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $rown['aid']);
        $stmt->bindParam(':amount', $trxn['amount']);
        $stmt->execute();
        $receiver = $rown['aid'];
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
        $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, description, _authorizedby, mid, approvedby, _branch, cr_acid,dr_acid)
        VALUES (:amount,:ttype,:descri,:_auth,:mid,:apby,:bran,:crid,:drid)
                ';

        $stmt = $this->conn->prepare($sqlQuery);
        $mid = 0;
        $tty = 'TTT';
        $descr = 'Agent to Teller Cash Transfer on Approval of Trxn- REF:-' . $this->auth_id;
        $stmt->bindParam(':amount', $trxn['amount']);
        $stmt->bindParam(':ttype', $tty);
        $stmt->bindParam(':descri', $descr);
        $stmt->bindParam(':_auth', $this->uid);
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':apby', $this->uid);
        $stmt->bindParam(':bran', $trxn['_branch']);
        $stmt->bindParam(':crid', $receiver);
        $stmt->bindParam(':drid', $sender);

        $stmt->execute();


        //  get bank id from branch
        $sqlQuery = 'select "bankId" from  public."Branch" where id=:id';
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $trxn['_branch']);
        $stmt->execute();
        $branch = $stmt->fetch();
        $charges = 0;
        // check whether branch -- bank has deposit charges 
        $sqlQuery = 'select * from  public."transaction_charges" where bankid=:id and c_status=1 and c_application=:appln';
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
                        $charges = ($use['charge'] / 100) * $trxn['amount'];
                    }

                    $cid = $use['c_id'];
                } else {
                    if (($use['min_amount'] <= $trxn['amount']) && ($trxn['amount'] <= $use['max_amount'])) {
                        if ($use['charge_mode'] == 'fixed') {
                            $charges = $use['charge'];
                        } else {
                            $charges = ($use['charge'] / 100) * $trxn['amount'];
                        }
                        $cid = $use['c_id'];
                    }
                }
            }
        }


        // get client names,left balance
        $sqlQuery = 'SELECT *, public."Client".id AS client_id FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $trxn['mid']);
        $stmt->execute();
        $row = $stmt->fetch();
        $client = $row;

        $aid = 0;
        if ($row['membership_no'] > 0) {
            $this->left_balance = $row['acc_balance'] + $trxn['amount'];


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
                $stmt->bindParam(':amount', $trxn['amount']);
                $stmt->execute();
            }
        } else {
            $this->left_balance = $row['loan_wallet'] + $trxn['amount'];
        }
        $this->acc_name = $row['firstName'] . ' ' . $row['lastName'];
        $this->t_type = 'D';

        $sqlQuery = 'UPDATE public."transactions" SET _status=:st, approvedby=:apby WHERE tid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $st = 1;
        $stmt->bindParam(':st', $st);

        $stmt->bindParam(':id', $this->auth_id);
        $stmt->bindParam(':apby', $this->uid);


        $stmt->execute();

        if ($charges > 0) {
            $this->t_type = 'I';
            $desc = 'Deposit Fees';
            $pmethod = 'saving';
            $this->left_balance = $this->left_balance - $charges;

            $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,cash_acc,date_created,charges) VALUES
          (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:cash_acc,:date_created,:charges)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':amount', $charges);
            $stmt->bindParam(':descri', $desc);
            $stmt->bindParam(':autho', $trxn['_authorizedby']);
            $stmt->bindParam(':actby', $trxn['_actionby']);
            $stmt->bindParam(':accname', $trxn['acc_name']);
            $stmt->bindParam(':mid', $trxn['mid']);
            $stmt->bindParam(':approv', $trxn['_authorizedby']);
            $stmt->bindParam(':branc', $trxn['_branch']);
            $stmt->bindParam(':leftbal', $this->left_balance);
            $stmt->bindParam(':ttype', $this->t_type);
            $stmt->bindParam(':acid', $aid);
            $stmt->bindParam(':pay_method', $pmethod);
            $stmt->bindParam(':cash_acc', $trxn['cash_acc']);
            // $stmt->bindParam(':send_sms', $this->send_sms);
            $stmt->bindParam(':date_created', $trxn['date_created']);
            $stmt->bindParam(':charges', $charges);


            $stmt->execute();
        }


        $fee = new Fee($this->conn);
        $fee->_authorizedby = $trxn['_authorizedby'];
        $fee->_actionby = $trxn['_actionby'];
        $fee->acc_name = $trxn['acc_name'];
        $fee->mid = $trxn['mid'];
        $fee->_branch = $trxn['_branch'];
        $fee->cash_acc = $trxn['cash_acc'];
        $fee->pmethod = $pmethod;
        $fee->date_created = $trxn['date_created'];

        $balance = $trxn['amount'] - $charges;
        $fee->computeDepositTransaction($trxn['mid'], $balance);




        return true;
    }

    public function approveAllAgentDeposits()
    {
        $sqlQuery = 'SELECT * FROM public."transactions"  WHERE  public."transactions"._authorizedby=:id AND _status=0';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->createdAt);
        $stmt->execute();
        // if ($stmt->rowCount() > 0) {
        foreach ($stmt as $trxn) {

            $cash_acc = 0;
            $charges = 0;
            $sender = '';
            $receiver = '';

            $sqlQuery = 'UPDATE public."transactions" SET _status=:st, approvedby=:apby WHERE tid=:id';

            $stmt = $this->conn->prepare($sqlQuery);
            $st = 1;
            $stmt->bindParam(':st', $st);

            $stmt->bindParam(':id', $trxn['tid']);
            $stmt->bindParam(':apby', $this->uid);


            $stmt->execute();

            /* agent cash account update */

            // get agent cash account id
            $sqlQuery = 'SELECT public."Account".id AS aid FROM  public."staff_cash_accounts" LEFT JOIN public."Account" ON public."staff_cash_accounts".id = public."Account".is_cash_account  WHERE public."staff_cash_accounts".userid=:id AND public."staff_cash_accounts".branchid=:bid';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $trxn['_authorizedby']);
            $stmt->bindParam(':bid', $trxn['_branch']);
            $stmt->execute();
            $rown = $stmt->fetch();

            $amount = $trxn['amount'] + $trxn['agent_loan_amount'];

            // update cash account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance-:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $rown['aid']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();

            $sender = $rown['aid'];


            /* update cash account of the one approving */

            // get agent cash account id
            $sqlQuery = 'SELECT public."Account".id AS aid FROM  public."staff_cash_accounts" LEFT JOIN public."Account" ON public."staff_cash_accounts".id = public."Account".is_cash_account  WHERE public."staff_cash_accounts".userid=:id AND public."staff_cash_accounts".branchid=:bid';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->uid);
            $stmt->bindParam(':bid', $trxn['_branch']);
            $stmt->execute();
            $rown = $stmt->fetch();



            // update cash account balance
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $rown['aid']);
            $stmt->bindParam(':amount', $amount);
            $stmt->execute();

            $receiver = $rown['aid'];


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
            $sqlQuery = 'INSERT INTO public.transactions(amount, t_type, description, _authorizedby, mid, approvedby, _branch, cr_acid,dr_acid,date_created)
        VALUES (:amount,:ttype,:descri,:_auth,:mid,:apby,:bran,:crid,:drid,:dc)
                ';

            $stmt = $this->conn->prepare($sqlQuery);
            $mid = 0;
            $tty = 'TTT';
            $descr = 'Agent to Teller Cash Transfer on Approval of Trxn- REF:-' . $trxn['tid'];

            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':dc', $trxn['date_created']);
            $stmt->bindParam(':ttype', $tty);
            $stmt->bindParam(':descri', $descr);
            $stmt->bindParam(':_auth', $this->uid);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':apby', $this->uid);
            $stmt->bindParam(':bran', $trxn['_branch']);
            $stmt->bindParam(':crid', $receiver);
            $stmt->bindParam(':drid', $sender);

            $stmt->execute();

            //  get bank id from branch
            $sqlQuery = 'select "bankId" from  public."Branch" where id=:id';
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $trxn['_branch']);
            $stmt->execute();
            $branch = $stmt->fetch();

            // check whether branch -- bank has deposit charges 
            $sqlQuery = 'select * from  public."transaction_charges" where bankid=:id and c_status=1 and c_application=:appln';
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
                            $charges = ($use['charge'] / 100) * $trxn['amount'];
                        }

                        $cid = $use['c_id'];
                    } else {
                        if (($use['min_amount'] <= $trxn['amount']) && ($trxn['amount'] <= $use['max_amount'])) {
                            if ($use['charge_mode'] == 'fixed') {
                                $charges = $use['charge'];
                            } else {
                                $charges = ($use['charge'] / 100) * $trxn['amount'];
                            }
                            $cid = $use['c_id'];
                        }
                    }
                }
            }


            // get client names,left balance
            $sqlQuery = 'SELECT *, public."Client".id AS client_id FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $trxn['mid']);
            $stmt->execute();
            $row = $stmt->fetch();
            $client = $row;

            $aid = 0;
            if ($row['membership_no'] > 0) {
                $this->left_balance = $row['acc_balance'] + $trxn['amount'];


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
                    $stmt->bindParam(':amount', $trxn['amount']);
                    $stmt->execute();
                }
            } else {
                $this->left_balance = $row['loan_wallet'] + $trxn['amount'];
            }
            $this->acc_name = $row['firstName'] . ' ' . $row['lastName'];
            $this->t_type = 'D';



            if ($charges > 0) {
                $this->t_type = 'I';
                $desc = 'Deposit Fees';
                $pmethod = 'saving';
                $this->left_balance = $this->left_balance - $charges;

                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
        acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,cash_acc,date_created,charges) VALUES
          (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:cash_acc,:date_created,:charges)';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount', $charges);
                $stmt->bindParam(':descri', $desc);
                $stmt->bindParam(':autho', $trxn['_authorizedby']);
                $stmt->bindParam(':actby', $trxn['_actionby']);
                $stmt->bindParam(':accname', $trxn['acc_name']);
                $stmt->bindParam(':mid', $trxn['mid']);
                $stmt->bindParam(':approv', $trxn['_authorizedby']);
                $stmt->bindParam(':branc', $trxn['_branch']);
                $stmt->bindParam(':leftbal', $this->left_balance);
                $stmt->bindParam(':ttype', $this->t_type);
                $stmt->bindParam(':acid', $aid);
                $stmt->bindParam(':pay_method', $pmethod);
                $stmt->bindParam(':cash_acc', $trxn['cash_acc']);
                // $stmt->bindParam(':send_sms', $this->send_sms);
                $stmt->bindParam(':date_created', $trxn['date_created']);
                $stmt->bindParam(':charges', $charges);


                $stmt->execute();
            }


            $fee = new Fee($this->conn);
            $fee->_authorizedby = $trxn['_authorizedby'];
            $fee->_actionby = $trxn['_actionby'];
            $fee->acc_name = $trxn['acc_name'];
            $fee->mid = $trxn['mid'];
            $fee->_branch = $trxn['_branch'];
            $fee->cash_acc = $trxn['cash_acc'];
            $fee->pmethod = $pmethod;
            $fee->date_created = $trxn['date_created'];

            $balance = $trxn['amount'] - $charges;
            $fee->computeDepositTransaction($trxn['mid'], $balance);
        }
        return true;
        // }


        // return false;
    }

    public function getAppMembers($bid)
    {

        $sqlQuery = 'SELECT "bankId" FROM  public."Branch"  WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $bid);
        $stmt->execute();
        $row = $stmt->fetch();


        $sqlQuery = 'SELECT membership_no,"firstName","lastName",shared_name, public."Client"."userId" AS useid, public."savingaccounts".name AS accname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id
        LEFT JOIN public."Branch" ON public."Branch".id=public."Client"."branchId" WHERE public."Branch"."bankId"=:id
        ORDER BY public."Client"."userId" ASC ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $row['bankId']);
        $stmt->execute();
        return $stmt;
    }


    public function createDepositAgent()
    {
        $cash_acc = 0;
        $this->pay_method = $this->pay_method ?? 0;
        // get staff details
        $sqlQuery = 'SELECT "branchId" FROM  public."Staff"  WHERE public."Staff"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->user_id);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$this->cash_acc) {
            // get agent cash account id
            $sqlQuery = 'SELECT public."Account".id AS aid FROM  public."staff_cash_accounts" LEFT JOIN public."Account" ON public."staff_cash_accounts".id = public."Account".is_cash_account  WHERE public."staff_cash_accounts".userid=:id AND public."staff_cash_accounts".branchid=:bid ORDER BY public."staff_cash_accounts".id DESC LIMIT 1';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->user_id);
            $stmt->bindParam(':bid', $row['branchId']);
            $stmt->execute();
            $rown = $stmt->fetch();

            $this->cash_acc = $rown['aid'];
        }


        // update cash account balance
        // $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

        // $stmt = $this->conn->prepare($sqlQuery);

        // $stmt->bindParam(':id', $rown['aid']);
        // $stmt->bindParam(':amount', $this->amount);
        // $stmt->execute();
        $aid = $this->cash_acc ?? '';

        if ($this->cash_acc) {
            $tot = $this->amount + ($this->pay_method ?? 0);
            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:bal WHERE public."Account".id=:id  ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $this->cash_acc);
            $stmt->bindParam(':bal', $tot);
            $stmt->execute();
        }

        // create deposit trxn

        $tt_type = 'D';
        $trxn_status = 0;
        $charges = 0;
        $pm = 'cash';

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,left_balance,t_type,acid,pay_method,bacid,cheque_no,cash_acc,charges,_status,agent_loan_amount) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:acid,:pay_method,:bacid,:cheque,:cash_acc,:charges,:_status,:agent_loan_amount)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':agent_loan_amount', $this->pay_method);
        $stmt->bindParam(':descri', $this->details);
        $stmt->bindParam(':autho', $this->user_id);
        $stmt->bindParam(':actby', $this->actby);
        $stmt->bindParam(':accname', $this->acc_name);
        $stmt->bindParam(':mid', $this->account_id);
        $stmt->bindParam(':approv', $this->user_id);
        $stmt->bindParam(':branc', $row['branchId']);
        $stmt->bindParam(':leftbal', $charges);
        $stmt->bindParam(':ttype', $tt_type);
        $stmt->bindParam(':acid', $aid);
        $stmt->bindParam(':pay_method', $pm);
        $stmt->bindParam(':bacid', $charges);
        $stmt->bindParam(':cheque', $charges);
        $stmt->bindParam(':cash_acc', $aid);
        $stmt->bindParam(':charges', $charges);
        $stmt->bindParam(':_status', $trxn_status);


        $stmt->execute();

        $last_mid = $this->conn->lastInsertId();


        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."User" ON public."transactions".mid=public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."transactions".mid WHERE public."transactions".tid=:id  ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $last_mid);
        $stmt->execute();

        return $stmt;
    }

    public function getMemberProduct($id)
    {
        $sqlQuery = 'SELECT  public."savingaccounts".name AS accname FROM public."Client" LEFT JOIN public."savingaccounts" ON public."Client".actype=public."savingaccounts".id WHERE public."Client"."userId"=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['accname'] ?? '';
    }
    public function getUserNames($id)
    {
        $sqlQuery = 'SELECT  "firstName","lastName" FROM public."User"  WHERE public."User".id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['firstName'] ?? '' . ' ' . $row['lastName'] ?? '';
    }

    public function getAgentTransactions()
    {
        $sqlQuery = 'SELECT * FROM public."transactions" LEFT JOIN public."User" ON public."transactions".mid=public."User".id LEFT JOIN public."Client" ON public."Client"."userId"=public."transactions".mid WHERE public."transactions"._authorizedby=:id AND public."transactions"._status=0 ORDER BY public."transactions".tid ASC ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->account_id);
        $stmt->execute();
        return $stmt;
    }

    public function loginEmployee()
    {
        $stt = 'ACTIVE';
        $sqlQuery = 'SELECT * FROM public."User" WHERE email=:email AND password=:pass AND status=:stt ORDER BY id ASC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':pass', $this->password);
        $stmt->bindParam(':stt', $stt);

        $stmt->execute();
        return $stmt;
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

    public function getStaffDetails($id)
    {
        $sqlQuery = 'SELECT "branchId" FROM public."Staff"  WHERE "userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);


        $stmt->bindParam(':id', $id);


        $stmt->execute();
        $row = $stmt->fetch();
        return $row['branchId'];
    }

    public function getAgentTotalDeposits($uid)
    {
        $sqlQuery = 'SELECT SUM(amount) AS tot FROM public."transactions"  WHERE public."transactions"._authorizedby=:bid AND _status=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }

    public function getAgentActiveMembersToday($uid)
    {
        $sqlQuery = 'SELECT COUNT(*) AS tot FROM public."User"  WHERE public."User".status=:bid AND entered_by=:idd';
        $stmt = $this->conn->prepare($sqlQuery);

        $st = 'ACTIVE';
        $stmt->bindParam(':bid', $st);
        $stmt->bindParam(':idd', $uid);

        $stmt->execute();
        $row = $stmt->fetch();

        return $row['tot'] ?? 0;
    }
}
