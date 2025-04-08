<?php
require_once('Filter.php');
class TransactionReport extends Filter
{
    public $conn;
    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getTransactionReport()
    {
        $binding_array = [];

        $selected_branch = null;
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id, public."Client".id AS client_id, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no, public."Account".name AS journal_account,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \',public."User".shared_name)) FROM public."User" WHERE public."User".id= transaction.mid ) as client_names,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."Account" ON transaction.acid=public."Account".id  
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        if ($this->is_loan_report2) {
            $sqlQuery .= ' LEFT JOIN public."loan" ON transaction.loan_id=public."loan".loan_no ';
        }
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND transaction._branch = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND transaction._branch = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        if (@$this->filter_credit_officer) {
            $sqlQuery .= ' AND public."loan".loan_officer = :filter_authorized_by_id2 ';
            $binding_array[':filter_authorized_by_id2'] = $this->filter_credit_officer;
        }



        if (@$this->filter_actype) {

            if ($this->is_loan_report2) {
                $sqlQuery .= ' AND loan.loan_type = :filter_actype ';
                $binding_array[':filter_actype'] = $this->filter_actype;
            } else {
                $sqlQuery .= ' AND "Client".actype = :filter_actype ';
                $binding_array[':filter_actype'] = $this->filter_actype;
            }
        }

        if (@$this->filter_transaction_type) {
            $sqlQuery .= ' AND transaction.t_type = :filter_transaction_type ';
            $binding_array[':filter_transaction_type'] = $this->filter_transaction_type;
        } else {
            if ($this->is_savings_report) {
                $sqlQuery .= ' AND transaction.t_type IN (\'D\',\'W\') ';
            }
            if ($this->is_loan_report) {
                $sqlQuery .= ' AND transaction.t_type IN (\'L\') ';
            }
            if ($this->is_disburse_report) {
                $sqlQuery .= ' AND transaction.t_type IN (\'A\') ';
            }
        }

        if (@$this->filter_acid) {
            $sqlQuery .= ' AND transaction.acid = :filter_acid ';
            $binding_array[':filter_acid'] = $this->filter_acid;
        }

        // if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
        $this->filter_transaction_start_date = $this->filter_transaction_start_date ?? date('Y-m-d');
        $this->filter_transaction_end_date = $this->filter_transaction_end_date ?? date('Y-m-d');
        $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
        $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
        $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
        // }

        if (@$this->is_expense_report) {
            $sqlQuery .= ' AND transaction.t_type IN (\'E\') ';
        }

        $sqlQuery .= ' ORDER BY transaction.date_created DESC LIMIT 1500';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getDisbursementReport()
    {
        $binding_array = [];


        $sqlQuery = 'SELECT *,transaction.date_created AS trxn_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \',public."User".shared_name)) FROM public."User" WHERE public."User".id= transaction.mid ) as client_names,
        (SELECT type_name FROM loantypes WHERE type_id=public."loan".loanproductid ) AS type_name,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."loan" ON transaction.loan_id=public."loan".loan_no  
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" WHERE transaction.t_type=\'A\' ';
        if ($this->bankId && !@$this->branchId) {
            $sqlQuery .= ' AND public."Branch"."bankId" = :bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
        }

        if (@$this->branchId || @$this->filter_branch_id) {
            $sqlQuery .= ' AND transaction._branch = :branch ';
            $binding_array[':branch'] = $this->filter_branch_id;
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND public."loan".loan_officer = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        if (@$this->filter_actype) {
            $sqlQuery .= ' AND loan.loan_type = :filter_actype ';
            $binding_array[':filter_actype'] = $this->filter_actype;
        }


        $this->filter_transaction_start_date = $this->filter_transaction_start_date ?? date('Y-m-d');
        $this->filter_transaction_end_date = $this->filter_transaction_end_date ?? date('Y-m-d');
        $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
        $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
        $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;


        $sqlQuery .= ' ORDER BY transaction.tid DESC ';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getInterestWaiverReport()
    {
        $binding_array = [];

        $selected_branch = null;
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id, public."Client".id AS client_id, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no, public."Account".name AS journal_account,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id= transaction.mid ) as client_names,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."Account" ON transaction.acid=public."Account".id 
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND public."loan".branchid = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND public."loan"."branchid" = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }
        if (@$this->is_loan_report) {
            $sqlQuery .= ' AND transaction.loan_id = :lid ';
            $binding_array[':lid'] = $this->is_loan_report;
        }


        $sqlQuery .= ' AND transaction.t_type IN (\'WLI\') ';

        if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
        }



        $sqlQuery .= ' ORDER BY transaction.date_created DESC LIMIT 1500';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPenaltyWaiverReport()
    {
        $binding_array = [];

        $selected_branch = null;
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id, public."Client".id AS client_id, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no, public."Account".name AS journal_account,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id= transaction.mid ) as client_names,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."Account" ON transaction.acid=public."Account".id 
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND public."loan".branchid = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND public."loan"."branchid" = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }
        if (@$this->is_loan_report) {
            $sqlQuery .= ' AND transaction.loan_id = :lid ';
            $binding_array[':lid'] = $this->is_loan_report;
        }


