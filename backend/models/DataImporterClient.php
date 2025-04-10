<?php
require_once '../../config/DbHandler.php';
require_once '../../config/functions.php';
require_once 'DataImporterHelper.php';
require_once 'User.php';
class DataImporterClient
{
    public $db_handler;
    public $auth_id;
    public $bank_id;
    public $batch_name;
    public $batch_id;
    public $client_type;
    public $records;
    public $create_transactions;
    public $branch_id;
    public $num_batch_records;
    public $request;
    public $record_id;
    public $records_with_errors;
    public $importer_helper;
    public function __construct()
    {
        $this->importer_helper = new DataImporterHelper();
        // $this->importer_helper->initiate();
    }


    public function createNewBatch()
    {
        return $this->importer_helper->db_handler->insert('data_importer_client_batches', [
            'batch_name' => $this->batch_name,
            'created_by' => $this->auth_id,
            'bank_id' => $this->bank_id,
            'branch_id' => $this->branch_id,
            'client_type' => $this->client_type,
            'create_transactions' => $this->create_transactions,
        ]);
    }

    public function initiate_client_data($client)
    {
        $data = [
            'old_membership_no' => $client['MembershipNumber'],
            'branch_code' => $client['BranchCode'],
            'first_name' => @$client['FirstName'] ?? @$client['InstitutionName'] ?? @$client['GroupName'],
            'last_name' => @$client['LastName'],
            'email' => @$client['Email'],
            'account_type_id' => (int)@$client['AccountTypeID'],
            'savings_officer_id' => (int)@$client['SavingsOfficerID'],
            'loan_wallet' => amount_to_integer(@$client['LoanWallet']),
            'account_balance' => amount_to_integer(@$client['AccountBalance']),
            'freezed_amount' => amount_to_integer(@$client['FreezedAmount']),
            'membership_fee' => amount_to_integer(@$client['MembershipFee']),
            'gender' => @$client['Gender'],
            'date_of_birth' => db_date_format(@$client['DateofBirth']),
            'primary_phone_number' => @$client['PrimaryTelephoneNumber'],
            'secondary_phone_number' => @$client['SecondaryTelephoneNumber'],
            'next_of_kin_names' => @$client['NextOfKinName'],
            'next_of_kin_phone_number' => @$client['NextOfKinTelephone'],
            'country' => @$client['Country'],
            'district' => @$client['District'],
            'subcounty' => @$client['SubCounty'],
            'parish' => @$client['Parish'],
            'village' => @$client['Village'],
            'registration_date' => db_date_format(@$client['RegistrationDate']),
            'client_type' => @$client['client_type'],
            'shared_name' => @$client['InstitutionName'] ?? @$client['GroupName'],
            'business_type' => tableize(@$client['BusinessType']),
            'business_type_other' => @$client['OtherBusinessType'],
            'is_registered' => strtolower(@$client['RegistrationStatus']) == 'registered' || @$client['IsRegistered'] == "YES" ? true : false,
            'business_registration_number' => @$client['BusinessRegistrationNumber'],
            'business_description' => @$client['BusinessDescription'],
            'number_of_members' => @$client['NumberOfMembers'],
            'is_imported' => false,
            'message_consent' => $client["MessageConsent"] == "YES" ? true : false,
            'error' => null,
            'error_log' => null,
            'branch_id' => null,
            'bank_id' => $this->bank_id,
            'branch_id' => $this->branch_id,
            'created_by' => $this->auth_id,
        ];

        return $data;
    }

    /**
     * imports individual clients
     */
    public function importClientsBatch()
    {
        $this->importer_helper->bank_id = @$this->bank_id;
        $this->importer_helper->branch_id = @$this->branch_id;
        $this->importer_helper->initiate();
        $this->importer_helper->savings_products_ids;
        $records = $this->records ?? [];

        $this->records_with_errors = 0;

        $batch_id = $this->createNewBatch();

        /**
         * if batch has been created
         */
        if ($batch_id) {
            foreach ($records as $client) {
                try {
                    $this->addClientSingle($client, $batch_id);
                } catch (\Throwable $th) {
                    continue;
                }
            }
        }


        // return $user;
        return ['success' => true, 'records_with_errors' => @$this->records_with_errors];
    }

