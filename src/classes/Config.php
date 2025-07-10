<?php

namespace Najla\Core;

class Config
{
    private static $config;

    static public function init() {
        self::$config = new \stdClass();
        
        $configFile = BASE_PATH . '/config/config.php';
        
        if (file_exists($configFile)) {
            $config = require $configFile;
            self::$config = (object) $config;
        } else {
            throw new \Exception('Configuration file not found: ' . $configFile);
        }
    }

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
