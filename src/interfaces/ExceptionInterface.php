<?php

namespace Najla\Interfaces;

/**
 * Interface ExceptionInterface
 * 
 * This interface provides utility functions for the application.
 * 
 * @package     Najla\Interfaces 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
interface ExceptionInterface
{
    /**
     * Get the log level
     * 
     * This method:
     * - Returns the log level
     * 
     * @return string The log level
     */
    public function getLogLevel(): string;

    /**
     * Get the context
     * 
     * This method:
     * - Returns the context
     * 
     * @return array The context
     */
    public function getContext(): array;

    /**
     * Get the log file
     * 
     * This method:
     * - Returns the log file
     * 
     * @return string The log file
     */
    public function getLogFile(): string;
}
