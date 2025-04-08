<?php
require __DIR__ . '../../../vendor/autoload.php';

use Ramsey\Uuid\Nonstandard\Uuid;


if (!function_exists('StringToArray')) {
    function StringToArray($string, $case = 'uppercase')
    {
        return $searchWords = explode(' ', @$string);
        return $searchWords = array_map($case, $searchWords);
    }
}

if (!function_exists('db_date_format')) {
    function db_date_format($date, $set_to_default = true)
    {
        $timestamp = strtotime($date);
        if ($timestamp) {
            return date('Y-m-d', strtotime(@$date));
        }

        return  $set_to_default ? date('Y-m-d') : null;
    }
}

if (!function_exists('normal_date')) {
    function normal_date($date)
    {
        return date('jS F, Y', strtotime(@$date));
    }
}

if (!function_exists('normal_date_short')) {
    function normal_date_short($date)
    {
        return date('jS M, Y', strtotime(@$date));
    }
}

/**
 * returns the number of days between two dates
 */

if (!function_exists('days_between_dates')) {
    function days_between_dates($date1, $date2)
    {
        if ($date1 && $date2) {
            $earlier = new DateTime($date1);
            $later = new DateTime($date2);
            return $later->diff($earlier)->format("%a");
        }

        return 0;
    }
}

/**
 * check if a number is between two numbers in a sequence
 */

if (!function_exists('number_is_between')) {
    function number_is_between($value, $start, $end)
    {
        if ($value >= $start && $value <= $end) return true;
        return false;
    }
}

if (!function_exists('amount_to_integer')) {
    function amount_to_integer($amount = 0)
    {
        return (float) str_replace([',', 'UGX', ' '], '', $amount);
    }
}


if (!function_exists('now')) {
    function now()
    {
        return date('Y-m-d H:i:s');
    }
}

// decryption 
if (!function_exists('encrypt_data')) {
    function encrypt_data($param)
    {
        $times = 5;
        for ($i = 0; $i < $times; $i++) {
            $param = strrev(base64_encode($param));
        }
        return $param;
    }
}
// decryption   
if (!function_exists('decrypt_data')) {
    function decrypt_data($param)
    {
        $times = 5;
        for ($i = 0; $i < $times; $i++) {
            $param = base64_decode(strrev($param));
        }
        return $param;
    }
}


if (!function_exists('getAccountType')) {
    function getAccountType($account_type_name)
    {
        $transaction_type = '';
        if ($account_type_name == "INCOMES") {
            $transaction_type = 'I';
        } else if ($account_type_name == "ASSETS") {
            $transaction_type = 'ASS';
        } else if ($account_type_name == "LIABILITIES") {
            $transaction_type = 'LIA';
        } else if ($account_type_name == "EXPENSES") {
            $transaction_type = 'E';
        } else if ($account_type_name == "CAPITAL") {
            $transaction_type = 'CAP';
        } else if ($account_type_name == "SUSPENSES") {
            $transaction_type = 'BF';
        }

        return $transaction_type;
    }
}


if (!function_exists('is_valid_uuid')) {
    function is_valid_uuid($string)
    {
        return Uuid::isValid($string);
    }
}

if (!function_exists('tableize')) {
    function tableize($string)
    {
        if (!@$string) return '';
        $inflector = \Doctrine\Inflector\InflectorFactory::create()->build();
        return $inflector->tableize(str_replace(' ', '', $string));
    }
}

if (!function_exists('nullify')) {
    function nullify($str)
    {
        return empty($str) ? null : $str;
    }
}


if (!function_exists('loan_active_statuses')) {
    function loan_active_statuses()
    {
        return [2, 3, 4];
    }
}
