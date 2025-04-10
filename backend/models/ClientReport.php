<?php
require_once('Filter.php');
class ClientReport extends Filter
{
    public $conn;
    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAgentPerformanceReport()
    {
        $binding_array = [];
        $query = '
        SELECT *, public."User".id AS user_id, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names, public."User"."createdAt" AS member_created_at, public."Branch".name AS bname,
        (SELECT COUNT(*)  FROM public."User"  WHERE  entered_by=st."userId")  AS new_members,
        (SELECT SUM(amount)  FROM public."transactions"  WHERE public."transactions"._authorizedby=st."userId") AS deposits ,
        (SELECT COUNT(DISTINCT(mid))  FROM public."transactions"  WHERE public."transactions"._authorizedby=st."userId" ) AS customers,
        (SELECT COUNT(DISTINCT(loan_no))  FROM public."loan"  WHERE public."loan".loan_officer=st."userId" ) AS loan_aplns
        
        FROM public."Staff" AS st
        LEFT JOIN public."User" ON st."userId"=public."User".id
        LEFT JOIN public."Branch" ON st."branchId" = public."Branch".id
         
        WHERE st."roleId" IN (\'4bbcbdc7-1902-4c1f-abb1-d2de53d1df99\',\'fe209aae-0c0d-46f2-ba0b-be5f50da8519\') ';

        if (@$this->branch) {
            $query .= ' AND public."transactions"._branch=:bank_id ';
            $binding_array[':bank_id'] = $this->branch;
        }

        if (@$this->bankId && !@$this->branch) {
            $query .= ' AND public."Branch"."bankId"=:bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
        }
        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getMemberhipScheduleReport()
    {
        $binding_array = [];
        $query = 'SELECT *,
         public."User".id AS user_id, 
        TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names,
         public."Client"."createdAt" AS member_created_at,
         (SELECT SUM(share_amount) FROM public."share_register" WHERE public."share_register".userid=public."User".id) AS share_amount,
         (SELECT SUM(no_shares) FROM public."share_register" WHERE public."share_register".userid=public."User".id) AS shares,
         (SELECT public.savingaccounts.name FROM public.savingaccounts WHERE public."Client".actype=public.savingaccounts.id) AS c_type,

        TRIM(CONCAT(public."User"."primaryCellPhone", \', \', public."User"."secondaryCellPhone", \', \',public."User"."otherCellPhone")) AS client_contacts,
        public."Branch".name AS branch_name
        
        
          FROM public."User"
        LEFT JOIN public."Client" ON public."User".id=public."Client"."userId" 
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id 
        WHERE public."Client"."userId" > :membership_no ';
        $binding_array[':membership_no'] = 0;

        if ($this->branch) {
            $query .= ' AND public."Client"."branchId"=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= ' AND public."Client"."branchId"=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                $query .= ' AND public."Branch"."bankId"=:bank_id ';
                $binding_array[':bank_id'] = $this->bankId;
            }
        }

        /**
         * filter by date
         */
        if (@$this->filter_start_date && @$this->filter_end_date) {
            $query .= ' AND DATE(public."Client"."createdAt") >= :from_date AND DATE(public."Client"."createdAt") <= :end_date';
            $binding_array[':from_date'] = @$this->filter_start_date;
            $binding_array[':end_date'] = @$this->filter_end_date;
        }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $query .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }

        /**
         * filter by membership renewal status
         */
        if (@$this->filter_reg_renew && @$this->filter_reg_renew != 'all') {
            $query .= ' AND public."Client".membership_renewal_status = :regg ';
            $binding_array[':regg'] = $this->filter_reg_renew;
        }

        /**
         * filter by account type
         */
        if (@$this->filter_actype) {
            $query .= ' AND public."Client".actype =:actype ';
            $binding_array[':actype'] = $this->filter_actype;
        }

        $query .= ' ORDER BY public."User".id DESC LIMIT 6000';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDormantAccountsReport()
    {
        $binding_array = [];
        $query = 'SELECT *, public."User".id AS user_id, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names, public."User"."createdAt" AS member_created_at, (SELECT SUM(share_amount) FROM public."share_register" WHERE public."share_register".userid=public."User".id) AS share_amount, (SELECT SUM(no_shares) FROM public."share_register" WHERE public."share_register".userid=public."User".id) AS shares  FROM public."User"
        LEFT JOIN public."Client" ON public."User".id=public."Client"."userId"
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id
        WHERE public."Client".membership_no > :membership_no AND (SELECT COUNT(*) AS tot FROM public."transactions" where public."transactions".mid=public."Client"."userId" AND DATE(public."transactions".date_created) >= NOW() - INTERVAL \'90 days\')>1 ';
        $binding_array[':membership_no'] = 0;

        if ($this->branch) {
            $query .= 'AND public."Client"."branchId"=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."Client"."branchId"=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                $query .= 'AND public."Branch"."bankId"=:bank_id ';
                $binding_array[':bank_id'] = $this->bankId;
            }
        }

        /**
         * filter by date
         */
        if ($this->filter_start_date && $this->filter_end_date) {
            $query .= ' AND DATE(public."User"."createdAt") >= :from_date AND DATE(public."User"."createdAt") <= :end_date';
            $binding_array[':from_date'] = $this->filter_start_date;
            $binding_array[':end_date'] = $this->filter_end_date;
        }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $query .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }

        /**
         * filter by account type
         */
        if (@$this->filter_actype) {
            $query .= ' AND public."Client".actype =:actype ';
            $binding_array[':actype'] = $this->filter_actype;
        }

        $query .= 'ORDER BY public."User"."createdAt" DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSharePurchasesReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, public."Client".id AS client_id, 

        (SELECT SUM(no_shares) FROM public."share_register" WHERE public."share_register".userid = public."share_purchases".user_id ) AS shares,

         (SELECT SUM(share_amount) FROM public."share_register" WHERE public."share_register".userid = public."share_purchases".user_id) AS share_amount,
        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=public."share_purchases".added_by) AS auth_by,

        TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names, public."Client"."createdAt" AS client_created_at FROM public."share_purchases"
        LEFT JOIN public."Client" ON public."share_purchases".user_id=public."Client"."userId"
        LEFT JOIN public."User" ON public."User".id=public."share_purchases".user_id
        LEFT JOIN public."Branch" ON public."share_purchases".branch_id=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."share_purchases".branch_id=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."share_purchases".branch_id=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }

        /**
         * fitler by client type
         */
        if (@$this->filter_subcounty) {
            // $MemberShipUserId = 0;
            // if ($this->filter_client_type == "member") {
            $query .= ' AND public."User".subcounty = :MemberShipUserId ';
            // } else {
            //     $query .= 'AND public."Client"."membership_no" = :MemberShipUserId ';
            // }
            $binding_array[':MemberShipUserId'] = $this->filter_subcounty;
        }

        if (@$this->filter_district) {
            $query .= ' AND public."User".district = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_district;
        }
        if (@$this->filter_village) {
            $query .= ' AND public."User".village = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_village;
        }
        if (@$this->filter_parish) {
            $query .= ' AND public."User".parish = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_parish;
        }

        /**
         * filter by date
         */
        // if ($this->filter_start_date && $this->filter_end_date) {
        $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."share_purchases".record_date) >= :from_date AND DATE(public."share_purchases".record_date) <= :end_date ';
        $binding_array[':from_date'] = $this->filter_start_date;
        $binding_array[':end_date'] = $this->filter_end_date;
        // }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $query .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }

