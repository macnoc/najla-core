<?php

namespace Najla\Core;

/**
 * Class Helper
 * 
 * This class provides utility functions for the application.
 */
class Helper
{
    private static $viewServices = null;

    private static function loadViewServices()
    {
        if (Config::get('view_services') === null) {
            return null;
        }
        if (self::$viewServices === null) {
            self::$viewServices = Config::get('view_services');
        }
        return self::$viewServices;
    }

    public static function getView($view, $data = [])
    {
        $services = self::loadViewServices();

        if ($services !== null) {
            foreach ($services as $key => $serviceClass) {
                if (method_exists($serviceClass, 'getInstance')) {
                    $data[$key] = $serviceClass::getInstance();
                }
            }
        }

        extract($data);

        $view = str_replace('.', '/', $view);
        require_once BASE_PATH . '/Views/' . $view . '.view.php';
    }

    public static function generateUsername($email)
    {
        $name_part = explode('@', $email)[0];
        
        // Remove everything after '+' if it exists
        $name_part = explode('+', $name_part)[0];
        
        // Remove special characters and accents
        $name_part = iconv('UTF-8', 'ASCII//TRANSLIT', $name_part);
        $name_part = preg_replace('/[^a-zA-Z0-9.]/', '', $name_part);
        
        $name_parts = explode('.', $name_part);
        
        // Handle cases where there is no dot
        if (count($name_parts) === 1) {
            $first_name = $name_parts[0];
            $last_name = '';
        } else {
            $first_name = $name_parts[0];
            $last_name = implode(' ', array_slice($name_parts, 1));
        }
        
        $first_name = ucfirst(strtolower($first_name));
        $last_name = ucwords(strtolower($last_name));
        
        // Handle short names
        if (strlen($first_name) < 2) {
            $first_name = substr($email, 0, 2);
        }
        
        return trim($first_name . ' ' . $last_name);
    }


    /**
     * Formats a number into a short human-readable string with suffix.
     *
     * @param float $n The number to format.
     * @param int $precision The number of decimal places to round to (default: 1).
     * @return string The formatted number with appropriate suffix.
     */
    public static function number_format_short($n, $precision = 1)
    {
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'k';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'm';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'b';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 't';
        }

        // Remove unnecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }

        return $n_format . $suffix;
    }

    public static function generateRandomString($length =  8)
    {
        $selector = substr(md5(uniqid()), 0, $length);
        return $selector;
    }

    public static function generateUuid()
    {
        return bin2hex(random_bytes(16));
    }
}
