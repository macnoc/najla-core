<?php

namespace Najla\Core;

use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Class Local
 * 
 * This class provides utility functions for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class Local
{
    private static $translator = null;

    /**
     * Get the translator instance
     * 
     * This method:
     * - Returns the translator instance
     * 
     * @return Translator The translator instance
     */
    public static function translator(): Translator
    {
        if (self::$translator === null) {
            self::$translator = new Translator(LANG);
            self::$translator->addLoader('json', new JsonFileLoader());

            $localeFile = ROOT . '/Data/locales/' . LANG . '/translations/messages.json';
            if (file_exists($localeFile)) {
                self::$translator->addResource('json', $localeFile, LANG);
            } else {
                throw new \Exception('Locale file not found: ' . $localeFile);
            }
        }

        return self::$translator;
    }

    /**
     * Set the locale
     * 
     * This method:
     * - Sets the locale
     * 
     * @return void
     */
    public static function setLocale(): void
    {
        date_default_timezone_set(Config::get('locales.available.' . LANG . '.timezone'));
        setlocale(LC_TIME, LANG . '.utf8', LANG, 'english');
        setlocale(LC_ALL, LANG . '.utf8');
        putenv('LC_ALL=' . LANG . '.utf8');
        putenv('LANG=' . LANG);
        textdomain("messages");
        bindtextdomain("messages", ROOT . "/Data/locales");
        bind_textdomain_codeset("messages", 'UTF-8');
    }

    /**
     * Get the language
     * 
     * This method:
     * - Returns the language
     * 
     * @return string The language
     */
    public static function getLang()
    {
        return explode('_', LANG)[0];
    }

    /**
     * Get the data
     * 
     * This method:
     * - Returns the data
     * 
     * @param string $key The key
     * @return mixed The data
     */
    public static function getData($key)
    {
        if (!Config::get('locales.available.' . LANG . '.' . $key)) {
            return Config::get('locales.available.' . Config::get('locales.default') . '.' . $key);
        }
        return Config::get('locales.available.' . LANG . '.' . $key);
    }
}
