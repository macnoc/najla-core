<?php

namespace Najla\Core;

use Rakit\Validation\Validator;
use Najla\Core\Config;
use Najla\Validation\CorrectPassword;

class Controller
{
    public function __construct() {}

    public function inputsAsJson()
    {
        $json_data = file_get_contents("php://input");

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return json_decode($json_data, true);
    }

    public function inputsAsJsonOr400()
    {
        $inputs = $this->inputsAsJson();
        if (!$inputs) {
            http_response_code(400);
            exit;
        }
        return $inputs;
    }

    public function inputsAsJsonAndValidate($rules)
    {
        $validator = new Validator([
            'required' => x('validation.required'),
            'email' => x('validation.email'),
            'in' => x('validation.in')
        ]);

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

    public function inputsAsFormAndValidate($rules)
    {
        $validator = new Validator;
        $validator->addValidator('correct_password', new CorrectPassword());

        $validation = $validator->validate($_POST + $_FILES, $rules);

        if ($validation->fails()) {
            throw new \Najla\Exceptions\ValidationException('Invalid input', 400, $validation->errors()->firstOfAll());
        }

        return $validation->getValidData();
    }

    public function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        return;
    }

    public function jsonSuccess($data = [])
    {
        $this->json(array_merge([
            'status' => 'success'
        ], $data));
    }

    public function jsonError($data = [])
    {
        $this->json(array_merge([
            'status' => 'error',
        ], $data));
    }

    public function view($view, $data = [])
    {
        Helper::getView($view, $data);
    }

    public function view404()
    {
        http_response_code(404);
        Helper::getView('404');
    }

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
