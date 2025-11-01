<?php

namespace Najla\Core;

/**
 * Config.php 
 * 
 * The configuration class
 * 
 * This class is responsible for loading and managing the application configuration.
 *
 * @package     Najla\Core
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */

class Config
{
    private static $config;

    /**
     * Initialize the configuration
     * 
     * This method:
     * - Loads the configuration file
     * - Sets up the configuration object
     * 
     * @throws Exception If the configuration file is not found
     */
    static public function init() {
        self::$config = new \stdClass();
        
        $configFile = ROOT . '/Config/config.php';
        
        if (file_exists($configFile)) {
            $config = require $configFile;
            self::$config = (object) $config;
        } else {
            throw new \Exception('Configuration file not found: ' . $configFile);
        }
    }

    /**
     * Get a configuration value
     * 
     * This method:
     * - Retrieves a configuration value by key
     * - Supports dot notation for nested keys
     * 
     * @param string $key The configuration key
     * @return mixed The configuration value, or null if not found
     */
    static public function get($key){
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = self::$config;
            
            foreach ($keys as $k) {
                if (is_object($value)) {
                    if (!isset($value->$k)) {
                        return null;
                    }
                    $value = $value->$k;
                } elseif (is_array($value)) {
                    if (!isset($value[$k])) {
                        return null;
                    }
                    $value = $value[$k];
                }
            }
            
            return $value;
        }
        
        return isset(self::$config->$key) ? self::$config->$key : null;
    }

    /**
     * Set a configuration value
     * 
     * This method:
     * - Sets a configuration value by key
     * - Supports dot notation for nested keys
     * 
     * @param string $key The configuration key
     * @param mixed $value The configuration value
     */
    static public function set($key, $value){
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $current = &self::$config;
            
            foreach ($keys as $i => $k) {
                if ($i === count($keys) - 1) {
                    if (is_object($current)) {
                        $current->$k = $value;
                    } else {
                        $current[$k] = $value;
                    }
                } else {
                    if (is_object($current)) {
                        if (!isset($current->$k)) {
                            $current->$k = new \stdClass();
                        }
                        $current = &$current->$k;
                    } elseif (is_array($current)) {
                        if (!isset($current[$k])) {
                            $current[$k] = [];
                        }
                        $current = &$current[$k];
                    }
                }
            }
        } else {
            self::$config->$key = $value;
        }
    }
}
