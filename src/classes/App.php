<?php

namespace Najla\Core;

use Najla\Core\Config;
use Najla\Core\Route;
use Najla\Core\Path;
use Najla\Core\Local;

class App
{
    public function __construct()
    {
        define('BASE_PATH', Path::base());

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

    private function loadRoutes()
    {
        if (!file_exists(BASE_PATH . '/Routes')) {
            throw new \Exception('Routes folder not found');
        }

        $routes_files = glob(BASE_PATH . '/{Routes/*.php,Routes/**/*.php}', GLOB_BRACE | GLOB_NOSORT);

        foreach ($routes_files as $file) {
            require_once($file);
        }
    }

    public function init()
    {
        Local::setLocale();

        Route::init();

        $this->loadRoutes();

        Route::dispatch();
    }
}
