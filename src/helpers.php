<?php

/**
 * This file contains helper functions for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */

use Najla\Core\ErrorHandler;
use Najla\Core\Local;

/**
 * This function is used to log an error.
 * 
 * @param string $message
 * @return void
 */
function log_error($message)
{
    ErrorHandler::handleException(new \Exception($message));
}

/**
 * This function is used to translate a key.
 * 
 * @param string $key
 * @param array $parameters
 * @param string $locale
 * @return string
 */
function __($key, $parameters = [], $locale = null)
{
    return Local::translator()->trans($key, $parameters, $locale);
}

/**
 * This function is used to echo a translated string.
 * 
 * @param string $key
 * @param array $parameters
 * @param string $locale
 * @return void
 */
function _e($key, $parameters = [], $locale = null)
{
    echo esc_html(__($key, $parameters, $locale));
}


/**
 * This function is used to trim and escape HTML entities.
 * 
 * @param string|null $string
 * @return string
 */
function esc_html($string)
{
    if ($string === null || !is_string($string)) {
        return null;
    }
    return trim(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
}

/**
 * This function is used to escape text content (strips HTML tags).
 *
 * @param string|null $string
 * @return string
 */
function esc_text($string)
{
    if ($string === null || !is_string($string)) {
        return null;
    }
    return trim(strip_tags($string));
}

/**
 * This function is used to escape attributes.
 *
 * @param string|null $string
 * @return string
 */
function esc_attr($string)
{
    if ($string === null || !is_string($string)) {
        return null;
    }
    return trim(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
}

/**
 * This function is used to escape URLs.
 *
 * @param string|null $url
 * @return string
 */
function esc_url($url)
{
    if ($url === null || !is_string($url)) {
        return null;
    }
    return trim(filter_var($url, FILTER_SANITIZE_URL));
}

/**
 * This function is used to escape JavaScript.
 *
 * @param string|null $string
 * @return string
 */
function esc_js($string)
{
    if ($string === null || !is_string($string)) {
        return null;
    }
    return trim(str_replace(
        ['<script', '</script>', 'javascript:', 'vbscript:', 'onload', 'onerror'],
        ['&lt;script', '&lt;/script&gt;', 'javascript&#58;', 'vbscript&#58;', 'onload&#58;', 'onerror&#58;'],
        $string
    ));
}

/**
 * This function is used to escape CSS.
 *
 * @param string|null $string
 * @return string
 */
function esc_css($string)
{
    if ($string === null || !is_string($string)) {
        return null;
    }
    return trim(preg_replace('/[^a-zA-Z0-9\s\-_\.]/', '', $string));
}

/**
 * This function is used to escape numbers.
 *
 * @param int|null $number
 * @return int
 */
function esc_number($number)
{
    if ($number === null || !is_numeric($number)) {
        return null;
    }
    return (int) $number;
}

/**
 * This function is used to escape email addresses.
 *
 * @param string|null $email
 * @return string
 */
function esc_email($email)
{
    if ($email === null || !is_string($email)) {
        return null;
    }
    return trim(filter_var($email, FILTER_SANITIZE_EMAIL));
}
