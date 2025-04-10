<?php
require __DIR__ . '../../../vendor/autoload.php';
require __DIR__ . '/Inflector.php';

use Carbon\Carbon;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('amount_to_integer')) {
    function amount_to_integer($amount)
    {
        return (float) str_replace([',', 'UGX', ' '], '', $amount);
    }
}

// --------------------------------------------------------------------
if (!function_exists('h_convert_number_to_words')) {
    function h_convert_number_to_words($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . h_convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . h_convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = h_convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= h_convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}

if (!function_exists('StringToArray')) {
    function StringToArray($string, $case = 'uppercase')
    {
        return $searchWords = explode(' ', @$string);
        return $searchWords = array_map($case, $searchWords);
    }
}

if (!function_exists('db_date_format')) {
    function db_date_format($date)
    {
        if (!$date) {
            return '-- -- ---';
        }
        return date('Y-m-d', strtotime(@$date));
    }
}

if (!function_exists('to_picker_format')) {
    function to_picker_format($date)
    {
        if (!$date) {
            return '';
        }
        return date('m/d/Y', strtotime(@$date));
    }
}

if (!function_exists('normal_date')) {
    function normal_date($date)
    {
        if (!$date) {
            return '-- -- ---';
        }
        return date('jS F Y', strtotime(@$date));
    }
}

if (!function_exists('normal_date_short')) {
    function normal_date_short($date)
    {
        if (!$date) {
            return '-- -- ---';
        }
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
if (!function_exists('money_format')) {
    function money_format($num, $decimal_places = 0)
    {
        return number_format($num, $decimal_places);
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

if (!function_exists('setSessionMessage')) {
    function setSessionMessage($success = true, $message = null)
    {
        if ($success) {
            $_SESSION['success_message'] = $message ?? 'Action Successful';
        } else {
            $_SESSION['error_message'] = $message ?? 'Action Failed';
        }
    }
}
if (!function_exists('setSessionMessageEr')) {
    function setSessionMessageEr($success = true, $message = null)
    {
        if ($success) {
            $_SESSION['success_message'] = $message ?? 'Action Successful';
        } else {
            $_SESSION['error_message'] = $message ?? 'Action Failed';
        }
    }
}
if (!function_exists('setSessionMessageWithConfirm')) {
    function setSessionMessageWithConfirm($success = true, $message = null, $tid = 0,$type='D')
    {
        if ($success) {
            $_SESSION['success_message_confirm'] = $message ?? 'Action Successful';
            $_SESSION['success_message_tid'] = $tid ?? 0;
            $_SESSION['success_message_type'] = $type;
        } else {
            $_SESSION['error_message'] = $message ?? 'Action Failed';
        }
    }
}

if (!function_exists('get_week_days')) {
    function get_week_days()
    {
        return [
            1 => "Monday",
            2 => "Tuesday",
            3 => "Wednesday",
            4 => "Thursday",
            5 => "Friday",
            6 => "Saturday",
            7 => "Sunday",
        ];
    }
}


if (!function_exists('loan_frequencies')) {
    function loan_frequencies()
    {
        return [
            'DAILY',
            'WEEKLY',
            'MONTHLY',
            'BI-MONTHLY',
            'ANNUALY'
        ];
    }
}

if (!function_exists('loan_recycle_types')) {
    function loan_recycle_types()
    {
        return [
            'DAYS',
            'WEEKS',
            'MONTHS',
            'YEARS'
        ];
    }
}

if (!function_exists('searchMultiDimensionalArrayByKey')) {
    function searchMultiDimensionalArrayByKey($array, $key, $field)
    {
        return array_search($key, array_column($array, $field));
    }
}


if (!function_exists('checkWorkingHoursLapsed')) {
    function checkWorkingHoursLapsed($time = null, $message = null)
    {
        $time = $time ?? @$_SESSION['working_hours_end_at'];
        if ($time) {
            $end_at = Carbon::parse($time);
            $now = Carbon::now();
            /**
             * logout user if maximum working hours have reached 
             */
            if ($now->greaterThan($end_at)) {
                session_destroy();
                session_start();
                $_SESSION['error'] = $message ?? "Your Working session has expired";
                header('Location:login.php');
                exit();
            }
        }
    }
}


if (!function_exists('checkWorkingHoursLogin')) {
    function checkWorkingHoursLogin($time = null, $termiate_session = false, $message = null)
    {
        $time = $time ?? @$_SESSION['working_hours_start_at'];
        if ($time || $termiate_session) {
            $start_at = new Carbon($time);
            $now = Carbon::now();
            /**
             * if the user is logging in and login time is outside working hours, then log them out
             */
            if ($now->lessThan($start_at) || $termiate_session) {
                session_destroy();
                session_start();
                $_SESSION['error'] =  $message ?? "You have to login within working hours";
                header('Location:login.php');
                exit();
            }
        }
    }
}

if (!function_exists('RedirectReferrer')) {
    function RedirectReferrer()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

if (!function_exists('RedirectCurrent')) {
    function RedirectCurrent()
    {
        header('Location:' .  $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
        exit;
    }
}

if (!function_exists('RedirectSelf')) {
    function RedirectSelf()
    {
        // header('Location:' . str_replace('.php', '', $_SERVER['PHP_SELF']));
        header('Location:' .  $_SERVER['PHP_SELF']);
        exit;
    }
}

if (!function_exists('Redirect')) {
    function Redirect($location)
    {
        header('Location:' . $location);
        exit;
    }
}


if (!function_exists('business_types')) {
    function business_types()
    {
        return [
            'sole_proprietor' => 'Sole Proprietor',
            'University' => 'University',
            'Secondary School' => 'Secondary School',
            'Primary School' => 'Primary School',
            'Kindergaten' => 'Kindergaten',
            'cooperative' => 'Cooperative (saccos, credit unions etc)',
            'general_partnership' => 'General Partnership',
            'limited_partnership' => 'Limited Partnership',
            'limited_liability_partnership' => 'Limited Liability Partnership',
            'limited_liability_company' => 'Limited Liability Company',
            'corporation' => 'Corporation',
            'unincorporated' => 'Unincorporated Associations (Clubs, Civic Groups etc.)'
        ];
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



if (!function_exists('to_plural')) {
    function to_plural($string)
    {

        $plural = array(
            '/(quiz)$/i' => '1zes',
            '/^(ox)$/i' => '1en',
            '/([m|l])ouse$/i' => '1ice',
            '/(matr|vert|ind)ix|ex$/i' => '1ices',
            '/(x|ch|ss|sh)$/i' => '1es',
            '/([^aeiouy]|qu)ies$/i' => '1y',
            '/([^aeiouy]|qu)y$/i' => '1ies',
            '/(hive)$/i' => '1s',
            '/(?:([^f])fe|([lr])f)$/i' => '12ves',
            '/sis$/i' => 'ses',
            '/([ti])um$/i' => '1a',
            '/(buffal|tomat)o$/i' => '1oes',
            '/(bu)s$/i' => '1ses',
            '/(alias|status)/i' => '1es',
            '/(octop|vir)us$/i' => '1i',
            '/(ax|test)is$/i' => '1es',
            '/s$/i' => 's',
            '/$/' => 's'
        );

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
            'person' => 'people',
            'man' => 'men',
            'child' => 'children',
            'sex' => 'sexes',
            'move' => 'moves'
        );

        $lowercased_word = strtolower($string);

        foreach ($uncountable as $_uncountable) {
            if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
                return $string;
            }
        }

        foreach ($irregular as $_plural => $_singular) {
            if (preg_match('/(' . $_plural . ')$/i', $string, $arr)) {
                return preg_replace('/(' . $_plural . ')$/i', substr($arr[0], 0, 1) . substr($_singular, 1), $string);
            }
        }

        foreach ($plural as $rule => $replacement) {
            if (preg_match($rule, $string)) {
                return preg_replace($rule, $replacement, $string);
            }
        }
        return false;

        // $inflector = \Doctrine\Inflector\InflectorFactory::create()->build();
        // return $inflector->pluralize($string);
    }
}

if (!function_exists('to_singular')) {
    function to_singular($string)
    {
        $singular = array(
            '/(quiz)zes$/i' => '\1',
            '/(matr)ices$/i' => '\1ix',
            '/(vert|ind)ices$/i' => '\1ex',
            '/^(ox)en/i' => '\1',
            '/(alias|status)es$/i' => '\1',
            '/([octop|vir])i$/i' => '\1us',
            '/(cris|ax|test)es$/i' => '\1is',
            '/(shoe)s$/i' => '\1',
            '/(o)es$/i' => '\1',
            '/(bus)es$/i' => '\1',
            '/([m|l])ice$/i' => '\1ouse',
            '/(x|ch|ss|sh)es$/i' => '\1',
            '/(m)ovies$/i' => '\1ovie',
            '/(s)eries$/i' => '\1eries',
            '/([^aeiouy]|qu)ies$/i' => '\1y',
            '/([lr])ves$/i' => '\1f',
            '/(tive)s$/i' => '\1',
            '/(hive)s$/i' => '\1',
            '/([^f])ves$/i' => '\1fe',
            '/(^analy)ses$/i' => '\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
            '/([ti])a$/i' => '\1um',
            '/(n)ews$/i' => '\1ews',
            '/s$/i' => '',
        );

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
            'person' => 'people',
            'man' => 'men',
            'child' => 'children',
            'sex' => 'sexes',
            'move' => 'moves'
        );

        $lowercased_word = strtolower($string);
        foreach ($uncountable as $_uncountable) {
            if (substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable) {
                return $string;
            }
        }

        foreach ($irregular as $_plural => $_singular) {
            if (preg_match('/(' . $_singular . ')$/i', $string, $arr)) {
                return preg_replace('/(' . $_singular . ')$/i', substr($arr[0], 0, 1) . substr($_plural, 1), $string);
            }
        }

        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $string)) {
                return preg_replace($rule, $replacement, $string);
            }
        }

        return $string;
    }
}

if (!function_exists('parsed_id')) {
    function parsed_id($id)
    {
        return is_numeric($id) ? $_GET['id'] : decrypt_data($id);
    }
}

if (!function_exists('camelize')) {
    function camelize($word)
    {
        return str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/', ' ', $word)));
    }
}

if (!function_exists('underscore')) {
    function underscore($word)
    {
        return  strtolower(preg_replace(
            '/[^A-Z^a-z^0-9]+/',
            '_',
            preg_replace(
                '/([a-zd])([A-Z])/',
                '1_2',
                preg_replace('/([A-Z]+)([A-Z][a-z])/', '1_2', $word)
            )
        ));
    }
}

if (!function_exists('humanize')) {
    function humanize($word, $uppercase = '')
    {
        $uppercase = $uppercase == 'all' ? 'ucwords' : 'ucfirst';
        return $uppercase(str_replace('_', ' ', preg_replace('/_id$/', '', $word)));
    }
}

if (!function_exists('days_in_arrears')) {
    function days_in_arrears($loan)
    {
        $arrears_collection_date = @$loan['arrearsbegindate'] ?? Carbon::now();
        $date = Carbon::parse($arrears_collection_date);
        $now = Carbon::now();
        return $date->diffInDays($now);
    }
}
