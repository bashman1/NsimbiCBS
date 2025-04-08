<?php

use function PHPSTORM_META\map;

require_once __DIR__ . '../../config/functions.php';

class DataImporterLoanBatch
{
    private $conn;
    public $batch_id;
    public $name;
    public $branch_id;
    public $auth_id;
    public $bank_id;
    public $membership_no;
    public $duration;
    public $interest_method;
    public $interest_rate;
    public $disbursement_date;
    public $credit_officer_id;
    public $loan_product_id;
    public $loan_amount;
    public $principal_balance;
    public $principal_arrears;
    public $interest;
    public $interest_arrears;
    public $interest_balance;
    public $frequency;
    public $recyle_type;
    public $penalty_balance;
    public $next_due_date;
    public $status;
    public $loan_number;
    public $amount_paid;
    public $amount_in_arrears;
    public $loan_id;
    public $data;
    public $bank_object;
    public $audit_trail;
    public $records;
    public $batch_name;
    public $conn_extra;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
        // $this->conn_extra = $conn_extra;
    }

    public function batchQuery()
    {
        return 'SELECT batch.id AS batch_id, batch.name AS batch_name, batch.created_by, batch.created_at AS imported_at,
        TRIM(CONCAT(importer."firstName", \' \', importer."lastName",importer.shared_name)) as imported_by, batch.status
        AS batch_status,

        (SELECT COUNT(*) FROM data_importer_loan_batch_records WHERE batch_id = batch.id) AS number_of_loans,

        (COALESCE((SELECT SUM(loan_amount) FROM data_importer_loan_batch_records WHERE batch_id = batch.id), 0)) AS total_loan_amount,

        (COALESCE((SELECT SUM(principal_balance) FROM data_importer_loan_batch_records WHERE batch_id = batch.id),0)) AS total_principal_balance,

        (COALESCE((SELECT SUM(interest_balance) FROM data_importer_loan_batch_records WHERE batch_id = batch.id),0)) AS total_interest_balance,

        (SELECT COUNT(*) FROM data_importer_loan_batch_records WHERE batch_id=batch.id AND import_status=true) AS exported_to_main,

        (SELECT COUNT(*) FROM data_importer_loan_batch_records WHERE batch_id=batch.id AND import_status= false) AS total_pending

        FROM data_importer_loan_batches AS batch 
		LEFT JOIN "User" importer ON batch.created_by = importer.id ';
    }

    public function getLoanBatchDetails($id = null)
    {
        $id = $id ?? $this->batch_id;
        $sqlQuery = $this->batchQuery();
        $sqlQuery .= ' WHERE batch.id = :batch_id ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':batch_id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getBankLoanBatches($id = null)
    {
        $id = $id ?? $this->bank_id;
        $sqlQuery = $this->batchQuery();
        $sqlQuery .= ' WHERE batch.bank_id = :bank_id AND deleted_at IS NULL ORDER BY batch.created_at DESC';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':bank_id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // return $this->conn->database->fetchAll($sql, $id);
        // return $this->conn->database->fetchAll($sql, 'data_importer_loan_batches', $id);
    }

    public function getBankFdBatches($id = null)
    {
        $id = $id ?? $this->bank_id;
        $sqlQuery = $this->batchQuery();
        $sqlQuery .= ' WHERE batch.bank_id = :bank_id AND deleted_at IS NULL ORDER BY batch.created_at DESC';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':bank_id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // return $this->conn->database->fetchAll($sql, $id);
        // return $this->conn->database->fetchAll($sql, 'data_importer_loan_batches', $id);
    }

    public function getBranchBankId($bid)
    {
        $sqlQuery = 'SELECT * FROM public."Branch" WHERE public."Branch".id = :bid';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':bid' => $bid]);
        $row =  $stmt->fetch();

        return $row['bankId'];
    }

    public function createNewBatch()
    {
        // $sqlQuery = 'INSERT INTO public.data_importer_loan_batches (name, bank_id, created_by) VALUES (:name,:bank_id, :created_by)';
        // $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->execute([':name' => $this->name, 'created_by' => $this->auth_id, ':bank_id' => $this->bank_id]);
        // return $this->conn->lastInsertId();

        return $this->conn->insert('data_importer_loan_batches', [
            'name' => $this->batch_name,
            'created_by' => $this->auth_id,
            'bank_id' => $this->bank_id,
        ]);
    }

    public function createNewFdBatch()
    {
        // $sqlQuery = 'INSERT INTO public.data_importer_loan_batches (name, bank_id, created_by) VALUES (:name,:bank_id, :created_by)';
        // $stmt = $this->conn->prepare($sqlQuery);
        // $stmt->execute([':name' => $this->name, 'created_by' => $this->auth_id, ':bank_id' => $this->bank_id]);
        // return $this->conn->lastInsertId();

        return $this->conn->insert('data_importer_fd_batches', [
            'name' => $this->batch_name,
            'created_by' => $this->auth_id,
            'bank_id' => $this->bank_id,
        ]);
    }

    public function getLoanBatchById($id = null)
    {
        $id = $id ?? $this->batch_id;
        return $this->conn->fetch('data_importer_loan_batches', 'id', $id);
    }

    public function batchLoanQuery()
    {
        return '  SELECT loan.id as loan_id, loan.loan_number, loan.client_id, client.membership_no, loan.loan_amount, loan.duration, loan.interest_rate, loan.interest_method, loan.credit_officer_id, loan.loan_product_id, loan.principal_balance, loan.interest_balance, loan.principal_arrears, loan.interest_arrears, loan.disbursement_date, loan.import_status, loan_product.type_name AS loan_type_name, loan.recycle_type, loan.frequency, loan.next_due_date,loan.amount_paid,loan.frequency, loan.interest_amount,
        TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName", \' \',public."User".shared_name)) AS client_names,
        
        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id = loan.credit_officer_id) AS credit_officer_names

        FROM public.data_importer_loan_batch_records loan
        LEFT JOIN "Client" client ON loan.client_id = client."userId"
        LEFT JOIN public."User" ON public."User".id = client."userId"
        LEFT JOIN public.loantypes loan_product ON loan.loan_product_id = loan_product.type_id
        ';
    }

    public function getBatchLoanDetails($id = null)
    {
        $id = $id ?? $this->loan_id;
        $sqlQuery = $this->batchLoanQuery();

        $sqlQuery .= ' WHERE loan.id =:loan_id AND deleted_at IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':loan_id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

        // return $this->conn->fetch('data_importer_loan_batches', 'id', $id);
    }

    public function getBatchLoanRecords($id = null)
    {
        $id = $id ?? $this->batch_id;
        $sqlQuery = $this->batchLoanQuery();

        $sqlQuery .= ' WHERE loan.batch_id =:batch_id AND deleted_at IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':batch_id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * imports loans
     */
    public function importLoans()
    {
        $records = $this->records ?? [];
        if (count($records)) {
            $bank =  $this->conn->fetch('Bank', ['id' => $this->bank_id]);
            if (!@$bank) return "Bank not found";

            $bank_branches =  $this->conn->fetchAll('Branch', 'bankId', $this->bank_id);
            $branch_codes = array_column($bank_branches, 'bcode');

            $batch_id = $this->createNewBatch();
            /**
             * create loan bactch records
             */
            foreach ($records as $record) {
                $branch_code = trim($record['BranchCode']);
                if ($branch_code && !in_array($branch_code, $branch_codes)) continue;
                $branch = $bank_branches[array_search($branch_code, array_column($bank_branches, 'bcode'))];
                if (!@$branch) continue;

                // $current_loan = $this->conn->database->fetch('SELECT * FROM data_importer_loan_batch_records WHERE loan_number = ? AND loan_number IS NOT NULL ', $record['LoanNumber']);

                // if ($current_loan) continue;
                $record['PrincipalBalance'] =
                    amount_to_integer($record['PrincipleBalance']);
                $record['InterestBalance'] =
                    amount_to_integer($record['InterestBalance']);
                $record['PrincipalInArrears'] =
                    amount_to_integer($record['PrincipleInArrears']);
                $record['InterestInArrears'] =
                    amount_to_integer($record['InterestInArrears']);
                $record['BranchID'] = $branch['id'];

                $record['MembershipNumber'] = trim((string) $record['MembershipNumber']);
                $record['BranchID'] = trim($record['BranchID']);

                $client = $this->conn->fetch('Client', ['membership_no' => trim($record['MembershipNumber']), 'branchId' => $record['BranchID']]);
                // return $client;
                if (!$client) continue;

                // $amount_paid = amount_to_integer($record['AmountPaid']);
                // $loan_amount = amount_to_integer($record['LoanAmount']);

                // $principal_balance = amount_to_integer($record['PrincipalBalance']);
                // $interest_balance = amount_to_integer($record['InterestBalance']);
                // if ($amount_paid > 0) {
                //     $principal_balance = max($loan_amount - $amount_paid, 0);
                //     $interest_balance = 0;
                // }

                $imported_data = [
                    'client_id' => $client['userId'],
                    'loan_amount' => amount_to_integer($record['LoanAmount']),
                    'amount_paid' => amount_to_integer($record['PenaltyBalance']),
                    'duration' => @$record['Duration'],
                    'loan_number' => @$record['LoanNumber'],
                    'interest_method' => strtolower(@$record['InterestMethod']),
                    'interest_rate' => @$record['InterestRate'],
                    'disbursement_date' => $record['DisbursementDate'],
                    'credit_officer_id' => @$record['LoanOfficerID'],
                    'loan_product_id' => @$record['LoanProductID'],
                    'principal_balance' => amount_to_integer($record['PrincipalBalance']),
                    'interest_balance' => amount_to_integer($record['InterestBalance']),
                    'interest_amount' => 0,
                    'principal_arrears' => amount_to_integer(@$record['PrincipalInArrears']),
                    'interest_arrears' => amount_to_integer(@$record['InterestInArrears']),
                    'frequency' => @$record['RepaymentFrequency'],
                    'recycle_type' => @$record['DurationType'],
                    'penalty_balance' =>  0,
                    'next_due_date' => db_date_format(@$record['NextDueDate']),
                    'status' => strtolower(@$record['LoanStatus']),
                    'batch_id' => $batch_id,
                ];

                $this->conn->insert('data_importer_loan_batch_records', $imported_data);

                // return "Created";

            }

            // return false;
            return true;
        }

        return "Records not entered";
    }

    function deleteBatchLoan()
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $this->auth_id,
        ];
        return $this->conn->update('data_importer_loan_batch_records', $data, 'id', $this->loan_id);
    }


    /**
     * imports fds
     */
    public function importFds()
    {
        $records = $this->records ?? [];
        if (count($records)) {
            $bank =  $this->conn->fetch('Bank', ['id' => $this->bank_id]);
            if (!@$bank) return "Bank not found";

            $bank_branches =  $this->conn->fetchAll('Branch', 'bankId', $this->bank_id);
            $branch_codes = array_column($bank_branches, 'bcode');

            $batch_id = $this->createNewFdBatch();
            /**
             * create fd batch records
             */
            foreach ($records as $record) {
                $branch_code = trim($record['BranchCode']);
                if ($branch_code && !in_array($branch_code, $branch_codes)) continue;
                $branch = $bank_branches[array_search($branch_code, array_column($bank_branches, 'bcode'))];
                if (!@$branch) continue;


                $record['BranchID'] = $branch['id'];


                $record['MembershipNumber'] = trim((string) $record['MembershipNumber']);
                $record['BranchID'] = trim($record['BranchID']);

                $client = $this->conn->fetch('Client', ['membership_no' => trim($record['MembershipNumber']), 'branchId' => $record['BranchID']]);
                // return $client;
                if (!$client) continue;

                $record['FDStatus'] = 0;

                if (amount_to_integer($record['PrincipalPaid']) >= amount_to_integer($record['Amount'])) {
                    $record['FDStatus'] = 1;
                }

                $imported_data = [
                    'user_id' => $client['userId'],
                    'fd_amount' => amount_to_integer($record['Amount']),
                    'mem_no' => @$record['MembershipNumber'],
                    'fd_date' =>  db_date_format(@$record['DepositDate']),
                    'fd_maturity_date' =>  db_date_format(@$record['MaturityDate']),
                    'fd_branch' => @$record['BranchID'],
                    'fd_int_paid' => amount_to_integer($record['InterestPaid']),
                    'wht_paid' => amount_to_integer($record['WHTPaid']),
                    'princ_paid' => amount_to_integer($record['PrincipalPaid']),
                    'fd_status' => @$record['FDStatus'],
                    'compound_freq' => @$record['CompoundingFrequency'],
                    'int_rate' => @$record['InterestRate/Annum'],
                    'duration_type' => @$record['DurationType'],
                    'fd_duration' => @$record['Duration'],
                    'wht' => @$record['WHTRate'],
                    'batch_id' => $batch_id,
                ];

                $this->conn->insert('data_importer_fd_batch_records', $imported_data);
            }

            // return false;
            return true;
        }

        return "Records not entered";
    }

    function deleteLoanBatch()
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $this->auth_id,
        ];
        $this->conn->update('data_importer_loan_batches', $data, 'id', $this->batch_id);

        /**
         * also delete batch records
         */
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $this->auth_id,
        ];

        $this->conn->update('data_importer_loan_batch_records', $data, 'batch_id', $this->batch_id);
        return true;
    }


    function updateBatchLoan()
    {
        $update_data = [
            'loan_amount' => $this->data['loan_amount'],
            'amount_paid' => $this->data['amount_paid'],
            'interest_rate' => $this->data['interest_rate'],
            'interest_method' => $this->data['interest_method'],
            'credit_officer_id' => $this->data['credit_officer_id'],
            'loan_product_id' => $this->data['loan_product_id'],
            'principal_balance' => $this->data['principal_balance'],
            'interest_balance' => $this->data['interest_balance'],
            'principal_arrears' => $this->data['principal_arrears'],
            'interest_arrears' => $this->data['interest_arrears'],
            'disbursement_date' => $this->data['disbursement_date'],
            'recycle_type' => $this->data['recycle_type'],
            'frequency' => $this->data['frequency'],
            'next_due_date' => $this->data['next_due_date'],
        ];
        return $this->conn->update('data_importer_loan_batch_records', $update_data, 'id', $this->data['loan_id']);
    }


    /**
     * decliningTotalInt method
     */
    private function decliningTotalInt($obj)
    {
        $t_interest = 0;
        if ($obj->amount > 0 && $obj->period > 0) {
            //declining balance
            $principal = $obj->amount / $obj->period;
            $out_standing_principal = $obj->amount;

            for ($i = 0; $i < $obj->period; $i++) {
                $interest = $out_standing_principal * $obj->int_for_one;
                $t_interest += $interest;
                $out_standing_principal -= $principal;
            }

            return $t_interest;
        }
    }

    public function approveBatchLoan($loan_id)
    {
        $batch_loan = $this->conn->fetch('data_importer_loan_batch_records', 'id', $loan_id);
        $client = $this->conn->fetch('Client', 'userId', $batch_loan['client_id']);
        if ($batch_loan && $batch_loan->deleted_at == null && $client) {
            $client_user = $this->conn->fetch('User', 'id', $client['userId']);
            $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];

            $staff_user = $this->conn->fetch('Staff', 'userId', $this->auth_id);

            $duration = $batch_loan['duration'];
            $monthly_interest_rate = round($batch_loan['interest_rate'] / 100, 4);
            if ($batch_loan['interest_method'] == 'flat_rate') {
                $my_int = $batch_loan['loan_amount'] * ($monthly_interest_rate / 1200) * $duration;
            } else {
                // $my_int = $batch_loan['loan_amount'] * ($monthly_interest_rate / 1200) * $duration;

                $obj = new stdClass();
                $obj->int_rate = $monthly_interest_rate;
                $obj->period = $duration;
                $obj->int_for_one =  ($monthly_interest_rate / 1200);
                $obj->amount = $batch_loan['loan_amount'];
                $obj->date = date('Y-m-d');
                $obj->int_method = 'declining';
                $obj->grace_period = 0;
                $obj->period_type = 'm';
                $obj->frequency_type = 'm';
                $obj->frequency = 'MONTHS';
                $obj->grace_period_type = 'pay_none';
                $obj->refineSchedule = 1;

                $my_int =   $this->decliningTotalInt($obj);
              
            }


            $total_loan_amount = $batch_loan['loan_amount'] + $my_int;

            $outstanding = $total_loan_amount - $batch_loan['amount_paid'];
            $date_of_first_pay = $batch_loan['disbursement_date'];

            $total_outstanding = $outstanding + 0;



            $interest_method_id = 1;

            if ($batch_loan['interest_method'] == 'flat_rate') {
                $interest_method_id = 1;
            } else {
                $interest_method_id = 2;
            }

            $repay_cycle_id = 1;
            if ($batch_loan['recycle_type'] == 'WEEKS') {
                $repay_cycle_id = 2;
            } else if ($batch_loan['recycle_type'] == 'MONTHS') {
                $repay_cycle_id = 3;
            } else if ($batch_loan['recycle_type'] == 'DAYS') {
                $repay_cycle_id = 1;
            } else if ($batch_loan['recycle_type'] == 'YEARS') {
                $repay_cycle_id = 5;
            } else {
                $repay_cycle_id = 4;
            }


            if ($batch_loan['amount_paid'] > 0) {
                $batch_amount_paid = $batch_loan['amount_paid'];
            } else {
                $batch_amount_paid = $total_loan_amount - $outstanding;
            }

            $is_performing = true;
            if ($total_outstanding > 0) {
                $is_performing = false;
            }

            $l_int = $my_int - $batch_loan['interest_balance'];
            $l_princ = $batch_loan['loan_amount'] - $batch_loan['principal_balance'];
            // short version
            // if ($batch_loan['amount_paid'] <= $my_int) {
            //     $l_int = $batch_loan['amount_paid'];
            //     $l_princ = 0;
            // } else {
            //     $l_int = $my_int;
            //     $l_princ = $batch_loan['amount_paid'] - $my_int;
            // }



            $loan_data_imported = [
                'batch_record_id' => $batch_loan['id'],
                'branchid' => $client['branchId'],
                'external_loan_no' => @$batch_loan['loan_number'],
                'loanproductid' => $batch_loan['loan_product_id'],
                'loan_type' => $batch_loan['loan_product_id'],
                'principal' => $batch_loan['loan_amount'],
                'interest_amount' => $my_int,
                'requestedamount' => $batch_loan['loan_amount'],
                'approvedamount' => $batch_loan['loan_amount'],
                'disbursedamount' => $batch_loan['loan_amount'],
                'current_balance' => $total_outstanding,
                'status' => $is_performing ? 2 : 4,
                'account_id' => $client['userId'],
                'date_disbursed' => $batch_loan['disbursement_date'],
                'requesteddisbursementdate' => $batch_loan['disbursement_date'],
                'isinrepayment' => true,
                'isperforming' => $is_performing,
                'isapproved' => true,
                'loan_officer' => $batch_loan['credit_officer_id'],
                'repay_cycle_id' => $repay_cycle_id,
                'requested_loan_duration' => $duration,
                'approved_loan_duration' => $duration,
                'interest_method_id' => $interest_method_id,
                'monthly_interest_rate' => $monthly_interest_rate,
                'date_of_first_pay' => $date_of_first_pay,
                'mode_of_disbursement' => 'cash',
                'principal_balance' => $batch_loan['principal_balance'],
                'interest_balance' => $batch_loan['interest_balance'],
                'penalty_balance' => 0,
                'principal_arrears' => $batch_loan['principal_arrears'],
                'interest_arrears' => $batch_loan['interest_arrears'],
                'amount_paid' => max($batch_loan['amount_paid'], 0),
                'total_loan_amount' => $total_loan_amount,
            ];
            // $this->conn->upsert('loan', $loan_data_imported, 'batch_record_id');
            $actual_loan = $this->conn->fetch('loan', 'batch_record_id', $batch_loan['id']);

            if ($actual_loan) {
                $actual_loan_id = $actual_loan['loan_no'];
                $this->conn->update('loan', $loan_data_imported, 'batch_record_id', $batch_loan['id']);
                // return "updated";
            } else {
                $actual_loan_id = $this->conn->insert('loan', $loan_data_imported);
                $actual_loan = $this->conn->fetch('loan', 'loan_no', $actual_loan_id);
                // return "created";
            }



            /**
             * create loan transaction
             */
            // $loan_transaction = $this->conn->fetch('transactions', 'loan_id', $actual_loan_id);
            // if ($loan_transaction) {
            // }



            $this->conn->delete('transactions', 'loan_id', $actual_loan_id);



            $this->conn->insert('transactions', [
                'amount' => max($l_princ, 0),
                'description' => 'Amount Paid as at '.date('Y-m-d'),
                '_authorizedby' => $this->auth_id,
                '_actionby' => $this->auth_id,
                'acc_name' => $client_names,
                'mid' => $client['userId'],
                'approvedby' => $this->auth_id,
                '_branch' => $client['branchId'],
                't_type' => 'L',
                'date_created' => date('Y-m-d'),
                'loan_id' => $actual_loan_id,
                'outstanding_amount' => max($batch_loan['loan_amount'] - $l_princ, 0),
                'outstanding_amount_total' => max($batch_loan['loan_amount'] - $l_princ, 0),
                'loan_interest' => max($l_int, 0),
                'outstanding_interest' => max($my_int - $l_int, 0),
                'outstanding_interest_total' => max($my_int - $l_int, 0),
                'pay_method' => 'cash',
                'loan_penalty' => 0,
                'entry_chanel' => 'data_importer',
            ]);

            /**
             * create loan schedule
             */

            $clear_loan = $total_outstanding > 0 ? false : true;
            $this->bank_object->applyLoanSchedule($actual_loan['loan_no']);
            $this->bank_object->updateTotalLoanAmount($actual_loan['loan_no'], $clear_loan);

            $actual_loan = $this->conn->fetch('loan', 'loan_no', $actual_loan_id);

            $loan_schedule = $this->conn->fetchAll('loan_schedule', 'loan_id', $actual_loan['loan_no']);

            // return $loan_schedule;

            /**
             * update loan interest if wasnt given by data entrant
             */
            if ($batch_loan['amount_paid'] > 0) {
                $i = 0;
                $amount_balance = $batch_loan['amount_paid'];
                $princ_given = max($l_princ, 0);
                $int_given = max($l_int, 0);
                $princ_balancen =
                max($l_princ, 0);
                $int_balancen =
                max($l_int, 0);

                $total_amount_paid = 0;

                $schedule_status_active = 'active';
                $schedule_status_paid = 'paid';

                $principal_components = $this->bank_object->getLoanPrincipal($actual_loan_id);
                $interest_components = $this->bank_object->getLoanInterest($actual_loan_id);
                $schedule_ids = [];
                $next_pay_dates_array = [];
                while ($amount_balance > 0 && $i < count($loan_schedule)) {
                    $current_schedule = $loan_schedule[$i];
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
                    if ($int_given <= $current_schedule['outstanding_interest']) {
                        $interest_paid = $int_given;
                    } else if ($int_given > $current_schedule['outstanding_interest']) {
                        $interest_paid = $current_schedule['outstanding_interest'];

                        /**
                         * if amount can clear both interest and principal
                         */
                        $balance = $amount_balance - $interest_paid;
                        $int_balancen = $int_given - $interest_paid;
                       
                    }

                    if ($princ_given <= $current_schedule['outstanding_principal']) {
                        $principal_paid = $princ_given;
                    } else if ($princ_given > $current_schedule['outstanding_principal']) {
                        $principal_paid = $current_schedule['outstanding_principal'];

                        $balance = $amount_balance - $principal_paid;
                        $princ_balancen = $princ_given - $principal_paid;
                       
                    }

                    $total_paid = $interest_paid + $principal_paid;
                    $amount_balance -= $total_paid;
                    $princ_given -= $principal_paid;
                    $int_given -= $interest_paid;

                    $total_amount_paid += $total_paid;

                    $amount_balance = max($amount_balance, 0);

                    $loan_total_outstanding_principal = max($actual_loan['principal_balance'] - $principal_paid, 0);
                    $loan_total_outstanding_interest = max($actual_loan['interest_balance'] - $interest_paid, 0);
                    $loan_total_outstanding = $loan_total_outstanding_principal + $loan_total_outstanding_interest;

                    $transaction_principal_balance = $current_schedule['principal'] - ($principal_paid + $current_schedule['principal_paid']);
                    $transaction_interest_balance = $current_schedule['interest'] - ($interest_paid + $current_schedule['interest_paid']);

                    /**
                     * Create Loan transaction
                     */
                    // $this->conn->insert('transactions', [
                    //     'amount' => $principal_paid,
                    //     'description' => 'Loan Payment ' . date('jS M, Y'),
                    //     '_authorizedby' => $this->auth_id,
                    //     '_actionby' => $this->auth_id,
                    //     'acc_name' => $client_names,
                    //     'mid' => $client['userId'],
                    //     'approvedby' => $this->auth_id,
                    //     '_branch' => $client['branchId'],
                    //     't_type' => 'L',
                    //     'date_created' => date('Y-m-d'),
                    //     'loan_id' => $actual_loan_id,
                    //     'outstanding_amount' => $principal_components['balance'] - $principal_paid,
                    //     'outstanding_amount_total' => $principal_components['balance'] - $principal_paid,
                    //     'loan_interest' => $interest_paid,
                    //     'outstanding_interest' => $interest_components['balance'] - $interest_paid,
                    //     'outstanding_interest_total' => $interest_components['balance'] - $interest_paid,
                    //     'pay_method' => 'cash',
                    //     'loan_penalty' => 0,
                    //     'entry_chanel' => 'data_importer',
                    // ]);


                    //TODO uncomment this
                    $this->bank_object->updateTotalLoanAmount($loan_id);

                    /**
                     * get next schedule
                     */
                    $forward_schedule = null;
                    if ($amount_balance > 0) {
                        /** get schedule with due balances (status active) 
                         * and can be cleared off now
                         * */
                        $forward_schedule = $this->conn->database->fetch('SELECT * FROM loan_schedule WHERE loan_id = ? AND status=? AND date_of_payment >? ORDER BY date_of_payment ASC ', $loan_id, $schedule_status_active, $current_schedule['date_of_payment']);

                        if ($forward_schedule) {
                            array_push($next_pay_dates_array, $forward_schedule['date_of_payment']);
                        }
                    }

                    /**
                     * update schedule
                     */
                    $schedule_status = $total_paid >= $current_schedule_outstanding_total ? $schedule_status_paid : $schedule_status_active;

                    $this->conn->update('loan_schedule', [
                        'principal_paid' => $principal_paid,
                        'interest_paid' => $interest_paid,
                        'outstanding_principal' => $transaction_principal_balance,
                        'outstanding_interest' => $transaction_interest_balance,
                        'status' => $schedule_status,
                    ], 'schedule_id', $current_schedule['schedule_id']);

                    // $current_schedule = $backward_schedule ? $backward_schedule : $forward_schedule;
                    array_push($schedule_ids, $current_schedule['schedule_id']);
                    $current_schedule = $forward_schedule;

                    // var_dump($current_schedule['schedule_id']);
                    // return [$current_schedule, $amount_balance];
                    $i++;
                }
            } else {
                // $total_interest = $actual_loan['interest_amount'];
                // $total_principal = $actual_loan['principal'];
                // $total_interest_paid = max($total_interest - $batch_loan['interest_balance'], 0);
                // $total_principal_paid = max($total_principal - $batch_loan['principal_balance'], 0);

                // // return $amount_paid;
                // $i = 0;
                // $current_schedule = $loan_schedule[$i];
                // while ($total_interest_paid > 0 && $current_schedule) {
                //     $interest_paid = 0;
                //     if ($total_interest_paid <= $current_schedule['interest']) {
                //         $interest_paid = $total_interest_paid;
                //     } else {
                //         $interest_paid = $current_schedule['interest'];
                //     }

                //     $outstanding_interest = max((int)$current_schedule['interest'] - $interest_paid, 0);
                //     $schedule_data = [
                //         'interest_paid' => $interest_paid,
                //         'outstanding_interest' => $outstanding_interest,
                //     ];

                //     $this->conn->update('loan_schedule', $schedule_data, 'schedule_id', $current_schedule['schedule_id']);

                //     $total_interest_paid -= $interest_paid;
                //     $loan_schedule[$i]['interest_paid'] = $interest_paid;
                //     $i++;
                //     $current_schedule = $loan_schedule[$i];
                // }


                // $i = 0;
                // $current_schedule = $loan_schedule[$i];
                // while ($total_principal_paid > 0 && $current_schedule) {
                //     $interest_paid = (int)$current_schedule['interest_paid'];
                //     $total_due = (int) $current_schedule['principal'] + (int)$current_schedule['interest'];
                //     if ($total_principal_paid <= $current_schedule['principal']) {
                //         $principal_paid = $total_principal_paid;
                //     } else {
                //         $principal_paid = $current_schedule['principal'];
                //     }

                //     $total_paid = $principal_paid + $interest_paid;
                //     $outstanding_principal = max((int)$current_schedule['principal'] - $principal_paid, 0);

                //     $status = ($total_paid >= $total_due) ? 'paid' : 'active';
                //     $schedule_data = [
                //         'principal_paid' => $principal_paid,
                //         'outstanding_principal' => $outstanding_principal,
                //         'status' => $status,
                //     ];

                //     $this->conn->update('loan_schedule', $schedule_data, 'schedule_id', $current_schedule['schedule_id']);

                //     $total_principal_paid -= $principal_paid;
                //     $i++;
                //     $current_schedule = $loan_schedule[$i];
                // }
            }

            $this->bank_object->updateTotalLoanAmount($actual_loan['loan_no']);

            $this->conn->update('data_importer_loan_batch_records', ['import_status' => true], 'id', $batch_loan['id']);

            /**
             * get parent batch pending loans
             */
            $parent_batch_pending_loans = $this->conn->database->fetchAll(' SELECT * FROM data_importer_loan_batch_records WHERE batch_id=? AND import_status=? AND deleted_at IS NULL ', $batch_loan['batch_id'], false);

            /**
             * if all batch loans are cleared
             */
            if (count($parent_batch_pending_loans) <= 0) {
                $this->conn->update('data_importer_loan_batches', ['status' => true], 'id', $batch_loan['batch_id']);
            }


            /**
             * create audit trail
             */
            $this->audit_trail->type = 'loan_data_importer';
            $this->audit_trail->staff_id = $this->auth_id;
            $this->audit_trail->bank_id = $staff_user['bankId'];
            $this->audit_trail->log_message = 'Imported loan - ' . $actual_loan['loan_no'] . ' :: for ' . $client_names . ' on ' . date('jS M, Y');
            $this->audit_trail->create();
            return true;
        }

        return false;
    }


    public function approveLoanBatch()
    {
        if ($this->batch_id) {
            $batch = $this->getLoanBatchById($this->batch_id);

            if (!@$batch) {
                return "Loan batch not found";
            }

            $loans = $this->conn->database->fetchAll(' SELECT * FROM data_importer_loan_batch_records WHERE batch_id=? AND import_status=? AND deleted_at IS NULL  ORDER BY id ASC LIMIT 10', $batch['id'], false);
            // $loans = $this->conn->fetchAll('data_importer_loan_batch_records', 'batch_id', $batch['id']);
            if ($loans) {
                foreach ($loans as $loan) {
                    $this->approveBatchLoan($loan['id']);
                }
            }

            // $this->conn->update('data_importer_loan_batches', ['status' => true], 'id', $batch['id']);

            return true;
        }

        return "Batch Id missing from request";
    }
}
