<?php

class Database{
    private const HOST = 'db';
    private const PORT = 3307;
    private const NAME = 'szrotomoto';
    private const USER = 'appuser';
    private const PASSWORD = 'apppass';

    private string $dsn = "mysql:host=".self::HOST.";port=".self::PORT.";dbname=".self::NAME.";charset=utf8";
    private ?PDO $pdo;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->pdo = new PDO($this->dsn, self::USER, self::PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    public function getPdo()
    {
        if (!$this->pdo) {
            $this->connect();
        }

        return $this->pdo;
    }
}