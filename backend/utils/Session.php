<?php
class Session{
    public static function allowAuthenticatedOnly(){
        self::start();

        if(!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true){
            Response::error('Authentication required', Response::HTTP_UNAUTHORIZED);
            exit;
        }
    }

    public static function allowUnauthenticatedOnly(){
        self::start();

        if(isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true){
            Response::error('Already authenticated', Response::HTTP_BAD_REQUEST);
            exit;
        }
    }

    public static function login(int $userId, string $userName){
        self::start();

        $_SESSION['is_authenticated'] = true;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
    }

    public static function getUserId(): ?int
    {
        self::start();

        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    public static function logout(){
        self::start();

        session_unset();
        session_destroy();
    }

    public static function start(){
        if(session_status() === PHP_SESSION_NONE){
            // Configure session cookie to work with cross-origin requests
            // Frontend runs on port 80, backend on port 3000
            session_set_cookie_params([
                'lifetime' => 0, // Session cookie (expires when browser closes)
                'path' => '/',
                'domain' => '', // Empty = current domain
                'secure' => false, // Set to true in production with HTTPS
                'httponly' => true, // Prevent JavaScript access for security
                'samesite' => 'Lax' // Allow cross-origin requests but maintain CSRF protection
            ]);
            session_start();
        }
    }
}