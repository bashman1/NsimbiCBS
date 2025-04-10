<?php
require_once('includes/functions.php');
require '../vendor/autoload.php';

use Carbon\Carbon;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('includes/response.php');
$response = new Response();
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $login = $response->loginStaff($email, $password);
    // var_dump($login);
    // exit;
    if ($login != "") {

        $user_id = $login[0]['id'];

        $host = $_SERVER['HTTP_HOST'];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/'))) . '://';
        $endpoint = "User/staff_details.php";
        // $url = $protocol . $_SERVER['HTTP_HOST'] . "/backend/api/" . $endpoint;
        // $url = "https://app.ucscucbs.net/backend/api/" . $endpoint;
        $url = "http://localhost/ucscudevmain/backend/api/" . $endpoint;
        $data = array(
            'id' => @$user_id
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header' =>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create($options);
        $responseb = file_get_contents($url, false, $context);
        $details = json_decode($responseb, true);

        // var_dump($data);
        // exit;

        if ($details['success']) {
            $user =  $details['data'];
            $session_user =  @$details['data'][0];
            $_SESSION['session_user'] = $session_user;
            $_SESSION['user'] = $session_user;

            /**
             * get user permissions
             */
            // $options = array(
            //     'http' => array(
            //         'method'  => 'POST',
            //         'content' => json_encode($data),
            //         'header' =>  "Content-Type: application/json\r\n" .
            //             "Accept: application/json\r\n"
            //     )
            // );

            // $response = new Response();
            $role = $response->getRole($_SESSION['session_user']['roleId']);
            $permissions = array_column($role['permissions'], 'slug');
            $child_permissions = array_column($role['child_permissions'], 'rights');

            $staff_level_permissions = $response->getStaffPermissions($_SESSION['session_user']['userId']);
            $staff_permissions = array_column($staff_level_permissions['permissions'], 'slug') ?? [];
            $staff_sub_permissions = array_column($staff_level_permissions['child_permissions'], 'rights') ?? [];


            $permissions = array_merge($permissions, $staff_permissions);
            $child_permissions = array_merge($child_permissions, $staff_sub_permissions);
            $all_permissions = array_merge($permissions, $child_permissions, $staff_permissions, $staff_sub_permissions);

            $_SESSION['permissions'] = $permissions;
            $_SESSION['sub_permissions'] = $child_permissions;
            $_SESSION['all_permissions'] = $all_permissions;

            /**
             * only add working hours to a session if the user has a branch
             */
            if ($_SESSION['session_user']['branchId']) {
                $branch = $response->getBranchDetails($_SESSION['session_user']['branchId']);
                $working_hours = $branch['working_hours'] ?? [];
                $working_hours_roles = $branch['roles'] ?? [];

                /**
                 * check if the user branch has working hours
                 */
                if ($branch['working_hours']) {
                    $day_id = Carbon::now()->dayOfWeekIso;
                    $found_key = searchMultiDimensionalArrayByKey($branch['working_hours'], $day_id, 'day_id');

                    /**
                     * if branch is working today
                     */
                    if ($found_key !== false) {
                        $hours = $branch['working_hours'][$found_key];
                        if (!@$hours['is_working_day']) {
                            checkWorkingHoursLogin($_SESSION['working_hours_start_at'], true);
                            return;
                        }

                        if (@$hours['start_at']) {
                            $_SESSION['working_hours_start_at'] = Carbon::parse(@$hours['start_at'])->format('Y-m-d H:i:s');
                        }
                        if (@$hours['end_at']) {
                            $_SESSION['working_hours_end_at'] = Carbon::parse(@$hours['end_at'])->format('Y-m-d H:i:s');
                        }
                        checkWorkingHoursLogin(@$_SESSION['working_hours_start_at']);
                    }
                }

                /**
                 * check if the user role has working hours
                 */
                if ($branch['roles']) {
                    $found_key = searchMultiDimensionalArrayByKey($branch['roles'], $_SESSION['session_user']['roleId'], 'id');
                    if ($found_key !== false) {
                        $hours = @$branch['roles'][$found_key] ?? [];

                        if (@$hours['working_hours_start_at']) {
                            $_SESSION['working_hours_start_at'] = Carbon::parse(@$hours['working_hours_start_at'])->format('Y-m-d H:i:s');
                        }

                        if (@$hours['working_hours_end_at']) {
                            $_SESSION['working_hours_end_at'] = Carbon::parse(@$hours['working_hours_end_at'])->format('Y-m-d H:i:s');
                        }
                    }
                }

                checkWorkingHoursLogin(@$_SESSION['working_hours_start_at']);
                checkWorkingHoursLapsed(@$_SESSION['working_hours_end_at'], "You have to login within working hours");
            }
            header('location: index.php');
            exit;
        }

        $_SESSION['error'] = "Invalid Credentials !";
        header('location:login.php');
        exit;
    } else {
        $_SESSION['error'] = "Invalid Credentials !";
        header('location:login.php');
        exit;
        // echo $_SESSION['error'];
    }
} else {
    $_SESSION['error'] = "You need to login first !";
    header('location:login.php');
    exit;
}
