<?php
require_once('Filter.php');
/**
 * This class is responsible for registering user activities such as creating, updating and 
 * deleting records
 */
class AuditTrail extends Filter
{
    public $conn;
    public $type;
    public $log_message;
    public $staff_id;
    public $branch_id;
    public $bank_id;
    public $ip_address;
    public $status;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function create()
    {
        $binding_array = [];
        $query = 'INSERT INTO public."audit_trail_logs" 
        (type,log_message,staff_id,branch_id,bankid,ip_address,status) 
        VALUES(:type,:log_message,:staff_id,:branch_id,:bank_id,:ip_address,:status)';

        $status = $this->status ?? 'success';
        $type = $this->type ?? '';
        $log_message = $this->log_message ?? '';
        $staff_id = @$this->staff_id ?? 0;
        $branch_id = @$this->branch_id ?? null;
        if ($branch_id == '') $branch_id = null;
        $bank_id = @$this->bank_id ?? null;
        $ip_address = @$this->ip_address ?? null;

        $binding_array[':type'] = $type;
        $binding_array[':log_message'] = $log_message;
        $binding_array[':staff_id'] = $staff_id;
        $binding_array[':branch_id'] = $branch_id;
        $binding_array[':bank_id'] = $bank_id;
        $binding_array[':ip_address'] = $ip_address;
        $binding_array[':status'] = $status;
        $stmt = $this->conn->prepare($query);
        $stmt->execute($binding_array);
        return true;
    }


    public function get()
    {
        $binding_array = [];
        $query = ' SELECT *, "Branch".name AS branch_name, public."audit_trail_logs".id AS id, TRIM(CONCAT(public."User"."firstName", \' \', public."User"."lastName"))  AS staff_names FROM public."audit_trail_logs" 
        LEFT JOIN public."User" ON public."User".id = public."audit_trail_logs".staff_id
        LEFT JOIN public."Branch" ON public."audit_trail_logs".branch_id = public."Branch".id
        LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId"
        WHERE public."audit_trail_logs".date_added  IS NOT NULL ';

        if (!$this->filter_branch_id && $this->bankId) {
            $query .= 'AND ( public."Branch"."bankId" = :bank_id ';
            // $binding_array[':bank_id'] = $this->bankId;

            $query .= ' OR public."audit_trail_logs".bankid = :bank_id) ';
            $binding_array[':bank_id'] = $this->bankId;
        }

        if ($this->filter_branch_id) {
            $query .= ' AND public."Branch".id = :branch_id ';
            $binding_array[':branch_id'] = $this->filter_branch_id;
        }

        if ($this->filter_bank_id) {
            $query .= ' AND public."audit_trail_logs".bankid = :bank_id ';
            $binding_array[':bank_id'] = $this->filter_bank_id;
        }

        if ($this->filter_staff_id) {
            $query .= 'AND public."audit_trail_logs".staff_id = :staff_id ';
            $binding_array[':staff_id'] = $this->filter_staff_id;
        }

        if (@$this->filter_start_date && @$this->filter_end_date) {
            $query .= ' AND (DATE(public."audit_trail_logs".date_added) >= :filter_start_date AND DATE(public."audit_trail_logs".date_added) <= :filter_end_date) ';
            $binding_array[':filter_start_date'] = $this->filter_start_date;
            $binding_array[':filter_end_date'] = $this->filter_end_date;
        }

        $query .= ' ORDER BY public."audit_trail_logs".id DESC LIMIT 1000 ';

        // if ($this->is_datatables) {
        //     $query .= ' LIMIT :limit OFFSET :offset ';
        //     $binding_array[':limit'] = $this->filter_per_page;
        //     $binding_array[':offset'] = $this->filter_page;
        // }

        $trail = $this->conn->prepare($query);
        $trail->execute($binding_array);
        return $trail->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBranchAuditTrail()
    {
        $binding_array = [];
        $query = ' SELECT * FROM public."audit_trail_logs" WHERE public."audit_trail_logs".date_added  IS NOT NULL ';

        $query .= 'AND public."audit_trail_logs".branch_id = :branch_id ';
        $binding_array[':branch_id'] = $this->filter_branch_id;

        if ($this->filter_start_date && $this->filter_end_date) {
            $query .= ' AND DATE("audit_trail_logs".date_added) >= :transaction_start_date AND DATE("audit_trail_logs".date_added) <= :transaction_end_date ';
            $binding_array[':transaction_start_date'] = $this->filter_start_date;
            $binding_array[':transaction_end_date'] = $this->filter_end_date;
        }

        $query .= ' ORDER BY public."audit_trail_logs".id DESC';

        $trail = $this->conn->prepare($query);
        $trail->execute($binding_array);
        return $trail->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBankAuditTrail()
    {

        $binding_array = [];
        $query = ' SELECT * FROM public."audit_trail_logs" LEFT JOIN public."Branch" ON public."audit_trail_logs".branch_id = public."Branch".id
        LEFT JOIN public."Bank" ON public."Bank".id = public."Branch"."bankId" WHERE public."audit_trail_logs".date_added  IS NOT NULL ';

        $query .= 'AND (public."Branch"."bankId" = :bank_id ';
        // $binding_array[':bank_id'] = $this->bankId;

        $query .= ' OR public."audit_trail_logs".bankid = :bank_id) ';
        $binding_array[':bank_id'] = $this->bankId;

        // $query .= ' AND public."Branch"."bankId" = :bank_id ';
        // $binding_array[':bank_id'] = $this->bankId;
        if ($this->filter_start_date && $this->filter_end_date) {
            $query .= ' AND (DATE("audit_trail_logs".date_added) >= :transaction_start_date AND DATE("audit_trail_logs".date_added) <= :transaction_end_date) ';
            $binding_array[':transaction_start_date'] = $this->filter_start_date;
            $binding_array[':transaction_end_date'] = $this->filter_end_date;
        }

        $query .= ' ORDER BY public."audit_trail_logs".id DESC LIMIT 1000';

        $trail = $this->conn->prepare($query);
        $trail->execute($binding_array);
        return $trail->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSystemAuditTrail()
    {

        $binding_array = [];
        $query = ' SELECT * FROM public."audit_trail_logs" LEFT JOIN public."Branch" ON public."audit_trail_logs".bankid=public."Branch"."bankId" WHERE public."audit_trail_logs".date_added  IS NOT NULL  ';

        $query .= '';
        $binding_array[':bank_id'] = $this->bankId;

        if ($this->filter_start_date && $this->filter_end_date) {
            $query .= ' AND DATE("audit_trail_logs".date_added) >= :transaction_start_date AND DATE("audit_trail_logs".date_added) <= :transaction_end_date ';
            $binding_array[':transaction_start_date'] = $this->filter_start_date;
            $binding_array[':transaction_end_date'] = $this->filter_end_date;
        }

        $query .= ' ORDER BY public."audit_trail_logs".id DESC';

        $trail = $this->conn->prepare($query);
        $trail->execute($binding_array);
        return $trail->fetchAll(PDO::FETCH_ASSOC);
    }
}
