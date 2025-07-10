<?php

namespace Najla\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Throwable;

class ErrorHandler
{
    private static $logger;

    public static function init()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', Config::get('debug') ? 1 : 0);
        ini_set('log_errors', 1);
        set_error_handler([__CLASS__, 'handleError']);
        set_exception_handler([__CLASS__, 'handleException']);
        register_shutdown_function([__CLASS__, 'handleShutdown']);

        self::$logger = new Logger('Error');
        self::$logger->pushHandler(new StreamHandler(BASE_PATH . '/Logs/php_error.log'));
    }

    public static function handleError($level, $message, $file = '', $line = '')
    {
        $errorId = self::generateErrorId();
        $logLevel = self::mapErrorLevel($level);
        self::$logger->log($logLevel, $message, ['errorId' => $errorId, 'file' => $file, 'line' => $line]);

        if (Config::get('debug')) {
            echo "<b>Error:</b> [$level] $message in $file on line $line<br>";
        }
    }

    public static function handleException(Throwable $exception)
    {
        $errorId = self::generateErrorId();
        self::$logger->log(Level::Critical, $exception->getMessage(), [
            'errorId' => $errorId,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        if (Config::get('debug')) {
            echo "<b>Exception:</b> " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "<br>";
            echo nl2br($exception->getTraceAsString());
        }
    }

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

            if (Config::get('debug')) {
                echo "<b>Shutdown Error:</b> " . $error['message'] . " in " . $error['file'] . " on line " . $error['line'] . "<br>";
            }
        }
    }

    private static function generateErrorId(): string 
    {
        return date('Ymd') . '-' . substr(uniqid(), -6);
    }

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
