<?php

$host = $_SERVER['HTTP_HOST'];
$production_host = "app.ucscucbs.net";
$semi_production_host = "ucscu-staging.ucscucbs.net";
$dev_production_host = "dev.ucscucbs.net";
$local_host = "localhost";

// if ($host == $production_host) {
//     $base_url = "https://$host/backend/api/";
// } else if ($host == $semi_production_host) {
//     $base_url = "http://$host/backend/api/";
// } else if ($host == $dev_production_host) {
//     $base_url = "http://$host/backend/api/";
// } else if ($host == $local_host) {
//     $base_url = "http://$host/backend/api/";
// } else {
//     $base_url = "https://ucscucbs.herokuapp.com/backend/api/";
// }
$host = $_SERVER['HTTP_HOST'];
// $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/'))) . '://';

$protocol = 'https://';
if ($host == $local_host) {
    $protocol = 'http://';
}

// $base_url = $protocol . $host . "/backend/api/";
// $base_url = $protocol . $host . "/backend/api/";
if ($host == $local_host) {
    $base_url = $protocol . $host . "/ucscudevmain/backend/api/"; // Include /ucscudev-main for localhost
} else {
    $base_url = $protocol . $host . "/backend/api/"; // No '/ucscudev-main' for production or staging
}

defined("BASE_URL")                       or define("BASE_URL", $base_url);
defined("BACKEND_BASE_URL") or define("BACKEND_BASE_URL", $base_url);
defined("BACKEND_BASE_LOCALLY_URL") or define("BACKEND_BASE_LOCALLY_URL", '../backend/api');
defined("SUPER_ADMIN_ROLE_ID") or define("SUPER_ADMIN_ROLE_ID", 'becedad5-8159-4543-911f-da4805e29f77');
