<?php
require_once('AuditTrail.php');
require_once '../../config/functions.php';
class Account
{
    public $conn;
    public $bank_id;
    public $branch_id;
    public $type;
    public $with_income_totals;
    public $with_expenditure_totals;
    public $with_liabilities_totals;
    public $with_capital_totals;
    public $with_suspense_totals;
    public $with_assets_totals;
    public $transaction_start_date;
    public $transaction_end_date;
    public $is_trial_balance;
    public $request;


    public $account_id;
    public $details;
    public $user_id;
    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getMainAccounts()
    {
        $sqlQuery = 'SELECT * FROM public.system_gl_accounts
        ORDER BY account_code ASC ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSaccoDetails($sacco_id)
    {
        $sqlQuery = 'SELECT * FROM public."Bank" WHERE mobile_wallet_code=:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $sacco_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubAccounts()
    {
        $sqlQuery = 'SELECT * FROM public."Account" WHERE main_account_id IS NOT NULL AND acc_deleted=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAccountBalance($phone)
    {
        // $sqlQuery = 'SELECT acc_balance FROM public."Client" where "userId" = :id ';
        $sqlQuery = 'SELECT  acc_balance FROM public."Client" WHERE public."Client"."userId"=:id ORDER BY public."Client".id ASC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $phone);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['acc_balance'] ?? 0;
    }

    public function verifyUssdMpin($phone)
    {

        $sqlQuery = 'SELECT  mpin FROM public."Client" WHERE public."Client"."userId"=:id ORDER BY public."Client".id ASC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $phone);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['mpin'] ?? '';
    }
    public function getSchoolPayInsts()
    {
        $sqlQuery = 'SELECT *,public."Branch".name AS bname,public."savingaccounts".name AS sname FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id
       LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id LEFT JOIN public."savingaccounts" ON public."savingaccounts".id=public."Client".actype 
       WHERE public."Branch"."bankId"=\'c52f1a9d-5634-47de-81c1-c15896ac22db\' AND public."Client".client_type=\'institution\' AND public."User".status<>:stt AND public."Client".school_pay=1  ORDER BY public."Client"."createdAt" ASC';
        $stmt = $this->conn->prepare($sqlQuery);

        $stt = 'INACTIVE';

        $cs = '';

        $stmt->bindParam(':stt', $stt);

        // $stmt->bindParam(':bid', $bidd);

        $stmt->execute();
        return $stmt;
    }


    public function getPhoneDetails($phone)
    {

        $sqlQuery = 'SELECT  *, public."User".id AS uid, public."Client".id AS cid FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id LEFT JOIN public."Branch" ON public."Branch".id = public."Client"."branchId" WHERE (public."User"."primaryCellPhone"=:id OR public."User"."secondaryCellPhone"=:id)  ORDER BY public."User".id ASC';

        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->bindParam(':id', $phone);
        $transactions->execute();
        return $transactions;
    }

    public function getPhoneDetailsUssd($phone, $sid)
    {

        $sqlQuery = 'SELECT * FROM public."Bank" WHERE mobile_wallet_code=:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $sid);
        $stmt->execute();
        $row = $stmt->fetch();

        $sqlQuery = 'SELECT  *, public."User".id AS uid, public."Client".id AS cid, public."savingaccounts".name AS sname FROM public."User" LEFT JOIN public."Client" ON public."Client"."userId"=public."User".id LEFT JOIN public."Branch" ON public."Branch".id = public."Client"."branchId" LEFT JOIN public."savingaccounts" ON public."savingaccounts".id = public."Client".actype WHERE (public."User"."primaryCellPhone" IN(:p1,:p2,:p3,:p4,:p5) OR public."User"."secondaryCellPhone" IN(:p1,:p2,:p3,:p4,:p5)) AND public."Branch"."bankId"=:bid ORDER BY public."User".id ASC';

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
        $transactions->bindParam(':bid', $row['id']);
        $transactions->execute();
        return $transactions;
    }
    public function getSubMainAccounts()
    {
        $sqlQuery = 'SELECT * FROM public."Account" WHERE main_account_id IS NULL AND acc_deleted=0';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByBankId()
    {
        $sqlQuery = 'SELECT *, public."Account".name AS account_name,public."Branch".name AS branch_name,public."Account".id AS aid FROM public."Account" 
        LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id
        LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id
        WHERE public."Bank".id=:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->bank_id);
        $stmt->execute();
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccountDetails()
    {
        $sqlQuery = 'SELECT *, public."Account".name AS aname,public."Branch".name AS bname,public."Account".id AS aid FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE public."Account".id=:id ';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->account_id);

        $stmt->execute();
        return $stmt;
    }

    public function delete_sub_account()
    {
        $sqlQuery = 'SELECT COUNT(*) AS num FROM  public."transactions"  WHERE cr_acid=:id OR dr_acid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->account_id);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row['num'] > 0) {
            return 'Account has some Transactions against it! So you can\'t delete it unless you delete those transactions first.';
        }

        $sqlQuery = 'UPDATE public."Account" SET acc_deleted=1 WHERE public."Account".id=:id AND balance=0';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->account_id);

        if ($stmt->execute()) {
            // insert into audit trail

            $auditTrail = new AuditTrail($this->conn);
            $auditTrail->type = 'Trashed Chart Account';
            $auditTrail->staff_id = $this->user_id;
            $auditTrail->bank_id = $this->bank_id;
            $auditTrail->branch_id = $this->branch_id;

            $auditTrail->log_message = 'Trashed Chart Account : ' . $this->account_id;
            $auditTrail->create();
        } else {
            return 'Account Balance is greater than 0! You can\'t delete such an account unless you transfer the Balance first.';
        }




        return true;
    }

    public function get_all_sub_accounts()
    {
        $sqlQuery = 'SELECT * FROM public."Account" WHERE public."Account".main_account_id=:id AND acc_deleted=0';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->account_id);

        $stmt->execute();
        return $stmt;
    }

