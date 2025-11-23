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
    public static function base(): string
    {
        return dirname(__DIR__, 5);
    }

    public static function request_uri($uri, $if_true, $if_false = null)
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        // Escape special regex characters in the URI, then prepare for wildcard replacement.
        // We use '#' as a delimiter for preg_quote to avoid escaping slashes unnecessarily.
        $pattern = preg_quote($uri, '#');

        // Replace '\*?' with '(?:.*)?' to handle optional wildcard segments (e.g., /path/*?)
        // The (?:...) creates a non-capturing group.
        $pattern = str_replace('\*?', '(?:.*)?', $pattern);

        // Replace '\*' with '.*' for standard wildcard segments (e.g., /path/*)
        $pattern = str_replace('\*', '.*', $pattern);

        // Add start and end anchors to ensure the entire URI matches the pattern
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $requestUri) ? $if_true : $if_false;
    }
}