<?php
class Env {
    private static ?array $variables = null;

    public static function load()
    {
        if (!file_exists(__DIR__ . '/../.env')) {
            throw new RuntimeException('.env file not found');
        }

        self::$variables = parse_ini_file(__DIR__ . '/../.env');

        if (self::$variables === false) {
            throw new RuntimeException('Failed to load .env file');
        }
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