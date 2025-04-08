<?php
require_once('../client/includes/response.php');
require_once('../client/includes/functions.php');
require '../vendor/autoload.php';

use Carbon\Carbon;

date_default_timezone_set("Africa/Kampala");



if (session_status() === PHP_SESSION_NONE) {
	session_start();
	// server should keep session data for AT LEAST 2 hour
	ini_set('session.gc_maxlifetime', 7200);

	// each client should remember their session id for EXACTLY 2 hour
	session_set_cookie_params(7200);
}

if (isset($_SESSION['session_user']) && $_SESSION['session_user'] !== "") {
	$session_user = $_SESSION['session_user'];
	$user =  [0 => $_SESSION['session_user']];
	if (isset($_SESSION['working_hours_end_at'])) {
		checkWorkingHoursLapsed();
	}
} else {
	// $host = $_SERVER['HTTP_HOST'];
	// $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/'))) . '://';
	// $endpoint = "User/staff_details.php";
	// $url = $protocol . $_SERVER['HTTP_HOST'] . "/backend/api/" . $endpoint;

	// $data = array(
	// 	'id' => @$_SESSION['user']
	// );

	// $options = array(
	// 	'http' => array(
	// 		'method'  => 'POST',
	// 		'content' => json_encode($data),
	// 		'header' =>  "Content-Type: application/json\r\n" .
	// 			"Accept: application/json\r\n"
	// 	)
	// );

	// $context  = stream_context_create($options);
	// $response = file_get_contents($url, false, $context);
	// $data = json_decode($response, true);

	// // var_dump($data);
	// // exit;

	// if ($data['success']) {
	// 	$user =  $data['data'];
	// 	$session_user =  @$data['data'][0];
	// 	$_SESSION['session_user'] = $session_user;

	// 	/**
	// 	 * get user permissions
	// 	 */
	// 	$options = array(
	// 		'http' => array(
	// 			'method'  => 'POST',
	// 			'content' => json_encode($data),
	// 			'header' =>  "Content-Type: application/json\r\n" .
	// 				"Accept: application/json\r\n"
	// 		)
	// 	);

	// 	$response = new Response();
	// 	$role = $response->getRole($_SESSION['session_user']['roleId']);
	// 	$permissions = array_column($role['permissions'], 'slug');
	// 	$child_permissions = array_column($role['child_permissions'], 'rights');

	// 	$staff_level_permissions = $response->getStaffPermissions($_SESSION['session_user']['userId']);
	// 	$staff_permissions = array_column($staff_level_permissions['permissions'], p'slug') ?? [];
	// 	$staff_sub_permissions = array_column($staff_level_permissions['child_permissions'], 'rights') ?? [];


	// 	$permissions = array_merge($permissions, $staff_permissions);
	// 	$child_permissions = array_merge($child_permissions, $staff_sub_permissions);
	// 	$all_permissions = array_merge($permissions, $child_permissions, $staff_permissions, $staff_sub_permissions);

	// 	$_SESSION['permissions'] = $permissions;
	// 	$_SESSION['sub_permissions'] = $child_permissions;
	// 	$_SESSION['all_permissions'] = $all_permissions;

	// 	/**
	// 	 * only add working hours to a session if the user has a branch
	// 	 */
	// 	if ($_SESSION['session_user']['branchId']) {
	// 		$branch = $response->getBranchDetails($_SESSION['session_user']['branchId']);
	// 		$working_hours = $branch['working_hours'] ?? [];
	// 		$working_hours_roles = $branch['roles'] ?? [];

	// 		/**
	// 		 * check if the user branch has working hours
	// 		 */
	// 		if ($branch['working_hours']) {
	// 			$day_id = Carbon::now()->dayOfWeekIso;
	// 			$found_key = searchMultiDimensionalArrayByKey($branch['working_hours'], $day_id, 'day_id');

	// 			/**
	// 			 * if branch is working today
	// 			 */
	// 			if ($found_key !== false) {
	// 				$hours = $branch['working_hours'][$found_key];
	// 				if (!@$hours['working_day']) {

	// 					var_dump($found_key);
	// 					exit;

	// 					checkWorkingHoursLogin($_SESSION['working_hours_start_at'], true);
	// 				}

	// 				if (@$hours['start_at']) {
	// 					$_SESSION['working_hours_start_at'] = Carbon::parse(@$hours['start_at'])->format('Y-m-d H:i:s');
	// 				}
	// 				if (@$hours['end_at']) {
	// 					$_SESSION['working_hours_end_at'] = Carbon::parse(@$hours['end_at'])->format('Y-m-d H:i:s');
	// 				}
	// 				checkWorkingHoursLogin($_SESSION['working_hours_start_at']);
	// 			}
	// 		}

	// 		/**
	// 		 * check if the user role has working hours
	// 		 */
	// 		if ($branch['roles']) {
	// 			$found_key = searchMultiDimensionalArrayByKey($branch['roles'], $_SESSION['session_user']['roleId'], 'id');
	// 			if ($found_key !== false) {
	// 				$hours = @$branch['roles'][$found_key] ?? [];

	// 				if (@$hours['working_hours_start_at']) {
	// 					$_SESSION['working_hours_start_at'] = Carbon::parse(@$hours['working_hours_start_at'])->format('Y-m-d H:i:s');
	// 				}

	// 				if (@$hours['working_hours_end_at']) {
	// 					$_SESSION['working_hours_end_at'] = Carbon::parse(@$hours['working_hours_end_at'])->format('Y-m-d H:i:s');
	// 				}
	// 			}
	// 		}

	// 		checkWorkingHoursLogin($_SESSION['working_hours_start_at']);
	// 		checkWorkingHoursLapsed($_SESSION['working_hours_end_at']);
	// 	}
	// }
}
