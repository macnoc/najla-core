<?php

namespace Najla\Core;

/**
 * Class Flash
 * 
 * This class provides flash messages for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class Flash
{
    /**
     * Set a flash message
     * 
     * @param string $key The message key
     * @param string $message The message to set
     * @return void
     */
    public static function set($key, $message)
    {
        if (!isset($_SESSION))
            session_start();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get a flash message
     * 
     * @param string $key The message key
     * @return string|null The flash message or null if not found
     */
    public static function get($key)
    {
        if (!isset($_SESSION))
            session_start();

        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }

        return null;
    }
}