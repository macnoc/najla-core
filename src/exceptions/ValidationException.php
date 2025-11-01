<?php

namespace Najla\Exceptions;

/**
 * Class ValidationException
 * 
 * This class provides utility functions for the application.
 * 
 * @package     Najla\Exceptions 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class ValidationException extends \Exception
{
    private $data;

    /**
     * Construct the exception
     * 
     * This method:
     * - Constructs the exception
     * 
     * @param string $message The message
     * @param int $code The code
     * @param array $data The data
     * @return void
     */
    public function __construct(string $message, int $code = 400, array $data = [])
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    /**
     * Get the data
     * 
     * This method:
     * - Returns the data
     * 
     * @return array The data
     */
    public function getData(): array
    {
        return $this->data;
    }
}