    public function createSubAccount()
    {
        $sqlQuery = 'INSERT INTO public."Account"(
    type, "branchId",name, description,main_account_id,is_sub_account)
   VALUES (:typee,:bid,:nname,:descr,:maid,:issub )';
        $atype = $this->details['type'];
        $nname = $this->details['name'];
        $descr = $this->details['descr'];
        $issub = true;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':typee', $atype);
        $stmt->bindParam(':bid', $this->details['branch']);
        $stmt->bindParam(':nname', $nname);
        $stmt->bindParam(':descr', $descr);
        $stmt->bindParam(':maid', $this->details['mid']);
        $stmt->bindParam(':issub', $issub);

        $stmt->execute();


        // insert into audit trail

        $auditTrail = new AuditTrail($this->conn);
        $auditTrail->type = 'Created Sub Account';
        $auditTrail->staff_id = $this->details['user'];
        $auditTrail->bank_id = $this->details['bank'];
        $auditTrail->branch_id = $this->details['branch'];

        $auditTrail->log_message = 'Created Sub Account for: ' . $this->details['bname'];
        $auditTrail->create();


        return true;
    }

    public function getBankMainBranchId($bid)
    {
        $sqlQuery = 'SELECT id FROM "Branch" WHERE "bankId"=:bid AND is_main=1';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':bid', $bid);

        $stmt->execute();

        $row = $stmt->fetch();

        return @$row['id'] ?? '';
    }

    public function getAccounts()
    {


        $binding_array = [];
        $query = ' SELECT *, account.id AS account_id, account.name AS account_name, (SELECT COUNT(*) FROM public."Account" WHERE public."Account".main_account_id=account.id) AS subs ';

        if ($this->bank_id) {
            $query .= ', branch.name AS branch_name ';
        }

        if (
            $this->with_income_totals ||
            $this->with_expenditure_totals ||
            $this->with_assets_totals ||
            $this->with_liabilities_totals ||
            $this->with_capital_totals || $this->with_suspense_totals
        ) {
            $query .= ',  
            (
                COALESCE( (SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
                WHERE transaction.acid=account.id OR transaction.cr_acid::uuid=account.id ';

            if ($this->transaction_start_date && $this->transaction_end_date) {
                $query .= ' AND DATE(transaction.date_created) >= :transaction_start_date AND DATE(transaction.date_created) <= :transaction_end_date ';
                $binding_array[':transaction_start_date'] = $this->transaction_start_date;
                $binding_array[':transaction_end_date'] = $this->transaction_end_date;
            }

            /**
             * general assets
             */
            if ($this->with_assets_totals) {
                $query .= ' AND transaction.t_type IN (\'ASS\',\'AJE\')';
            }

            /**
             * general liabilities and deposits
             */
            if ($this->with_liabilities_totals) {
                $query .= ' AND transaction.t_type IN (\'LIA\',\'D\',\'W\')';
            }

            /**
             * general capital transactions
             */
            if ($this->with_capital_totals) {
                $query .= ' AND transaction.t_type IN (\'CAP\')';
            }

            /**
             * general incomes, charges and sms transactions
             */
            if ($this->with_income_totals) {
                $query .= ' AND transaction.t_type IN (\'I\',\'C\',\'SMS\',\'R\')';
            }

            /**
             * general expenses and withdrawals
             */
            if ($this->with_expenditure_totals) {
                $query .= ' AND transaction.t_type IN (\'E\')';
            }

            /**
             * general suspenses
             */
            if ($this->with_suspense_totals) {
                $query .= ' AND transaction.t_type IN (\'BF\')';
            }

            $query .= '),0) )  AS total_amount ';




            if ($this->with_income_totals) {
                // sum for loan interest income
                $query .= ',  
            (
                COALESCE( (SELECT SUM(transaction.loan_interest) FROM public.transactions AS transaction 
                WHERE transaction.acid=account.id OR transaction.cr_acid::uuid=account.id ';

                if ($this->transaction_start_date && $this->transaction_end_date) {
                    $query .= ' AND DATE(transaction.date_created) >= :transaction_start_date AND DATE(transaction.date_created) <= :transaction_end_date ';
                    $binding_array[':transaction_start_date'] = $this->transaction_start_date;
                    $binding_array[':transaction_end_date'] = $this->transaction_end_date;
                }

                /**
                 * general incomes, charges and sms transactions
                 */
                // if ($this->with_income_totals) {
                $query .= ' AND transaction.t_type IN (\'L\')';
                // }

                $query .= '),0) )  AS total_interest ';
            }
        }



        if ($this->is_trial_balance && $this->transaction_start_date) {
            // $query .= ', (
            //     COALESCE( (SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
            //     WHERE transaction.date_created < :opening_balance_date AND transaction.acid=account.id OR transaction.cr_acid::uuid=account.id  ';

            // /**
            //  * general assets
            //  */
            // if ($this->with_assets_totals) {
            //     $query .= ' AND transaction.t_type IN (\'ASS\',\'AJE\')';
            // }

            // /**
            //  * general liabilities and deposits
            //  */
            // if ($this->with_liabilities_totals) {
            //     $query .= ' AND transaction.t_type IN (\'LIA\',\'D\',\'W\')';
            // }

            // /**
            //  * general capital transactions
            //  */
            // if ($this->with_capital_totals) {
            //     $query .= ' AND transaction.t_type IN (\'CAP\')';
            // }

            // /**
            //  * general incomes, charges and sms transactions
            //  */
            // if ($this->with_income_totals) {
            //     $query .= ' AND transaction.t_type IN (\'I\',\'C\',\'SMS\',\'R\')';
            // }

            // /**
            //  * general expenses and withdrawals
            //  */
            // if ($this->with_expenditure_totals) {
            //     $query .= ' AND transaction.t_type IN (\'E\')';
            // }

            // /**
            //  * general suspenses
            //  */
            // if ($this->with_suspense_totals) {
            //     $query .= ' AND transaction.t_type IN (\'BF\')';
            // }

            // $query .= '),0) )  AS opening_balance ';
            // $binding_array[':opening_balance_date'] = $this->transaction_start_date;
        }

        // if ($this->with_expenditure_totals) {

        //     $query .= ',  
        //     (
        //         COALESCE( (SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
        //         WHERE transaction.acid=account.id ';

        //     if ($this->transaction_start_date && $this->transaction_end_date) {
        //         $query .= ' AND DATE(transaction.date_created) >= :transaction_start_date AND DATE(transaction.date_created) <= :transaction_end_date ';
        //         $binding_array[':transaction_start_date'] = $this->transaction_start_date;
        //         $binding_array[':transaction_end_date'] = $this->transaction_end_date;
        //     }

        //     $query .= ' AND transaction.t_type IN (\'E\', \'W\')),0) ) AS total_amount ';
        // }

        $query .= ' FROM public."Account" AS account ';

        if ($this->bank_id) {
            $query .= ' LEFT JOIN public."Branch" AS branch ON account."branchId"=branch.id ';
            $query .= ' LEFT JOIN public."Bank" AS bank ON bank.id=branch."bankId" ';
        }

        $query .= ' WHERE account.acc_deleted=0  ';

        if ($this->bank_id) {
            $query .= ' AND account."branchId" IN(SELECT public."Branch".id FROM public."Branch" WHERE public."Branch"."bankId"=:bank_id AND is_main=1) ';
            $binding_array[':bank_id'] = $this->bank_id;
        }

        if ($this->branch_id) {
            $query .= ' AND account."branchId"=:branch_id ';
            $binding_array[':branch_id'] = $this->branch_id;
        }

        if ($this->type) {
            $query .= ' AND account.type=:type ';
            $binding_array[':type'] = $this->type;
        }

        $query .= ' ORDER BY account."createdAt" ASC';


        $accounts = $this->conn->prepare($query);
        $accounts->execute($binding_array);
        return $accounts->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccounts2()
    {

        $binding_array = [];
        $query = ' SELECT *, account.id AS account_id, account.name AS account_name, (SELECT COUNT(*) FROM public."Account" WHERE public."Account".main_account_id=account.id) AS subs ';

        if ($this->bank_id) {
            $query .= ', branch.name AS branch_name ';
        }

        if (
            $this->with_income_totals ||
            $this->with_expenditure_totals ||
            $this->with_assets_totals ||
            $this->with_liabilities_totals ||
            $this->with_capital_totals
            || $this->with_suspense_totals
        ) {
            $query .= ',  
            (
                COALESCE( (SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
                WHERE (transaction.acid=account.id OR transaction.cr_acid::uuid=account.id) ';

            if ($this->transaction_start_date && $this->transaction_end_date) {
                $query .= ' AND DATE(transaction.date_created) >= :transaction_start_date AND DATE(transaction.date_created) <= :transaction_end_date ';
                $binding_array[':transaction_start_date'] = $this->transaction_start_date;
                $binding_array[':transaction_end_date'] = $this->transaction_end_date;
            }

            /**
             * general assets
             */
            if ($this->with_assets_totals) {
                $query .= ' AND transaction.t_type IN (\'ASS\')';
            }

            /**
             * general liabilities and deposits
             */
            if ($this->with_liabilities_totals) {
                $query .= ' AND transaction.t_type IN (\'LIA\',\'D\',\'W\')';
            }

            /**
             * general capital transactions
             */
            if ($this->with_capital_totals) {
                $query .= ' AND transaction.t_type IN (\'CAP\')';
            }

            /**
             * general incomes, charges and sms transactions
             */
            if ($this->with_income_totals) {
                $query .= ' AND transaction.t_type IN (\'I\',\'C\',\'SMS\',\'L\',\'R\',\'W\')';
            }

            /**
             * general expenses and withdrawals
             */
            if ($this->with_expenditure_totals) {
                $query .= ' AND transaction.t_type IN (\'E\',\'D\')';
            }
            /**
             * general suspenses
             */
            if ($this->with_suspense_totals) {
                $query .= ' AND transaction.t_type IN (\'BF\')';
            }

            $query .= '),0) )  AS total_amount ';
        }

        // if ($this->is_trial_balance && $this->transaction_start_date) {
        //     $query .= ', (
        //         COALESCE( (SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
        //         WHERE transaction.date_created < :opening_balance_date AND (transaction.acid=account.id OR transaction.cr_acid::uuid=account.id) ';

        //     /**
        //      * general assets
        //      */
        //     if ($this->with_assets_totals) {
        //         $query .= ' AND transaction.t_type IN (\'ASS\')';
        //     }

        //     /**
        //      * general liabilities and deposits
        //      */
        //     if ($this->with_liabilities_totals) {
        //         $query .= ' AND transaction.t_type IN (\'LIA\',\'D\',\'W\')';
        //     }

        //     /**
        //      * general capital transactions
        //      */
        //     if ($this->with_capital_totals) {
        //         $query .= ' AND transaction.t_type IN (\'CAP\')';
        //     }

        //     /**
        //      * general incomes, charges and sms transactions
        //      */
        //     if ($this->with_income_totals) {
        //         $query .= ' AND transaction.t_type IN (\'I\',\'C\',\'SMS\',\'L\',\'R\')';
        //     }

        //     /**
        //      * general expenses and withdrawals
        //      */
        //     if ($this->with_expenditure_totals) {
        //         $query .= ' AND transaction.t_type IN (\'E\')';
        //     }
        //     /**
        //      * general suspenses
        //      */
        //     if ($this->with_suspense_totals) {
        //         $query .= ' AND transaction.t_type IN (\'BF\')';
        //     }

        //     $query .= '),0) )  AS opening_balance ';
        //     $binding_array[':opening_balance_date'] = $this->transaction_start_date;
        // }

        // if ($this->with_expenditure_totals) {

        //     $query .= ',  
        //     (
        //         COALESCE( (SELECT SUM(transaction.amount) FROM public.transactions AS transaction 
        //         WHERE transaction.acid=account.id ';

        //     if ($this->transaction_start_date && $this->transaction_end_date) {
        //         $query .= ' AND DATE(transaction.date_created) >= :transaction_start_date AND DATE(transaction.date_created) <= :transaction_end_date ';
        //         $binding_array[':transaction_start_date'] = $this->transaction_start_date;
        //         $binding_array[':transaction_end_date'] = $this->transaction_end_date;
        //     }

        //     $query .= ' AND transaction.t_type IN (\'E\', \'W\')),0) ) AS total_amount ';
        // }

        $query .= ' FROM public."Account" AS account ';

        if ($this->bank_id) {
            $query .= ' LEFT JOIN public."Branch" AS branch ON account."branchId"=branch.id ';
            $query .= ' LEFT JOIN public."Bank" AS bank ON bank.id=branch."bankId" ';
        }

        $query .= ' WHERE account."deletedAt" IS NULL AND account.main_account_id IS NOT NULL  ';

        if ($this->bank_id) {
            $query .= ' AND account."branchId" IN(SELECT public."Branch".id FROM public."Branch" WHERE public."Branch"."bankId"=:bank_id AND is_main=1) ';
            $binding_array[':bank_id'] = $this->bank_id;
        }

        if ($this->branch_id) {
            $query .= ' AND account."branchId"=:branch_id ';
            $binding_array[':branch_id'] = $this->branch_id;
        }

        if ($this->type) {
            $query .= ' AND account.type=:type ';
            $binding_array[':type'] = $this->type;
        }

        $query .= ' ORDER BY account."createdAt" ASC';


        $accounts = $this->conn->prepare($query);
        $accounts->execute($binding_array);
        return $accounts->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * creates new account via data importer
     */
    public function create_new_account_data_importer()
    {
        $bank_id = $this->request['bank_id'];
        $branch_ids = [$this->request['account_branch_id']];
        if (!is_valid_uuid($this->request['account_branch_id'])) {
            $branches = $this->conn->fetchAll('Branch', ['bankId' => $bank_id, 'deletedAt' => null, 'deleted' => 0]);
            $branch_ids = array_column($branches, 'id');
            // $branch_ids = ['e6330f19-df8b-4e5f-ba40-41882ed81234','8cba1e04-c0e0-4ebe-a43f-7dd12272d7af','1690b215-1365-4c1b-b73f-bc6bd11b5232','e9e1b0da-9ef0-42bd-8513-cbd97a0844eb'];
        }

        if (!@$this->request['parent_id']) $this->request['parent_id'] = null;

        // get selected parent account details
        $p_details = $this->conn->fetchAll('Account', ['id' => @$this->request['parent_id'], 'deletedAt' => null, 'acc_deleted' => 0]);


        foreach ($branch_ids as $branch_id) {
            if (is_null($this->request['parent_id'])) {
                // main account
                $data = [
                    'account_code_used' => @$this->request['account_id'],
                    'name' => @$this->request['account_name'],
                    'branchId' => @$branch_id,
                    'type' => @$this->request['account_type'],
                    'isSystemGenerated' => false,
                    'main_account_id' => @$this->request['parent_id'],
                    'balance' => 0
                ];
                $this->conn->insert('Account', $data);
            } else {

                // for sub accounts


                // get branch parent account match details
                $branch_p_details = $this->conn->fetchAll('Account', ['account_code_used' => $p_details[0]['account_code_used'], 'deletedAt' => null, 'acc_deleted' => 0, 'branchId' => @$branch_id]);

                // create sub account
                $data = [
                    'account_code_used' => @$this->request['account_id'],
                    'name' => @$this->request['account_name'],
                    'branchId' => @$branch_id,
                    'type' => @$this->request['account_type'],
                    'isSystemGenerated' => false,
                    'main_account_id' => $branch_p_details[0]['id'],
                    'balance' => 0
                ];
                $this->conn->insert('Account', $data);
            }
        }

        return true;
    }
}
