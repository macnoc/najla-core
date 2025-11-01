<?php

namespace Najla\Core;

/**
 * Class View
 * 
 * This class provides utility functions for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class View
{
    private $viewServices = null;

    /**
     * Load the view services
     * 
     * This method:
     * - Loads the view services
     * 
     * @return array The view services
     */
    private function loadViewServices()
    {
        if (Config::get('view_services') === null) {
            return null;
        }
        if ($this->viewServices === null) {
            $this->viewServices = Config::get('view_services');
        }
        return $this->viewServices;
    }

    /**
     * Inject the view services
     * 
     * This method:
     * - Injects the view services
     * 
     * @param array $data The data
     * @return void
     */
    private function injectViewServices(&$data)
    {
        $services = $this->loadViewServices();

        if ($services !== null) {
            foreach ($services as $key => $serviceClass) {
                if (method_exists($serviceClass, 'getInstance')) {
                    $data[$key] = $serviceClass::getInstance();
                }
            }
        }
    }

    /**
     * Render the view
     * 
     * This method:
     * - Renders the view
     * 
     * @param string $view The view
     * @param array $data The data
     * @return void
     */
    public function render($view, $data = [])
    {
        $this->injectViewServices($data);

        extract($data);

        $view = str_replace('.', '/', $view);

        if (!$this->viewExists($view)) {
            throw new \Exception('View not found: ' . ROOT . '/Views/' . $view . '.view.php');
        }

        require_once ROOT . '/Views/' . $view . '.view.php';
    }

    /**
     * Check if the view exists
     * 
     * This method:
     * - Checks if the view exists
     * 
     * @param string $view The view
     * @return bool True if the view exists, false otherwise
     */
    public function viewExists($view)
    {
        $view = str_replace('.', '/', $view);
        return file_exists(ROOT . '/Views/' . $view . '.view.php');
    }

    /**
     * Render the error
     * 
     * This method:
     * - Renders the error view if it exists otherwise renders a default error view
     * 
     * @param string $error The error
     * @param array $data The data
     * @return void
     */
    public function renderError($error, $data = [])
    {
        if (is_numeric($error)) {
            $error = (string) $error;
        }

        $errors = [
            '404' => [
                'title' => __('Page not found'),
                'description' => __('The page you are looking for does not exist.'),
            ],
            '500' => [
                'title' => __('Server error'),
                'description' => __('An error occurred on the server.'),
            ],
        ];

        if ($this->viewExists($error)) {
            $this->render($error, $data);
        } else {
            echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $errors[$error]['title'] . '</title>
        </head>
        <body>
            <h1>' . $errors[$error]['title'] . '</h1>
            <p>' . $errors[$error]['description'] . '</p>
        </body>
        </html>';
        }
    }

    /**
     * Render the page
     * 
     * This method:
     * - Renders the page
     * 
     * @param string $page The page
     * @param array $data The data
     * @return void
     */
    public function renderPage($page, $data = [])
    {
        $this->injectViewServices($data);

        extract($data);

        $page = str_replace('.', '/', $page);

        if (!$this->pageExists($page)) {
            throw new \Exception('Page not found: ' . ROOT . '/Data/locales/' . LANG . '/pages/' . $page . '.page.php');
        }

        require_once ROOT . '/Data/locales/' . LANG . '/pages/' . $page . '.page.php';
    }

    /**
     * Check if the page exists
     * 
     * This method:
     * - Checks if the page exists
     * 
     * @param string $page The page
     * @return bool True if the page exists, false otherwise
     */
    public function pageExists($page)
    {
        $page = str_replace('.', '/', $page);
        return file_exists(ROOT . '/Data/locales/' . LANG . '/pages/' . $page . '.page.php');
    }
}
