<?php

class Database{
    private static ?string $DB_HOST = null;
    private static ?int $DB_PORT = null;
    private static ?string $DB_NAME = null;
    private static ?string $DB_USER = null;
    private static ?string $DB_PASSWORD = null;

    private static ?PDO $pdo = null;

    private static function initConfig()
    {
        self::$DB_HOST = Env::get('DB_HOST');
        self::$DB_PORT = Env::get('DB_PORT');
        self::$DB_NAME = Env::get('DB_NAME');
        self::$DB_USER = Env::get('DB_USER');
        self::$DB_PASSWORD = Env::get('DB_PASSWORD');
    }

    private static function getDsn(): string
    {
        return "mysql:host=".self::$DB_HOST.";port=".self::$DB_PORT.";dbname=".self::$DB_NAME.";charset=utf8";
    }

    public static function connect()
    {
        if (
            self::$DB_HOST === null
            || self::$DB_PORT === null
            || self::$DB_NAME === null
            || self::$DB_USER === null
            || self::$DB_PASSWORD === null
        ) {
            self::initConfig();
        }

        if (self::$pdo === null) {
            self::$pdo = new PDO(self::getDsn(), self::$DB_USER, self::$DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
    }

    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            self::connect();
        }

        return self::$pdo;
    }
}