        $sqlQuery .= ' AND transaction.t_type IN (\'WLP\') ';

        if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
        }



        $sqlQuery .= ' ORDER BY transaction.date_created DESC LIMIT 1500';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTransactionReport2()
    {
        $binding_array = [];

        $selected_branch = null;
        $acc_name  = null;
        if (@$this->filter_acid) {
            $sqlQuery = 'SELECT * FROM public."Account" WHERE  public."Account".id=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->filter_acid);

            $stmt->execute();
            $row = $stmt->fetch();

            $acc_name = $row['name'] ?? '';
        }
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id, public."Client".id AS client_id, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no, public."Account".name AS journal_account,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id= transaction.mid ) as client_names,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."Account" ON transaction.acid=public."Account".id  AND transaction.cr_acid::uuid=public."Account".id AND transaction.dr_acid::uuid=public."Account".id 
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND transaction._branch = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND transaction._branch = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        // if (@$this->filter_authorized_by_id) {
        //     $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
        //     $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        // }

        if (@$this->filter_actype) {
            $sqlQuery .= ' AND "Client".actype = :filter_actype ';
            $binding_array[':filter_actype'] = $this->filter_actype;
        }

        if (@$this->filter_transaction_type) {
            $sqlQuery .= ' AND transaction.t_type = :filter_transaction_type ';
            $binding_array[':filter_transaction_type'] = $this->filter_transaction_type;
        } else {
            if (@$this->is_expense_report) {
                $sqlQuery .= ' AND transaction.t_type IN (\'I\',\'E\',\'LIA\',\'ASS\',\'R\',\'D\',\'W\',\'SMS\',\'AJE\',\'CAP\',\'BF\',\'C\') ';
            }
        }

        if (@$this->filter_acid) {
            if (!@$this->branchId && @$this->bankId) {
                $sqlQuery .= ' AND (transaction.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$acc_name . '\') OR transaction.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$acc_name . '\') OR transaction.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$acc_name . '\')) ';
                $binding_array[':bk'] = $this->bankId;
            } else {
                $sqlQuery .= ' AND (transaction.acid::text = :filter_acid OR transaction.cr_acid=:filter_acid OR transaction.dr_acid=:filter_acid) ';
                $binding_array[':filter_acid'] = $this->filter_acid;
            }
        }

        if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
            // $this->filter_transaction_start_date = $this->filter_transaction_start_date ?? date('Y-m-d');
            // $this->filter_transaction_end_date = $this->filter_transaction_end_date ?? date('Y-m-d');
            $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
        }



        $sqlQuery .= ' ORDER BY transaction.date_created ASC ';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTrashedTransactionReport()
    {
        $binding_array = [];

        $selected_branch = null;
        $acc_name  = null;
        if (@$this->filter_acid) {
            $sqlQuery = 'SELECT * FROM public."Account" WHERE  public."Account".id=:id ';

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':id', $this->filter_acid);

            $stmt->execute();
            $row = $stmt->fetch();

            $acc_name = $row['name'] ?? '';
        }
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id, public."Client".id AS client_id, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no, public."Account".name AS journal_account,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id= transaction.mid ) as client_names,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ) as authorized_by_names, (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction.trashed_by ) as trashed_by_names ';

        $sqlQuery .= ' FROM public."trash_transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."Account" ON transaction.acid=public."Account".id  AND transaction.cr_acid::uuid=public."Account".id AND transaction.dr_acid::uuid=public."Account".id 
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND transaction._branch = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND transaction._branch = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        // if (@$this->filter_authorized_by_id) {
        //     $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
        //     $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        // }

        if (@$this->filter_actype) {
            $sqlQuery .= ' AND "Client".actype = :filter_actype ';
            $binding_array[':filter_actype'] = $this->filter_actype;
        }

        if (@$this->filter_transaction_type) {
            $sqlQuery .= ' AND transaction.t_type = :filter_transaction_type ';
            $binding_array[':filter_transaction_type'] = $this->filter_transaction_type;
        } else {
            if (@$this->is_expense_report) {
                $sqlQuery .= ' AND transaction.t_type IN (\'I\',\'E\',\'LIA\',\'ASS\',\'R\',\'D\',\'W\',\'SMS\',\'AJE\',\'CAP\',\'BF\',\'C\') ';
            }
        }

        if (@$this->filter_acid) {
            if (!@$this->branchId && @$this->bankId) {
                $sqlQuery .= ' AND (transaction.acid::text IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$acc_name . '\') OR transaction.cr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$acc_name . '\') OR transaction.dr_acid IN(SELECT public."Account".id::text FROM public."Account" LEFT JOIN public."Branch" ON public."Account"."branchId"=public."Branch".id WHERE "bankId"=:bk AND public."Account".name LIKE \'' . @$acc_name . '\')) ';
                $binding_array[':bk'] = $this->bankId;
            } else {
                $sqlQuery .= ' AND (transaction.acid::text = :filter_acid OR transaction.cr_acid=:filter_acid OR transaction.dr_acid=:filter_acid) ';
                $binding_array[':filter_acid'] = $this->filter_acid;
            }
        }

        if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
            // $this->filter_transaction_start_date = $this->filter_transaction_start_date ?? date('Y-m-d');
            // $this->filter_transaction_end_date = $this->filter_transaction_end_date ?? date('Y-m-d');
            $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
        }



        $sqlQuery .= ' ORDER BY transaction.trash_id DESC ';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAccReconciliationsReport()
    {
        $binding_array = [];

        $selected_branch = null;
        $acc_name  = null;

        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, public."Branch".name AS branch_name,
 (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction.rec_by ) as trashed_by_names ';

        $sqlQuery .= ' FROM public."acc_reconciliations" AS transaction

        LEFT JOIN public."Branch" ON transaction.rec_client_branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND transaction.rec_client_branch = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND transaction.rec_client_branch = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction.rec_by = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }


        if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(transaction.rec_date) >= :filter_transaction_start_date AND DATE(transaction.rec_date) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date;
        }



        $sqlQuery .= ' ORDER BY transaction.rec_id DESC ';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getDayTransactionReport()
    {
        $binding_array = [];

        $selected_branch = null;
        if (@$this->filter_branch_id) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->filter_branch_id);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id, public."Client".id AS client_id, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no, public."Account".name AS journal_account,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \', public."User".shared_name)) FROM public."User" WHERE public."User".id= transaction.mid ORDER BY public."User".id DESC LIMIT 1) as client_names,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ORDER BY public."User".id DESC LIMIT 1 ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."Account" ON transaction.acid=public."Account".id 
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND transaction._branch = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND transaction._branch = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        if (@$this->filter_actype) {
            $sqlQuery .= ' AND "Client".actype = :filter_actype ';
            $binding_array[':filter_actype'] = $this->filter_actype;
        }

        if (@$this->filter_transaction_type) {
            if ($this->filter_transaction_type == 'I') {
                $sqlQuery .= ' AND transaction.t_type IN (\'I\',\'R\',\'SMS\',\'C\') ';
            } else {
                $sqlQuery .= ' AND transaction.t_type = :filter_transaction_type ';
                $binding_array[':filter_transaction_type'] = $this->filter_transaction_type;
            }
        } else {
            if ($this->is_savings_report) {
                $sqlQuery .= ' AND transaction.t_type IN (\'D\',\'W\',\'ASS\',\'LIA\',\'E\',\'I\',\'L\',\'WLI\',\'WLP\',\'CAP\',\'R\',\'SMS\') ';
            }
        }

        if (@$this->filter_acid) {
            $sqlQuery .= ' AND transaction.acid = :filter_acid ';
            $binding_array[':filter_acid'] = $this->filter_acid;
        }

        // if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
        $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
        $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date ?? date('Y-m-d');
        $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date ?? date('Y-m-d');
        // }



        $sqlQuery .= ' ORDER BY transaction.tid ASC ;';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCashTransfersTransactionReport()
    {
        $binding_array = [];

        $selected_branch = null;
        if (@$this->filter_branch_id) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->filter_branch_id);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id,  transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name,

        (SELECT name FROM public."Account" WHERE  public."Account".id=transaction.dr_acid::uuid) AS dr_acc,

        (SELECT name FROM public."Account" WHERE  public."Account".id=transaction.cr_acid::uuid) AS cr_acc,
       

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ORDER BY public."User".id DESC LIMIT 1 ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction 

        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';

        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND transaction._branch = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND transaction._branch = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        if (@$this->filter_transaction_type) {

            $sqlQuery .= ' AND transaction.t_type = :filter_transaction_type ';
            $binding_array[':filter_transaction_type'] = $this->filter_transaction_type;
        } else {

            $sqlQuery .= ' AND transaction.t_type IN (\'STS\',\'STT\',\'TTS\',\'TTT\',\'TTB\',\'BTB\',\'BTS\',\'STB\',\'BRTBR\') ';
        }

        if (@$this->filter_transaction_start_date && @$this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = @$this->filter_transaction_start_date ?? date('Y-m-d');
            $binding_array[':filter_transaction_end_date'] = @$this->filter_transaction_end_date ?? date('Y-m-d');
        }



        $sqlQuery .= ' ORDER BY transaction.tid DESC ;';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDayTransactionReportMM()
    {
        $binding_array = [];

        $selected_branch = null;
        if (@$this->filter_branch_id) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->filter_branch_id);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $selected_branch = $result;
            $this->bankId = $result['bankId'];
        }

        $sqlQuery = 'SELECT *, transaction.tid AS transaction_id, public."Client".id AS client_id, transaction.description AS transaction_description, transaction.date_created AS transaction_date, public."Branch".name AS branch_name, public."Client".membership_no AS membership_no, public."Account".name AS journal_account,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",\' \', public."User".shared_name)) FROM public."User" WHERE public."User".id= transaction.mid ORDER BY public."User".id DESC LIMIT 1) as client_names,

        (SELECT TRIM(public."mm_logs".log_phone) FROM public."mm_logs" WHERE public."mm_logs".log_ext_ref_no= transaction.cheque_no ORDER BY public."mm_logs".log_id DESC LIMIT 1) as mm_phone,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=transaction._authorizedby ORDER BY public."User".id DESC LIMIT 1 ) as authorized_by_names ';

        $sqlQuery .= ' FROM public."transactions" AS transaction
        LEFT JOIN public."Client" ON transaction.mid = public."Client"."userId"
        LEFT JOIN public."Account" ON transaction.acid=public."Account".id 
        LEFT JOIN public."Branch" ON transaction._branch=public."Branch".id ';

        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id AND pay_method IN(\'mobile_money\',\'flutterwave\') ';
        $binding_array[':bank_id'] = $this->bankId;

        if (@$this->branchId) {
            $sqlQuery .= ' AND transaction._branch = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND transaction._branch = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND transaction._authorizedby = :filter_authorized_by_id ';
            $binding_array[':filter_authorized_by_id'] = $this->filter_authorized_by_id;
        }

        if (@$this->filter_actype) {
            $sqlQuery .= ' AND "Client".actype = :filter_actype ';
            $binding_array[':filter_actype'] = $this->filter_actype;
        }

        if (@$this->filter_transaction_type) {
            if ($this->filter_transaction_type == 'I') {
                $sqlQuery .= ' AND transaction.t_type IN (\'I\',\'R\',\'SMS\') ';
            } else {
                $sqlQuery .= ' AND transaction.t_type = :filter_transaction_type ';
                $binding_array[':filter_transaction_type'] = $this->filter_transaction_type;
            }
        } else {
            if ($this->is_savings_report) {
                $sqlQuery .= ' AND transaction.t_type IN (\'D\',\'W\',\'ASS\',\'LIA\',\'E\',\'I\',\'L\',\'WLI\',\'WLP\',\'CAP\',\'R\',\'SMS\') ';
            }
        }

        if (@$this->filter_acid) {
            $sqlQuery .= ' AND transaction.acid = :filter_acid ';
            $binding_array[':filter_acid'] = $this->filter_acid;
        }

        // if ($this->filter_transaction_start_date && $this->filter_transaction_end_date) {
        $sqlQuery .= ' AND DATE(transaction.date_created) >= :filter_transaction_start_date AND DATE(transaction.date_created) <= :filter_transaction_end_date ';
        $binding_array[':filter_transaction_start_date'] = $this->filter_transaction_start_date ?? date('Y-m-d');
        $binding_array[':filter_transaction_end_date'] = $this->filter_transaction_end_date ?? date('Y-m-d');
        // }



        $sqlQuery .= ' ORDER BY transaction.tid ASC ;';


        $transactions = $this->conn->prepare($sqlQuery);
        $transactions->execute($binding_array);
        return $transactions->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLoansLedgerReport()
    {
        $binding_array = [];

        $sqlQuery = 'SELECT *,
      
       (SELECT type_name FROM public."loantypes" WHERE public."loantypes".type_id=public."loan".loanproductid) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_no,

      (SELECT acc_balance FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_bal,

      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_name,

      
(SELECT SUM(amount) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :fs AND DATE(public."transactions".date_created) <= :fe) AS amount_paid_month,

(SELECT SUM(loan_interest) FROM public."transactions" WHERE public."transactions".loan_id=public."loan".loan_no AND public."transactions".t_type=\'L\' AND DATE(public."transactions".date_created) >= :fs AND DATE(public."transactions".date_created) <= :fe) AS int_paid_month

       FROM public."loan" 
 WHERE  public."loan".status IN(2,3,4,5) ';

        $binding_array[':fs'] = '1900-01-01';
        $binding_array[':fe'] = @$this->filter_transaction_end_date ?? date('Y-m-d');

        if (@$this->filter_branch_id) {

            $sqlQuery .= ' AND public."loan".branchid=:bid   ';

            $binding_array[':bid'] = @$this->filter_branch_id;
        } else {
            $sqlQuery .= ' AND public."loan".branchid IN(SELECT id FROM "Branch" WHERE "bankId"=:bid)   ';

            $binding_array[':bid'] = @$this->bankId;
        }



        // loan product acid
        if (@$this->filter_loan_product_id) {
            $sqlQuery .= ' AND public."loan".loanproductid = ( SELECT lpid FROM public."Account" WHERE public."Account".id= :filter_transaction_typen) ';
            $binding_array[':filter_transaction_typen'] = @$this->filter_loan_product_id;
        }

        // disbursement date
        if (@$this->filter_transaction_start_date && @$this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(public."loan".date_disbursed) >= :filter_transaction_start_date AND DATE(public."loan".date_disbursed) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = @$this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = @$this->filter_transaction_end_date;
        }

        // credit officer
        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND public."loan".loan_officer = :lofficer ';
            $binding_array[':lofficer'] = @$this->filter_authorized_by_id;
        }

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getWrittenOffLoansLedgerReport()
    {
        $binding_array = [];

        $sqlQuery = 'SELECT *,
      
       (SELECT type_name FROM public."loantypes" WHERE public."loantypes".type_id=public."loan".loanproductid) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_no,


      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_name

      


       FROM public."loan" 
 WHERE  public."loan".is_written_off=1 ';



        if (@$this->filter_branch_id) {

            $sqlQuery .= ' AND public."loan".branchid=:bid   ';

            $binding_array[':bid'] = @$this->filter_branch_id;
        } else {
            $sqlQuery .= ' AND public."loan".branchid IN(SELECT id FROM "Branch" WHERE "bankId"=:bid)   ';

            $binding_array[':bid'] = @$this->bankId;
        }



        // loan product acid
        if (@$this->filter_loan_product_id) {
            $sqlQuery .= ' AND public."loan".loanproductid = ( SELECT lpid FROM public."Account" WHERE public."Account".id= :filter_transaction_typen) ';
            $binding_array[':filter_transaction_typen'] = @$this->filter_loan_product_id;
        }

        // disbursement date
        if (@$this->filter_transaction_start_date && @$this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(public."loan".date_written_off) >= :filter_transaction_start_date AND DATE(public."loan".date_written_off) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = @$this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = @$this->filter_transaction_end_date;
        }

        // credit officer
        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND public."loan".loan_officer = :lofficer ';
            $binding_array[':lofficer'] = @$this->filter_authorized_by_id;
        }

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getWrittenOffLoansLedgerReport2()
    {
        $binding_array = [];

        $sqlQuery = 'SELECT *,
      
       (SELECT type_name FROM public."loantypes" WHERE public."loantypes".type_id=public."loan".loanproductid) AS product_name,

      (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."loan".account_id) AS ac_no,


      (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."loan".account_id) AS client_name,

        (CURRENT_DATE - public."loan".arrearsbegindate::date) AS arrears_days

      
       FROM public."loan" 
 WHERE  public."loan".status IN(2,3,4) AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >=180) ';


        if (@$this->filter_branch_id) {

            $sqlQuery .= ' AND public."loan".branchid=:bid   ';

            $binding_array[':bid'] = @$this->filter_branch_id;
        } else {
            $sqlQuery .= ' AND public."loan".branchid IN(SELECT id FROM "Branch" WHERE "bankId"=:bid)   ';

            $binding_array[':bid'] = @$this->bankId;
        }



        // loan product acid
        if (@$this->filter_loan_product_id) {
            $sqlQuery .= ' AND public."loan".loanproductid = ( SELECT lpid FROM public."Account" WHERE public."Account".id= :filter_transaction_typen) ';
            $binding_array[':filter_transaction_typen'] = @$this->filter_loan_product_id;
        }

        // disbursement date
        if (@$this->filter_transaction_start_date && @$this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(public."loan".date_disbursed) >= :filter_transaction_start_date AND DATE(public."loan".date_disbursed) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = @$this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = @$this->filter_transaction_end_date;
        }

        // credit officer
        if (@$this->filter_authorized_by_id) {
            $sqlQuery .= ' AND public."loan".loan_officer = :lofficer ';
            $binding_array[':lofficer'] = @$this->filter_authorized_by_id;
        }

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getLoansLedgerReportInterest()
    {

        $binding_array = [];

        // computed repaid interest amount
        $sqlQuery = 'SELECT *, transactions.date_created AS dc from transactions LEFT JOIN loan ON transactions.loan_id=loan.loan_no where loan.loan_type=:id AND transactions.t_type=\'L\' ';

        $binding_array[':id'] = @$this->filter_lpid;
        // $binding_array[':bid'] = $row['branchId'];
        if (@$this->filter_branch_id) {

            $sqlQuery .= ' AND transactions._branch=:bid ';
            $binding_array[':bid'] = @$this->filter_branch_id;
        } else {

            $sqlQuery .= '  AND (transactions._branch IN(SELECT public."Branch".id FROM public."Branch" WHERE "bankId"=:bk))  ';
            $binding_array[':bk'] = @$this->bankId;
        }

        if (@$this->filter_transaction_start_date && @$this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(transactions.date_created) >= :transaction_start_date AND DATE(transactions.date_created) <= :transaction_end_date ';
            $binding_array[':transaction_start_date'] = @$this->filter_transaction_start_date;
            $binding_array[':transaction_end_date'] = @$this->filter_transaction_end_date;
        }
        $sqlQuery .= ' ORDER BY transactions.tid ASC ';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute($binding_array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getfdsLedgerReport()
    {
        $binding_array = [];

        $sqlQuery = 'SELECT *,  (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."fixed_deposits".user_id) AS ac_no

       FROM public."fixed_deposits" LEFT JOIN public."User" ON public."User".id=public."fixed_deposits".user_id 
 WHERE public."fixed_deposits".fd_status=0  ';
        if (@$this->filter_branch_id) {
            $sqlQuery .= ' AND public."fixed_deposits".fd_branch=:bid ';
            $binding_array[':bid'] = @$this->filter_branch_id;
        } else {
            $sqlQuery .= ' AND public."fixed_deposits".fd_branch IN(select id from "Branch" where "bankId"=:bid) ';
            $binding_array[':bid'] = @$this->bankId;
        }

        if (@$this->filter_transaction_start_date && @$this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(fd_date) >= :filter_transaction_start_date AND DATE(fd_date) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = @$this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = @$this->filter_transaction_end_date;
        }

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getshareLedgerReport()
    {
        $binding_array = [];

        $sqlQuery = 'SELECT *, public."share_purchases".id AS sid,  (SELECT membership_no FROM public."Client" WHERE public."Client"."userId"=public."share_purchases".user_id) AS ac_no

       FROM public."share_purchases" LEFT JOIN public."User" ON public."User".id=public."share_purchases".user_id 
 WHERE public."share_purchases".deleted=0 AND public."share_purchases".branch_id=:bid ';
        $binding_array[':bid'] = @$this->filter_branch_id;

        if (@$this->filter_transaction_start_date && @$this->filter_transaction_end_date) {
            $sqlQuery .= ' AND DATE(record_date) >= :filter_transaction_start_date AND DATE(record_date) <= :filter_transaction_end_date ';
            $binding_array[':filter_transaction_start_date'] = @$this->filter_transaction_start_date;
            $binding_array[':filter_transaction_end_date'] = @$this->filter_transaction_end_date;
        }

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSavingsLedgerReport()
    {
        $binding_array = [];

        $sqlQuery = 'SELECT *,
      
       (SELECT name FROM public."savingaccounts" WHERE public."savingaccounts".id=public."Client".actype) AS product_name

       FROM public."Client" LEFT JOIN public."User" ON public."User".id=public."Client"."userId" 
 WHERE public."Client"."branchId"=:bid ';
        $binding_array[':bid'] = @$this->filter_branch_id;

        if (@$this->filter_loan_product_id) {
            $sqlQuery .= ' AND public."Client".actype = :filter_transaction_typen ';
            $binding_array[':filter_transaction_typen'] = @$this->filter_loan_product_id;
        }

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->execute($binding_array);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