        /**
         * filter by account type
         */
        if (@$this->filter_actype) {
            $query .= ' AND public."Client".actype =:actype ';
            $binding_array[':actype'] = $this->filter_actype;
        }

        $query .= ' ORDER BY public."share_purchases".id DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getDebtorsReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, public."Account".name AS cname,  public."Branch".name AS bname

        FROM public."debtors"

        LEFT JOIN public."Account" ON public."debtors".deb_chart_acc=public."Account".id::text
       
        LEFT JOIN public."Branch" ON public."debtors".branch_id=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."debtors".branch_id=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."debtors".branch_id=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }


        /**
         * filter by date
         */
        // if ($this->filter_start_date && $this->filter_end_date) {
        $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."debtors".datecreated) >= :from_date AND DATE(public."debtors".datecreated) <= :end_date ';
        $binding_array[':from_date'] = $this->filter_start_date;
        $binding_array[':end_date'] = $this->filter_end_date;
        // }


        $query .= ' ORDER BY public."debtors".deb_id DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReceivablesReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, public."Account".name AS cname,  public."Branch".name AS bname

        FROM public."receivables"

         LEFT JOIN public."debtors" ON public."debtors".deb_id=public."receivables".p_creditor

        LEFT JOIN public."Account" ON public."receivables".deb_acid=public."Account".id
       
        LEFT JOIN public."Branch" ON public."receivables".p_branch_id=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."receivables".p_branch_id=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."receivables".p_branch_id=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }


        /**
         * filter by date
         */
        // if ($this->filter_start_date && $this->filter_end_date) {
        $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."receivables".pay_trxn_date) >= :from_date AND DATE(public."receivables".pay_trxn_date) <= :end_date ';
        $binding_array[':from_date'] = $this->filter_start_date;
        $binding_array[':end_date'] = $this->filter_end_date;
        // }


        $query .= ' ORDER BY public."receivables".p_id DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCreditorsReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, public."Account".name AS cname,  public."Branch".name AS bname

        FROM public."creditors"

        LEFT JOIN public."Account" ON public."creditors".cred_chart_acc=public."Account".id::text
       
        LEFT JOIN public."Branch" ON public."creditors".branch_id=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."creditors".branch_id=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."creditors".branch_id=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }


        /**
         * filter by date
         */
        // if ($this->filter_start_date && $this->filter_end_date) {
        $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."creditors".datecreated) >= :from_date AND DATE(public."creditors".datecreated) <= :end_date ';
        $binding_array[':from_date'] = $this->filter_start_date;
        $binding_array[':end_date'] = $this->filter_end_date;
        // }


        $query .= ' ORDER BY public."creditors".deb_id DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getShareTransfersReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, 

        
        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=public."share_transfers".added_by) AS auth_by,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) FROM public."User" WHERE public."User".id=public."share_transfers".from_uid ) AS from_name,
        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) FROM public."User" WHERE public."User".id=public."share_transfers".to_uid ) AS to_name,

         (SELECT public."Client".membership_no FROM public."Client" WHERE public."Client"."userId"=public."share_transfers".to_uid ) AS to_mno,
          (SELECT public."Client".membership_no FROM public."Client" WHERE public."Client"."userId"=public."share_transfers".from_uid ) AS from_mno


         FROM public."share_transfers"
       
        LEFT JOIN public."Branch" ON public."share_transfers".branch_id=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."share_transfers".branch_id=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."share_transfers".branch_id=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }



        /**
         * filter by date
         */
        // if ($this->filter_start_date && $this->filter_end_date) {
        $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."share_transfers".record_date) >= :from_date AND DATE(public."share_transfers".record_date) <= :end_date ';
        $binding_array[':from_date'] = $this->filter_start_date;
        $binding_array[':end_date'] = $this->filter_end_date;
        // }



        $query .= ' ORDER BY public."share_transfers".tr_id DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getClientScheduleReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, public."Client".id AS client_id, 

        (SELECT COUNT(*) FROM public."loan" WHERE public."loan".account_id = public."Client"."userId" AND public."loan".status IN (2,3,4)) AS total_loans,

        TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names, public."Client"."createdAt" AS client_created_at FROM public."Client"
        LEFT JOIN public."User" ON public."User".id=public."Client"."userId"
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."Client"."branchId"=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."Client"."branchId"=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }
        // savings officer --- entered by filter
        if (@$this->filter_loan_officer_id) {
            $query .= ' AND public."User".entered_by = :filter_loan_officer_id ';
            $binding_array[':filter_loan_officer_id'] = $this->filter_loan_officer_id;
        }

        /**
         * fitler by client type
         */
        if (@$this->filter_subcounty) {
            // $MemberShipUserId = 0;
            // if ($this->filter_client_type == "member") {
            $query .= ' AND public."User".region = :MemberShipUserId ';
            // } else {
            //     $query .= 'AND public."Client"."membership_no" = :MemberShipUserId ';
            // }
            $binding_array[':MemberShipUserId'] = $this->filter_subcounty;
        }

        if (@$this->filter_district) {
            $query .= ' AND public."User".district = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_district;
        }
        if (@$this->filter_village) {
            $query .= ' AND public."User".education_level = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_village;
        }
        if (@$this->filter_parish) {
            $query .= ' AND public."User".parish = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_parish;
        }

        /**
         * filter by date
         */
        // if ($this->filter_start_date && $this->filter_end_date) {
        $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."Client"."createdAt") >= :from_date AND DATE(public."Client"."createdAt") <= :end_date ';
        $binding_array[':from_date'] = $this->filter_start_date;
        $binding_array[':end_date'] = $this->filter_end_date;
        // }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $query .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }

        /**
         * filter by account type
         */
        if (@$this->filter_actype) {
            $query .= ' AND public."Client".actype =:actype ';
            $binding_array[':actype'] = $this->filter_actype;
        }

        $query .= 'ORDER BY public."Client"."createdAt" DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClientScheduleReport2()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, public."Client".id AS client_id, 

       

        TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names, public."Client"."createdAt" AS client_created_at 
        FROM public."Client"
        LEFT JOIN public."User" ON public."User".id=public."Client"."userId"
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."Client"."branchId"=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."Client"."branchId"=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }

        /**
         * fitler by client type
         */
        if (@$this->filter_subcounty) {
            $query .= ' AND public."User".region = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_subcounty;
        }

        if (@$this->filter_district) {
            $query .= ' AND public."User".district = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_district;
        }
        if (@$this->filter_start_date) {
            $query .= ' AND public."User".education_level = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_start_date;
        }


        /**
         * filter by date
         */

        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."User"."createdAt") <= :end_date ';
        $binding_array[':end_date'] = $this->filter_end_date;
        // }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $query .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }



        $query .= 'ORDER BY public."User"."createdAt" DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getShareScheduleReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, public."Client".id AS client_id, 

        (SELECT SUM(no_shares) FROM public."share_register" WHERE public."share_register".userid = public."Client"."userId" ) AS shares,

         (SELECT SUM(share_amount) FROM public."share_register" WHERE public."share_register".userid = public."Client"."userId") AS share_amount,
          (SELECT share_value FROM public."Branch" LEFT JOIN public."Bank" ON public."Branch"."bankId"=public."Bank".id WHERE public."Branch".id = public."Client"."branchId") AS share_value,

        TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names, public."Client"."createdAt" AS client_created_at FROM public."Client"
        LEFT JOIN public."User" ON public."User".id=public."Client"."userId"
        LEFT JOIN public."Branch" ON public."Client"."branchId"=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."Client"."branchId"=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."Client"."branchId"=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }

        /**
         * fitler by client type
         */
        if (@$this->filter_subcounty) {
            // $MemberShipUserId = 0;
            // if ($this->filter_client_type == "member") {
            $query .= ' AND public."User".subcounty = :MemberShipUserId ';
            // } else {
            //     $query .= 'AND public."Client"."membership_no" = :MemberShipUserId ';
            // }
            $binding_array[':MemberShipUserId'] = $this->filter_subcounty;
        }

        if (@$this->filter_district) {
            $query .= ' AND public."User".district = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_district;
        }
        if (@$this->filter_village) {
            $query .= ' AND public."User".village = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_village;
        }
        if (@$this->filter_parish) {
            $query .= ' AND public."User".parish = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_parish;
        }

        /**
         * filter by date
         */
        // if ($this->filter_start_date && $this->filter_end_date) {
        $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
        $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
        $query .= ' AND DATE(public."Client"."createdAt") >= :from_date AND DATE(public."Client"."createdAt") <= :end_date ';
        $binding_array[':from_date'] = $this->filter_start_date;
        $binding_array[':end_date'] = $this->filter_end_date;
        // }

        /**
         * filter by gender
         */
        if (@$this->filter_gender) {
            $query .= ' AND public."User".gender = :gender ';
            $binding_array[':gender'] = $this->filter_gender;
        }

        /**
         * filter by account type
         */
        if (@$this->filter_actype) {
            $query .= ' AND public."Client".actype =:actype ';
            $binding_array[':actype'] = $this->filter_actype;
        }

        $query .= ' ORDER BY public."Client"."createdAt" DESC';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }

    public function FixedDepositScheduleReport()
    {
        $binding_array = [];
        if (@$this->branch) {
            $brunchQuery = ' SELECT * FROM public."Branch" WHERE public."Branch".id=:branch_id_1 ';
            $brunchStmt = $this->conn->prepare($brunchQuery);
            $brunchStmt->bindParam(':branch_id_1', $this->branch);
            $brunchStmt->execute();
            $result = $brunchStmt->fetch(PDO::FETCH_ASSOC);
            $this->bankId = $result['bankId'];
        }

        $query = 'SELECT *, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName",public."User".shared_name)) AS client_names FROM public."fixed_deposits"
        LEFT JOIN public."User" ON public."User".id=public."fixed_deposits".user_id LEFT JOIN public."Client" ON public."Client"."userId"=public."fixed_deposits".user_id
        LEFT JOIN public."Branch" ON public."fixed_deposits".fd_branch=public."Branch".id ';


        $query .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
        $query .= ' WHERE public."Bank".id=:bank_id ';
        $binding_array[':bank_id'] = @$this->bankId;

        if ($this->branch) {
            $query .= 'AND public."fixed_deposits".fd_branch=:branch_id ';
            $binding_array[':branch_id'] = $this->branch;
        } else {
            if ($this->filter_branch_id) {
                $query .= 'AND public."fixed_deposits".fd_branch=:filter_branch_id ';
                $binding_array[':filter_branch_id'] = $this->filter_branch_id;
            } else {
                // $query .= 'AND public."Branch"."bankId"=:bank_id ';
                // $binding_array[':bank_id'] = $this->bankId;
            }
        }

        /**
         * fitler by client type
         */
        // if (@$this->is_client_type) {
        if (@$this->is_client_type == 2) {
            $query .= ' AND DATE(public."fixed_deposits".fd_maturity_date) <= current_date AND public."fixed_deposits".fd_status<>1  ';
        } else if (@$this->is_client_type == 0 || @$this->is_client_type == 1) {
            $query .= ' AND public."fixed_deposits".fd_status = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->is_client_type;
        }
        // }

        /**
         * filter by date
         */
        if ($this->filter_start_date && $this->filter_end_date) {
            // $this->filter_start_date = $this->filter_start_date ?? date('Y-m-d');
            // $this->filter_end_date = $this->filter_end_date ?? date('Y-m-d');
            $query .= ' AND DATE(public."fixed_deposits".fd_maturity_date) >= :from_date AND DATE(public."fixed_deposits".fd_maturity_date) <= :end_date ';
            $binding_array[':from_date'] = $this->filter_start_date;
            $binding_array[':end_date'] = $this->filter_end_date;
        }





        $query .= 'ORDER BY public."fixed_deposits".fd_id DESC ';

        $members = $this->conn->prepare($query);
        $members->execute($binding_array);
        return $members->fetchAll(PDO::FETCH_ASSOC);
    }
}
