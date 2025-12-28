<?php
class Session{
    public static function allowAuthenticatedOnly(){
        self::start();

        if(!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true){
            Response::json(['error' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
            exit;
        }
    }

    public static function allowUnauthenticatedOnly(){
        self::start();

        if(isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'] === true){
            Response::json(['error' => 'Already authenticated'], Response::HTTP_BAD_REQUEST);
            exit;
        }
    }

    private static function start(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }
}