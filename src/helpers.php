<?php

use Najla\Services\TranslationService;
use Najla\Core\ErrorHandler;
use Najla\Core\Local;

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
if (!function_exists('x')) {
    function x($key, $parameters = [], $locale = null)
    {
        return Local::translator()->trans($key, $parameters, $locale);
    }
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
 * This function is used to trim and strip tags.
 *
 * @param string|null $string
 * @return string
 */
function esc_text($string)
{
    if ($string === null || !is_string($string)) {
        return null;
    }
    return trim(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
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
