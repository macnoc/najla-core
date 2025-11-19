<?php

namespace Najla\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Throwable;

/**
 * ErrorHandler.php 
 * 
 * The error handler class
 * 
 * This class is responsible for handling the application errors.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class ErrorHandler
{
    
    private static $logger;

    /**
     * Initialize the error handler
     * 
     * This method:
     * - Sets the error reporting level
     * - Sets the display errors setting
     * - Sets the log errors setting
     * - Sets the error handler
     * - Sets the exception handler
     * - Sets the shutdown function
     * 
     * @throws Exception If the configuration file is not found
     */
    public static function init()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', Config::get('app.debug') ? 1 : 0);
        ini_set('log_errors', 1);
        set_error_handler([__CLASS__, 'handleError']);
        set_exception_handler([__CLASS__, 'handleException']);
        register_shutdown_function([__CLASS__, 'handleShutdown']);

        self::$logger = new Logger('Error');
        self::$logger->pushHandler(new StreamHandler(ROOT . '/data/logs/php_error.log'));
    }

    /**
     * Handle a PHP error
     * 
     * This method:
     * - Generates an error ID
     * - Maps the error level
     * - Logs the error
     * - Displays the error if debug is enabled
     * 
     * @param int $level The error level
     * @param string $message The error message
     * @param string $file The file where the error occurred
     * @param int $line The line where the error occurred
     */
    public static function handleError($level, $message, $file = '', $line = '')
    {
        $errorId = self::generateErrorId();
        $logLevel = self::mapErrorLevel($level);
        self::$logger->log($logLevel, $message, ['errorId' => $errorId, 'file' => $file, 'line' => $line]);

        if (Config::get('app.debug')) {
            echo "<b>Error:</b> [$level] $message in $file on line $line<br>";
        }
    }

    /**
     * Handle a PHP exception
     * 
     * This method:
     * - Generates an error ID
     * - Logs the exception
     * - Displays the exception if debug is enabled
     * 
     * @param Throwable $exception The exception to handle
     */
    public static function handleException(Throwable $exception)
    {
        $errorId = self::generateErrorId();
        self::$logger->log(Level::Critical, $exception->getMessage(), [
            'errorId' => $errorId,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        if (Config::get('app.debug')) {
            echo "<b>Exception:</b> " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "<br>";
            echo nl2br($exception->getTraceAsString());
        }
    }

    /**
     * Handle a PHP shutdown error
     * 
     * This method:
     * - Retrieves the last error
     * - Generates an error ID
     * - Maps the error level
     * - Logs the error
     * - Displays the error if debug is enabled
     * 
     * @throws Exception If the configuration file is not found
     */
    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error !== null) {
            $errorId = self::generateErrorId();
            $logLevel = self::mapErrorLevel($error['type']);
            self::$logger->log($logLevel, $error['message'], [
                'errorId' => $errorId,
                'file' => $error['file'],
                'line' => $error['line']
            ]);

            if (Config::get('app.debug')) {
                echo "<b>Shutdown Error:</b> " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "<br>";
            }
        }
    }

    /**
     * Generate an error ID
     * 
     * This method:
     * - Returns a unique error ID
     * 
     * @return string The error ID
     */
    private static function generateErrorId(): string 
    {
        return date('Ymd') . '-' . substr(uniqid(), -6);
    }

    /**
     * Map an error level to a Monolog level
     * 
     * This method:
     * - Maps an error level to a Monolog level
     * 
     * @param int $level The error level
     * @return Level The Monolog level
     */
    private static function mapErrorLevel($level)
    {
        switch ($level) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return Level::Critical;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return Level::Warning;
            case E_NOTICE:
            case E_USER_NOTICE:
                return Level::Notice;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return Level::Warning;
            default:
                return Level::Error;
        }
    }
}
