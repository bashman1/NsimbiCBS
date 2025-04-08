<?php
require_once 'User.php';
require_once 'Transaction.php';
require_once 'Bank.php';
require_once 'Loan.php';
require_once 'DataImporterLoanBatch.php';
require_once 'DataImporterClient.php';
require_once '../../config/functions.php';
require_once '../../config/database.php';
require_once '../../config/DbHandler.php';
class DataImporter
{
    public $conn;
    public $clients;
    public $transactions;
    public $create_transactions;
    public $auth_id;
    public $bank_id;
    public $branch_id;
    public $batch_name;
    public $client_type;
    public $batch_id;
    public $records;
    public $data;
    public $db_handler;
    public $entry_chanel = 'data_importer';
    public $loan_instance;
    public $bank_instance;
    public $bank_branches;
    public $branch_codes;
    public $loan_products;
    public $loan_product_ids;
    public $staff;
    public $staff_ids;
    public $savings_products;
    public $savings_products_ids;
    public $importer_type;
    public $record_id;
    public $request;
    public function __construct($conn = null)
    {
        $this->conn = $conn;
        $this->db_handler = new DbHandler();

        $database = new Database();
        $db = $database->connect();

        $this->bank_instance = new Bank($db);
        $this->loan_instance = new Loan($db);
    }

    /**
     * initiate all data importer resources
     */
    public function initiate()
    {

        if ($this->bank_id) {
            $this->bank_instance->id = $this->bank_id;
            $this->bank_branches = $this->bank_instance->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);

            $this->staff = $this->bank_instance->getAllBankStaffs()->fetchAll(PDO::FETCH_ASSOC);

            $this->bank_instance->bank = $this->bank_id;
            $this->savings_products = $this->bank_instance->getBankSavingAccount()->fetchAll(PDO::FETCH_ASSOC);

            $this->loan_instance->createdById = $this->bank_id;
            $this->loan_products = $this->loan_instance->getAllLoanProducts()->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->bank_instance->branchId = $this->branch_id;
            $branch = $this->bank_instance->getBranchDetails($this->branch_id);
            $this->bank_branches = @$branch ?  [$branch] : [];

            $this->bank_instance->id = $this->branch_id;
            $this->staff = $this->bank_instance->getAllBranchStaffs()->fetchAll(PDO::FETCH_ASSOC);


            $this->bank_instance->branch = $this->branch_id;
            $this->savings_products = $this->bank_instance->getBranchSavingAccounts()->fetchAll(PDO::FETCH_ASSOC);

            $this->loan_instance->branchId = $this->branch_id;
            $this->loan_products = $this->loan_instance->getAllBranchLoanProducts()->fetchAll(PDO::FETCH_ASSOC);
        }

