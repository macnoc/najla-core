<?php
namespace Najla\Core;

use Najla\Core\Config;

/**
 * Class SEO
 * 
 * This class provides utility functions for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class SEO {

    private static $props = [
        'title' => '',
        'description' => '',
        'image' => '',
        'canonical' => '',
        'robots' => ''
    ];

    private static $json_ld = '';

    private static $customProps = [];
    private static $SEOData;
    private static $initialized = false;
    private static $viewId = '';

    /**
     * Initialize the SEO
     * 
     * This method:
     * - Initializes the SEO
     * 
     * @return void
     */
    public static function init() {
        if (!file_exists(ROOT . "/Data/locales/" . LANG . "/seo.json")) {
            throw new \Exception('SEO data file not found: ' . ROOT . "/Data/locales/" . LANG . "/seo.json");
        }

        $seoFile = ROOT . "/Data/locales/" . LANG . "/seo.json";
        self::$SEOData = json_decode(file_get_contents($seoFile), true);
        
        self::$props['title'] = self::$SEOData['title'] ?? '';
        self::$props['description'] = self::$SEOData['description'] ?? '';
        self::$props['image'] = self::$SEOData['image'] ?? '';
        self::$props['robots'] = 'index, follow';
        self::$props['canonical'] = self::getPageUrl();
        self::$json_ld = self::$SEOData['ld'] ?? '';
        self::$initialized = true;
    }

    /**
     * Get the page URL
     * 
     * This method:
     * - Returns the page URL
     * 
     * @return string The page URL
     */
    public static function getPageUrl() {
        $uri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_SPECIAL_CHARS);
        
        if (empty($uri)) {
            return '';
        }
        
        $url = Config::get('url') . $uri;
        
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return '';
        }
        
        return $url;
    }

    /**
     * Get the property
     * 
     * This method:
     * - Returns the property
     * 
     * @param string $name The name
     * @return mixed The property
     */
    public static function get($name) {
        if (!self::$initialized) {
            self::init();
        }

        if (!isset(self::$props[$name])) {
            return null;
        }

        if (isset(self::$customProps[$name])) {
            return self::$customProps[$name];
        }

        if (self::$viewId && isset(self::$SEOData['pages'][self::$viewId][$name])) {
            return self::$SEOData['pages'][self::$viewId][$name];
        }

        return self::$props[$name];
    }

    /**
     * Set the property
     * 
     * This method:
     * - Sets the property
     * 
     * @param string $name The name
     * @param mixed $value The value
     * @return void
     */
    public static function set($name, $value) {
        if (!self::$initialized) {
            self::init();
        }

        if (isset(self::$props[$name])) {
            self::$customProps[$name] = $value;
        }
    }

    /**
     * Set the schema
     * 
     * This method:
     * - Sets the schema
     * 
     * @param array $schema The schema
     * @return void
     */
    public static function setSchema($schema) {
        if (!self::$initialized) {
            self::init();
        }

        self::$json_ld = json_encode($schema);
    }

    /**
     * Render the SEO
     * 
     * This method:
     * - Renders the SEO
     * 
     * @return string The SEO
     */
    public static function render() {
        if (!self::$initialized) {
            self::init();
        }

        $schema = '';

        if (self::$viewId && isset(self::$SEOData['pages'][self::$viewId]['ld'])) {
            $schema = "<script type='application/ld+json'>" . json_encode(self::$SEOData['pages'][self::$viewId]['ld']) . "</script>";
        } elseif (!empty(self::$json_ld)) {
            $schema = "<script type='application/ld+json'>" . self::$json_ld . "</script>";
        }

        return "
            <title>" . self::get('title') . " | " . Config::get('app.name') . "</title>

            <meta name='description' content='" . self::get('description') . "'>
            <link rel='icon' type='image/png' href='" . Config::get('url') . "/assets/favicon.png'>

            <meta property='og:image' content='" . self::get('image') . "'>
            <meta property='og:url' content='" . self::getPageUrl() . "'>
            <meta property='og:type' content='website'>

            <meta name='twitter:image' content='" . self::get('image') . "'>
            <meta name='twitter:card' content='summary_large_image'>
            <meta name='twitter:title' content='" . self::get('title') . "'>
            <meta name='twitter:description' content='" . self::get('description') . "'>
            <meta name='twitter:url' content='" . self::getPageUrl() . "'>
            <meta name='twitter:domain' content='{$_SERVER['HTTP_HOST']}'>

            <meta name='generator' content='" . Config::get('app.name') . "'>
            <meta name='author' content='Nabil Makhnouq'>

            <link rel='canonical' href='" . self::get('canonical') . "'>
            <meta name='robots' content='" . self::get('robots') . "'>

            $schema
        ";
    }

    /**
     * Set the view ID
     * 
     * This method:
     * - Sets the view ID
     * 
     * @param string $id The ID
     * @return void
     */
    public static function setViewId($id) {
        if (!self::$initialized) {
            self::init();
        }
        self::$viewId = $id;
    }
}
