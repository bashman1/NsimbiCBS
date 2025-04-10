<?php
require_once('Bank.php');

class Fee
{
    public $bankId;
    public $branchId;
    public $conn;
    public $fee_id;
    public $fee_name;
    public $amount;
    public $applies_to;
    public $account_id;
    public $saving_accounts;
    public $_authorizedby;
    public $_actionby;
    public $acc_name;
    public $mid;
    public $_branch;
    public $cash_acc;
    public $pmethod;
    public $date_created;
    public $shares;
    public $passbook;
    public $pass_acid;

    public $chart_account_id;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getById($fee_id = null)
    {
        $fee_id = $fee_id ?? $this->fee_id;
        $sqlQuery = $this->generalQuery() . '  WHERE fees.id=:fee_id AND fees.deleted_at IS NULL ';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':fee_id' => $fee_id]);
        return $record->fetch(PDO::FETCH_ASSOC);
    }

    public function getByBranchId($branch_id = null)
    {
        $branch_id = $branch_id ?? $this->branchId;
        $sqlQuery = $this->generalQuery() . '  WHERE fees.branch_id=:branch_id AND fees.deleted_at IS NULL ';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':branch_id' => $branch_id]);
        return $record->fetch(PDO::FETCH_ASSOC);
    }

    public function getFeeTypeByBranchId($branch_id = null)
    {
        $branch_id = $branch_id ?? $this->branchId;
        $sqlQuery = ' SELECT COUNT(*) AS num FROM  account_opening_fees AS fees 
        LEFT JOIN public."Branch" AS branch ON fees.branch_id = branch.id 
        LEFT JOIN public."Bank" AS bank ON branch."bankId" = bank.id  WHERE fees.branch_id=:branch_id AND fees.deleted_at IS NULL ';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':branch_id' => $branch_id]);
        return $record->fetch(PDO::FETCH_ASSOC);
    }

    public function getClientShareValue($user_id = null)
    {
        $sqlQuery = 'SELECT no_shares, share_amount FROM share_register  WHERE userid=:branch_id';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':branch_id' => $user_id]);
        return $record->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * general query for generating fees
     */
    private function generalQuery()
    {
        return ' SELECT fees.id as fee_id, fees.fee_name, fees.amount, fees.applies_to, bank.id AS bank_id, fees.branch_id, branch.name AS branch_name, account_id, passbook_charges,no_shares,pass_acid FROM  account_opening_fees AS fees 
        LEFT JOIN public."Branch" AS branch ON fees.branch_id = branch.id 
        LEFT JOIN public."Bank" AS bank ON branch."bankId" = bank.id ';
    }

    public function getTotalAccountFees($bid)
    {
        $sqlQuery = 'SELECT SUM(amount) AS tot_fees FROM  account_opening_fees  WHERE branch_id=:branch_id AND deleted_at IS NULL';
        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':branch_id' => $bid]);
        $row = $record->fetch(PDO::FETCH_ASSOC);
        return $row['tot_fees'] ?? 0;
    }

    /**
     * Get all fees and their savings accounts
     */
    public function getAllAccountOpeningFees()
    {
        $binding_array = [];
        $sqlQuery = $this->generalQuery();

        if ($this->bankId) {
            $sqlQuery .= ' WHERE branch."bankId"=:bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
        } else {
            $sqlQuery .= ' WHERE fees.branch_id=:branch_id ';
            $binding_array[':branch_id'] = $this->branchId;
        }

        $sqlQuery .= ' AND fees.deleted_at IS NULL ORDER BY fees.created_at DESC';

        $records = $this->conn->prepare($sqlQuery);
        $records->execute($binding_array);
        $records = $records->fetchAll(PDO::FETCH_ASSOC);
        $records = array_map(function ($record) {
            $this->fee_id = $record['fee_id'];
            $record['saving_accounts'] = $this->getFeeSavingsAccounts();
            return $record;
        }, $records);

        return $records;
    }

    /**
     * gets a particular fee and its savinga accounts
     */
    public function getAccountOpeningFee($fee_id = null)
    {
        $fee_id = $fee_id ?? $this->fee_id;
        $sqlQuery = ' SELECT fees.id as fee_id, fees.fee_name, fees.amount, fees.applies_to, bank.id AS bank_id, fees.branch_id, branch.name AS branch_name FROM  account_opening_fees AS fees 
        LEFT JOIN public."Branch" AS branch ON fees.branch_id = branch.id 
        LEFT JOIN public."Bank" AS bank ON branch."bankId" = bank.id 
        
        WHERE fees.id=:fee_id AND fees.deleted_at IS NULL ';

        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':fee_id' => $fee_id]);
        $record = $record->fetch(PDO::FETCH_ASSOC);

        $savings_accounts  = $this->getFeeSavingsAccounts();
        $record['saving_accounts'] = $savings_accounts;
        return $record;
    }

    /**
     * gets a fee savings accounts
     */
    public function getFeeSavingsAccounts($fee_id = null)
    {
        $fee_id = $fee_id ?? $this->fee_id;
        $sqlQuery = ' SELECT settings.id as id, settings.fee_id, settings.account_id, accounts.name as account_name  FROM  account_opening_fees_saving_accounts AS settings 
        LEFT JOIN public.savingaccounts AS accounts ON settings.account_id = accounts.id 
        WHERE settings.fee_id=:fee_id ';

        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':fee_id' => $fee_id]);
        return $record->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * create or update fee
     */
    public function upsertFee()
    {
        $bank = new Bank($this->conn);
        $branches_ids = [];
        if ($this->bankId) {
            $bank->id = $this->bankId;
            $bank_branches = $bank->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);
            $branches_ids = array_column($bank_branches, 'id');
        }

        if ($this->branchId) {
            array_push($branches_ids, $this->branchId);
        }

        // var_dump($branches_ids);

        if ($this->fee_id) {
            $fee = $this->getById($this->fee_id);
            if (in_array($fee['branch_id'], $branches_ids)) {
                $sqlQuery = ' UPDATE account_opening_fees SET fee_name=:fee_name, amount=:amount, applies_to=:applies_to, account_id=:account_id, pass_acid=:pacid,no_shares=:nshares,passbook_charges=:pcharge WHERE id=:fee_id ';
                $update = $this->conn->prepare($sqlQuery);
                $update->execute(
                    [
                        ':fee_name' => $this->fee_name,
                        ':amount' => $this->amount,
                        ':applies_to' => $this->applies_to,
                        ':account_id' => $this->account_id,
                        ':pacid' => $this->pass_acid,
                        ':nshares' => $this->shares,
                        ':pcharge' => $this->passbook,
                        ':fee_id' => $this->fee_id
                    ]
                );

                $this->createAccountFees($this->fee_id);
                return true;
            }
        } else {
            /**
             * bank level insertion
             */
            if ($this->bankId) {
                foreach ($branches_ids as $branch_id) {
                    $fee_id = $this->branchInsertion($branch_id);
                    $this->createAccountFees($fee_id);
                }
            }

            /**
             * branch level insertion
             */
            else {
                $fee_id = $this->branchInsertion($this->branchId);
                $this->createAccountFees($fee_id);
            }

            return true;
        }
        return false;
    }

    /**
     * creates/updates/deletes fee savings accounts
     */
    private function createAccountFees($fee_id)
    {
        $fee_accounts = $this->getFeeSavingsAccounts($fee_id) ?? [];
        $accounts_id = array_column($fee_accounts, 'account_id');
        // var_dump($this->saving_accounts);
        if (is_array($this->saving_accounts) && count($this->saving_accounts)) {
            foreach ($this->saving_accounts as $account) {
                if (!in_array($account, $accounts_id)) {
                    $sqlQuery = ' INSERT INTO account_opening_fees_saving_accounts (fee_id, account_id) VALUES (:fee_id, :account_id) ';
                    $insert = $this->conn->prepare($sqlQuery);
                    $insert->execute(
                        [
                            ':fee_id' => $fee_id,
                            ':account_id' => $account
                        ]
                    );
                }
            }

            /**
             * Remove unassociated accounts
             */
            $inValues = implode(',', $this->saving_accounts);
            $deleteQuery = ' DELETE FROM  public.account_opening_fees_saving_accounts WHERE fee_id=:fee_id AND public.account_opening_fees_saving_accounts.account_id NOT IN(' . $inValues . ')  ';
            $delete = $this->conn->prepare($deleteQuery);
            $delete->bindParam(':fee_id', $fee_id);
            $delete->execute();
        } else {
            $deleteQuery = ' DELETE FROM  public.account_opening_fees_saving_accounts WHERE fee_id=:fee_id ';
            $delete = $this->conn->prepare($deleteQuery);
            $delete->bindParam(':fee_id', $fee_id);
            $delete->execute();
        }
    }

    /**
     * main insertion into the account_opening_fees table at branch level
     */
    private function branchInsertion($branch_id)
    {

        $sqlQuery = 'SELECT * FROM public."Account" WHERE id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $this->chart_account_id);

        $stmt->execute();
        $rown = $stmt->fetch();

        $sqlQuery = 'SELECT * FROM public."Account" WHERE account_code_used=:id AND "branchId"=:bid ORDER BY id ASC LIMIT 1';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':bid', $branch_id);
        $stmt->bindParam(':id', $rown['account_code_used']);

        $stmt->execute();
        $row = $stmt->fetch();

        $this->account_id = $row['id'];


        $sqlQuery = ' INSERT INTO account_opening_fees (fee_name, amount, applies_to, account_id, branch_id, pass_acid, no_shares, passbook_charges) VALUES (:fee_name, :amount, :applies_to, :account_id,:branch_id, :pacid,:nshares,:pcharge) ';
        $insert = $this->conn->prepare($sqlQuery);
        $insert->execute(
            [
                ':fee_name' => $this->fee_name,
                ':amount' => $this->amount,
                ':applies_to' => $this->applies_to,
                ':account_id' => $this->account_id,
                ':pacid' => $this->pass_acid,
                ':nshares' => $this->shares,
                ':pcharge' => $this->passbook,
                ':branch_id' => $branch_id
            ]
        );
        return $last_id = $this->conn->lastInsertId();
        $this->fee_id = $last_id;
        return $this->getById();
    }

    /**
     * delete fee
     */
    public function delete()
    {
        $bank = new Bank($this->conn);
        $branches_ids = [];
        if ($this->bankId) {
            $bank->id = $this->bankId;
            $bank_branches = $bank->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);
            $branches_ids = array_column($bank_branches, 'id');
        }

        if ($this->branchId) {
            array_push($branches_ids, $this->branchId);
        }

        $fee = $this->getById();
        if (in_array($fee['branch_id'], $branches_ids)) {
            $sqlQuery = ' UPDATE account_opening_fees SET deleted_at = now() WHERE id=:fee_id';
            $delete = $this->conn->prepare($sqlQuery);
            $delete->execute([':fee_id' => $this->fee_id]);
            return true;
        }

        return false;
    }

    public function getBranchSavingsAccountFee($branch_id, $account_id)
    {
        $sqlQuery = ' SELECT accounts.id AS main_accounts_id,  fees.id as fee_id, fees.fee_name, fees.amount, fees.applies_to, bank.id AS bank_id, fees.branch_id, branch.name AS branch_name, fees.account_id , fees.pass_acid,fees.no_shares, fees.passbook_charges
        FROM  account_opening_fees_saving_accounts AS accounts 
        LEFT JOIN account_opening_fees AS fees ON fees.id=accounts.fee_id
        LEFT JOIN public."Branch" AS branch ON fees.branch_id = branch.id 
        LEFT JOIN public."Bank" AS bank ON branch."bankId" = bank.id 
        WHERE accounts.account_id=:account_id AND fees.branch_id=:branch_id  AND fees.deleted_at IS NULL ';

        $record = $this->conn->prepare($sqlQuery);
        $record->execute([':account_id' => $account_id, ':branch_id' => $branch_id]);
        return $record->fetch(PDO::FETCH_ASSOC);
    }


    public function computeDepositTransaction($member_id, $amount)
    {
        $sqlQuery = ' SELECT * FROM "Client" WHERE "userId"=:member_id ';
        $client = $this->conn->prepare($sqlQuery);
        $client->execute([':member_id' => $member_id]);
        $client = $client->fetch(PDO::FETCH_ASSOC);

        // var_dump($client);
        /**
         * check if client exists
         */
        if ($client) {

            // $member_id = $client['userId'];
            $branch_fee_type = $this->getFeeTypeByBranchId($client['branchId']);
            if ($branch_fee_type['num'] > 1) {
                $branch_fee = $this->getBranchSavingsAccountFee($client['branchId'], $client['actype']);
            } else {
                $branch_fee = $this->getByBranchId($client['branchId']);
            }

            $current_share_value = $this->getClientShareValue($client['userId']);
            $current_membership_fee = $client['membership_fee'];
            $current_passbook_fee = $client['pass_book_fees'];
            /**
             * check if we have a branch fee associate with client branch 
             * and client has not completed membership fee
             */
            if ($amount > 0 && $branch_fee && $current_membership_fee < $branch_fee['amount']) {

                /**
                 * if fee applies to all clients
                 */
                // if ($branch_fee['applies_to'] != 'all_clients') {
                //     /**

                //     $branch_fee = $this->getBranchSavingsAccountFee($branch_fee['branch_id'], $client['actype']) ?? $branch_fee;
                // }

                $branch_fee_amount = $branch_fee['amount'];

                // $bank_ = new Bank($this->conn);
                // $bank = $bank_->getAllBankDetails($branch_fee['bank_id'])->fetch(PDO::FETCH_ASSOC);

                $current_membership_fee_balance = $branch_fee_amount - $current_membership_fee;


                /**
                 * if client has enough funds to clear the fee
                 */
                if ($amount >= $current_membership_fee_balance) {
                    $amount = $amount - $current_membership_fee_balance;

                    $this->createFeeTransaction($member_id, $current_membership_fee_balance, $branch_fee['branch_id'], $branch_fee['account_id'], 'membership');
                }
                /**
                 * if deposit amount is not enough to clear current balance, 
                 * then transfer deposited amount to membership charges
                 */
                else {

                    $this->createFeeTransaction($member_id, $amount, $branch_fee['branch_id'], $branch_fee['account_id'], 'membership');
                    $amount = 0;
                }
            }


            /**
             * check if we have a branch pass book fee associated with client branch 
             * and client has not completed passbook fee
             */
            if ($amount > 0 && $branch_fee && $current_passbook_fee < $branch_fee['passbook_charges']) {

                // charge branch passbook fee


                $branch_fee_passbook = $branch_fee['passbook_charges'];

                // $bank_ = new Bank($this->conn);
                // $bank = $bank_->getAllBankDetails($branch_fee['bank_id'])->fetch(PDO::FETCH_ASSOC);

                $current_passbook_fee_balance = $branch_fee_passbook - $current_passbook_fee;


                /**
                 * if client has enough funds to clear the fee
                 */
                if ($amount >= $current_passbook_fee_balance) {
                    $amount = $amount - $current_passbook_fee_balance;

                    $this->createFeeTransaction($member_id, $current_passbook_fee_balance, $branch_fee['branch_id'], $branch_fee['pass_acid'], 'passbook');
                }
                /**
                 * if deposit amount is not enough to clear current balance, 
                 * then transfer deposited amount to passbook charges
                 */
                else {

                    $this->createFeeTransaction($member_id, $amount, $branch_fee['branch_id'], $branch_fee['pass_acid'], 'passbook');
                    $amount = 0;
                }
            }


            /**
             * check if we have a branch compulsory share purchase  associated with client branch 
             * and client has not completed the required share value
             */
            if ($amount > 0 && $branch_fee && $branch_fee['no_shares'] >0  && $current_share_value['no_shares'] < $branch_fee['no_shares']) {

                $branch_shares = $branch_fee['no_shares'];

                $bank_ = new Bank($this->conn);
                $bank = $bank_->getAllBankDetails($branch_fee['bank_id'])->fetch(PDO::FETCH_ASSOC);

                $current_share_balance = $branch_shares - $current_share_value;

                $current_share_balance_amount = $current_share_balance * $bank['share_value'];


                /**
                 * if client has enough funds to clear the fee
                 */
                if ($amount >= $current_share_balance_amount) {
                    $amount = $amount - $current_share_balance_amount;

                    $this->purchaseDefaultShares($member_id, $current_share_balance_amount, $current_share_balance, $branch_fee['branch_id'], $bank['share_value'], $bank['share_acid']);
                }
                /**
                 * if deposit amount is not enough to clear current balance, 
                 * then transfer deposited amount to shares charges
                 */
                else {

                    $sh = $amount / $bank['share_value'];
                    $this->purchaseDefaultShares($member_id, $amount, $sh, $branch_fee['branch_id'], $bank['share_value'], $bank['share_acid']);
                    $amount = 0;
                }
            }



            /**
             * if bank does not charge membership fee 
             * or if members has completed membership fee
             */
            // else {
            $this->updateAccountBalance($member_id, $amount);
            // }
        }
    }

    public function purchaseDefaultShares($userid, $sa, $no_shares, $branch, $csv, $share_acid)
    {

        $sqlQuery = 'SELECT * FROM public."share_register" WHERE userid=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $userid);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // update existing share holder details
            $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount+:sa,no_shares=no_shares+:ns WHERE userid=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $userid);
            $stmt->bindParam(':sa',  $sa);
            $stmt->bindParam(':ns', $no_shares);
            $stmt->execute();
        } else {
            // create share holder
            $sqlQuery = 'INSERT INTO public.share_register(
	 userid, share_amount, no_shares, added_by, branch_id)
	VALUES (:uid,:sa,:ns,:adb,:bid)';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':uid', $userid);
            $stmt->bindParam(':sa',  $sa);
            $stmt->bindParam(':adb', $this->_authorizedby);
            $stmt->bindParam(':bid', $branch);
            $stmt->bindParam(':ns', $no_shares);
            $stmt->execute();
        }



        $tt_type = 'W';
        $pay_method = 'saving';
        $descri = 'Share Purchase Using Savings';
        $auth = 0;

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
      acc_name,mid,approvedby,_branch,t_type,pay_method,date_created) VALUES
        (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount',  $sa);
        $stmt->bindParam(':descri', $descri);
        $stmt->bindParam(':autho', $auth);
        $stmt->bindParam(':actby', $userid);
        $stmt->bindParam(':accname', $userid);
        $stmt->bindParam(':mid', $userid);
        $stmt->bindParam(':approv', $auth);
        $stmt->bindParam(':branc', $branch);
        $stmt->bindParam(':ttype', $tt_type);
        $stmt->bindParam(':pay_method', $pay_method);
        $stmt->bindParam(':date_created', $this->date_created);


        $stmt->execute();



        // create share purchase trxn

        $sqlQuery = 'INSERT INTO public.share_purchases(
	user_id, decription, no_of_shares, current_share_value, amount, pay_method, notes, record_date, added_by, branch_id,pay_method_acid)
	VALUES (:uid,:descri,:ns,:csv,:sa,:paymeth,:notes,:trxndate,:adb,:bid,:acid)';

        $stmt = $this->conn->prepare($sqlQuery);
        $acid = $userid;
        $description = 'Share Purchase on Account Opening';
        $stmt->bindParam(':uid', $userid);
        $stmt->bindParam(':sa',  $sa);
        $stmt->bindParam(':adb', $this->_authorizedby);
        $stmt->bindParam(':bid', $branch);
        $stmt->bindParam(':ns', $no_shares);
        $stmt->bindParam(':csv', $csv);
        $stmt->bindParam(':descri', $description);
        $stmt->bindParam(':notes', $description);
        $stmt->bindParam(':paymeth', $pay_method);
        $stmt->bindParam(':trxndate', $this->date_created);

        $stmt->bindParam(':acid', $acid);

        $stmt->execute();


        $sqlQuery = 'SELECT * FROM public."Account"  WHERE public."Account".id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $share_acid);
        $stmt->execute();

        $rown = $stmt->fetch();

        $sqlQuery = 'UPDATE  public."Account" SET balance=balance+:bal  WHERE public."Account".account_code_used=:id AND "branchId"=:bid';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $rown['account_code_used']);
        $stmt->bindParam(':bid', $branch);
        $stmt->bindParam(
            ':bal',
            $sa
        );
        $stmt->execute();
    }

    /**
     * create fee transaction
     */
    public function createFeeTransaction($member_id, $amount_paid, $branch_id, $acc_id, $fee_type)
    {
        if ($fee_type == 'passbook') {
            /**
             * update passbook fee
             */
            $sqlQuery = ' UPDATE public."Client" SET pass_book_fees=pass_book_fees+:membership_fee_payment WHERE public."Client"."userId"=:member_id ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([':member_id' => $member_id, ':membership_fee_payment' => $amount_paid]);


            $transaction_type = 'I';
            $desc = 'Passbook Fees';
            $pmethod = 'saving';

            $sqlQuery = 'INSERT INTO public."transactions" 
        (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,date_created) VALUES
          (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:date_created)';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':amount', $amount_paid);
            $stmt->bindParam(':descri', $desc);
            $stmt->bindParam(':autho', $this->_authorizedby);
            $stmt->bindParam(':actby', $this->_actionby);
            $stmt->bindParam(':accname', $this->acc_name);
            $stmt->bindParam(':mid', $this->mid);
            $stmt->bindParam(':approv', $this->_authorizedby);
            $stmt->bindParam(':branc', $branch_id);
            $stmt->bindParam(':ttype', $transaction_type);
            $stmt->bindParam(':acid', $acc_id);
            $stmt->bindParam(':pay_method', $pmethod);
            // $stmt->bindParam(':cash_acc', $this->cash_acc);
            $stmt->bindParam(':date_created', $this->date_created);
            $stmt->execute();


            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acc_id);
            $stmt->bindParam(':amount', $amount_paid);
            $stmt->execute();
        } else {
            /**
             * update membership fee
             */
            $sqlQuery = ' UPDATE public."Client" SET membership_fee=membership_fee+:membership_fee_payment WHERE public."Client"."userId"=:member_id ';
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([':member_id' => $member_id, ':membership_fee_payment' => $amount_paid]);

            // TODO carry  $current_membership_fee_balance into transactions with t_type=R

            $transaction_type = 'R';
            $desc = 'Membership Fee';
            $pmethod = 'membership fee';

            $sqlQuery = 'INSERT INTO public."transactions" 
        (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,date_created) VALUES
          (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:date_created)';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':amount', $amount_paid);
            $stmt->bindParam(':descri', $desc);
            $stmt->bindParam(':autho', $this->_authorizedby);
            $stmt->bindParam(':actby', $this->_actionby);
            $stmt->bindParam(':accname', $this->acc_name);
            $stmt->bindParam(':mid', $this->mid);
            $stmt->bindParam(':approv', $this->_authorizedby);
            $stmt->bindParam(':branc', $branch_id);
            $stmt->bindParam(':ttype', $transaction_type);
            $stmt->bindParam(':acid', $acc_id);
            $stmt->bindParam(':pay_method', $pmethod);
            // $stmt->bindParam(':cash_acc', $this->cash_acc);
            $stmt->bindParam(':date_created', $this->date_created);
            $stmt->execute();


            $sqlQuery = 'UPDATE public."Account" SET balance=balance+:amount WHERE id=:id';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $acc_id);
            $stmt->bindParam(':amount', $amount_paid);
            $stmt->execute();
        }
    }

    function updateAccountBalance($member_id, $amount, $wallet = 'savings')
    {
        if ($amount > 0) {
            $sqlQuery = ' SELECT * FROM "Client" WHERE "userId"=:member_id ';
            $client = $this->conn->prepare($sqlQuery);
            $client->execute([':member_id' => $member_id]);
            $client = $client->fetch(PDO::FETCH_ASSOC);


            if ($wallet == 'savings') {
                $new_balance = $client['acc_balance'] + $amount;
                $sqlQuery = ' UPDATE public."Client" SET acc_balance=:new_balance WHERE public."Client"."userId"=:member_id ';
            } else {
                $new_balance = $client['loan_wallet'] + $amount;
                $sqlQuery = ' UPDATE public."Client" SET loan_wallet=:new_balance WHERE public."Client"."userId"=:member_id ';
            }

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute([':member_id' => $member_id, ':new_balance' => $new_balance]);


            $sqlQuery = ' UPDATE public."User" SET status=:st WHERE public."User".id=:member_id ';
            $stmt = $this->conn->prepare($sqlQuery);
            $st = 'ACTIVE';
            $stmt->execute([':member_id' => $member_id, ':st' => $st]);


            // $sqlQuery = ' UPDATE public."Account" SET balance=:st WHERE public."User".id=:member_id ';
            // $stmt = $this->conn->prepare($sqlQuery);
            // $st = 'ACTIVE';
            // $stmt->execute([':member_id' => $member_id, ':st' => $st]);



        }
    }
}
