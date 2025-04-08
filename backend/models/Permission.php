<?php

class Permission
{
    private $conn;
    public $id;
    public $role_id;
    public $staff_id;
    public $staff_details;
    public $role_uuid;
    public $name;
    public $description;
    public $bank_id;
    public $branch_id;
    public $permissions = [];
    public $child_permissions = [];

    // Constructor with DB
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * ger role
     */
    public function getRole()
    {
        $sqlQuery = 'SELECT * FROM public."Role" WHERE id=:id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $this->role_uuid);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ger role
     */
    public function getBranchRoles($id)
    {
        $id = $id ?? $this->branch_id;
        $sqlQuery = 'SELECT id, name, "branchId" AS branch_id, role_id, working_hours_start_at, working_hours_end_at FROM public."Role" WHERE "branchId"=:branch_id';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute([':branch_id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get role permissions
     */
    public function getRolePermissions()
    {
        $sqlQuery = ' SELECT public.role_permission.id, 
                    public.role_permission.role_id,  
                    public.mainpermissions.id AS permission_id,  
                    public.mainpermissions.name,  
                    public.mainpermissions.slug  
                    FROM public.role_permission 
                    INNER  JOIN public.mainpermissions ON public.mainpermissions.id = public.role_permission.permission_id
                    WHERE role_id=:role_id AND permission_id IS NOT NULL AND sub_permission_id IS NULL AND staff_id IS NULL';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':role_id', $this->role_id);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get role child permissions
     */
    public function getRoleChildPermissions()
    {
        $sqlQuery = ' SELECT public.role_permission.id,
                    public.role_permission.role_id,  
                    public.subpermissions.mid AS permission_id,  
                    public.role_permission.sub_permission_id,  
                    public.subpermissions.sname AS name,  
                    public.subpermissions.rights  
                    
                    FROM public.role_permission 
                    INNER  JOIN public.subpermissions ON public.subpermissions.sid = public.role_permission.sub_permission_id
                    WHERE role_id=:role_id AND sub_permission_id IS NOT NULL AND permission_id IS NULL AND staff_id IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':role_id', $this->role_id);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get staff permissions
     */
    public function getStaffPermissions()
    {
        $sqlQuery = ' SELECT public.role_permission.id, 
                    public.role_permission.role_id,  
                    public.mainpermissions.id AS permission_id,  
                    public.mainpermissions.name,  
                    public.mainpermissions.slug  
                    FROM public.role_permission 
                    INNER  JOIN public.mainpermissions ON public.mainpermissions.id = public.role_permission.permission_id
                    WHERE staff_id=:staff_id AND permission_id IS NOT NULL AND sub_permission_id IS NULL ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':staff_id', $this->staff_id);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get staff permissions
     */
    public function getStaffSubPermissions()
    {
        $sqlQuery = ' SELECT public.role_permission.id,
                    public.role_permission.role_id,  
                    public.subpermissions.mid AS permission_id,  
                    public.role_permission.sub_permission_id,  
                    public.subpermissions.sname AS name,  
                    public.subpermissions.rights  
                    
                    FROM public.role_permission 
                    INNER  JOIN public.subpermissions ON public.subpermissions.sid = public.role_permission.sub_permission_id
                    WHERE staff_id=:staff_id AND sub_permission_id IS NOT NULL AND permission_id IS NULL';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':staff_id', $this->staff_id);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get permissions
     */
    public function getMainPermissions()
    {
        $sqlQuery = 'SELECT * FROM public."mainpermissions" ';
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get sub permissions
     */
    public function getSubPermissions($permission_id)
    {
        $sqlQuery = 'SELECT * FROM public."subpermissions" WHERE mid=:id';

        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(':id', $permission_id);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * add/update role and associated permissions
     */
    public function saveRole()
    {
        if ($this->branch_id) {
            /**
             * Update role
             */
            if ($this->role_uuid) {
                $role = $this->getRole();
                $sqlQuery = ' UPDATE public."Role" SET name=:name,description=:description,"branchId"=:branchId WHERE id=:role_id';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':role_id', $this->role_uuid);
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':description', $this->description);
                $stmt->bindParam(':branchId', $this->branch_id);
                $stmt->execute();
                $role_id = $role['role_id'];
            }

            /**
             * create role
             */
            else {
                $sqlQuery = 'INSERT INTO public."Role" (name,description,"branchId") VALUES
                (:name,:description,:branchId)';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':description', $this->description);
                $stmt->bindParam(':branchId', $this->branch_id);
                $stmt->execute();
                $role_id = $this->conn->lastInsertId();
            }
        } else {
            if ($this->role_uuid) {
                $role = $this->getRole();
                $sqlQuery = ' UPDATE public."Role" SET name=:name,description=:description,"branchId"=:branchId, "bankId"=:bankId WHERE id=:role_id';

                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':role_id', $this->role_uuid);
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':description', $this->description);
                $stmt->bindParam(':branchId', $this->branch_id);
                $stmt->bindParam(':bankId', $this->bank_id);
                $stmt->execute();
                $role_id = $role['role_id'];
            } else {
                $sqlQuery = 'INSERT INTO public."Role" (name,description,"branchId","bankId") VALUES
            (:name,:description,:branchId,:bankId)';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':description', $this->description);
                $stmt->bindParam(':branchId', $this->branch_id);
                $stmt->bindParam(':bankId', $this->bank_id);
                $stmt->execute();
                $role_id = $this->conn->lastInsertId();
            }
        }




        /**
         * create permissions
         */
        foreach ($this->permissions as $permission) {
            /**
             * check if permission exists
             */
            $checkQuery = ' SELECT * FROM  public.role_permission WHERE role_id=:role_id AND permission_id=:permission_id ';
            $exists = $this->conn->prepare($checkQuery);
            $exists->bindParam(':role_id', $role_id);
            $exists->bindParam(':permission_id', $permission);
            $exists->execute();
            $_exists = $exists->fetch(PDO::FETCH_ASSOC);
            /**
             * if permission does not exist then create it
             */
            if (!$_exists) {
                $sqlQuery = 'INSERT INTO public.role_permission (role_id,permission_id) VALUES
                (:role_id,:permission_id)';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':role_id', $role_id);
                $stmt->bindParam(':permission_id', $permission);
                $stmt->execute();
            }
        }

        /**
         * Remove unassociated permissions
         */
        if (count($this->permissions)) {
            $inValues = implode(',', $this->permissions);
            $deleteQuery = ' DELETE FROM  public.role_permission WHERE role_id=:role_id AND public.role_permission.permission_id NOT IN(' . $inValues . ') AND public.role_permission.permission_id IS NOT NULL ';
            $delete = $this->conn->prepare($deleteQuery);
            $delete->bindParam(':role_id', $role_id);
            $delete->execute();
        }


        /**
         * create sub permissions
         */
        foreach ($this->child_permissions as $child_permission) {
            /**
             * check if permission exists
             */
            $checkQuery = ' SELECT * FROM  public.role_permission WHERE role_id=:role_id AND sub_permission_id=:sub_permission_id ';
            $exists = $this->conn->prepare($checkQuery);
            $exists->bindParam(':role_id', $role_id);
            $exists->bindParam(':sub_permission_id', $child_permission);
            $exists->execute();
            $_exists = $exists->fetch(PDO::FETCH_ASSOC);
            /**
             * if permission does not exist then create it
             */
            if (!$_exists) {
                $sqlQuery = 'INSERT INTO public.role_permission (role_id,sub_permission_id) VALUES
            (:role_id,:sub_permission_id)';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':role_id', $role_id);
                $stmt->bindParam(':sub_permission_id', $child_permission);
                $stmt->execute();
            }
        }

        /**
         * Remove unassociated sub permissions
         */
        if (count($this->child_permissions)) {
            $inValues = implode(',', $this->child_permissions);
            $deleteQuery = ' DELETE FROM  public.role_permission WHERE role_id=:role_id AND public.role_permission.sub_permission_id NOT IN(' . $inValues . ') AND public.role_permission.sub_permission_id IS NOT NULL ';
            $delete = $this->conn->prepare($deleteQuery);
            $delete->bindParam(':role_id', $role_id);
            $delete->execute();
        }

        return true;
    }

    public function saveStaffPermissions()
    {
        $role = $this->getRole();
        $this->role_id = $role['role_id'];
        $role['permissions'] = $this->getRolePermissions();
        $role['child_permissions'] = $this->getRoleChildPermissions();

        $role_permissions_ids = array_column($role['permissions'], 'permission_id');
        $role_child_permissions_ids = array_column($role['child_permissions'], 'sub_permission_id');

        $staff = [];
        $staff['permissions'] = $this->getStaffPermissions();
        $staff['child_permissions'] = $this->getStaffSubPermissions();

        $staff_permissions_ids = array_column($staff['permissions'], 'permission_id');
        $staff_child_permissions_ids = array_column($staff['child_permissions'], 'sub_permission_id');

        // return $role_child_permissions_ids;

        /**
         * create staff permissions
         */
        foreach ($this->permissions as $permission) {
            if (
                !in_array($permission, $staff_permissions_ids) &&
                !in_array($permission, $role_permissions_ids)
            ) {
                $sqlQuery = 'INSERT INTO public.role_permission (staff_id,permission_id) VALUES
            (:staff_id,:permission_id)';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':staff_id', $this->staff_id);
                $stmt->bindParam(':permission_id', $permission);
                $stmt->execute();
            }
        }


        /**
         * Remove unassociated permissions
         */
        if (count($this->permissions) && $this->staff_id) {
            $inValues = implode(',', $this->permissions);
            $deleteQuery = ' DELETE FROM  public.role_permission WHERE staff_id=:staff_id AND public.role_permission.permission_id NOT IN(' . $inValues . ') AND public.role_permission.permission_id IS NOT NULL ';
            $delete = $this->conn->prepare($deleteQuery);
            $delete->bindParam(':staff_id', $this->staff_id);
            $delete->execute();
        }


        /**
         * create staff sub permissions
         */
        foreach ($this->child_permissions as $child_permission) {
            if (
                !in_array($child_permission, $staff_child_permissions_ids) &&
                !in_array($child_permission, $role_child_permissions_ids)
            ) {
                $sqlQuery = 'INSERT INTO public.role_permission (staff_id,sub_permission_id) VALUES
            (:staff_id,:sub_permission_id)';
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->bindParam(':staff_id', $this->staff_id);
                $stmt->bindParam(':sub_permission_id', $child_permission);
                $stmt->execute();
            }
        }

        /**
         * Remove unassociated sub permissions
         */
        if (count($this->child_permissions) && $this->staff_id) {
            $inValues = implode(',', $this->child_permissions);
            $deleteQuery = ' DELETE FROM  public.role_permission WHERE staff_id=:staff_id AND public.role_permission.sub_permission_id NOT IN(' . $inValues . ') AND public.role_permission.sub_permission_id IS NOT NULL ';
            $delete = $this->conn->prepare($deleteQuery);
            $delete->bindParam(':staff_id', $this->staff_id);
            $delete->execute();
        }
    }
}
