<?php

namespace Najla\Core;

use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Translator;

class Local
{
    private static $translator = null;

    public static function translator(): Translator
    {
        if (self::$translator === null) {
            self::$translator = new Translator(LANG);
            self::$translator->addLoader('json', new JsonFileLoader());

            $localeFile = BASE_PATH . '/Locales/' . LANG . '/translations/messages.json';
            if (file_exists($localeFile)) {
                self::$translator->addResource('json', $localeFile, LANG);
            } else {
                throw new \Exception('Locale file not found: ' . $localeFile);
            }
        }

        return self::$translator;
    }

    public static function setLocale(): void
    {
        setlocale(LC_ALL, LANG . '.utf8');
        putenv('LC_ALL=' . LANG . '.utf8');
        putenv('LANG=' . LANG);
        textdomain("messages");
        bindtextdomain("messages", BASE_PATH . "/Locales");
        bind_textdomain_codeset("messages", 'UTF-8');
    }
}