<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('./includes/constants.php');

class PermissionMiddleware
{
    public $permissions;
    public $sub_permissions;
    public $show_403;
    function __construct()
    {
        $this->permissions = @$_SESSION['permissions'] ?? [];
        $this->sub_permissions = @$_SESSION['sub_permissions'] ?? [];
    }

    public function disregardPermissions()
    {
        return $this->IsSuperAdmin() || $this->IsBankAdmin();
    }

    /**
     * This fuction takes a string or array of permissions
     * It check to see if a user has all permissions passed to the function.
     */
    public function hasPermissions($permissions, $show_403 = false)
    {
        $this->checkIsAuthenticated();

        if ($this->disregardPermissions()) return true;

        $this->show_403 = $show_403;
        if (is_array($permissions)) {
            if (empty(array_diff($permissions, $this->permissions))) {
                return $this->is_permitted();
            }
        } else {
            if (in_array($permissions, $this->permissions)) {
                return $this->is_permitted();
            }
        }

        return $this->isNotPermitted();
    }

    /**
     * This fuction takes a string or array of permissions
     * It check to see if a user has any of the permissions passed to the function.
     */
    public function hasAnyPermissions($permissions, $show_403 = false)
    {
        $this->checkIsAuthenticated();

        if ($this->disregardPermissions()) return true;

        $this->show_403 = $show_403;
        if (is_array($permissions)) {
            if ((bool) count(array_intersect($permissions, $this->permissions))) {
                return $this->is_permitted();
            }
        } else {
            if (in_array($permissions, $this->permissions)) {
                return $this->is_permitted();
            }
        }

        return $this->isNotPermitted();
    }


    public function hasSubPermissions($sub_permissions, $show_403 = false, $parent_permission = null)
    {
        $this->checkIsAuthenticated();

        if ($this->disregardPermissions()) return true;

        $this->show_403 = $show_403;
        if (is_array($sub_permissions)) {
            if (empty(array_diff($sub_permissions, $this->sub_permissions))) {
                if ($parent_permission && !$this->hasPermissions($parent_permission)) return $this->isNotPermitted();

                return $this->is_permitted();
            }
        } else {
            if (in_array($sub_permissions, $this->sub_permissions)) {
                if ($parent_permission && !$this->hasPermissions($parent_permission)) return $this->isNotPermitted();
                return $this->is_permitted();
            }
        }

        return $this->isNotPermitted();
    }

    /**
     * This fuction takes a string or array of permissions
     * It check to see if a user has any of the permissions passed to the function.
     */
    public function hasAnySubPermissions($sub_permissions, $show_403 = false)
    {
        $this->checkIsAuthenticated();

        if ($this->disregardPermissions()) return true;

        $this->show_403 = $show_403;
        if (is_array($sub_permissions)) {
            if ((bool) count(array_intersect($sub_permissions, $this->sub_permissions))) {
                return $this->is_permitted();
            }
        } else {
            if (in_array($sub_permissions, $this->sub_permissions)) {
                return $this->is_permitted();
            }
        }

        return $this->isNotPermitted();
    }

    private function is_permitted()
    {
        return true;
    }

    public function isNotPermitted($redirect = false)
    {
        $this->checkIsAuthenticated();
        if ($this->show_403 || $redirect) {
            header("Location: 403.php");
            exit;
        }
        return false;
    }

    public function IsSuperAdmin()
    {
        $this->checkIsAuthenticated();
        return $_SESSION['session_user']['roleId'] == SUPER_ADMIN_ROLE_ID;
    }

    public function IsBankAdmin()
    {
        $this->checkIsAuthenticated();
        if($_SESSION['session_user']['isadmin']){
            return true;
        }
        return false;
    }

    public function IsBankBranchStuff()
    {
        $this->checkIsAuthenticated();
        return $_SESSION['session_user']['branchId'];
    }

    public function IsBankStuff()
    {
        $this->checkIsAuthenticated();
       
        // return $this->IsBankAdmin() || $this->IsBankBranchStuff();
         if($this->IsBankAdmin()){
            return true;
         }else if($this->IsBankBranchStuff()){
            return true;
         }

         return false;
    }

    public function checkIsAuthenticated($redirect = true)
    {
        if (!@$_SESSION['user'] && @$_SESSION['user'] == "") {
            if ($redirect) {
                // header('location: /client/login.php');
                header('location:'. BASE_URL. '../../client/login.php');
                exit();
            }
            return false;
        }

        return true;
    }
}





// $permissionMiddleware = new PermissionMiddleware();
