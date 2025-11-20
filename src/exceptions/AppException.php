<?php

namespace Najla\Exceptions;

/**
 * Class AppException
 * 
 * This class provides utility functions for the application.
 * 
 * @package     Najla\Exceptions 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class AppException extends \Exception
{
    private $error;

    /**
     * Construct the exception
     * 
     * This method:
     * - Constructs the exception
     * 
     * @param string $message The message
     * @param int $code The code
     * @param string $error The error
     * @return void
     */
    public function __construct(string $message, int $code = 400, string $error = '')
    {
        parent::__construct($message, $code);
        $this->error = $error;
    }

    /**
     * Get the error
     * 
     * This method:
     * - Returns the error
     * 
     * @return string The error
     */
    public function getError(): string
    {
        return $this->error;
    }
}