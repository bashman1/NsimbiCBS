<?php
class Reschedule
{
    // DB stuff
    private $conn;
    public $lno;
    public $amount;
    public $record_date;
    public $frequency;
    public $duration;
    public $int_method;
    public $int_rate;
    public $comments;
    public $userId;
    public $bank_class_instance;
    public $audit_trail_instance;
    public $audit_trail;
    public $data;


    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function applyLoanTopUp()
    {
        $data = $this->data;
        $loan_amount = $data["loan_amount"];

        if (!@$data["auth_id"]) return "Authorizing staff not found";

        $current_loan = $this->conn->fetch('loan', ['loan_no' => $data['loan_id']]);
        if (!@$current_loan) return "Loan not found";

        $client = $this->conn->fetch('Client', 'userId', $current_loan['account_id']);

        if (!@$client) return "Client not found";

        $current_loan_id = $current_loan["loan_no"];

        $client_user = $this->conn->fetch('User', 'id', $client['userId']);
        $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'] . ' '
            . @$client_user['shared_name'];

        $new_principal = (int)$current_loan["principal_balance"] + (int)$current_loan["interest_balance"] + (int)$current_loan["penalty_balance"];

        if ($loan_amount < $new_principal) return "Invalid Amount";


        /**
         * Clear off current loan instance if not yet cleared
         */
        if ($current_loan['status'] != 5) {
            $this->bank_class_instance->updateTotalLoanAmount($current_loan_id, true);
        }

        $new_principal = (int)$current_loan["principal_balance"] + (int)$current_loan["interest_balance"] + (int)$current_loan["penalty_balance"];

        $new_loan_data = [
            'loanproductid' => $current_loan["loanproductid"],
            'principal' => $loan_amount,
            'requestedamount' => $loan_amount,
            'approvedamount' => $loan_amount,
            'disbursedamount' => $loan_amount,
            'branchid' => $current_loan["branchid"],
            'current_balance' => 0,
            'loan_type' => $current_loan["loan_type"],
            'status' => 2,
            'application_date' => $data["application_date"],
            'requesteddisbursementdate' => $data["application_date"],
            'date_disbursed' => $data["application_date"],
            'account_id' => $current_loan["account_id"],
            'loan_officer' => $current_loan["loan_officer"],
            'requested_loan_duration' => $data["loan_period"],
            'repay_cycle_id' => $data["freq"],
            'date_of_first_pay' => $data["application_date"],
            'interest_method_id' => $current_loan["interest_method_id"],
            'monthly_interest_rate' => $current_loan["monthly_interest_rate"],
            'notes' => @$data["comment"],
            'auto_pay' => @$current_loan["auto_pay"],
            'reviewedbyid' => @$current_loan["reviewedbyid"],
            'approved_loan_duration' => $data["loan_period"],
            'mode_of_disbursement' => $current_loan["mode_of_disbursement"],
        ];

        /**
         * create new loan instance
         */
        $new_loan_id = $this->conn->insert('loan', $new_loan_data);
        $this->bank_class_instance->applyLoanSchedule($new_loan_id);


        if ($client['membership_no'] > 0) {
            $left_balance = max($client['acc_balance'] + $loan_amount, 0);
        } else {
            $left_balance = max($client['loan_wallet'] + $loan_amount, 0);
        }

        /**
         * create new loan disbursement transaction
         */
        $this->conn->insert('transactions', [
            'amount' => $loan_amount,
            'description' => 'LOAN TOPUP DISBURSEMENT - LN ' . $new_loan_id,
            '_authorizedby' => $data["auth_id"],
            '_actionby' => "LOANS DEPARTMENT",
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'approvedby' => $data["auth_id"],
            '_branch' => $client['branchId'],
            't_type' => 'A',
            'loan_id' => $new_loan_id,
            'left_balance' => max($left_balance, 0),
        ]);

        $loan_product_fee = $this->conn->fetch('loanproducttofee', ["lp_id" => $current_loan["loanproductid"]]);

        if (@$loan_product_fee["fee_id"]) {
            $fee = $this->conn->fetch('Fee', ["id" => $loan_product_fee["fee_id"]]);
            $fee_amount = $fee['rateAmount'];
            $fee_type = $fee['type'];
            $payType = $fee['paymentType'];

            if ($payType == 'DISBURSEMENT') {
                if ($fee_type == 'INTEREST_RATE') {
                    $charge_amount = ($fee_amount / 100) * $loan_amount;
                } else {
                    $charge_amount = $fee_amount;
                }

                $left_balance -= $charge_amount;

                // get chart account
                $acid = 0;
                $chart_acc = $this->conn->fetch('Account', ["feeid" => $loan_product_fee["fee_id"], "branchId" => $current_loan['branchid']]);

                if (@$chart_acc['id']) {
                    $acid = $chart_acc['id'];
                }

                /**
                 * create new loan disbursement transaction
                 */
                $this->conn->insert('transactions', [
                    'amount' => $charge_amount,
                    'description' => 'ON DISBURSEMENT FEES - ' . $fee['name'],
                    '_authorizedby' => $data["auth_id"],
                    '_actionby' => "LOANS DEPARTMENT",
                    'acc_name' => $client_names,
                    'mid' => $client['userId'],
                    'approvedby' => $data["auth_id"],
                    '_branch' => $client['branchId'],
                    't_type' => 'I',
                    'acid' => $acid,
                    'loan_id' => $new_loan_id,
                    'left_balance' => max($left_balance, 0),
                    '_feeid' => $fee["id"],
                ]);
            }
        }

        /**
         * create clearance transaction for the old loan
         */
        $this->conn->insert('transactions', [
            'amount' => $current_loan["principal_balance"],
            'description' => 'Loan Clearance with loan topup - LN ' . $new_loan_id,
            '_authorizedby' => $data["auth_id"],
            '_actionby' => $data["auth_id"],
            'acc_name' => $client_names,
            'mid' => $client['userId'],
            'approvedby' => $data["auth_id"],
            '_branch' => $client['branchId'],
            't_type' => 'L',
            'loan_id' => $current_loan_id,
            'outstanding_amount' => 0,
            'outstanding_amount_total' => 0,
            'loan_interest' => $current_loan['interest_balance'],
            'outstanding_interest' => 0,
            'outstanding_interest_total' => 0,
            'loan_penalty' => 0,
            'left_balance' => max($left_balance - $current_loan['current_balance'], 0),
        ]);

        $this->bank_class_instance->updateTotalLoanAmount($current_loan_id, true);


        if ($current_loan['mode_of_disbursement'] == 'Via Savings Account') {
            $left_balance -= $current_loan['penalty_balance'];
            $left_balance -= ($current_loan["principal_balance"] + $current_loan['interest_balance']);
            $this->conn->update('Client', [
                'acc_balance' => max($left_balance, 0),
            ], 'userId', $client['userId']);
        }

        $this->bank_class_instance->updateTotalLoanAmount($new_loan_id);

        /**
         * create audit trail
         */
        $this->audit_trail->type = 'loan_topup';
        $this->audit_trail->staff_id = $data['auth_id'];
        $this->audit_trail->branch_id = $client['branchId'];
        $this->audit_trail->log_message = 'Loan Topup LN - ' . $current_loan_id . ' :: on ' . date('jS M, Y');
        $this->audit_trail->create();

        return true;
    }

