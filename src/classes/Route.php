<?php

namespace Najla\Core;

use AltoRouter;
use Najla\Core\SEO;
use Najla\Core\Helper;

class Route
{
    private static $router;

    public static function init()
    {
        self::$router = new AltoRouter();
    }

    public static function route(){
        return self::$router;
    }

    public static function get($route, $callback, $name = null)
    {
        return self::$router->map('GET', $route, $callback, $name);
    }

    public static function post($route, $callback, $name = null)
    {
        return self::$router->map('POST', $route, $callback, $name);
    }

    public static function put($route, $callback, $name = null)
    {
        return self::$router->map('PUT', $route, $callback, $name);
    }

    public static function dispatch()
    {
        $match = self::$router->match();

        if (is_array($match)) {
            if (is_callable($match['target'])) {
                call_user_func_array($match['target'], $match['params']);
            } 
            // Hantera controller metoder [Controller::class, 'method']
            else if (is_array($match['target'])) {
                $controller = new $match['target'][0]();
                call_user_func_array([$controller, $match['target'][1]], $match['params']);
            }
        } else {
            SEO::set('title', x('general.404.title'));
            SEO::set('description', x('general.404.description'));

            http_response_code(404);
            Helper::getView('404');
        }
    }

    public static function view($route, $view, $name = null, $data = [])
    {
        

        return self::$router->map('GET', $route, function () use ($view, $name, $data) {
            if($name){
                SEO::setViewId($name);
            }

            Helper::getView($view, $data);
        }, $name);
    }

}
