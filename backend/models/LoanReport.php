<?php
require_once('Filter.php');
class LoanReport extends Filter
{
    public $conn;
    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCreditOfficersReport()
    {
        $binding_array = [];
        $selected_branch = null;


        $sqlQuery = 'SELECT *,public."loan".status AS lstatus, 

        public."Client".membership_no AS membership_no,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=public."loan".account_id ) as client_names,

        (SELECT SUM(principal_paid) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no ) as principal_paid,
        (SELECT SUM(interest_paid) FROM public."loan_schedule" WHERE public."loan_schedule".loan_id=public."loan".loan_no ) as interest_paid,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone", \' \', public."User"."secondaryCellPhone")) FROM public."User" WHERE public."User".id=public."loan".account_id ) as client_initials,
        (SELECT TRIM(CONCAT(public."User"."addressLine1", \' \', public."User"."addressLine2")) FROM public."User" WHERE public."User".id=public."loan".account_id ) as client_address

     

        -- (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone", \' \', public."User"."secondaryCellPhone")) FROM public."User" WHERE public."User".id=public."guarantors"._mid ) as guarantor_initials

        -- (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone", \' \', public."User"."secondaryCellPhone")) FROM public."User" WHERE public."User".id=public."guarantors"._mid AND public."loan".loan_no = public."guarantors"._loanid) as guarantor_initials

        FROM public."loan"
        LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN public."User" ON public."loan".account_id = public."User".id
        LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
        LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id ';
        $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" WHERE public."loan".loan_no>0 ';

        // $binding_array[':filter_pay_start_date'] = $this->filter_payment_from;
        // $binding_array[':filter_pay_end_date'] = $this->filter_payment_to;

        if (@$this->bankId && !@$this->branchId) {

            $sqlQuery .= '  AND public."Branch"."bankId" = :bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
        }


        if (@$this->branchId || @$this->filter_branch_id) {
            $sqlQuery .= ' AND public."loan".branchid = :branch ';
            $binding_array[':branch'] = $this->filter_branch_id;
        }

        if (@$this->filter_loan_status) {

            if ($this->filter_loan_status == 'active') {
                $sqlQuery .= ' AND public."loan".status IN(2,3,4) ';
            } else if ($this->filter_loan_status == 0) {
                $sqlQuery .= ' AND public."loan".status IN(2,3,4,5) ';
            } else if ($this->filter_loan_status == 6) {
                $sqlQuery .= ' AND public."loan".is_written_off=1 ';
            } else {
                $sqlQuery .= ' AND public."loan".status = :loan_status ';
                $binding_array[':loan_status'] = $this->filter_loan_status;
            }
        }

        if (@$this->filter_loan_product_id) {
            $sqlQuery .= ' AND public."loan".loanproductid = :loan_type_id ';
            $binding_array[':loan_type_id'] = $this->filter_loan_product_id;
        }

        if (@$this->filter_loan_officer_id) {
            $sqlQuery .= ' AND public."loan".loan_officer = :filter_loan_officer_id ';
            $binding_array[':filter_loan_officer_id'] = $this->filter_loan_officer_id;
        }

        if (@$this->filter_subcounty) {

            $sqlQuery .= ' AND public."User".subcounty = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_subcounty;
        }

        if (@$this->filter_district) {
            $sqlQuery .= ' AND public."User".district = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_district;
        }
        if (@$this->filter_village) {
            $sqlQuery .= ' AND public."User".village = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_village;
        }
        if (@$this->filter_parish) {
            $sqlQuery .= ' AND public."User".parish = :MemberShipUserId ';
            $binding_array[':MemberShipUserId'] = $this->filter_parish;
        }


        /**
         * Active loans filters
         */
        // if ($this->filter_disbursement_start_date && $this->filter_disbursement_end_date) {
        $this->filter_disbursement_start_date = $this->filter_disbursement_start_date ?? date('Y-m-d', strtotime('-7 days'));
        $this->filter_disbursement_end_date = $this->filter_disbursement_end_date ?? date('Y-m-d');
        $sqlQuery .= ' AND DATE(public."loan".date_disbursed) >= :disbursement_start_date AND DATE(public."loan".date_disbursed) <= :disbursement_end_date ';
        $binding_array[':disbursement_start_date'] = $this->filter_disbursement_start_date;
        $binding_array[':disbursement_end_date'] = $this->filter_disbursement_end_date;
        // }

        /**
         * Approved loans filters
         */
        if (@$this->filter_disbursement_date) {
            $sqlQuery .= ' AND DATE(public."loan".date_disbursed) = :filter_disbursement_date ';
            $binding_array[':filter_disbursement_date'] = $this->filter_disbursement_date;
        }

        if (@$this->filter_application_start_date && @$this->filter_application_end_date) {
            $sqlQuery .= ' AND DATE(public."loan".application_date) >= :filter_application_start_date AND DATE(public."loan".application_date) <= :filter_application_end_date ';
            $binding_array[':filter_application_start_date'] = $this->filter_application_start_date;
            $binding_array[':filter_application_end_date'] = $this->filter_application_end_date;
        }

        if (@$this->filter_is_loan_arrears || $this->filter_is_ageing_report) {
            $sqlQuery .= ' AND public."loan".principal_arrears > 0 ';
        }


        $sqlQuery .= ' ORDER BY public."loan".loan_no DESC ';


        $loans = $this->conn->prepare($sqlQuery);
        $loans->execute($binding_array);
        return $loans->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArrearsReport()
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

        $sqlQuery = 'SELECT *,public."loan".status AS lstatus, 

        public."Client".membership_no AS membership_no,
        public."Client".acc_balance,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName")) FROM public."User" WHERE public."User".id=public."loan".account_id ) as client_names,

        (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone", \' \', public."User"."secondaryCellPhone")) FROM public."User" WHERE public."User".id=public."loan".account_id ) as client_initials

        -- (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone", \' \', public."User"."secondaryCellPhone")) FROM public."User" WHERE public."User".id=public."guarantors"._mid ) as guarantor_initials

        -- (SELECT TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName", \' \', public."User"."primaryCellPhone", \' \', public."User"."secondaryCellPhone")) FROM public."User" WHERE public."User".id=public."guarantors"._mid AND public."loan".loan_no = public."guarantors"._loanid) as guarantor_initials

        FROM public."loan"
        LEFT JOIN public."loantypes" ON public."loan".loanproductid=public."loantypes".type_id LEFT JOIN public."User" ON public."loan".account_id = public."User".id
        LEFT JOIN public."Client" ON public."loan".account_id = public."Client"."userId"
        LEFT JOIN public."Branch" ON public."loan".branchid=public."Branch".id ';


        if ($this->bankId) {
            $sqlQuery .= ' LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" ';
            $sqlQuery .= ' WHERE public."Branch"."bankId" = :bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
        }

        $sqlQuery .= ' AND public."loan".status IN(2,3,4)';

        if (@$this->branchId) {
            $sqlQuery .= ' AND public."loan".branchid = :branch ';
            $binding_array[':branch'] = $selected_branch['id'];
        } else {
            if (@$this->filter_branch_id) {
                $sqlQuery .= ' AND public."loan"."branchid" = :branch_id ';
                $binding_array[':branch_id'] = $this->filter_branch_id;
            }
        }

        if (@$this->filter_loan_status) {
            $sqlQuery .= ' AND public."loan".status = :loan_status ';
            $binding_array[':loan_status'] = $this->filter_loan_status;
        }

        if (@$this->filter_loan_product_id) {
            $sqlQuery .= ' AND public."loan".loan_type = :loan_type_id ';
            $binding_array[':loan_type_id'] = $this->filter_loan_product_id;
        }

        if (@$this->filter_loan_officer_id) {
            $sqlQuery .= ' AND public."loan".loan_officer = :filter_loan_officer_id ';
            $binding_array[':filter_loan_officer_id'] = $this->filter_loan_officer_id;
        }




        /**
         * Active loans filters
         */
        if (@$this->filter_disbursement_start_date && @$this->filter_disbursement_end_date) {
            // $this->filter_disbursement_start_date = $this->filter_disbursement_start_date ?? date('Y-m-d', strtotime('-180 days'));
            // $this->filter_disbursement_end_date = $this->filter_disbursement_end_date ?? date('Y-m-d');
            $sqlQuery .= ' AND DATE(public."loan".date_disbursed) >= :disbursement_start_date AND DATE(public."loan".date_disbursed) <= :disbursement_end_date ';
            $binding_array[':disbursement_start_date'] = $this->filter_disbursement_start_date;
            $binding_array[':disbursement_end_date'] = $this->filter_disbursement_end_date;
        }

        /**
         * Approved loans filters
         */
        if ($this->filter_disbursement_date) {
            $sqlQuery .= ' AND DATE(public."loan".date_disbursed) = :filter_disbursement_date ';
            $binding_array[':filter_disbursement_date'] = $this->filter_disbursement_date;
        }

        if ($this->filter_application_start_date && $this->filter_application_end_date) {
            $sqlQuery .= ' AND DATE(public."loan".application_date) >= :filter_application_start_date AND DATE(public."loan".application_date) <= :filter_application_end_date ';
            $binding_array[':filter_application_start_date'] = $this->filter_application_start_date;
            $binding_array[':filter_application_end_date'] = $this->filter_application_end_date;
        }

        // if (@$this->filter_is_loan_arrears || $this->filter_is_ageing_report) {

        // }
        if (@$this->filter_days_arrears && $this->filter_days_arrears > 1) {

            $sqlQuery .= ' AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >= :filter_loan_days) ';
            $binding_array[':filter_loan_days'] = $this->filter_days_arrears;
        } else {
            $sqlQuery .= ' AND public."loan".principal_arrears > 0 ';
        }

        $sqlQuery .= ' ORDER BY public."loan".application_date DESC LIMIT 1000 ';


        $loans = $this->conn->prepare($sqlQuery);
        $loans->execute($binding_array);
        return $loans->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPARReport2()
    {
        if (@$this->filter_par_type == 'branch') {
            // fetch per branch
            if (@$this->filter_as_at_date == date('Y-m-d')) {
                $binding_array = [];

                $sqlQuery = 'SELECT public."Branch".name AS bname, public."Branch".id AS bid,
            
            (SELECT SUM(principal_balance) FROM public."loan" where public."loan".branchid=public."Branch".id AND public."loan".status IN(2,3,4) ) AS tot_portifolio,

             (SELECT COUNT(*) FROM public."loan" where public."loan".branchid=public."Branch".id AND public."loan".status IN(2,3,4) ) AS no_loans,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".branchid=public."Branch".id AND public."loan".status IN(2,3,4) ) AS tot_portifolio_arrears,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".branchid=public."Branch".id AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >30) AND public."loan".status IN(2,3,4) ) AS arrears_30,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".branchid=public."Branch".id AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >90) AND public."loan".status IN(2,3,4) ) AS arrears_90
      

      FROM public."Branch" WHERE
       
  ';

                if (@$this->filter_branch) {
                    $sqlQuery .=  ' public."Branch"."bankId"=(SELECT "bankId" FROM "Branch" WHERE id=:bid) ';
                    $binding_array[':bid'] = @$this->filter_branch;
                }

                if (@$this->filter_bankk) {
                    $sqlQuery .=  ' public."Branch"."bankId"=:bi ';
                    $binding_array[':bi'] = @$this->filter_bankk;
                }


                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->execute($binding_array);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $binding_array = [];

                $sqlQuery = 'SELECT public."Branch".name AS bname, public."Branch".id AS bid,
            
            (SELECT SUM(principal_balance) FROM public."loan" where public."loan".branchid=public."Branch".id AND (public."loan".date_disbursed::date <= DATE \'' . $this->filter_as_at_date . '\') AND public."loan".status IN(2,3,4) ) AS tot_portifolio,

             (SELECT COUNT(*) FROM public."loan" where public."loan".branchid=public."Branch".id AND public."loan".status IN(2,3,4) ) AS no_loans,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".branchid=public."Branch".id AND (public."loan".arrearsbegindate::date <= DATE \'' . $this->filter_as_at_date . '\') AND  public."loan".status IN(2,3,4)) AS tot_portifolio_arrears,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".branchid=public."Branch".id AND ((DATE \'' . $this->filter_as_at_date . '\'  - public."loan".arrearsbegindate::date) >30) AND public."loan".status IN(2,3,4) ) AS arrears_30,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".branchid=public."Branch".id AND ((DATE \'' . $this->filter_as_at_date . '\'  - public."loan".arrearsbegindate::date) >90) AND public."loan".status IN(2,3,4) ) AS arrears_90
      

      FROM public."Branch" ';

                if (@$this->filter_branch) {
                    $sqlQuery .=  ' WHERE public."Branch"."bankId"=(SELECT "bankId" FROM "Branch" WHERE id=:bid) ';
                    $binding_array[':bid'] = @$this->filter_branch;
                }

                if (@$this->filter_bankk) {
                    $sqlQuery .=  ' WHERE public."Branch"."bankId"=:bi ';
                    $binding_array[':bi'] = @$this->filter_bankk;
                }




                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->execute($binding_array);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        if (@$this->filter_par_type == 'officer') {
            // fetch per loan officer
            if (@$this->filter_as_at_date == date('Y-m-d')) {
                $binding_array = [];

                $sqlQuery = 'SELECT (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."Staff"."userId") AS bname, 
            public."Staff"."userId" AS bid,
            
            (SELECT SUM(principal_balance) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND public."loan".status IN(2,3,4)) AS tot_portifolio,

             (SELECT COUNT(*) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND public."loan".status IN(2,3,4) ) AS no_loans,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" ) AS tot_portifolio_arrears,
              (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >30) AND public."loan".status IN(2,3,4)) AS arrears_30,
             
             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >90)  AND public."loan".status IN(2,3,4)) AS arrears_90
      
      

      FROM public."Staff"
       
 WHERE public."Staff".is_credit_officer=1  ';


                if (@$this->filter_branch) {
                    $sqlQuery .=  ' AND public."Staff"."branchId"=:bid  ';
                    $binding_array[':bid'] = @$this->filter_branch;
                }

                if (@$this->filter_bankk) {
                    $sqlQuery .=  ' AND public."Staff"."branchId" IN(SELECT id FROM "Branch" WHERE "bankId"=:bi)  ';
                    $binding_array[':bi'] = @$this->filter_bankk;
                }

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->execute($binding_array);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $binding_array = [];

                $sqlQuery = 'SELECT (SELECT CONCAT(public."User"."firstName",\' \', public."User"."lastName",\' \', public."User".shared_name) FROM public."User" WHERE public."User".id=public."Staff"."userId") AS bname, 
            public."Staff"."userId" AS bid,
            
            (SELECT SUM(principal_balance) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId"  AND (public."loan".date_disbursed::date <= DATE \'' . $this->filter_as_at_date . '\') AND public."loan".status IN(2,3,4)) AS tot_portifolio,

             (SELECT COUNT(*) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND public."loan".status IN(2,3,4) ) AS no_loans,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND (public."loan".arrearsbegindate::date <= DATE \'' . $this->filter_as_at_date . '\') ) AS tot_portifolio_arrears,
              (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND ((DATE \'' . $this->filter_as_at_date . '\' - public."loan".arrearsbegindate::date) >30) AND public."loan".status IN(2,3,4)) AS arrears_30,
             
             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loan_officer=public."Staff"."userId" AND ((DATE \'' . $this->filter_as_at_date . '\' - public."loan".arrearsbegindate::date) >90)  AND public."loan".status IN(2,3,4)) AS arrears_90
      
      

      FROM public."Staff"
       
 WHERE public."Staff".is_credit_officer=1  ';

                if (@$this->filter_branch) {
                    $sqlQuery .=  ' AND public."Staff"."branchId"=:bid  ';
                    $binding_array[':bid'] = @$this->filter_branch;
                }

                if (@$this->filter_bankk) {
                    $sqlQuery .=  ' AND public."Staff"."branchId" IN(SELECT id FROM "Branch" WHERE "bankId"=:bi)  ';
                    $binding_array[':bi'] = @$this->filter_bankk;
                }

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->execute($binding_array);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        if (@$this->filter_par_type == 'product') {
            // fetch per loan product
            if (@$this->filter_as_at_date == date('Y-m-d')) {
                $binding_array = [];

                $sqlQuery = 'SELECT public."loantypes".type_name AS bname, public."loantypes".type_id AS bid,
            
            (SELECT SUM(principal_balance) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND (public."loan".date_disbursed::date <= DATE \'' . $this->filter_as_at_date . '\')  AND public."loan".status IN(2,3,4)) AS tot_portifolio,

             (SELECT COUNT(*) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND public."loan".status IN(2,3,4) ) AS no_loans,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND (public."loan".arrearsbegindate::date <= DATE \'' . $this->filter_as_at_date . '\') ) AS tot_portifolio_arrears,
              (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND ((DATE \'' . $this->filter_as_at_date . '\' - public."loan".arrearsbegindate::date) >30) AND public."loan".status IN(2,3,4)) AS arrears_30,
             
             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND ((DATE \'' . $this->filter_as_at_date . '\' - public."loan".arrearsbegindate::date) >90) AND public."loan".status IN(2,3,4)) AS arrears_90
      
      

      FROM public."loantypes"
       
  ';


                if (@$this->filter_branch) {
                    $sqlQuery .=  ' WHERE public."loantypes"."bankId"=(SELECT "bankId" FROM "Branch" WHERE id=:bid)  ';
                    $binding_array[':bid'] = @$this->filter_branch;
                }

                if (@$this->filter_bankk) {
                    $sqlQuery .=  ' WHERE public."loantypes"."bankId"=:bi  ';
                    $binding_array[':bi'] = @$this->filter_bankk;
                }

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->execute($binding_array);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $binding_array = [];

                $sqlQuery = 'SELECT public."loantypes".type_name AS bname, public."loantypes".type_id AS bid,
            
            (SELECT SUM(principal_balance) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id  AND public."loan".status IN(2,3,4)) AS tot_portifolio,

             (SELECT COUNT(*) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND public."loan".status IN(2,3,4) ) AS no_loans,

             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id ) AS tot_portifolio_arrears,
              (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >30) AND public."loan".status IN(2,3,4)) AS arrears_30,
             
             (SELECT SUM(principal_arrears) FROM public."loan" where public."loan".loanproductid=public."loantypes".type_id AND ((CURRENT_DATE - public."loan".arrearsbegindate::date) >90) AND public."loan".status IN(2,3,4)) AS arrears_90
      
      

      FROM public."loantypes"
        ';
                if (@$this->filter_branch) {
                    $sqlQuery .=  ' WHERE public."loantypes"."bankId"=(SELECT "bankId" FROM "Branch" WHERE id=:bid)  ';
                    $binding_array[':bid'] = @$this->filter_branch;
                }

                if (@$this->filter_bankk) {
                    $sqlQuery .=  ' WHERE public."loantypes"."bankId"=:bi  ';
                    $binding_array[':bi'] = @$this->filter_bankk;
                }

                $stmt = $this->conn->prepare($sqlQuery);

                $stmt->execute($binding_array);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }


    public function getPARReport()
    {

        if ($this->bankId) {
            $binding_array = [];

            $sqlQuery = 'SELECT  *, 
        (SELECT COUNT(*) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4)) AS num_loans  ,
        (SELECT SUM(principal_balance) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4)) AS principal_balance  ,
        (SELECT SUM(interest_balance) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4)) AS interest_balance  ,
        (SELECT SUM(principal_arrears) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4)) AS principal_arrears  ,
        (SELECT SUM(interest_arrears) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4)) AS interest_arrears  ,
         (SELECT SUM(principal) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id  AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4)) AS loan_amount  
         
         FROM  public."loantypes"
        ';

            $sqlQuery .= ' WHERE public."loantypes"."bankId" = :bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
        }

        if (@$this->branchId || @$this->filter_branch_id) {
            $binding_array = [];

            $sqlQuery = 'SELECT  *, 
        (SELECT COUNT(*) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".branchid=:br) AS num_loans  ,
        (SELECT SUM(principal_balance) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".branchid=:br) AS principal_balance  ,
        (SELECT SUM(interest_balance) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".branchid=:br) AS interest_balance  ,
        (SELECT SUM(principal_arrears) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".branchid=:br) AS principal_arrears  ,
        (SELECT SUM(interest_arrears) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".branchid=:br) AS interest_arrears  ,
         (SELECT SUM(principal) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id  AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".branchid=:br) AS loan_amount  
         
         FROM  public."loantypes"
        ';

            $sqlQuery .= ' WHERE public."loantypes"."bankId" = :bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
            if (@$this->filter_branch_id) {
                $binding_array[':br'] = $this->filter_branch_id;
            } else {
                $binding_array[':br'] = $this->branchId;
            }
        }

        if (@$this->filter_loan_officer || @$this->filter_loan_officer_id) {
            $binding_array = [];

            $sqlQuery = 'SELECT  *, 
        (SELECT COUNT(*) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".loan_officer=:br) AS num_loans  ,
        (SELECT SUM(principal_balance) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".loan_officer=:br) AS principal_balance  ,
        (SELECT SUM(interest_balance) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".loan_officer=:br) AS interest_balance  ,
        (SELECT SUM(principal_arrears) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".loan_officer=:br) AS principal_arrears  ,
        (SELECT SUM(interest_arrears) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".loan_officer=:br) AS interest_arrears  ,
         (SELECT SUM(principal) FROM public."loan" WHERE public."loan".loanproductid=public."loantypes".type_id  AND public."loan".principal_arrears > 0 AND public."loan".status IN(2,3,4) AND public."loan".loan_officer=:br) AS loan_amount  
         
         FROM  public."loantypes"
        ';

            $sqlQuery .= ' WHERE public."loantypes"."bankId" = :bank_id ';
            $binding_array[':bank_id'] = $this->bankId;
            if (@$this->filter_loan_officer) {
                $binding_array[':br'] = $this->filter_loan_officer;
            } else {
                $binding_array[':br'] = $this->filter_loan_officer_id;
            }
        }


        // if (@$this->branchId) {
        //     $sqlQuery .= ' AND public."Branch".id = :branch ';
        //     $binding_array[':branch'] = $selected_branch['id'];
        // } else {
        //     if (@$this->filter_branch_id) {
        //         $sqlQuery .= ' AND public."Branch".id = :branch_id ';
        //         $binding_array[':branch_id'] = $this->filter_branch_id;
        //     }
        // }




        // if (@$this->filter_loan_officer_id) {
        //     $sqlQuery .= ' AND public."loan".loan_officer = :filter_loan_officer_id ';
        //     $binding_array[':filter_loan_officer_id'] = $this->filter_loan_officer_id;
        // }


        /**
         * Active loans filters
         */



        $loans = $this->conn->prepare($sqlQuery);
        $loans->execute($binding_array);
        return $loans->fetchAll(PDO::FETCH_ASSOC);
    }
}
