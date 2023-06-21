<?php

namespace MyApp;

class Auth
{
    public static function checkAuthorization()
    {
        // Получаем токен из заголовка
        $token = self::getBearerToken();

        // Проверяем наличие токена
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        // Проверяем токен
        if ($token !== 'my_bearer_token') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
    }

    /**
     * Get header Authorization
     * */
    private static function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    private static function getBearerToken()
    {
        $headers = self::getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}

// Использование класса в файле с соответствующим именем по PSR 4
require_once DIR . '/../vendor/autoload.php';

use MyApp\Auth;

header('Content-Type: application/json');

Auth::checkAuthorization();

// Отправляем ответ
echo json_encode(['message' => 'synaptic forether']);
