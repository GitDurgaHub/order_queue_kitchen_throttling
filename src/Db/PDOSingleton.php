<?php
namespace App\Db;

use PDO;
use PDOException;

final class PDOSingleton
{
    private static $instance = null;
    private $pdo;

    private function __construct(array $config)
    {
        $dsn = $config['db']['dsn'];
        $user = $config['db']['user'];
        $pass = $config['db']['pass'];
        $options = $config['db']['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public static function init(array $config): void
    {
        if (self::$instance === null) {
            self::$instance = new PDOSingleton($config);
        }
    }

    public static function getInstance(): PDOSingleton
    {
        if (self::$instance === null) {
            throw new \RuntimeException('PDO not initialized. Call PDOSingleton::init($config) first.');
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Prevent cloning/unserializing
    private function __clone() {}
    private function __wakeup() {}
}