    public function addClientSingle($client, $batch_id)
    {
        $branch_code = $client['BranchCode'] = trim($client['BranchCode']);
        $old_membership_no = $client['MembershipNumber'] = trim($client['MembershipNumber']);
        $client['SavingsOfficerID'] = @$client['SavingOfficerID'] ?? @$client['SavingsOfficerID'];
        $client['client_type'] = $this->client_type;
        $insert_trail = $this->initiate_client_data($client);
        $insert_trail['batch_id'] = $batch_id;

        // validate branch code

        $current_insertion = null;

        $branch = $this->importer_helper->bank_branches[array_search($branch_code, array_column($this->importer_helper->bank_branches, 'bcode'))];
        $insert_trail['branch_id'] = @$branch['id'];

        if (@$client['client_id']) {
            $current_insertion = $this->importer_helper->db_handler->fetch('data_importer_clients', 'id', @$client['client_id']);
        } else {

            if (@$old_membership_no) {
                $current_insertion = $this->importer_helper->db_handler->database->fetch('SELECT * FROM `data_importer_clients` WHERE %and', [
                    ['old_membership_no IS NOT ?', NULL],
                    ['old_membership_no = ?', $old_membership_no],
                    ['%or', [
                        'bank_id' => $this->bank_id,
                        'branch_id' => $this->branch_id,
                    ]],
                ]);
            }
        }

        try {

            $current_client = null;
            if (@$old_membership_no) {
                $current_client = $this->importer_helper->db_handler->fetch('Client', ['old_membership_no' => $old_membership_no, 'branchId' => $branch['id']]);
            }

            /**
             * validate branch
             */
            if (!@$old_membership_no) {
                $insert_trail['error'] = 'Record is missing Membeship Member';
                $insert_trail['error_code'] = 'membership_member_missing';
            }

            /**
             * validate branch
             */
            if (!@$branch) {
                $insert_trail['error'] = 'Invalid BranchCode';
                $insert_trail['error_code'] = 'invalid_branch';
            }

            /**
             * check if member already exists
             */
            else if (@$current_client) {
                $insert_trail['error'] = 'Member already exists';
                $insert_trail['error_code'] = 'exists';
            }


            /**
             * validate account type/ savings account
             */
            else if (@$client['AccountTypeID'] && @$client['AccountTypeID'] > 0 && !in_array(@$client['AccountTypeID'], $this->importer_helper->savings_products_ids)) {
                $insert_trail['error'] = 'Invalid AccountTypeID';
                $insert_trail['error_code'] = 'invalid_account_type_id';
            }

            /**
             * validate staff/savings officer
             */
            else if (@$client['SavingsOfficerID'] && @$client['SavingsOfficerID'] > 0 && !in_array(@$client['SavingsOfficerID'], $this->importer_helper->staff_ids)) {
                $insert_trail['error'] = 'Invalid SavingsOfficerID';
                $insert_trail['error_code'] = 'invalid_savings_officer_id';
            }

            if ($current_insertion) {
                $this->importer_helper->db_handler->update('data_importer_clients', $insert_trail, 'id', $current_insertion['id']);
            } else {
                $this->importer_helper->db_handler->insert('data_importer_clients', $insert_trail);
            }

            if ($insert_trail['error']) {
                $this->records_with_errors++;
            }
        } catch (\Throwable $th) {
            $this->records_with_errors++;
            $insert_trail['error'] = 'Something went wrong';
            $insert_trail['error_code'] = 'SERVER_ERROR';
            $insert_trail['error_log'] = $th->getMessage();

            if ($current_insertion) {
                $this->importer_helper->db_handler->update('data_importer_clients', $insert_trail, 'id', $current_insertion['id']);
            } else {
                $this->importer_helper->db_handler->insert('data_importer_clients', $insert_trail);
            }
        }
    }

    /**
     * updates single client entry
     */
    public function updateSingleRecord()
    {
        $this->importer_helper->bank_id = @$this->request['bank_id'];
        $this->importer_helper->branch_id = @$this->request['branch_id'];
        $this->importer_helper->auth_id = @$this->request['auth_id'];
        $this->importer_helper->initiate();

        $this->records_with_errors = 0;
        $client = $this->importer_helper->db_handler->fetch('data_importer_clients', 'id', $this->request['client_id']);
        if (@$client) {
            /**
             * if client has not been yet imported
             */
            if (!@$client['is_imported']) {
                $this->client_type = @$client['client_type'];
                $request['client_type'] = @$client['client_type'];
                $this->addClientSingle(@$this->request, $client['batch_id']);
                return true;
            }
        }

        return ['success' => false, "message" => "Client not found"];
    }

