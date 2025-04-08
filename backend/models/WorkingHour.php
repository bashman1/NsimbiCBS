<?php
class WorkingHour
{
    private $conn;
    public $id;
    public $role_id;
    public $branch_id;
    public $start_at;
    public $end_at;
    public $working_hours;
    public $working_hours_roles;

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getBranchWorkingHours($id = null)
    {
        $id = $id ?? $this->branch_id;
        $sqlQuery = ' SELECT * FROM public.working_hours_branch WHERE branch_id =:branch_id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':branch_id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get branch working hours by day eg 1 for Monay, 2 for Tuesday etc
     */
    public function getBranchWorkingHoursByDay($day)
    {
        $sqlQuery = ' SELECT * FROM public.working_hours_branch WHERE day_id=:day_id AND branch_id=:branch_id ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':day_id' => $day, ':branch_id' => $this->branch_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setBranchWorkingHours($id = null)
    {
        $id = $id ?? $this->branch_id;

        if ($this->working_hours['day']) {
            for ($i = 0; $i < count($this->working_hours['day']); $i++) {
                $day_id = $this->working_hours['day'][$i];
                $exists = $this->getBranchWorkingHoursByDay($day_id);
                if ($exists) {
                    // return @$this->working_hours['start_at'][$i];
                    /**
                     * update existing record
                     */
                    $sqlQuery = ' UPDATE public.working_hours_branch SET start_at=:start_at, end_at=:end_at, is_working_day=:is_working_day WHERE branch_id=:branch_id AND day_id=:day_id ';
                    $statement = $this->conn->prepare($sqlQuery);
                } else {
                    /**
                     * insert new record
                     */
                    $sqlQuery = ' INSERT INTO public.working_hours_branch (day_id,branch_id,start_at,end_at,is_working_day) VALUES(:day_id,:branch_id,:start_at,:end_at, :is_working_day) ';
                    $statement = $this->conn->prepare($sqlQuery);
                }

                if (!@$this->working_hours['is_working_day'][$i]) {
                    @$this->working_hours['start_at'][$i] = NULL;
                    @$this->working_hours['end_at'][$i] = NULL;
                }

                $statement->bindValue(':branch_id', $this->branch_id);
                $statement->bindValue(':day_id', $day_id);
                $statement->bindValue(':start_at', @$this->working_hours['start_at'][$i]);
                $statement->bindValue(':end_at', @$this->working_hours['end_at'][$i]);
                $statement->bindValue(':is_working_day', @$this->working_hours['is_working_day'][$i], PDO::PARAM_BOOL);
                $statement->execute();
            }
        }

        /**
         * update branch working hours at roles level
         */
        if (@$this->working_hours_roles['role_id']) {
            for ($i = 0; $i < count($this->working_hours_roles['role_id']); $i++) {
                $sqlQuery = ' UPDATE "Role" SET working_hours_start_at=:start_at, working_hours_end_at=:end_at WHERE role_id=:role_id ';
                $statement = $this->conn->prepare($sqlQuery);
                $statement->execute([':role_id' => $this->working_hours_roles['role_id'][$i], ':start_at' => @$this->working_hours_roles['start_at'][$i], ':end_at' => @$this->working_hours_roles['end_at'][$i]]);
            }
        }
    }
}