    public function LoanTopup()
    {
        $sqlQueryx = 'SELECT * FROM public."loan" WHERE loan_no=:lid ';
        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':lid', $this->lno);

        $stmtx->execute();
        $row = $stmtx->fetch();

        $sqlQuery = 'INSERT INTO public."loan" 
        (
            loanproductid,principal,requestedamount,branchid,current_balance,loan_type,status,
            application_date,requesteddisbursementdate,account_id,loan_officer,requested_loan_duration,
            repay_cycle_id,date_of_first_pay,interest_method_id,monthly_interest_rate,notes,
            approvedamount,approved_loan_duration
        ) 
        VALUES(:lpid,:principal,:ra,:bid,:cb,:lt,:stat,:applydate,:disbursedate,:acid,:loff,:duration,:rcid,:startdate,:imid,:rate,:notes,:aa,:ald)';
        $cbb = 0;
        $stt = 0;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lpid', $row['loanproductid']);
        $stmt->bindParam(':principal', $this->amount);
        $stmt->bindParam(':ra', $this->amount);
        $stmt->bindParam(':bid', $row['branchid']);
        $stmt->bindParam(':cb', $cbb);
        $stmt->bindParam(':lt', $row['loan_type']);
        $stmt->bindParam(':stat', $stt);

        $stmt->bindParam(':applydate', $this->record_date);
        $stmt->bindParam(':disbursedate', $this->record_date);
        $stmt->bindParam(':acid', $row['account_id']);
        $stmt->bindParam(':loff', $this->userId);
        $stmt->bindParam(':duration', $this->duration);


        $stmt->bindParam(':rcid', $row['repay_cycle_id']);
        $stmt->bindParam(':startdate', $this->record_date);

        $stmt->bindParam(':imid', $row['interest_method_id']);

