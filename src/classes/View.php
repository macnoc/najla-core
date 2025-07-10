<?php

namespace Najla\Core;

class View
{
    private $viewServices = null;

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

    public function render($view, $data = [])
    {
        $services = $this->loadViewServices();

        if ($services !== null) {
            foreach ($services as $key => $serviceClass) {
                if (method_exists($serviceClass, 'getInstance')) {
                    $data[$key] = $serviceClass::getInstance();
                }
            }
        }

        extract($data);

        $view = str_replace('.', '/', $view);

        if (!$this->viewExists($view)) {
            throw new \Exception('View not found: ' . BASE_PATH . '/views/' . $view . '.view.php');
        }

        require_once BASE_PATH . '/views/' . $view . '.view.php';
    }

    public function viewExists($view)
    {
        $view = str_replace('.', '/', $view);
        return file_exists(BASE_PATH . '/views/' . $view . '.view.php');
    }

    public function renderError($error, $data = [])
    {
        if (is_numeric($error)) {
            $error = (string) $error;
        }

        $errors = [
            '404' => [
                'title' => x('Page not found'),
                'description' => x('The page you are looking for does not exist.'),
            ],
            '500' => [
                'title' => x('Server error'),
                'description' => x('An error occurred on the server.'),
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
}
