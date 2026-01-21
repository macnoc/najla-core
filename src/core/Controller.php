<?php

namespace Najla\Core;

use Rakit\Validation\Validator;
use Najla\Core\View;

/**
 * Controller.php 
 * 
 * The controller class
 * 
 * This class is responsible for handling the application logic.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */
class Controller
{
    private $validator;

    /**
     * Controller constructor.
     * 
     * Initializes the controller
     */
    public function __construct()
    {
    }

    /**
     * Initialize validator
     * 
     * This method:
     * - Initializes the validator
     */
    private function validator()
    {
        if ($this->validator === null) {
            // TODO: Move this to Config for more flexibility
            $this->validator = new Validator([
                'required' => __('validation.required'),
                'email' => __('validation.email'),
                'in' => __('validation.in')
            ]);

            $customValidators = Config::get('custom_validators');

            if ($customValidators !== null) {
                foreach ($customValidators as $key => $validatorClass) {
                    if (method_exists($validatorClass, 'check')) {
                        $this->validator->addValidator($key, new $validatorClass());
                    }
                }
            }
        }
        return $this->validator;
    }

    /**
     * Get the inputs as JSON
     * 
     * This method:
     * - Retrieves the JSON data from the request
     * - Returns the JSON data as an array
     * 
     * @return array|null The JSON data, or null if not found
     */
    public function inputsAsJson()
    {
        $json_data = file_get_contents("php://input");

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return json_decode($json_data, true);
    }

    /**
     * Get the inputs as JSON or return 400
     * 
     * This method:
     * - Retrieves the JSON data from the request
     * - Returns the JSON data as an array
     * - Returns 400 if the JSON data is not found
     * 
     * @return array|null The JSON data, or null if not found
     */
    public function inputsAsJsonOr400()
    {
        $inputs = $this->inputsAsJson();
        if (!$inputs) {
            http_response_code(400);
            exit;
        }
        return $inputs;
    }

    /**
     * Get the inputs as JSON and validate them
     * 
     * This method:
     * - Retrieves the JSON data from the request
     * - Validates the JSON data
     * - Returns the JSON data as an array
     * 
     * @param array $rules The validation rules
     * @return array The validated JSON data
     * @throws \Najla\Exceptions\ValidationException If the JSON data is invalid
     */
    public function inputsAsJsonAndValidate($rules)
    {
        $validator = $this->validator();

        $inputs = $this->inputsAsJson();
        if (!$inputs) {
            throw new \Najla\Exceptions\AppException('Invalid JSON', 400);
        }

        $validation = $validator->validate($inputs, $rules);

        if ($validation->fails()) {
            throw new \Najla\Exceptions\ValidationException('Invalid input', 400, $validation->errors()->firstOfAll());
        }

        return $validation->getValidData();
    }

    /**
     * Get the inputs as form and validate them
     * 
     * This method:
     * - Retrieves the form data from the request
     * - Validates the form data
     * - Returns the form data as an array
     * 
     * @param array $rules The validation rules
     * @return array The validated form data
     * @throws \Najla\Exceptions\ValidationException If the form data is invalid
     */
    public function inputsAsFormAndValidate($rules)
    {
        $validator = $this->validator();

        $validation = $validator->validate($_POST + $_FILES, $rules);

        if ($validation->fails()) {
            throw new \Najla\Exceptions\ValidationException('Invalid input', 400, $validation->errors()->firstOfAll());
        }

        return $validation->getValidData();
    }

    /**
     * Send a JSON response
     * 
     * This method:
     * - Sets the Content-Type header to application/json
     * - Sends the JSON data as a response
     * 
     * @param array $data The data to send as JSON
     */
    public function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        return;
    }

    /**
     * Send a JSON success response
     * 
     * This method:
     * - Sets the Content-Type header to application/json
     * - Sends the JSON data as a response
     * 
     * @param array $data The data to send as JSON
     */
    public function jsonSuccess($data = [])
    {
        $this->json(array_merge([
            'status' => 'success'
        ], $data));
    }

    /**
     * Send a JSON error response
     * 
     * This method:
     * - Sets the Content-Type header to application/json
     * - Sends the JSON data as a response
     * 
     * @param array $data The data to send as JSON
     */
    public function jsonError($data = [])
    {
        $this->json(array_merge([
            'status' => 'error',
        ], $data));
    }

    /**
     * Render a view
     * 
     * This method:
     * - Renders a view
     * 
     * @param string $view The view to render
     * @param array $data The data to pass to the view
     */
    public function view($view, $data = [])
    {
        $viewInstance = new View();
        $viewInstance->render($view, $data);
    }

    /**
     * Render a page
     * 
     * This method:
     * - Renders a page
     * 
     * @param string $page The page to render
     * @param array $data The data to pass to the page
     */
    public function page($page, $data = [])
    {
        $viewInstance = new View();
        $viewInstance->renderPage($page, $data);
    }

    /**
     * Render a 404 error page
     * 
     * This method:
     * - Renders a 404 error page
     */
    public function view404()
    {
        http_response_code(404);
        $viewInstance = new View();
        $viewInstance->renderError(404);
    }

    /**
     * Redirect to a URL
     * 
     * This method:
     * - Redirects to a URL
     * 
     * @param string $url The URL to redirect to
     * @param string $redirect_to The URL to redirect to
     */
    public function redirect($url, $redirect_to = null)
    {
        if (!$redirect_to) {
            $redirect_to = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
            header('Location: '
                . $url);
            exit;
        }

        header('Location: ' . $url . '?redirect=' . $redirect_to);
        exit;
    }
}
