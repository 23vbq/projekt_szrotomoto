<?php
class Env {
    private static ?array $variables = null;

    public static function load()
    {
        $envPath = __DIR__ . '/../.env';
        
        if (!file_exists($envPath)) {
            throw new RuntimeException('.env file not found at: ' . $envPath);
        }

        $variables = parse_ini_file($envPath);

        if ($variables === false) {
            $error = error_get_last();
            $errorMsg = $error ? $error['message'] : 'Unknown error';
            throw new RuntimeException('Failed to load .env file: ' . $errorMsg);
        }

        self::$variables = $variables;
    }

    public static function get(string $key)
    {
        if (self::$variables === null) {
            self::load();
        }

        if (!array_key_exists($key, self::$variables)) {
            throw new InvalidArgumentException("Environment variable '{$key}' not found");
        }

        return self::$variables[$key];
    }
}