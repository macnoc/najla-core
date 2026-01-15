<?php

namespace Najla\Core;

/**
 * Class Csrf
 * 
 * This class provides CSRF protection for the application.
 * 
 * @package     Najla\Core 
 * @author      Nabil Makhnouq
 * @version     1.0.0
 * @since       File available since Release 1.0.0
 */

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;
use Symfony\Component\Security\Csrf\CsrfToken;

class Csrf
{
    private static $tokenManager;

    /**
     * Initialize the CSRF token manager
     * 
     * @return void
     */
    public static function init()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        self::$tokenManager = new CsrfTokenManager(
            new UriSafeTokenGenerator(),
            new NativeSessionTokenStorage()
        );
    }

    /**
     * Get a CSRF token
     * 
     * @param string $tokenId The token ID
     * @return string The CSRF token
     */
    public static function getToken(string $tokenId = 'main'): string
    {
        if (!self::$tokenManager) {
            self::init();
        }
        return self::$tokenManager->getToken($tokenId)->getValue();
    }

    /**
     * Validate a CSRF token
     * 
     * @param string|null $token The token to validate
     * @param string $tokenId The token ID
     * @return bool True if the token is valid, false otherwise
     */
    public static function validateToken(?string $token = null, string $tokenId = 'main'): bool
    {
        if (!self::$tokenManager) {
            self::init();
        }

        if ($token === null) {
            $token = filter_input(INPUT_SERVER, 'HTTP_X_CSRF_TOKEN', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        if (!$token) {
            return false;
        }

        return self::$tokenManager->isTokenValid(new CsrfToken($tokenId, $token));
    }

    /**
     * Validate a CSRF token or return 403 Forbidden
     * 
     * @param string|null $token The token to validate
     * @param string $tokenId The token ID
     * @return void
     */
    public static function validateTokenOr403(?string $token = null, string $tokenId = 'main'): void
    {
        if (!self::validateToken($token, $tokenId)) {
            http_response_code(403);
            exit;
        }
    }
}