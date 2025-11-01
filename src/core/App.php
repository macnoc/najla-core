<?php

namespace Najla\Core;

use Najla\Core\Config;
use Najla\Core\Route;
use Najla\Core\Path;
use Najla\Core\Local;
use Exception;

/**
 * Main application class responsible for bootstrapping and running the Najla application.
 * 
 * This class serves as the core of the Najla framework, handling:
 * - Application initialization
 * - Configuration loading
 * - Session management
 * - Route loading and dispatching
 * - Localization setup
 * 
 * @package    Najla\Core
 * @author     Nabil Makhnouq
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
class App
{
    /**
     * App constructor.
     * 
     * Initializes the application by:
     * - Setting up the root directory constant
     * - Loading configuration
     * - Initializing error handling
     * - Setting up session management
     * - Starting the application initialization
     * 
     * @throws Exception If there's an error during initialization
     */
    public function __construct()
    {
        define('ROOT', Path::base());

        Config::init();
        ErrorHandler::init();

        define('LANG', Config::get('locales.default'));

        if (Config::get('app.cookies_domain') !== false) {
            ini_set('session.cookie_domain', Config::get('app.cookies_domain'));
        }

        session_name(Config::get('app.session_name'));
        session_start();

        $this->init();
    }

    /**
     * Load all route files from the Routes directory.
     * 
     * Scans the Routes directory (including subdirectories) for PHP files and includes them.
     * This allows for modular route definitions across multiple files.
     * 
     * @return void
     * @throws Exception If the Routes directory is not found
     */
    private function loadRoutes()
    {
        if (!file_exists(ROOT . '/Routes')) {
            throw new \Exception('Routes folder not found');
        }

        $routes_files = glob(ROOT . '/{Routes/*.php,Routes/**/*.php}', GLOB_BRACE | GLOB_NOSORT);

        foreach ($routes_files as $file) {
            require_once($file);
        }
    }

    /**
     * Initialize the application components.
     * 
     * This method:
     * - Sets up the application locale
     * - Initializes the router
     * - Loads all route definitions
     * - Dispatches the current request to the appropriate route handler
     * 
     * @return void
     */
    public function init()
    {
        Local::setLocale();

        Route::init();

        $this->loadRoutes();

        Route::dispatch();
    }
}