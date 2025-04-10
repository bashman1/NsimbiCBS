<?php

require_once 'User.php';
require_once 'Transaction.php';
require_once 'Bank.php';
require_once 'Loan.php';
require_once 'DataImporterLoanBatch.php';
require_once '../../config/functions.php';
require_once '../../config/database.php';
require_once '../../config/DbHandler.php';

class DataImporterHelper
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
    public $db;

    public function __construct()
    {
        $this->db_handler = new DbHandler();

        $database = new Database();
        $this->db = $database->connect();

        $this->bank_instance = new Bank($this->db);
        $this->loan_instance = new Loan($this->db);
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
}
