<?php

namespace App\Util;

class CsrfToken
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_LIFETIME = 3600;

    public static function generate(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $timestamp = time();

        $_SESSION[self::TOKEN_NAME] = [
            'token' => $token,
            'timestamp' => $timestamp
        ];

        return $token;
    }

    public static function validate(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME])) {
            return false;
        }

        $sessionToken = $_SESSION[self::TOKEN_NAME];

        if ((time() - $sessionToken['timestamp']) > self::TOKEN_LIFETIME) {
            unset($_SESSION[self::TOKEN_NAME]);
            return false;
        }

        if (!hash_equals($sessionToken['token'], $token)) {
            return false;
        }

        return true;
    }

    public static function getTokenName(): string
    {
        return self::TOKEN_NAME;
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION[self::TOKEN_NAME]);
    }

    public static function regenerate(): string
    {
        self::destroy();
        return self::generate();
    }
}
