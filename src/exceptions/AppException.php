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

    /**
     * Construct the exception
     * 
     * This method:
     * - Constructs the exception
     * 
     * @param string $message The message
     * @param int $code The code
     * @return void
     */
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}