        $this->branch_codes = array_column($this->bank_branches, 'bcode');
        $this->loan_product_ids = array_column($this->loan_products, 'type_id');
        $this->staff_ids = array_column($this->staff, 'userId');
        $this->savings_products_ids = array_column($this->savings_products, 'id');
    }

    public function initiate_client_data($client, $user)
    {
        $data = [
            'old_membership_no' => $client['MembershipNumber'],
            'branch_code' => $client['BranchCode'],
            'first_name' => @$client['FirstName'],
            'last_name' => @$client['LastName'],
            'email' => @$client['Email'],
            'account_type_id' => (int)@$client['AccountTypeID'],
            'savings_officer_id' => (int)@$client['SavingsOfficerID'],
            'loan_wallet' => (float)@$client['LoanWallet'],
            'account_balance' => (float)@$client['AccountBalance'],
            'freezed_amount' => (float)@$client['FreezedAmount'],
            'membership_fee' => (float)@$client['MembershipFee'],
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
            'client_type' => @$user->client_type,
            'shared_name' => @$client['InstitutionName'] ?? @$client['GroupName'],
            'business_type' => @$client['BusinessType'],
            'business_type_other' => @$client['OtherBusinessType'],
            'registration_status' => @$client['RegistrationStatus'],
            'business_registration_number' => @$client['BusinessRegistrationNumber'],
            'business_description' => @$client['BusinessDescription'],
            'number_of_members' => @$client['NumberOfMembers'],
            'is_imported' => false,
            'message_consent' => $user->message_consent == 1 ? true : false,
            'error' => null,
            'branch_id' => null,
            'bank_id' => $this->bank_id,
            'branch_id' => $this->branch_id,
            'created_by' => $this->auth_id,
        ];

        return $data;
    }

    /**
     *! this method is obsolete. Dont use
     */
    public function importIndividualClients()
    {

        $this->initiate();
        // return $this->savings_products_ids;
        $records = $this->records ?? [];

        $records_with_errors = 0;
        foreach ($records as $client) {
            $branch_code = $client['BranchCode'] = trim($client['BranchCode']);
            $old_membership_no = $client['MembershipNumber'] = trim($client['MembershipNumber']);
            $client['SavingsOfficerID'] = @$client['SavingOfficerID'] ?? @$client['SavingsOfficerID'];

            $user = new User($this->conn);
            $user->old_membership_no = trim($client['MembershipNumber']);
            $user->client_type = 'individual';
            $user->message_consent = $client['MessageConsent'] == 'YES' ? 1 : 0;

            $insert_trail = $this->initiate_client_data($client, $user);

            // validate branch code


            $branch = $this->bank_branches[array_search($branch_code, array_column($this->bank_branches, 'bcode'))];
            $insert_trail['branch_id'] = @$branch['id'];

            try {

                $current_client = $this->db_handler->fetch('Client', ['old_membership_no' => $old_membership_no, 'branchId' => $branch['id']]);

                $current_insert_error = $this->db_handler->database->fetch('SELECT * FROM `data_importer_clients` WHERE %and', [
                    ['old_membership_no = ?', $old_membership_no],
                    ['%or', [
                        'bank_id' => $this->bank_id,
                        'branch_id' => $this->branch_id,
                    ]],
                ]);;

                /**
                 * validate branch
                 */
                if (!@$branch) {
                    $insert_trail['error'] = 'Invalid BranchCode';
                }

                /**
                 * check if member already exists
                 */
                else if (@$current_client) {
                    $insert_trail['error'] = 'Member already exists';
                }


                /**
                 * validate account type/ savings account
                 */
                else if (@$client['AccountTypeID'] && @$client['AccountTypeID'] > 0 && !in_array(@$client['AccountTypeID'], $this->savings_products_ids)) {
                    $insert_trail['error'] = 'Invalid AccountTypeID';
                }

                /**
                 * validate staff/savings officer
                 */
                else if (@$client['SavingsOfficerID'] && @$client['SavingsOfficerID'] > 0 && !in_array(@$client['SavingsOfficerID'], $this->staff_ids)) {
                    $insert_trail['error'] = 'Invalid SavingsOfficerID';
                }


                if ($insert_trail['error']) {
                    $records_with_errors++;
                    if ($current_insert_error) {
                        $this->db_handler->update('data_importer_clients', $insert_trail, 'id', $current_insert_error['id']);
                    } else {
                        $this->db_handler->insert('data_importer_clients', $insert_trail);
                    }
                    continue;
                }

                /**
                 * Add client to system if not found
                 */

                // user level data
                $user->firstName = @$client['FirstName'];
                $user->lastName = @$client['LastName'];
                $user->email = @$client['Email'];
                $user->gender = @$client['Gender'];
                $user->country = @$client['Country'];
                // $user->addressLine1 = @$client['FirstName'];
                // $user->addressLine2 = @$client['FirstName'];
                $user->village = @$client['Village'];
                $user->confirmed = true;
                $user->parish = @$client['Parish'];
                $user->subcounty = @$client['SubCounty'];
                $user->district = @$client['District'];
                $user->primaryCellPhone = @$client['PrimaryTelephoneNumber'];
                $user->secondaryCellPhone = @$client['SecondaryTelephoneNumber'];
                // $user->mno = $user->mno;
                $user->entry_chanel = $this->entry_chanel;
                $user->spouseName = @$client['NextOfKinName'];
                $user->spouseCell = @$client['NextOfKinTelephone'];
                $user->status = 'ACTIVE';
                // $user->nin = $client['FirstName'];
                // $user->spouseNin = $client['FirstName'];
                // $user->profilePhoto = $client['FirstName'];
                // $user->sign = $client['FirstName'];
                // $user->other_attachments = $client['FirstName'];
                // $user->krelationship = $client['FirstName'];
                // $user->kaddress = $client['FirstName'];
                $user->dateOfBirth = db_date_format(@$client['DateofBirth']);
                // $user->profession = $client['FirstName'];
                $user->acc_balance = amount_to_integer(@$client['AccountBalance']);
                $user->createdAt = db_date_format(@$client['RegistrationDate']);
                // Client level data
                $user->branchId = @$branch['id'];
                $user->serialNumber = 100;
                $user->actype = (int)@$client['AccountTypeID'];
                $user->message_consent = @$client['MessageConsent'] == "YES" ? 1 : 0;
                $user->freezed_amount = amount_to_integer(@$client['FreezedAmount']);
                $user->loan_wallet = amount_to_integer(@$client['LoanWallet']);
                $user->savings_officer_id = (int)@$client['SavingsOfficerID'];
                $user->membership_fee = amount_to_integer(@$client['MembershipFee']);


                // Business level data
                $user->bname = null;
                $user->baddress = null;
                $user->bcity = null;
                $user->bcountry = null;
                $user->bregno = null;
                $user->btype = null;


                // check if the client is member or non-member
                if (@$client['AccountTypeID'] && @$client['AccountTypeID'] != 0) {
                    // is member generate account number

                    // get account number length && filler character in the account number of the bank
                    $getAccValues = $user->getBankAccLength($client['BranchID']);


                    // separate the return merge separated by / , i.e acc-length and filler character
                    $myArray = explode('/', $getAccValues);
                    $accLength = (int)$myArray[0];
                    $paddValue = $myArray[1];

                    // get the saving product code 
                    $accCode = $user->getAccountCode($client['AccountTypeID']);
                    $codelength = strlen($accCode['ucode']);
                    $uselength = $accLength - $codelength;

                    // insert client -- to get the userid of the client
                    $client_id = $user->createClient();

                    // generate the account number now
                    $take = $client_id;
                    $padd = sprintf('%' . $paddValue . '' . $uselength . 'd', $take);
                    $acc_use_no = $accCode['ucode'] . $padd;

                    // update the client and set the  generated account number
                    $user->setClientAccountNumber($acc_use_no, $client_id);
                } else {
                    // non-members account_number =0
                    $user->mno = 0;
                    // create the client 
                    $client_id = $user->createClient();
                }

                /**
                 * create successful data inporter record
                 */
                $insert_trail['is_imported'] = true;
                $this->db_handler->insert('data_importer_clients', $insert_trail);


                $user->clientId = $client_id;
                $created_client = $this->db_handler->fetch('Client', ['id' => $client_id]);
                if ($this->create_transactions && $user->acc_balance > 0) {
                    /**
                     * create account balance transaction
                     */
                    $transaction_type = 'D';
                    $desc = 'Data Importer as of ' . normal_date(now());
                    $pmethod = 'Account balance';
                    $cash_account = 0;
                    $acid = null;
                    $now = now();

                    $sqlQuery = 'INSERT INTO public."transactions" 
                        (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,cash_acc,date_created) VALUES
                        (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:cash_acc,:date_created)';
                    $account_names = $user->firstName . '' . $user->lastName;
                    $stmt = $this->conn->prepare($sqlQuery);
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

                    $insert_trail['is_imported'] = true;
                    if ($current_insert_error) {
                        $this->db_handler->update('data_importer_clients', $insert_trail, 'id', $current_insert_error['id']);
                    } else {
                        $this->db_handler->insert('data_importer_clients', $insert_trail);
                    }
                }
            } catch (\Throwable $th) {
                $records_with_errors++;
                $insert_trail['error'] = 'Something went wrong';
                $insert_trail['error_log'] = $th->getMessage();
                $this->db_handler->insert('data_importer_clients', $insert_trail);
                continue;
            }
        }
        // return $user;
        return ['success' => true, 'records_with_errors' => @$records_with_errors];
    }


    /**
     *! this method is obsolete. Dont use
     */
    public function importInstitutionClients()
    {
        $this->initiate();
        $records = $this->records ?? [];

        $records_with_errors = 0;
        foreach ($records as $client) {
            $branch_code = $client['BranchCode'] = trim($client['BranchCode']);
            $old_membership_no = trim($client['MembershipNumber']);
            $client['SavingsOfficerID'] = @$client['SavingOfficerID'] ?? @$client['SavingsOfficerID'];

            $user = new User($this->conn);
            $user->old_membership_no = trim($client['MembershipNumber']);
            $user->client_type = 'institution';
            $user->message_consent = $client['MessageConsent'] == 'YES' ? 1 : 0;

            $insert_trail = $this->initiate_client_data($client, $user);

            $branch = $this->bank_branches[array_search($branch_code, array_column($this->bank_branches, 'bcode'))];
            $insert_trail['branch_id'] = @$branch['id'];

            try {
                $current_client = $this->db_handler->fetch('Client', ['old_membership_no' => $old_membership_no, 'branchId' => $branch['id']]);

                $current_insert_error = $this->db_handler->database->fetch('SELECT * FROM `data_importer_clients` WHERE %and', [
                    ['old_membership_no = ?', $old_membership_no],
                    ['%or', [
                        'bank_id' => $this->bank_id,
                        'branch_id' => $this->branch_id,
                    ]],
                ]);

                /**
                 * validate branch
                 */
                if (!@$branch) {
                    $insert_trail['error'] = 'Invalid BranchCode';
                }

                /**
                 * check if member already exists
                 */
                else if (@$current_client) {
                    $insert_trail['error'] = 'Member already exists';
                }


                /**
                 * validate account type/ savings account
                 */
                else if (@$client['AccountTypeID'] && @$client['AccountTypeID'] > 0 && !in_array(@$client['AccountTypeID'], $this->savings_products_ids)) {
                    $insert_trail['error'] = 'Invalid AccountTypeID';
                }

                /**
                 * validate staff/savings officer
                 */
                else if (@$client['SavingsOfficerID'] && @$client['SavingsOfficerID'] > 0 && !in_array(@$client['SavingsOfficerID'], $this->staff_ids)) {
                    $insert_trail['error'] = 'Invalid SavingsOfficerID';
                }


                if ($insert_trail['error']) {
                    $records_with_errors++;
                    if ($current_insert_error) {
                        $this->db_handler->update('data_importer_clients', $insert_trail, 'id', $current_insert_error['id']);
                    } else {
                        $this->db_handler->insert('data_importer_clients', $insert_trail);
                    }
                    continue;
                }


                /**
                 * Add client to system if not found
                 */

                // user level data
                $user->name = @$client['InstitutionName'];
                $user->firstName = @$client['InstitutionName'];

                $business_type = strtolower(@$client['BusinessType']);
                $business_type = str_replace(' ', '_', $business_type);

                $user->business_type = @$business_type;
                $user->registration_status = ucwords(strtolower(@$client['RegistrationStatus']));
                // $user->registration_status = @$client['RegistrationStatus'] == "YES" ? "Registered" : "Not Registered";
                $user->bregno = @$client['BusinessRegistrationNumber'];
                $user->business_nature_description = @$client['BusinessDescription'];
                $user->email = @$client['Email'];
                $user->country = @$client['Country'];
                // $user->addressLine1 = @$client['FirstName'];
                // $user->addressLine2 = @$client['FirstName'];
                $user->village = @$client['Village'];
                $user->confirmed = true;
                $user->parish = @$client['Parish'];
                $user->subcounty = @$client['SubCounty'];
                $user->district = @$client['District'];
                $user->primaryCellPhone = @$client['PrimaryTelephoneNumber'];
                $user->secondaryCellPhone = @$client['SecondaryTelephoneNumber'];
                // $user->mno = $user->mno;
                $user->entry_chanel = $this->entry_chanel;
                $user->status = 'ACTIVE';
                $user->acc_balance = amount_to_integer(@$client['AccountBalance']);
                $user->createdAt = db_date_format(@$client['RegistrationDate']);
                // Client level data
                $user->branchId = @$branch['id'];
                $user->serialNumber = 100;
                $user->actype = @$client['AccountTypeID'] ?? 0;
                $user->message_consent = strcasecmp(@$client['MessageConsent'], "yes") == 0 ? 1 : 0;
                $user->freezed_amount = amount_to_integer(@$client['FreezedAmount']);
                $user->loan_wallet = amount_to_integer(@$client['LoanWallet']);
                $user->savings_officer_id = @$client['SavingsOfficerID'];
                $user->membership_fee = amount_to_integer(@$client['MembershipFee']);


                // Business level data
                $user->bname = null;
                $user->baddress = null;
                $user->bcity = null;
                $user->bcountry = null;
                $user->bregno = null;
                $user->btype = null;

                $sms_phone_numbers = [];
                if (@$client['PrimaryTelephoneNumber']) {
                    $sms_phone_numbers = [@$client['PrimaryTelephoneNumber']];
                } else if (@$client['SecondaryTelephoneNumber']) {
                    $sms_phone_numbers = [@$client['SecondaryTelephoneNumber']];
                }

                $user->sms_phone_numbers = $sms_phone_numbers;

                // check if the client is member or non-member
                if (@$client['AccountTypeID'] && @$client['AccountTypeID'] != 0) {
                    // is member generate account number

                    // get account number length && filler character in the account number of the bank
                    $getAccValues = $user->getBankAccLength($client['BranchID']);


                    // separate the return merge separated by / , i.e acc-length and filler character
                    $myArray = explode('/', $getAccValues);
                    $accLength = (int)$myArray[0];
                    $paddValue = $myArray[1];

                    // get the saving product code 
                    $accCode = $user->getAccountCode($client['AccountTypeID']);
                    $codelength = strlen($accCode['ucode']);
                    $uselength = $accLength - $codelength;

                    // insert client -- to get the userid of the client
                    $client_id = $user->createClient();

                    // generate the account number now
                    $take = $client_id;
                    $padd = sprintf('%' . $paddValue . '' . $uselength . 'd', $take);
                    $acc_use_no = $accCode['ucode'] . $padd;

                    // update the client and set the  generated account number
                    $user->setClientAccountNumber($acc_use_no, $client_id);
                } else {
                    // non-members account_number =0
                    $user->mno = 0;
                    // create the client 
                    $client_id = $user->createClient();
                }

                $user->clientId = $client_id;
                $created_client = $this->db_handler->fetch('Client', ['id' => $client_id]);

                if ($client['BusinessRegistrationNumber']) {
                    $created_client = $this->db_handler->update('Business', ['registrationNumber' => $client['BusinessRegistrationNumber']], 'clientId', $client_id);
                }

                if ($this->create_transactions && $user->acc_balance > 0) {
                    /**
                     * create account balance transaction
                     */
                    $transaction_type = 'D';
                    $desc = 'Data Importer as of ' . normal_date(now());
                    $pmethod = 'Account balance';
                    $cash_account = 0;
                    $acid = null;
                    $now = now();

                    $sqlQuery = 'INSERT INTO public."transactions" 
                        (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,cash_acc,date_created) VALUES
                        (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:cash_acc,:date_created)';
                    $account_names = $user->firstName . '' . $user->lastName;
                    $stmt = $this->conn->prepare($sqlQuery);
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
            } catch (\Throwable $th) {
                $records_with_errors++;
                $insert_trail['error'] = 'Something went wrong';
                $insert_trail['error_log'] = $th->getMessage();
                $this->db_handler->insert('data_importer_clients', $insert_trail);
                continue;
            }
        }
        // return $user;
        return ['success' => true, 'records_with_errors' => @$records_with_errors];
    }



    /**
     *! this method is obsolete. Dont use
     */
    public function importGroupClients()
    {
        $this->initiate();
        // return $this->savings_products_ids;
        $records = $this->records ?? [];

        $records_with_errors = 0;

        foreach ($records as $client) {
            $branch_code = $client['BranchCode'] = trim($client['BranchCode']);
            $old_membership_no = $client['MembershipNumber'] = trim($client['MembershipNumber']);
            $client['SavingsOfficerID'] = @$client['SavingOfficerID'] ?? @$client['SavingsOfficerID'];

            $user = new User($this->conn);
            $user->old_membership_no = trim($client['MembershipNumber']);
            $user->client_type = 'group';
            $user->message_consent = $client['MessageConsent'] == 'YES' ? 1 : 0;

            $insert_trail = $this->initiate_client_data($client, $user);

            // validate branch code
            $branch = $this->bank_branches[array_search($branch_code, array_column($this->bank_branches, 'bcode'))];
            $insert_trail['branch_id'] = @$branch['id'];

            try {
                $current_client = $this->db_handler->fetch('Client', ['old_membership_no' => $old_membership_no, 'branchId' => $branch['id']]);

                $current_insert_error = $this->db_handler->database->fetch('SELECT * FROM `data_importer_clients` WHERE %and', [
                    ['old_membership_no = ?', $old_membership_no],
                    ['%or', [
                        'bank_id' => $this->bank_id,
                        'branch_id' => $this->branch_id,
                    ]],
                ]);;

                /**
                 * validate branch
                 */
                if (!@$branch) {
                    $insert_trail['error'] = 'Invalid BranchCode';
                }

                /**
                 * check if member already exists
                 */
                else if (@$current_client) {
                    $insert_trail['error'] = 'Member already exists';
                }


                /**
                 * validate account type/ savings account
                 */
                else if (@$client['AccountTypeID'] && @$client['AccountTypeID'] > 0 && !in_array(@$client['AccountTypeID'], $this->savings_products_ids)) {
                    $insert_trail['error'] = 'Invalid AccountTypeID';
                }

                /**
                 * validate staff/savings officer
                 */
                else if (@$client['SavingsOfficerID'] && @$client['SavingsOfficerID'] > 0 && !in_array(@$client['SavingsOfficerID'], $this->staff_ids)) {
                    $insert_trail['error'] = 'Invalid SavingsOfficerID';
                }


                if ($insert_trail['error']) {
                    $records_with_errors++;
                    if ($current_insert_error) {
                        $this->db_handler->update('data_importer_clients', $insert_trail, 'id', $current_insert_error['id']);
                    } else {
                        $this->db_handler->insert('data_importer_clients', $insert_trail);
                    }
                    continue;
                }

                // return $current_client;

                /**
                 * Add client to system if not found
                 */

                // user level data
                $user->name = @$client['GroupName'];
                $user->firstName = @$client['GroupName'];
                $user->business_type = @$client['BusinessType'];
                $user->number_of_members = @$client['NumberOfMembers'];
                $user->email = @$client['Email'];
                $user->country = @$client['Country'];
                // $user->addressLine1 = @$client['FirstName'];
                // $user->addressLine2 = @$client['FirstName'];
                $user->village = @$client['Village'];
                $user->confirmed = true;
                $user->parish = @$client['Parish'];
                $user->subcounty = @$client['SubCounty'];
                $user->district = @$client['District'];
                $user->primaryCellPhone = @$client['PrimaryTelephoneNumber'];
                $user->secondaryCellPhone = @$client['SecondaryTelephoneNumber'];
                // $user->mno = $user->mno;
                $user->entry_chanel = $this->entry_chanel;
                $user->status = 'ACTIVE';
                $user->acc_balance = amount_to_integer(@$client['AccountBalance']);
                $user->createdAt = db_date_format(@$client['RegistrationDate']);
                // Client level data
                $user->branchId = @$branch['id'];
                $user->serialNumber = 100;
                $user->actype = @$client['AccountTypeID'] ?? 0;
                $user->message_consent = strcasecmp(@$client['MessageConsent'], "yes") == 0 ? 1 : 0;
                $user->freezed_amount = amount_to_integer(@$client['FreezedAmount']);
                $user->loan_wallet = amount_to_integer(@$client['LoanWallet']);
                $user->savings_officer_id = @$client['SavingsOfficerID'];
                $user->membership_fee = amount_to_integer(@$client['MembershipFee']);


                // Business level data
                $user->bname = null;
                $user->baddress = null;
                $user->bcity = null;
                $user->bcountry = null;
                $user->bregno = null;
                $user->btype = null;

                $sms_phone_numbers = [];
                if (@$client['PrimaryTelephoneNumber']) {
                    $sms_phone_numbers = [@$client['PrimaryTelephoneNumber']];
                } else if (@$client['SecondaryTelephoneNumber']) {
                    $sms_phone_numbers = [@$client['SecondaryTelephoneNumber']];
                }

                $user->sms_phone_numbers = $sms_phone_numbers;

                // check if the client is member or non-member
                if (@$client['AccountTypeID'] && @$client['AccountTypeID'] != 0) {
                    // is member generate account number

                    // get account number length && filler character in the account number of the bank
                    $getAccValues = $user->getBankAccLength($client['BranchID']);


                    // separate the return merge separated by / , i.e acc-length and filler character
                    $myArray = explode('/', $getAccValues);
                    $accLength = (int)$myArray[0];
                    $paddValue = $myArray[1];

                    // get the saving product code 
                    $accCode = $user->getAccountCode($client['AccountTypeID']);
                    $codelength = strlen($accCode['ucode']);
                    $uselength = $accLength - $codelength;

                    // insert client -- to get the userid of the client
                    $client_id = $user->createClient();

                    // generate the account number now
                    $take = $client_id;
                    $padd = sprintf('%' . $paddValue . '' . $uselength . 'd', $take);
                    $acc_use_no = $accCode['ucode'] . $padd;

                    // update the client and set the  generated account number
                    $user->setClientAccountNumber($acc_use_no, $client_id);
                } else {
                    // non-members account_number =0
                    $user->mno = 0;
                    // create the client 
                    $client_id = $user->createClient();
                }

                $user->clientId = $client_id;
                $created_client = $this->db_handler->fetch('Client', ['id' => $client_id]);
                if ($this->create_transactions && $user->acc_balance > 0) {
                    /**
                     * create account balance transaction
                     */
                    $transaction_type = 'D';
                    $desc = 'Data Importer as of ' . normal_date(now());
                    $pmethod = 'Account balance';
                    $cash_account = 0;
                    $acid = null;
                    $now = now();

                    $sqlQuery = 'INSERT INTO public."transactions" 
                        (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,cash_acc,date_created) VALUES
                        (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:cash_acc,:date_created)';
                    $account_names = $user->firstName . '' . $user->lastName;
                    $stmt = $this->conn->prepare($sqlQuery);
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

                $insert_trail['is_imported'] = true;
                $insert_trail['error'] = null;
                if ($current_insert_error) {
                    $this->db_handler->update('data_importer_clients', $insert_trail, 'id', $current_insert_error['id']);
                } else {
                    $this->db_handler->insert('data_importer_clients', $insert_trail);
                }
            } catch (\Throwable $th) {
                $records_with_errors++;
                $insert_trail['error'] = 'Something went wrong';
                $insert_trail['error_log'] = $th->getMessage();
                $this->db_handler->insert('data_importer_clients', $insert_trail);
                continue;
            }
        }
        // return $user;
        return ['success' => true, 'records_with_errors' => @$records_with_errors];
    }


    /**
     * imports savings
     */
    public function importTransactions()
    {
        $bank_instance = new Bank($this->conn);
        $bank_instance->id = $this->bank_id;
        $bank_branches = $bank_instance->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);
        $branch_codes = array_column($bank_branches, 'bcode');
        $records = $this->records ?? [];

        $has_created_transactions = false;
        foreach ($records as $record) {
            $branch_code = trim($record['BranchCode']);
            $old_membership_no = trim($record['MembershipNumber']);
            $transaction_reference = trim($record['TrxnRef']);

            if ($branch_code && !in_array($branch_code, $branch_codes)) continue;

            $branch = $bank_branches[array_search($branch_code, array_column($bank_branches, 'bcode'))];

            if (!@$branch) continue;

            $client = $this->db_handler->fetch('Client', ['old_membership_no' => $old_membership_no, 'branchId' => $branch['id']]);

            $client_user = $this->db_handler->fetch('User', 'id', $client['userId']);
            $client_names = @$client_user['firstName'] . ' ' . @$client_user['lastName'];

            if ($client) {
                $existing_transaction = $this->db_handler->fetch('transactions', ['trxn_ref' => $transaction_reference]);

                if ($existing_transaction)  continue;

                /**
                 * create account balance transaction
                 */
                $transaction_type = $record['TrxnType'];
                $desc = 'Data Importer as of ' . normal_date(now());
                $pmethod = @$record['PayMethod'] ?? 'cash';
                $cash_account = 0;
                $acid = @$record['Acid'];
                $bacid = @$record['Bacid'];
                $now = now();

                $sqlQuery = 'INSERT INTO public."transactions" 
                        (amount,description,_authorizedby,_actionby,acc_name,mid,approvedby,_branch,t_type,acid,pay_method,cash_acc,date_created,trxn_ref,entry_chanel, bacid,cheque_no,charges) VALUES
                        (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:acid,:pay_method,:cash_acc,:date_created,:trxn_ref,:entry_chanel,:bacid,:cheque,:charges)';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':amount', amount_to_integer($record['TrxnAmount']));
                $stmt->bindParam(':description', $desc);
                $stmt->bindParam(':autho', $this->auth_id);
                $stmt->bindParam(':actby', $this->auth_id);
                $stmt->bindParam(':bacid', $bacid);
                $stmt->bindParam(':cheque', $record['TrxnCheque']);
                $stmt->bindParam(':charges', amount_to_integer($record['TrxnCharge']));

                $stmt->bindParam(':accname', $client_names);
                $stmt->bindParam(':mid', $client['userId']);
                $stmt->bindParam(':approv', $this->auth_id);
                $stmt->bindParam(':branc', $client['branchId']);
                $stmt->bindParam(':ttype', $transaction_type);
                $stmt->bindParam(':acid', $acid);
                $stmt->bindParam(':pay_method', $pmethod);
                $stmt->bindParam(':cash_acc', $cash_account);
                $stmt->bindParam(':date_created', db_date_format($record['DateCreated']));
                $stmt->bindParam(':trxn_ref', $record['TrxnRef']);
                $stmt->bindParam(':entry_chanel', $this->entry_chanel);
                $stmt->execute();

                $has_created_transactions = true;
            }
        }

        return $has_created_transactions;
    }

    public function getClientByMembershipNumberBranchID($memebership_no, $branch_id)
    {
        $sqlQuery = ' SELECT * FROM "Client" WHERE membership_no=:membership_no AND "branchId"=:branch_id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':membership_no' => trim(@$memebership_no), ':branch_id' => trim($branch_id)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * imports shares
     */
    public function importShareRegister()
    {
        $bank_instance = new Bank($this->conn);
        $bank_instance->id = $this->bank_id;
        $bank_branches = $bank_instance->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);
        $branch_codes = array_column($bank_branches, 'bcode');
        $records = $this->records ?? [];

        foreach ($records as $record) {
            $branch_code = trim($record['BranchCode']);
            $old_membership_no = trim($record['MembershipNumber']);
            if ($branch_code && !in_array($branch_code, $branch_codes)) continue;

            $branch = $bank_branches[array_search($branch_code, array_column($bank_branches, 'bcode'))];

            if (!@$branch) continue;

            $client = $this->db_handler->fetch('Client', ['membership_no' => $old_membership_no, 'branchId' => $branch['id']]);

            if ($client) {
                // create share holder
                $sqlQuery = 'INSERT INTO public.share_register(
                userid, share_amount, no_shares, savings_dividends, shares_dividends, added_by, branch_id,date_added,entry_chanel)
               VALUES (:client_id,:share_amount,:no_shares,:savings_dividends,:shares_dividends,:added_by,:branch_id,:created_at,:entry_chanel)';

                $stmt = $this->conn->prepare($sqlQuery);
                $record['ShareAmount'] = $record['ShareAmount'] ?? 0;
                $record['NumberOfShares'] = $record['NumberOfShares'] ?? 0;
                $record['SavingsDividends'] = $record['SavingsDividends'] ?? 0;
                $record['SharesDividends'] = $record['SharesDividends'] ?? 0;

                $stmt->bindParam(':client_id', $client['userId']);
                $stmt->bindParam(':share_amount',  amount_to_integer($record['ShareAmount']));
                $stmt->bindParam(':no_shares',  amount_to_integer($record['NumberOfShares']));
                $stmt->bindParam(':savings_dividends',  amount_to_integer($record['SavingsDividends']));
                $stmt->bindParam(':shares_dividends',  amount_to_integer($record['SharesDividends']));
                $stmt->bindParam(':added_by',  $this->auth_id);
                $stmt->bindParam(':branch_id',  $client['branchId']);
                $stmt->bindParam(':created_at',  db_date_format($record['DateCreated']));

                $stmt->bindParam(':entry_chanel',  $this->entry_chanel);
                $stmt->execute();
            }
        }

        return true;
    }

    /**
     * imports transfer shares
     */
    public function importShareTransfers()
    {
        $bank_instance = new Bank($this->conn);
        $bank_instance->id = $this->bank_id;
        $bank_branches = $bank_instance->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);
        $branch_codes = array_column($bank_branches, 'bcode');

        $records = $this->records ?? [];
        foreach ($records as $record) {
            $sender_membership_no = trim($record['SenderMembershipNumber']);
            $sender_branch_code = trim($record['SenderBranchCode']);
            $receiver_membership_no = trim($record['ReceiverMembershipNumber']);
            $receiver_branch_code = trim($record['ReceiverBranchCode']);

            /**
             * sender can not transfer shares to themselves
             */
            if ($sender_membership_no == $receiver_membership_no) continue;

            if (!in_array($sender_branch_code, $branch_codes) || !in_array($receiver_branch_code, $branch_codes)) continue;

            // return "down here";

            $sender = new User($this->conn);
            $sender->mno = $sender_membership_no;
            $sender_data = $sender->getClientDetails()->fetch(PDO::FETCH_ASSOC);

            $receiver = new User($this->conn);
            $receiver->mno = $receiver_membership_no;
            $receiver_data = $receiver->getClientDetails()->fetch(PDO::FETCH_ASSOC);

            /**
             * if both clients are in the system
             * Also we check if the same client intends to transfer to themselves. 
             * If thats the case then terminate transaction
             */
            if ($sender_data && $receiver_data) {

                $current_share_value =
                    $record['CurrentShareValue'];
                $amoun = 0;


                if ($current_share_value <= 0) {
                    $no_of_shares = $record['NumberOfShares'];
                    $current_share_value = 1;
                } else {
                    $no_of_shares = $record['NumberOfShares'];
                }

                $amoun = $no_of_shares * $record['NumberOfShares'];

                $sqlQuery = 'SELECT * FROM public."share_register" WHERE userid=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $receiver_data['userId']);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // update existing share holder details
                    $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount+:sa,no_shares=no_shares+:ns WHERE userid=:id';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':id', $receiver_data['userId']);
                    $stmt->bindParam(':sa',  $amoun);
                    $stmt->bindParam(':ns', $no_of_shares);
                    $stmt->execute();
                } else {
                    // create share holder
                    $sqlQuery = 'INSERT INTO public.share_register(
	 userid, share_amount, no_shares, added_by, branch_id, date_added)
	VALUES (:uid,:sa,:ns,:adb,:bid,:date_added)';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':uid', $receiver_data['userId']);
                    $stmt->bindParam(':sa',  $amoun);
                    $stmt->bindParam(':adb', $this->auth_id);
                    $stmt->bindParam(':bid', $receiver_data['branchId']);
                    $stmt->bindParam(':ns', $no_of_shares);
                    $stmt->bindParam(':date_added', db_date_format($record['DateCreated']));
                    $stmt->execute();
                }


                // create share holder
                $sqlQuery = 'INSERT INTO public.share_transfers(
                from_uid, to_uid, no_shares, current_share_value, record_date, date_added,added_by,branch_id, entry_chanel)
               VALUES (:from_uid,:to_uid,:no_shares,:current_share_value,:record_date,:date_added,:added_by,:branch_id,:entry_chanel)';

                $stmt = $this->conn->prepare($sqlQuery);
                $record['NumberOfShares'] = $record['NumberOfShares'] ?? 0;
                $record['CurrentShareValue'] = $record['CurrentShareValue'] ?? 0;

                $stmt->bindParam(':from_uid', $sender_data['userId']);
                $stmt->bindParam(':to_uid', $receiver_data['userId']);
                $stmt->bindParam(':no_shares',  amount_to_integer($record['NumberOfShares']));
                $stmt->bindParam(':current_share_value',  amount_to_integer($record['CurrentShareValue']));
                $stmt->bindParam(':record_date',  db_date_format($record['DateCreated']));
                $stmt->bindParam(':date_added',  db_date_format(now()));
                $stmt->bindParam(':added_by',  $this->auth_id);
                $stmt->bindParam(':branch_id',  $sender_data['branchId']);
                $stmt->bindParam(':entry_chanel',  $this->entry_chanel);
                $stmt->execute();
            }
        }

        return true;
    }

    public function importSharePurchases()
    {
        $bank_instance = new Bank($this->conn);
        $bank_instance->id = $this->bank_id;
        $bank_branches = $bank_instance->getAllBranches()->fetchAll(PDO::FETCH_ASSOC);
        $branch_codes = array_column($bank_branches, 'bcode');
        $records = $this->records ?? [];

        foreach ($records as $record) {
            $old_membership_no = trim($record['MembershipNumber']);
            $branch_code = trim($record['BranchCode']);
            if ($branch_code && !in_array($branch_code, $branch_codes)) continue;

            $branch = $bank_branches[array_search($branch_code, array_column($bank_branches, 'bcode'))];
            if (!@$branch) continue;

            $client = $this->db_handler->fetch('Client', ['membership_no' => $old_membership_no, 'branchId' => $branch['id']]);

            // return $client;
            if ($client) {

                $record['CurrentShareValue'] = amount_to_integer($record['CurrentShareValue']);
                $record['Amount'] =
                    amount_to_integer($record['Amount']);
                $record['NumberOfShares'] = amount_to_integer($record['NumberOfShares']);
                // $current_share_value = $row['share_value'];
                $current_share_value = $record['CurrentShareValue'];


                if ($current_share_value <= 0) {
                    $no_of_shares = $record['Amount']  / 1;
                    $current_share_value = 1;
                } else {
                    $no_of_shares = $record['Amount']  / $current_share_value;
                }


                $sqlQuery = 'SELECT * FROM public."share_register" WHERE userid=:id';

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->bindParam(':id', $client['userId']);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // update existing share holder details
                    $sqlQuery = 'UPDATE public."share_register" SET share_amount=share_amount+:sa,no_shares=no_shares+:ns WHERE userid=:id';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':id', $client['userId']);
                    $stmt->bindParam(':sa',  $record['Amount']);
                    $stmt->bindParam(':ns', $no_of_shares);
                    $stmt->execute();
                } else {
                    // create share holder
                    $sqlQuery = 'INSERT INTO public.share_register(
	 userid, share_amount, no_shares, added_by, branch_id, date_added)
	VALUES (:uid,:sa,:ns,:adb,:bid,:date_added)';

                    $stmt = $this->conn->prepare($sqlQuery);

                    $stmt->bindParam(':uid', $client['userId']);
                    $stmt->bindParam(':sa',  $record['Amount']);
                    $stmt->bindParam(':adb', $this->auth_id);
                    $stmt->bindParam(':bid', $client['branchId']);
                    $stmt->bindParam(':ns', $no_of_shares);
                    $stmt->bindParam(':date_added', db_date_format($record['DateCreated']));
                    $stmt->execute();
                }
                // create share holder
                $sqlQuery = 'INSERT INTO public.share_purchases(
                user_id, no_of_shares, current_share_value, amount, added_by, branch_id,record_date, entry_chanel)
               VALUES (:user_id,:no_of_shares,:current_share_value,:amount,:added_by,:branch_id,:record_date,:entry_chanel)';

                $stmt = $this->conn->prepare($sqlQuery);
                $record['CurrentShareValue'] = $record['CurrentShareValue'] ?? 0;
                $record['Amount'] = $record['Amount'] ?? 0;
                $record['NumberOfShares'] = $record['NumberOfShares'] ?? 0;

                $stmt->bindParam(':user_id', $client['userId']);
                $stmt->bindParam(':no_of_shares', $no_of_shares);
                $stmt->bindParam(':current_share_value',  amount_to_integer($record['CurrentShareValue']));
                $stmt->bindParam(':amount',  amount_to_integer($record['Amount']));
                $stmt->bindParam(':added_by',  $this->auth_id);
                $stmt->bindParam(':branch_id',  $client['branchId']);
                $stmt->bindParam(':record_date',  db_date_format($record['DateCreated']));
                $stmt->bindParam(':entry_chanel',  $this->entry_chanel);
                $stmt->execute();

                if ($this->create_transactions) {
                    $tt_type = 'W';
                    $description = 'Share Purchase ::: Data Importer as of ' . normal_date(now());
                    $now = now();
                    $payment_method = "cash";
                    $account_name = $client['firstName'] . ' ' . $client['lastName'];

                    $sqlQuery = ' INSERT INTO public.transactions (amount,description,_authorizedby,_actionby, acc_name,mid,approvedby,_branch,t_type,pay_method,date_created,entry_chanel) 
                    VALUES
                        (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created,:entry_chanel)';

                    $stmt = $this->conn->prepare($sqlQuery);
                    $stmt->bindParam(':amount',   amount_to_integer($record['Amount']));
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':autho', $this->auth_id);
                    $stmt->bindParam(':actby', $this->auth_id);
                    $stmt->bindParam(':accname', $account_name);
                    $stmt->bindParam(':mid', $client['userId']);
                    $stmt->bindParam(':approv', $this->auth_id);
                    $stmt->bindParam(':branc', $client['branchId']);
                    $stmt->bindParam(':ttype', $tt_type);
                    $stmt->bindParam(':pay_method', $payment_method);
                    $stmt->bindParam(':date_created', $now);
                    $stmt->bindParam(':entry_chanel',  $this->entry_chanel);
                    $stmt->execute();
                }
            }
        }

        return true;
    }


    /**
     * imports chart of accounts and trial balance
     */
    public function importChartOfAccounts()
    {
        $data = $this->data;
        if (!@$data['account_id']) return false;

        $sqlQuery = ' SELECT * FROM "Account" WHERE id=:id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':id' => @$data['account_id']]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        // return $account;

        /**
         * if account is found
         */
        $amount = amount_to_integer($data['amount']);
        if ($account) {
            $tt_type = getAccountType($account['type']);
            $notes = @$data['notes'];
            $importer_description = 'Chart of Accounts ::: Data Importer B/F as of ' . normal_date(now());
            $description = $notes ? $notes . ' - ' . $importer_description : $importer_description;
            $payment_method = "cash";
            $account_name = "Chart of Accounts";

            if ($tt_type == 'ASS') {
                $cr_dr = 'debit';
            } else {
                $cr_dr = 'credit';
            }

            $sqlQuery = ' INSERT INTO public.transactions (amount,description,_authorizedby,_actionby, acc_name,mid,approvedby,_branch,t_type,pay_method,date_created,acid,entry_chanel,cr_dr) 
                    VALUES
                        (:amount,:description,:autho,:actby,:accname,:mid,:approv,:branc,:ttype,:pay_method,:date_created,:acid,:entry_chanel,:cr_dr)';

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':amount',   $amount);
            $stmt->bindParam(':cr_dr',   $cr_dr);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':autho', $this->auth_id);
            $actby = "Data Importer";
            $stmt->bindParam(':actby', $actby);
            $stmt->bindParam(':accname', $account_name);

            $mid = 0;
            $dd = db_date_format($data['record_date']);
            $stmt->bindParam(':mid', $mid);
            $stmt->bindParam(':approv', $this->auth_id);
            $stmt->bindParam(':branc', $account['branchId']);
            $stmt->bindParam(':ttype', $tt_type);
            $stmt->bindParam(':pay_method', $payment_method);

            $stmt->bindParam(':date_created', $dd);
            $stmt->bindParam(':acid', $account['id']);
            $stmt->bindParam(':entry_chanel',  $this->entry_chanel);
            $stmt->execute();

            $total_amount = $account['balance'] + $amount;
            $this->db_handler->update('Account', ['balance' => $total_amount], 'id', $data['account_id']);

            $data_importer_account = $this->db_handler->fetch('Account', ['type' => 'SUSPENSES', 'name' => 'Data Importer B/F A/C', 'branchId' => $account['branchId']]);
            if ($data_importer_account) {
                $this->db_handler->update('Account', ['balance' => $total_amount], 'id', $data_importer_account['id']);
            } else {
                $this->db_handler->insert('Account', ['branchId' => $account['branchId'], 'balance' => $amount, 'type' => 'SUSPENSES', 'name' => 'Data Importer B/F A/C', 'description' => 'Data importer Chart of Accounts', 'isSystemGenerated' => false]);
            }

            return true;
        }

        return false;
    }


    public function getClientsErrorLogs()
    {
        $this->initiate();
        // return [];
        $branch_ids = [$this->branch_id];
        if ($this->bank_id) {
            $branch_ids = array_column($this->bank_branches, 'id');
        }


        if ($this->bank_id) {
            // return $this->db_handler->database->fetchAll('SELECT * FROM data_importer_clients WHERE branch_id IN (%i) OR bank_id = %n ', $branch_ids, $this->bank_id);

            $or_where = [];

            foreach ($branch_ids as $branch_id) {
                $or_where[] = ['data_importer_clients.branch_id = ? ', $branch_id];
            }

            $and_where = [
                ['data_importer_clients.client_type = ?', $this->client_type],
                ['data_importer_clients.bank_id = ?', $this->bank_id],
                ['data_importer_clients.is_imported = ?', false],
                // ['%or', $or_where],
            ];

            // return $and_where;
            return $this->db_handler->database->fetchAll('SELECT *, data_importer_clients.bank_id AS client_bank_id, data_importer_clients.branch_id AS client_branch_id FROM `data_importer_clients` LEFT JOIN `data_importer_client_batches` ON data_importer_clients.batch_id=data_importer_client_batches.id WHERE data_importer_clients.deleted_at IS NULL AND data_importer_client_batches.deleted_at IS NULL AND %and', $and_where);
        }


        return $this->db_handler->database->fetchAll('SELECT *, data_importer_clients.bank_id AS client_bank_id, data_importer_clients.branch_id AS client_branch_id FROM `data_importer_clients` LEFT JOIN `data_importer_client_batches` ON data_importer_clients.batch_id=data_importer_client_batches.id WHERE data_importer_clients.deleted_at IS AND data_importer_client_batches.deleted_at IS NULL AND data_importer_clients.branch_id=%n', $this->branch_id);

        // return $this->db_handler->fetchAll('data_importer_clients', 'branch_id', $this->branch_id);
    }



    public function getDataImporterBatches()
    {
        $records = [];
        if ($this->request['type'] == 'clients') {
            $client_importer = new DataImporterClient();
            $client_importer->bank_id = @$this->request['bank_id'];
            $client_importer->branch_id = @$this->request['branch_id'];
            $client_importer->client_type = @$this->request['section'];
            return $records = $client_importer->getDataImporterBatches();
            // $records = ["odf"=> 1000];
        }
        return $records;
    }

    public function deleteBatchRecord()
    {
        if ($this->importer_type && $this->batch_id) {

            if ($this->importer_type == 'client') {
                $this->db_handler->update('data_importer_clients', ['deleted_at' => date('Y-m-d H:i:s')], 'id', $this->batch_id);
                $client_importer = new DataImporterClient();
                $client_importer->checkBatchIsComplete($this->batch_id);
            }
            return true;
        }

        return false;
    }


    /**
     * delete batch
     */
    public function deleteBatch()
    {
        if ($this->importer_type && $this->batch_id) {

            $table = null;
            if ($this->importer_type == 'clients') {
                $table = 'data_importer_client_batches';
            }
            if ($table) {
                $this->db_handler->update($table, ['deleted_at' => date('Y-m-d H:i:s')], 'id', $this->batch_id);
            }
            return true;
        }

        return false;
    }


    /**
     * move data importer records to main database
     */
    public function importBatchToMainDb()
    {

        if ($this->importer_type && $this->batch_id) {
            /**
             * import clients
             */
            if ($this->importer_type == 'clients') {
                $instance = new DataImporterClient();
                $instance->bank_id = $this->bank_id;
                $instance->branch_id = $this->branch_id;
                $instance->batch_id = $this->batch_id;
                $instance->client_type = $this->importer_type;
                return $instance->importBatchToMainDb();
            }
        }

        return false;
    }


    /**
     * move data importer records to main database
     */
    public function importSingleRecord()
    {

        if (@$this->request['type'] && @$this->request['record_id']) {
            /**
             * import clients
             */
            if (@$this->request['type'] == 'client') {
                $client_importer = new DataImporterClient();
                $record = $client_importer->getBatchSingleRecord($this->request['record_id']);
                return $client_importer->importSingleRecord($record, true);
            }

            return true;
        }

        return false;
    }


    /**
     * get data importer records
     */
    public function getBatchRecords()
    {
        $records = [];
        if ($this->request['type'] == 'clients') {
            $client_importer = new DataImporterClient();
            $client_importer->request = $this->request;
            $batch = $client_importer->getBatchDetails($this->request['id']);
            $records = $client_importer->getBatchRecords($this->request['id']);

            return ['records' => $records, 'batch' => $batch];
            // $records = ["odf"=> 1000];
        }
        return $records;
    }


    /**
     * get data importer records
     */
    public function getSingleRecord()
    {
        $records = null;
        if ($this->request['type'] == 'client') {
            $importer = new DataImporterClient();
            $importer->request = $this->request;
        }

        return $importer->getSingleRecord($this->request['id']);
    }

    public function updateSingleRecord()
    {
        if ($this->request['type'] == 'client') {
            $importer = new DataImporterClient();
            $importer->request = $this->request;
        }

        return $importer->updateSingleRecord();
    }
}
