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
    private $userMessage;

    /**
     * Construct the exception
     * 
     * This method:
     * - Constructs the exception
     * 
     * @param string $message The message
     * @param int $code The code
     * @param string|null $userMessage The user message (optional)
     * @return void
     */
    public function __construct(string $message, int $code = 400, string|null $userMessage = null)
    {
        parent::__construct($message, $code);
        $this->userMessage = $userMessage;
    }

    /**
     * Get the user message
     * 
     * This method:
     * - Returns the user message
     * 
     * @return string|null The user message
     */
    public function getUserMessage(): string|null
    {
        return !empty($this->userMessage) ? $this->userMessage : null;
    }
}