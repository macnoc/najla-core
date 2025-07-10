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
        define('LANG', Config::get('default_lang'));

        Config::init();
        ErrorHandler::init();

        if (Config::get('cookies_domain') !== false) {
            ini_set('session.cookie_domain', Config::get('cookies_domain'));
        }

        session_name(Config::get('session_name'));
        session_start();

        $this->init();
    }

    /* private function setLocales()
    {
        Local::setLocale();

        // Set locale and gettext
        setlocale(LC_ALL, Config::get('default_lang') . '.utf8');

        // Set timezone to Sweden
        date_default_timezone_set('Europe/Stockholm');

        // Set Swedish locale settings for date and time
        setlocale(LC_TIME, 'sv_SE.utf8', 'sv_SE', 'sv', 'swedish');

        putenv('LC_ALL=' . Config::get('default_lang') . '.utf8');
        putenv('LANG=' . Config::get('default_lang'));
        textdomain("messages");
        bindtextdomain("messages", BASE_PATH . "/locales");
        bind_textdomain_codeset("messages", 'UTF-8');

        Config::set('templates_path', BASE_PATH . "/Locales/" . LANG . "/emails/");
    } */

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