        $stmt->bindParam(':rate', $row['monthly_interest_rate']);
        $stmt->bindParam(':notes', $this->comments);
        $stmt->bindParam(':aa', $this->amount);
        $stmt->bindParam(':ald', $this->duration);

        $stmt->execute();

        $last_id = $this->conn->lastInsertId();

        // disburse loan & clear old loan
        // clear old loan
        $sqlQuery = 'UPDATE public."loan"  SET status=5,
        current_balance=0,amount_paid=amount_paid+:ap,principal_balance=0,interest_balance=0,penalty_balance=0
     WHERE  public."loan".loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->lno);
        $stmt->bindParam(':ap', $row['current_balance']);
        $stmt->execute();

        // update loan schedule status to complete
        $sqlQuery = 'UPDATE public."loan_schedule"  SET status=:st WHERE  public."loan_schedule".loan_id=:id';
        $st = 'completed';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->lno);
        $stmt->bindParam(':st', $st);
        $stmt->execute();


        // disburse new loan
        $sqlQuery = 'UPDATE public."loan"  SET 
        date_disbursed=:aa,status=2,date_of_first_pay=:notes,mode_of_disbursement=:mod
     WHERE  public."loan".loan_no=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $last_id);
        $stmt->bindParam(':aa', $this->record_date);
        $stmt->bindParam(':notes', $this->record_date);
        $stmt->bindParam(':mod', $row['mode_of_disbursement']);
        $stmt->execute();



        // insert into transaction table disbursement transaction
        $sqlQuery = 'SELECT * FROM public."Client" LEFT JOIN public."User" ON public."Client"."userId"=public."User".id WHERE public."Client"."userId"=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $row['account_id']);
        $stmt->execute();
        $rown = $stmt->fetch();
        $left_balance = $rown['acc_balance'] + $this->amount;

        $acc_name = $rown['firstName'] . ' ' . $rown['lastName'];
        $t_type = 'A';
        $descr = 'LOAN TOPUP DISBURSEMENT - LN ' . $last_id;
        $actb = 'LOANS DEPARTMENT';

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
          acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id) VALUES
            (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid)';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':descri', $descr);
        $stmt->bindParam(':autho', $this->userId);
        $stmt->bindParam(':actby', $actb);
        $stmt->bindParam(':accname', $acc_name);
        $stmt->bindParam(':mid', $row['account_id']);
        $stmt->bindParam(':approv', $row['account_id']);
        $stmt->bindParam(':branc', $row['branchid']);
        $stmt->bindParam(':leftbal', $left_balance);
        $stmt->bindParam(':lid', $last_id);
        $stmt->bindParam(':ttype', $t_type);

        $stmt->execute();

        /**
         * create loan repayment transaction for clearing old loan
         */
        $ddesc = 'Loan Clearance with loan topup - LN ' . $last_id;
        $left_balance2 = $left_balance - $row['current_balance'];
        $tttype = 'L';

        $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
                acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,date_created,outstanding_amount_total,loan_interest,outstanding_interest_total) VALUES
                  (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:dc,:oat,:li,:oit)';

        $stmt = $this->conn->prepare($sqlQuery);
        $ooii = 0;

        $stmt->bindParam(':amount', $row['principal_balance']);
        $stmt->bindParam(':descri', $ddesc);
        $stmt->bindParam(':autho', $this->userId);
        $stmt->bindParam(':actby', $this->userId);
        $stmt->bindParam(':accname', $acc_name);
        $stmt->bindParam(':mid', $row['account_id']);
        $stmt->bindParam(':approv', $this->userId);
        $stmt->bindParam(':branc', $row['branchid']);
        $stmt->bindParam(':leftbal', $left_balance2);
        $stmt->bindParam(':lid', $this->lno);
        $stmt->bindParam(':ttype', $tttype);
        $stmt->bindParam(':dc', $this->record_date);
        // $stmt->bindParam(':oa', $ooaa);
        $stmt->bindParam(':oat', $ooii);
        $stmt->bindParam(':li', $row['interest_balance']);
        // $stmt->bindParam(':oi', $ooii);
        $stmt->bindParam(':oit', $ooii);

        $stmt->execute();




        $chargeAmount = 0;

        // check for disbursement fees and deduct them and create transaction for the fees collected
        $sqlQuery = 'SELECT * FROM  public."loanproducttofee" LEFT JOIN public."Fee" ON public."loanproducttofee".fee_id=public."Fee".id 
        WHERE public."loanproducttofee".lp_id=:id';

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(':id', $row['loanproductid']);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num > 0) {
            $rowx = $stmt->fetch();

            $feeamount = $rowx['rateAmount'];
            $feetype = $rowx['type'];
            $payType = $rowx['paymentType'];
            if ($payType == 'DISBURSEMENT') {
                if ($feetype == 'INTEREST_RATE') {
                    $chargeAmount = ($feeamount / 100) * $this->amount;
                } else {
                    $chargeAmount = $feeamount;
                }
                $treas = 'ON DISBURSEMENT FEES - ' . $rowx['name'];
                $left_balance2 = $left_balance2 - $chargeAmount;
                $ttypee = 'C';
                // create fee charge transaction entry and mark it with the fee id

                $sqlQuery = 'INSERT INTO public."transactions" (amount,description,_authorizedby,_actionby,
                    acc_name,mid,approvedby,_branch,left_balance,t_type,loan_id,_feeid) VALUES
                      (:amount,:descri,:autho,:actby,:accname,:mid,:approv,:branc,:leftbal,:ttype,:lid,:fid)';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':amount', $chargeAmount);
                $stmt->bindParam(':descri', $treas);
                $stmt->bindParam(':autho', $this->userId);
                $stmt->bindParam(':actby', $actb);
                $stmt->bindParam(':accname', $acc_name);
                $stmt->bindParam(':mid', $row['account_id']);
                $stmt->bindParam(':approv', $this->userId);
                $stmt->bindParam(':branc', $row['branchid']);
                $stmt->bindParam(':leftbal', $left_balance2);
                $stmt->bindParam(':lid', $last_id);
                $stmt->bindParam(':ttype', $ttypee);
                $stmt->bindParam(':fid', $rowx['id']);

                $stmt->execute();
            }
        }

        // base on mode of disburse if its via a/c add money to customer acc
        if ($row['mode_of_disbursement'] == 'Via Savings Account') {
            $sqlQuery = 'UPDATE public."Client" SET acc_balance=acc_balance+:ac WHERE public."Client"."userId"=:id';
            $left_balance2 = $left_balance2 - $row['penalty_balance'];
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':id', $row['account_id']);
            $stmt->bindParam(':ac', $left_balance2);
            $stmt->execute();
        }


        $this->lno = $last_id;
        $this->int_method = $row['interest_method_id'];
        $this->frequency = $row['repay_cycle_id'];
        $this->int_rate = $row['monthly_interest_rate'];
        $this->applyLoanSchedule();

        $this->updateTotalLoanAmount2($last_id);

        return true;
    }

    public function rescheduleLoan()
    {
        $data = $this->data;
        // $data['interest_rate'] = round($data['interest_rate'] / 12, 1);
        $loan_id = @$data['loan_id'];
        $loan = $this->conn->fetch('loan', 'loan_no', $loan_id);

        // return $data['interest_rate'];
        if (!@$loan) return "Loan not found";

        $client = $this->conn->fetch('User', 'id', $loan['account_id']);

        if (!$client) return "Client not found";
        $client_names = @$client['firstName'] . ' ' . @$client['lastName'];
        // return $client;
        if (
            $loan['repay_cycle_id'] == $data['frequency'] &&
            $loan['approved_loan_duration'] == $data['duration'] &&
            $loan['interest_method_id'] == $data['interest_method'] &&
            $loan['monthly_interest_rate'] == $data['interest_rate']
        ) {
            return "Loan couldnt be rescheduled as it has the same values";
        }
        // return "went through";

        $amount_to_reschedule = (int)$loan['principal_balance'] + (int)$loan['interest_due'];
        $total_loan_paid = (int)$loan['amount_paid'];

        $schedule_data = [
            'loan_id' => $loan_id,
            'amount_paid' => $total_loan_paid,
            'amount_scheduled' => $amount_to_reschedule,
            'initial_principal' => $loan['principal'],
            'current_principal' => 0,
            'initial_interest' => $loan['interest_amount'],
            'current_interest' => 0,
            'initial_duration' => $loan['approved_loan_duration'],
            'current_duration' => $data['duration'],
            'initial_repay_cycle_id' => $loan['repay_cycle_id'],
            'current_repay_cycle_id' => $data['frequency'],
            // 'initial_frequency_id' => $data[''],
            // 'current_frequency_id' => $loan_id,
            'initial_interest_method_id' => $loan['interest_method_id'],
            'current_interest_method_id' => $data['interest_method'],

            'initial_interest_rate' => $loan['monthly_interest_rate'],
            'current_interest_rate' => $data['interest_rate'],

            'reschedule_date' => $data['reschedule_date'],
            'comments' => $data['comments'],
            'created_by' => @$data['auth_id'],
        ];

        /**
         * Add loan to scheduled records
         */
        $rescheduled_loan_id = $this->conn->insert('rescheduled_loans', $schedule_data);

        $loan_schedule = $this->conn->fetchAll('loan_schedule', 'loan_id', $loan_id);

        /**
         * Add loan to reschduled payments
         */
        foreach ($loan_schedule as $schedule) {
            $this->conn->insert('loan_rescheduled_repayments', [
                'loan_id' => $loan_id,
                'scheduled_id' => $rescheduled_loan_id,
                'amount' => $schedule['amount'],
                'interest' => $schedule['interest'],
                'principal' => $schedule['principal'],
                'balance' => $schedule['balance'],
                'date_of_payment' => $schedule['date_of_payment'],
                'status' => $schedule['status'],
                'last_updated' => $schedule['last_updated'],
                'outstanding_principal' => $schedule['outstanding_principal'],
                'outstanding_interest' => $schedule['outstanding_interest'],
                'principal_paid' => $schedule['principal_paid'],
                'interest_paid' => $schedule['interest_paid'],
                'performance_status' => $schedule['performance_status'],
                'interest_waivered' => $schedule['interest_waivered'],
                'created_by' => $data['auth_id'],
            ]);
        }

        /**
         * Add new loan details
         */
        $this->conn->update('loan', [
            'principal' => (float)@$amount_to_reschedule,
            'approved_loan_duration' => (int)@$data['duration'],
            'monthly_interest_rate' => (float)@$data['interest_rate'],
            'interest_method_id' => (int)@$data['interest_method'],
            'repay_cycle_id' => (int)@$data['frequency'],
        ], 'loan_no', $loan_id);

        /**
         * Delete current schedule
         */
        $this->conn->delete('loan_schedule', 'loan_id', $loan_id);

        /**
         * create new loan schedule
         */
        $this->bank_class_instance->applyLoanSchedule($loan_id);
        $loan_schedule = $this->conn->fetchAll('loan_schedule', 'loan_id', $loan_id);

        /**
         * start process of renconciling loan schedule with amount cleared on the loan
         */

        $schedule_status_active = 'active';
        $schedule_status_paid = 'paid';

        $i = 0;
        $current_schedule = $loan_schedule[$i];

        $amount_balance = $total_loan_paid;
        // return [$amount_balance, $current_schedule];
        while ($amount_balance > 0 && $current_schedule) {
            $total_paid = 0;
            $interest_paid = 0;
            $principal_paid = 0;

            if ($current_schedule['outstanding_principal'] == 0 && $current_schedule['principal_paid'] == 0) {
                $current_schedule['outstanding_principal'] = $current_schedule['principal'];
            }

            if ($current_schedule['outstanding_interest'] == 0 && $current_schedule['interest_paid'] == 0) {
                $current_schedule['outstanding_interest'] = $current_schedule['interest'];
            }

            $current_schedule_outstanding_total = $current_schedule['outstanding_interest'] + $current_schedule['outstanding_principal'];

            /**
             * if amount can only clear interest
             */
            if ($amount_balance <= $current_schedule['outstanding_interest']) {
                $interest_paid = $amount_balance;
            } else if ($amount_balance > $current_schedule['outstanding_interest']) {
                $interest_paid = $current_schedule['outstanding_interest'];

                /**
                 * if amount can clear both interest and principal
                 */
                $balance = $amount_balance - $interest_paid;
                if ($balance <= $current_schedule['outstanding_principal']) {
                    $principal_paid = $balance;
                } else {
                    $principal_paid = $current_schedule['outstanding_principal'];
                }
            }

            $total_paid = $interest_paid + $principal_paid;
            $amount_balance -= $total_paid;

            $amount_balance = max($amount_balance, 0);

            $transaction_principal_balance = $current_schedule['principal'] - ($principal_paid + $current_schedule['principal_paid']);
            $transaction_interest_balance = $current_schedule['interest'] - ($interest_paid + $current_schedule['interest_paid']);

            $schedule_status = $total_paid >= $current_schedule_outstanding_total ? $schedule_status_paid : $schedule_status_active;

            $schedule_performance_status = 'on_time';
            $loan_status = 2;

            /**
             * update schedule
             */
            $this->conn->update('loan_schedule', [
                'principal_paid' => $principal_paid,
                'interest_paid' => $interest_paid,
                'outstanding_principal' => $transaction_principal_balance,
                'outstanding_interest' => $transaction_interest_balance,
                'performance_status' => $schedule_performance_status,
                'status' => $schedule_status,
            ], 'schedule_id', $current_schedule['schedule_id']);

            $this->conn->update('loan', ['date_of_next_pay' => $current_schedule['date_of_payment']], 'loan_no', $loan_id);

            $i++;
            $current_schedule = $loan_schedule[$i];
        }

        $this->conn->update('loan', [
            'status' => $loan_status,
        ], 'loan_no', $loan_id);

        $this->bank_class_instance->updateTotalLoanAmount($loan_id);

        /**
         * create audit trail
         */

        $this->audit_trail_instance->type = 'loan_data_importer';
        $this->audit_trail_instance->staff_id = $data['auth_id'];
        $this->audit_trail_instance->bank_id = $loan['branchId'];
        $this->audit_trail_instance->log_message = 'Reschedule loan - ' . $loan['loan_no'] . ' :: for ' . $client_names . ' as on ' . date('jS M, Y');
        $this->audit_trail_instance->create();

        return true;
    }

    public function rescheduleLoan_old()
    {
        $reschedule_id = $this->saveResceduleData(0);
        $this->updateOldInstallments($reschedule_id);
        // generate new schedule
        $res =  $this->applyLoanSchedule();
        if ($res) {
            $this->updateTotalLoanAmount($this->lno);
            return true;
        }

        return false;
    }



    public function applyLoanSchedule()
    {
        return $this->saveSchedule($this->int_method);
    }



    private function getRightSchedule($rate, $period, $amount, $date, $method, $grace_period, $frequency, $ftype, $grace_type, $refine)
    {
        $endpoint = "Bank/loan_schedule.php";

        $url = BASE_URL . $endpoint;

        $data = array(
            "id" => 1,
            "rate" => $rate,
            "period" => $period,
            "amount" => $amount,
            "date" => $date,
            "int_method" => $method,
            "grace_period" => $grace_period,
            "frequency" => $frequency,
            "ftype" => $ftype,
            "grace_type" => $grace_type,
            "refine" => $refine
        );
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


        return $data;
    }

    private function saveSchedule($interestMethod)
    {

        // start
        $principal =  $this->amount;
        $duration = $this->duration;
        $cycle = $this->frequency;
        $date_first = $this->record_date;
        $rate = $this->int_rate;
        $grace_period = 0;
        $grace_type = 'pay_none';
        $refine = 1;
        $method = '';
        if ($interestMethod == 1) {
            $method = 'flat';
        } else if ($interestMethod == 2) {

            $method = 'declining';
        } else if ($interestMethod == 3) {

            $method = 'amortization';
        }

        if ($cycle == 1) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 1 days'));
            $frequency = 'd';
            $ftype = 'DAYS';
        } else if ($cycle == 2) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 7 days'));
            $frequency = 'w';
            $ftype = 'WEEKS';
        } else if ($cycle == 3) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 30 days'));
            $frequency = 'm';
            $ftype = 'MONTHS';
        } else if ($cycle == 4) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 15 days'));
            $frequency = 'd';
            $ftype = 'DAYS';
        } else if ($cycle == 5) {
            $use_date_first = date('Y-m-d', strtotime($date_first . ' + 360 days'));
            $frequency = 'y';
            $ftype = 'YEARS';
        }

        $details = $this->getRightSchedule($rate, $duration, $principal, $use_date_first, $method, $grace_period, $frequency, $ftype, $grace_type, $refine);

        $usedate = $use_date_first;
        foreach ($details['all_payments'] as $row) {

            $sqlQueryx = 'INSERT INTO public."loan_schedule" (loan_id,amount,interest,principal,balance,date_of_payment) VALUES(:lid,:amount,:inter,:principal,:bal,:dop)';

            $stmtx = $this->conn->prepare($sqlQueryx);
            $stmtx->bindParam(':lid', $this->lno);
            $stmtx->bindParam(':amount', $row['total_payment']);
            $stmtx->bindParam(':inter', $row['interest_expected']);
            $stmtx->bindParam(':principal', $row['principal_expected']);
            $stmtx->bindParam(':bal', $row['brought_forward']);
            $stmtx->bindParam(':dop', date('Y-m-d : H:i:s', strtotime($usedate)));

            $stmtx->execute();

            if ($cycle == 1) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 1 days'));
            } else if ($cycle == 2) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 7 days'));
            } else if ($cycle == 3) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 30 days'));
            } else if ($cycle == 4) {
                $usedate = date('Y-m-d', strtotime($usedate . ' + 15 days'));
            } else if ($cycle == 5) {
                $usedate = date('Y-m-d', strtotime($date_first . ' + 360 days'));
            }
        }
        return true;
    }

    public function getLoanPrincipal($loan_id)
    {

        $sqlQueryx = 'SELECT SUM(principal) AS total FROM public."loan_schedule" WHERE loan_id=:lid AND status<>:st';
        $st = 'rescheduled';
        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':lid', $loan_id);
        $stmtx->bindParam(':st', $st);

        $stmtx->execute();
        $row = $stmtx->fetch();
        $scheduled_principal = $row['total'];


        $sqlQuery = 'SELECT SUM(amount) AS total FROM public."transactions" WHERE t_type=:typ AND loan_id=:lid';
        $tyy = 'L';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':typ', $tyy);

        $stmt->execute();
        $rown = $stmt->fetch();
        $total_collection = $rown['total'];

        $balance = $scheduled_principal - $total_collection;

        return [
            "scheduled_principal" => $scheduled_principal,
            "amount_paid" => $total_collection,
            "balance" => max($balance, 0),
        ];
    }

    public function getLoanInterest($loan_id)
    {

        $sqlQueryx = 'SELECT SUM(interest) AS total FROM public."loan_schedule" WHERE loan_id=:lid AND status<>:st';
        $st = 'rescheduled';

        $stmtx = $this->conn->prepare($sqlQueryx);
        $stmtx->bindParam(':lid', $loan_id);
        $stmtx->bindParam(':st', $st);


        $stmtx->execute();
        $row = $stmtx->fetch();
        $scheduled_interest = $row['total'];


        $sqlQuery = 'SELECT SUM(loan_interest) AS total FROM public."transactions" WHERE t_type=:typ AND loan_id=:lid';
        $tyy = 'L';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':typ', $tyy);

        $stmt->execute();
        $rown = $stmt->fetch();
        $total_collection = $rown['total'];

        $balance = $scheduled_interest - $total_collection;

        return [
            "scheduled_interest" => $scheduled_interest,
            "amount_paid" => $total_collection,
            "balance" => max($balance, 0),
        ];
    }

    public function updateTotalLoanAmount($loan_id)
    {
        $principal_setup = $this->getLoanPrincipal($loan_id);
        $interest_setup = $this->getLoanInterest($loan_id);

        $sqlQuery = 'UPDATE public."loan" SET total_loan_amount=:tla,interest_amount=:ia,current_balance=:cb,amount_paid=:ap,principal_balance=:pb,interest_balance=:ib,principal=:prin,monthly_interest_rate=:mrate,interest_method_id=:imid,approved_loan_duration=:aduration,repay_cycle_id=:cycle WHERE loan_no=:lid';
        $tlaa = @$principal_setup["scheduled_principal"] + @$interest_setup["scheduled_interest"];
        $cbb = @$principal_setup["balance"] + @$interest_setup["balance"];
        $app = @$principal_setup["amount_paid"] + @$interest_setup["amount_paid"];
        $iaa = @$interest_setup["scheduled_interest"];
        $pb = @$principal_setup["balance"];
        $ib = @$interest_setup["balance"];

        $prin = @$principal_setup["scheduled_principal"];
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':tla', $tlaa);
        $stmt->bindParam(':ia', $iaa);
        $stmt->bindParam(':cb', $cbb);
        $stmt->bindParam(':ap', $app);
        $stmt->bindParam(':pb', $pb);
        $stmt->bindParam(':ib', $ib);

        $stmt->bindParam(':prin', $prin);
        $stmt->bindParam(':mrate', $this->int_rate);
        $stmt->bindParam(':imid', $this->int_method);
        $stmt->bindParam(':aduration', $this->duration);
        $stmt->bindParam(':cycle', $this->frequency);

        $stmt->execute();
    }

    public function updateTotalLoanAmount2($loan_id)
    {
        $principal_setup = $this->getLoanPrincipal($loan_id);
        $interest_setup = $this->getLoanInterest($loan_id);

        $sqlQuery = 'UPDATE public."loan" SET total_loan_amount=:tla,interest_amount=:ia,current_balance=:cb,amount_paid=:ap,principal_balance=:pb,interest_balance=:ib,principal=:prin WHERE loan_no=:lid';
        $tlaa = @$principal_setup["scheduled_principal"] + @$interest_setup["scheduled_interest"];
        $cbb = @$principal_setup["balance"] + @$interest_setup["balance"];
        $app = @$principal_setup["amount_paid"] + @$interest_setup["amount_paid"];
        $iaa = @$interest_setup["scheduled_interest"];
        $pb = @$principal_setup["balance"];
        $ib = @$interest_setup["balance"];

        $prin = @$principal_setup["scheduled_principal"];
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $loan_id);
        $stmt->bindParam(':tla', $tlaa);
        $stmt->bindParam(':ia', $iaa);
        $stmt->bindParam(':cb', $cbb);
        $stmt->bindParam(':ap', $app);
        $stmt->bindParam(':pb', $pb);
        $stmt->bindParam(':ib', $ib);

        $stmt->bindParam(':prin', $prin);

        $stmt->execute();
    }

    /**
     * saveResceduleData method
     *
     */
    private function saveResceduleData($interest)
    {

        $sqlQuery = 'INSERT INTO public."rescheduled_loans" 
        (lid,principal_rescheduled,duration,duration_type,frequency,interest,int_method,reschedule_date,comments,added_by)
        VALUES(:lid,:prin,:duration,:duration_type,:freq,:interest,:int_method,:rdate,:comments,:added)';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $this->lno);
        $stmt->bindParam(':prin', $this->amount);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':duration_type', $this->frequency);
        $stmt->bindParam(':freq', $this->frequency);
        $stmt->bindParam(':interest', $interest);
        $stmt->bindParam(':int_method', $this->int_method);
        $stmt->bindParam(':rdate', $this->record_date);
        $stmt->bindParam(':comments', $this->comments);
        $stmt->bindParam(':added', $this->userId);
        $stmt->execute();

        $id = $this->conn->lastInsertId();
        return $id;
    }


    /**
     * updateOldInstallments method
     *
     * Generally this 
     *
     */
    private function updateOldInstallments($rid)
    {
        $last_updated = date("Y-m-d H:i:s");

        $sqlQuery = 'SELECT * FROM public."loan_schedule" WHERE loan_id=:lid';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':lid', $this->lno);
        $stmt->execute();

        foreach ($stmt as $r) {


            // if a repayments is fully paid, just ignore it
            if ($r['status'] == 'paid')
                continue;
            //--------------------
            //if a repayment is fully not paid,we just cancel it by turning the status to resceheduled
            if ($r['status'] == 'active') {
                $sqlQuery = 'UPDATE public."loan_schedule" SET status=:st WHERE schedule_id=:lid';
                $stmt = $this->conn->prepare($sqlQuery);
                $stt = 'rescheduled';
                $stmt->bindParam(':lid', $r['schedule_id']);
                $stmt->bindParam(':st', $stt);
                $stmt->execute();
            }
            //--------------------

            // if a repayments has a partial amount paid to it, we cancel off its balance by turning the expected amount = to the paid amount
            if ($r['status'] == 'partial') {
                // get amount paid so far
                $sqlQuery = 'SELECT SUM(amount) AS p_paid, SUM(loan_interest) AS int_paid FROM public."transactions" WHERE schedule_id=:lid';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':lid', $r['schedule_id']);
                $stmt->execute();
                $row = $stmt->fetch();

                // update the schedule entry

                $sqlQuery = 'UPDATE public."loan_schedule" SET status=:st,principal=:prin,interest=:interest,amount=:amount,balance=:balance,last_updated=:lupdate WHERE schedule_id=:lid';
                $stmt = $this->conn->prepare($sqlQuery);
                $stt = 'paid';
                $tot = $row['int_paid'] + $row['p_paid'];
                $bal = 0;
                $stmt->bindParam(':lid', $r['schedule_id']);
                $stmt->bindParam(':prin', $row['p_paid']);
                $stmt->bindParam(':interest', $row['int_paid']);
                $stmt->bindParam(':amount', $tot);
                $stmt->bindParam(':balance', $bal);
                $stmt->bindParam(':st', $stt);
                $stmt->bindParam(':lupdate', $last_updated);
                $stmt->execute();

                // we keep a record, of how much has been offset from the above installment, just in case
                $sqlQuery = 'INSERT INTO public."loan_rescheduled_repayments" (loan_id,schedule_id,principal_deducted,interest_deducted,total_deducted,reschedule_id) 
                VALUES(:lid,:sid,:prin,:int_ded,:tot_ded,rrid)';
                $stmt = $this->conn->prepare($sqlQuery);
                $p = $r['principal'] - $row['p_paid'];
                $i = $r['interest'] -  $row['int_paid'];
                $tot = $p + $i;
                $stmt->bindParam(':lid', $this->lno);
                $stmt->bindParam(':sid', $r['schedule_id']);
                $stmt->bindParam(':prin', $p);
                $stmt->bindParam(':int_ded', $i);
                $stmt->bindParam(':tot_ded', $tot);
                $stmt->bindParam(':rrid', $rid);
                $stmt->execute();
            }
        }
    }
}
