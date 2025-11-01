<?php

namespace Najla\Core;

use AltoRouter;
use Najla\Core\SEO;
use Najla\Core\View;

/**
 * Class Route
 * 
 * This class provides utility functions for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class Route
{
    private static $router;

    /**
     * Initialize the router
     * 
     * This method:
     * - Initializes the router
     * 
     * @return void
     */
    public static function init()
    {
        self::$router = new AltoRouter();
    }

    /**
     * Get the router instance
     * 
     * This method:
     * - Returns the router instance
     * 
     * @return AltoRouter The router instance
     */
    public static function route(){
        return self::$router;
    }

    /**
     * Map a GET route
     * 
     * This method:
     * - Maps a GET route
     * 
     * @param string $route The route
     * @param callable $callback The callback
     * @param string $name The name
     * @return void
     */
    public static function get($route, $callback, $name = null)
    {
        return self::$router->map('GET', $route, $callback, $name);
    }

    /**
     * Map a POST route
     * 
     * This method:
     * - Maps a POST route
     * 
     * @param string $route The route
     * @param callable $callback The callback
     * @param string $name The name
     * @return void
     */
    public static function post($route, $callback, $name = null)
    {
        return self::$router->map('POST', $route, $callback, $name);
    }

    /**
     * Map a PUT route
     * 
     * This method:
     * - Maps a PUT route
     * 
     * @param string $route The route
     * @param callable $callback The callback
     * @param string $name The name
     * @return void
     */
    public static function put($route, $callback, $name = null)
    {
        return self::$router->map('PUT', $route, $callback, $name);
    }

    /**
     * Dispatch the router
     * 
     * This method:
     * - Dispatches the router
     * 
     * @return void
     */
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
            http_response_code(404);
            $viewInstance = new View();
            $viewInstance->renderError(404);
        }
    }

    /**
     * Map a view route
     * 
     * This method:
     * - Maps a view route
     * 
     * @param string $route The route
     * @param string $view The view
     * @param string $name The name
     * @param array $data The data
     * @return void
     */
    public static function view($route, $view, $name = null, $data = [])
    {
        return self::$router->map('GET', $route, function () use ($view, $name, $data) {
            if($name){
                SEO::setViewId($name);
            }

            $viewInstance = new View();
            $viewInstance->render($view, $data);
        }, $name);
    }

    /**
     * Map a page route
     * 
     * This method:
     * - Maps a page route
     * 
     * @param string $route The route
     * @param string $page The page
     * @param string $name The name
     * @param array $data The data
     * @return void
     */
    public static function page($route, $page, $name = null, $data = [])
    {
        return self::$router->map('GET', $route, function () use ($page, $name, $data) {
            if($name){
                SEO::setViewId($name);
            }

            $viewInstance = new View();
            $viewInstance->renderPage($page, $data);
        }, $name);
    }

}
