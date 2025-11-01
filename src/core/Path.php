<?php

namespace Najla\Core;

/**
 * Class Path
 * 
 * This class provides utility functions for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class Path
{
    /**
     * Get the base path
     * 
     * This method:
     * - Returns the base path
     * 
     * @return string The base path
     */
    public static function base(): string {
        return dirname(__DIR__, 5);
    }
}