    public function ClientsBatchQuery()
    {
        // set standard_conforming_strings = on;
        return 'SELECT data_importer_client_batches.id AS batch_id, batch_name, data_importer_client_batches.created_by, data_importer_client_batches.created_at, data_importer_client_batches.client_type,
        TRIM(CONCAT(`User`.`firstName`, \' \', `User`.`lastName`)) as imported_by, 
        data_importer_client_batches.is_imported,

        (SELECT COUNT(*) FROM data_importer_clients WHERE batch_id = data_importer_client_batches.id AND deleted_at IS NULL) AS number_of_records,

        -- (COALESCE((SELECT SUM(loan_amount) FROM data_importer_loan_batch_records WHERE batch_id = batch.id), 0)) AS total_loan_amount,

        (SELECT COUNT(*) FROM data_importer_clients WHERE batch_id=data_importer_client_batches.id AND is_imported=true  AND deleted_at IS NULL) AS num_imported,

        (SELECT COUNT(*) FROM data_importer_clients WHERE batch_id=data_importer_client_batches.id AND error IS NOT NULL  AND deleted_at IS NULL) AS num_failed

        FROM data_importer_client_batches
		LEFT JOIN `User` ON data_importer_client_batches.created_by = `User`.id ';
    }

    public function getDataImporterBatches()
    {
        $this->importer_helper->bank_id = $this->bank_id;
        $this->importer_helper->branch_id = $this->branch_id;

        $this->importer_helper->initiate();
        $branch_ids = [$this->branch_id];
        // return $this->impo
        if ($this->bank_id) {
            $branch_ids = array_column($this->importer_helper->bank_branches, 'id');
            $or_where = [];
            foreach ($branch_ids as $branch_id) {
                $or_where[] = ['data_importer_client_batches.branch_id = ? ', $branch_id];
            }

            $and_where = [
                ['data_importer_client_batches.deleted_at IS ?', null],
                ['data_importer_client_batches.bank_id = ?', $this->bank_id],
                ['data_importer_client_batches.client_type = ?', $this->client_type],
                // ['data_importer_client_batches.is_imported = ?', false],
                // ['%or', $or_where],
            ];

            $query = $this->ClientsBatchQuery();
            $query .= ' WHERE %and ';
            return $this->importer_helper->db_handler->database->fetchAll($query, $and_where);
        } else {
        }
    }


    public function getBatchSingleRecord($id = null)
    {
        $id = $id ?? $this->record_id;
        return $this->importer_helper->db_handler->fetch('data_importer_clients', 'id', (int)$id);
    }

    public function getBatchDetails($id = null)
    {
        $id = $id ?? $this->batch_id;
        $query = $this->ClientsBatchQuery();
        $query .= ' WHERE %and ';
        return $this->importer_helper->db_handler->database->fetch($query, [['data_importer_client_batches.id=?', $id]]);
    }

    public function getBatchRecords($id = null)
    {
        $id = $id ?? $this->batch_id;
        // return $this->importer_helper->db_handler->fetchAll('data_importer_clients', ['batch_id' => $id, 'error' => null, 'delete_at' => null]);
        $error_status = '';
        if (@$this->request['status'] == 'failed') {
            $error_status = 'AND error IS NOT NULL';
        }
        return $this->importer_helper->db_handler->database->fetchAll('SELECT * FROM data_importer_clients WHERE batch_id IN (%i) AND deleted_at IS NULL ' . $error_status, [$id]);
    }

    /**
     * imports clients to main database
     */
    public function importBatchToMainDb()
    {
        // return false;
        if ($this->batch_id) {
            $records = $this->getBatchRecords($this->batch_id);

            $this->num_batch_records = count($records);

            foreach ($records as $client) {
                try {
                    $this->importSingleRecord($client);
                } catch (\Throwable $th) {
                    continue;
                }
            }

            $this->checkBatchIsComplete($this->batch_id);
        }
    }

    public function importSingleRecord($client, $is_single = false)
    {
        $batch = $this->getBatchDetails($client['batch_id']);

        $existing_client = $this->importer_helper->db_handler->fetch('Client', ['old_membership_no' => $client['old_membership_no'], 'branchId' => $client['branch_id']]);

        /**
         * if client does not exist in the main database
         */
        if (!@$existing_client) {

            // return true;
            if ($client && $batch && !@$client['is_imported']) {

                if (@$is_single) {
                    $records = $this->getBatchRecords($client['batch_id']);
                    $this->num_batch_records = count($records);
                }

                // return $client;

                $user = new User($this->importer_helper->db);
                $user->old_membership_no = trim(@$client['loan_wallet']);
                $user->client_type = $client['client_type'];
                $user->message_consent = $client['message_consent'] ? 1 : 0;


                // user level data
                $user->firstName = @$client['first_name'] ?? $client['shared_name'];
                $user->name =  @$client['shared_name'];
                $user->lastName = @$client['last_name'];
                $user->email = @$client['email'];
                $user->gender = @$client['gender'];
                // $user->addressLine1 = @$client['FirstName'];
                // $user->addressLine2 = @$client['FirstName'];
                $user->confirmed = true;
                $user->income = 20000;
                $user->region = '';
                $user->country = @$client['country'];
                $user->district = @$client['district'];
                $user->village = @$client['village'];
                $user->parish = @$client['parish'];
                $user->subcounty = @$client['subcounty'];
                $user->is_registered = @$client['is_registered'];
                $user->primaryCellPhone = @$client['primary_phone_number'];
                $user->secondaryCellPhone = @$client['secondary_phone_number'];
                // $user->mno = $user->mno;
                $user->entry_chanel = $this->importer_helper->entry_chanel;
                $user->spouseName = @$client['next_of_kin_names'];
                $user->spouseCell = @$client['next_of_kin_phone_number'];
                $user->status = 'ACTIVE';
                // $user->nin = $client['FirstName'];
                // $user->spouseNin = $client['FirstName'];
                // $user->profilePhoto = $client['FirstName'];
                // $user->sign = $client['FirstName'];
                // $user->other_attachments = $client['FirstName'];
                // $user->krelationship = $client['FirstName'];
                // $user->kaddress = $client['FirstName'];
                $user->dateOfBirth = db_date_format(@$client['date_of_birth']);
                // $user->profession = $client['FirstName'];
                $user->acc_balance = @$client['account_balance'];
                $user->createdAt = db_date_format(@$client['registration_date']);
                // Client level data
                $user->branchId = @$client['branch_id'];
                $user->serialNumber = 100;
                $user->actype = (int)@$client['account_type_id'];
                $user->freezed_amount = amount_to_integer(@$client['freezed_amount']);
                $user->loan_wallet = 0;
                // $user->loan_wallet = amount_to_integer(@$client['loan_wallet']);
                $user->savings_officer_id = (int)@$client['savings_officer_id'];
                $user->membership_fee = amount_to_integer(@$client['membership_fee']);


                // Business level data

                $business_type = strtolower(@$client['business_type']);
                $business_type = str_replace(' ', '_', $business_type);
                $user->business_type = @$business_type;
                $user->registration_status = ucwords(strtolower(@$client['registration_status']));
                $user->bregno = @$client['business_registration_number'];
                $user->business_nature_description = @$client['business_description'];
                $user->business_type_other = @$client['business_type_other'];

                $user->bname = null;
                $user->baddress = null;
                $user->bcity = null;
                $user->bcountry = null;
                $user->btype = @$business_type;
                $user->is_data_importer_client = true;

                // return $user;


                // check if the client is member or non-member
                if (@$client['account_type_id'] && @$client['account_type_id'] != 0) {
                    // is member generate account number

                    // get account number length && filler character in the account number of the bank
                    // $getAccValues = $user->getBankAccLength($client['branch_id']);


                    // separate the return merge separated by / , i.e acc-length and filler character
                    // $myArray = explode('/', $getAccValues);
                    // $accLength = (int)$myArray[0];
                    // $paddValue = $myArray[1];

                    // get the saving product code 
                    // $accCode = $user->getAccountCode($client['account_type_id']);
                    // $codelength = strlen($accCode['ucode']);
                    // $uselength = $accLength - $codelength;

                    // insert client -- to get the userid of the client
                    $client_id = $user->createClient();

                    // generate the account number now
                    // $take = $client_id;
                    // $padd = sprintf('%' . $paddValue . '' . $uselength . 'd', $take);
                    // $acc_use_no = $accCode['ucode'] . $padd;

                    // update the client and set the  generated account number
                    $user->setClientAccountNumber($client['old_membership_no'], $client_id);
                } else {
                    // non-members account_number =0
                    $user->mno = 0;
                    // create the client 
                    $client_id = $user->createClient();
                }


                $user->clientId = $client_id;
                $created_client = $this->importer_helper->db_handler->fetch('Client', ['id' => $client_id]);

                if ($user->acc_balance > 0 || $user->acc_balance < 0) {
                    // @$batch['create_transactions'] &&
                    /**
                     * create account balance transaction
                     */
                    $transaction_type = 'D';
                    $desc = 'B/F as of ' . normal_date(now());
                    $pmethod = 'Account balance';
                    $cash_account = 0;
                    $acid = null;
                    $now = now();
                    $this->auth_id = 547511;
                    $sqlQuery = 'INSERT INTO public."transactions" 
                            (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,cash_acc,date_created) VALUES
                            (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:cash_acc,:date_created)';
                    $account_names = $user->firstName . '' . $user->lastName;
                    $stmt = $this->importer_helper->db->prepare($sqlQuery);
                    $stmt->bindParam(':amount', $user->acc_balance);
                    $stmt->bindParam(':description', $desc);
                    $stmt->bindParam(':autho', $this->auth_id);
                    $stmt->bindParam(':actby', $this->auth_id);
                    $stmt->bindParam(':accname', $account_names);
                    $stmt->bindParam(':mid', $created_client['userId']);
                    $stmt->bindParam(':approv', $this->auth_id);
                    $stmt->bindParam(':branc', $user->branchId);
                    $stmt->bindParam(':ttype', $transaction_type);
                    $stmt->bindParam(':acid', $acid);
                    $stmt->bindParam(':pay_method', $pmethod);
                    $stmt->bindParam(':cash_acc', $cash_account);
                    $stmt->bindParam(':date_created', $now);
                    $stmt->execute();
                }

                $this->importer_helper->db_handler->update('data_importer_clients', ['is_imported' => true], 'id', $client['id']);

                if ($this->num_batch_records > 0) {
                    $this->num_batch_records--;
                }

                if ($is_single) {
                    $this->checkBatchIsComplete($client['batch_id']);
                }

                return true;
            }
        }

        return false;
    }


    public function adjustImportedAccBal($client_id, $orig_bal, $new_bal)
    {
        $client = $this->getBatchSingleRecord($client_id);

        $existing_client = $this->importer_helper->db_handler->fetch('Client', ['old_membership_no' => $client['old_membership_no'], 'branchId' => $client['branch_id']]);
        $orig_bal = amount_to_integer($orig_bal);
        $new_bal = amount_to_integer($new_bal);

        $diff = $orig_bal - $new_bal;

        $new_client_bal = $existing_client['acc_balance'] + $diff;
        /**
         * if client does not exist in the main database
         */
        if (@$existing_client) {
            $this->importer_helper->db_handler->update('data_importer_clients', ['account_balance' => $new_bal], 'id', $client['id']);
            $this->importer_helper->db_handler->update('Client', ['acc_balance' => $new_client_bal], 'userId', $existing_client['userId']);

            /**
             * create account balance transaction
             */
            $transaction_type = 'D';
            $desc = 'B/F as of ' . normal_date($client['created_at']);
            $pmethod = 'Account balance';
            $cash_account = 0;
            $acid = null;
            $now = $client['created_at'];
            $this->auth_id = 547516;
            $sqlQuery = 'INSERT INTO public."transactions" 
                            (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,cash_acc,date_created) VALUES
                            (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:cash_acc,:date_created)';
            $account_names = @$client['first_name'] . '' . @$client['last_name'];
            $stmt = $this->importer_helper->db->prepare($sqlQuery);
            $stmt->bindParam(':amount', $diff);
            $stmt->bindParam(':description', $desc);
            $stmt->bindParam(':autho', $this->auth_id);
            $stmt->bindParam(':actby', $this->auth_id);
            $stmt->bindParam(':accname', $account_names);
            $stmt->bindParam(':mid', $existing_client['userId']);
            $stmt->bindParam(':approv', $this->auth_id);
            $stmt->bindParam(':branc', $existing_client['branchId']);
            $stmt->bindParam(':ttype', $transaction_type);
            $stmt->bindParam(':acid', $acid);
            $stmt->bindParam(':pay_method', $pmethod);
            $stmt->bindParam(':cash_acc', $cash_account);
            $stmt->bindParam(':date_created', $now);
            $stmt->execute();


            return true;
        }

        return false;
    }

    /**
     * if there are no more records to import for a batch, then 
     * set its imported status to true 
     */
    public function checkBatchIsComplete($batch_id)
    {
        //? $pending = $this->importer_helper->db_handler->database->fetchAll('SELECT * FROM data_importer_clients WHERE batch_id IN (%i) AND is_imported=false AND deleted_at IS NULL', [$batch_id]);
        // $pending_count = count($pending);

        $pending_count = $this->num_batch_records;

        if ($pending_count <= 0) {
            $this->importer_helper->db_handler->update('data_importer_client_batches', ['is_imported' => true], 'id', (int)$batch_id);
        }
    }

    /**
     * if there are no more records to import for a batch, then 
     * set its imported status to true 
     */
    public function getSingleRecord($id)
    {
        $id = $id ?? $this->record_id;
        return $this->importer_helper->db_handler->fetch('data_importer_clients', 'id', $id);
    }
}
