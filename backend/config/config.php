<?php
// Database Constants
/**
 * Production settings
 */

$host = $_SERVER['HTTP_HOST'];
$production_host = "core.nsimbi.io";
// $production_host = "app.ucscucbs.net";
$semi_production_host = "staging.ucscucbs.net";
// $dev_production_host = "dev.ucscucbs.net";
$dev_production_host = "tc.nsimbi.io";
$local_host = "localhost";

if ($host == $production_host) {
    $db_url  = "postgresql://postgres:wulyPnrvVFQNnmQFzlvAHEuambplkBIR@autorack.proxy.rlwy.net:20833/railway";
    $db_host = "autorack.proxy.rlwy.net";
    $db_user = "postgres";
    $db_password = "wulyPnrvVFQNnmQFzlvAHEuambplkBIR";
    $port = 20833;
    $db_name = "railway";
}
/**
 *  semi-production settings
 */
if ($host == $semi_production_host) {
    $db_url  = "postgresql://postgres:wulyPnrvVFQNnmQFzlvAHEuambplkBIR@autorack.proxy.rlwy.net:20833/railway";
    $db_host = "autorack.proxy.rlwy.net";
    $db_user = "postgres";
    $db_password = "wulyPnrvVFQNnmQFzlvAHEuambplkBIR";
    $port = 20833;
    $db_name = "railway";
}

/**
 *  dev-production settings
 */
if ($host == $dev_production_host) {
    // $db_url  = "postgresql://postgres:wulyPnrvVFQNnmQFzlvAHEuambplkBIR@autorack.proxy.rlwy.net:20833/railway";
    // $db_host = "autorack.proxy.rlwy.net";
    // $db_user = "postgres";
    // $db_password = "wulyPnrvVFQNnmQFzlvAHEuambplkBIR";
    // $port = 20833;
    // $db_name = "railway";
    $db_url  = "postgresql://saccox:asdQWE123@localhost:5432/railway";
    $db_host = "localhost";
    $db_user = "saccox";
    $db_password = "asdQWE123";
    $port = 5432;
    $db_name = "railway";
}
/**
 * local settings
 */
else {

    $db_url  = "postgresql://postgres:wulyPnrvVFQNnmQFzlvAHEuambplkBIR@autorack.proxy.rlwy.net:20833/railway";
    $db_host = "localhost";
    $db_user = "postgres";
    $db_password = "bsbs";
    $port = 5432;
    $db_name = "try_nsimbi";

}

if($host == $local_host) {
    $db_url  = "postgresql://postgres:wulyPnrvVFQNnmQFzlvAHEuambplkBIR@autorack.proxy.rlwy.net:20833/railway";
    $db_host = "localhost";
    $db_user = "postgres";
    $db_password = "bsbs";
    $port = 5432;
    $db_name = "try_nsimbi";
}

defined('DB_HOST') ? null : define("DB_HOST", $db_host); // server selection
defined('DB_USER')   ? null : define("DB_USER", $db_user); // database user
defined('DB_PASS')   ? null : define("DB_PASS", $db_password); // database password
defined('DB_NAME')   ? null : define("DB_NAME", $db_name); //database name
defined('DB_URL')   ? null : define("DB_URL", $db_url); //database name
defined('DB_PORT')   ? null : define("DB_PORT", $port); //database name