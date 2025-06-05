<?php

namespace App\Dal;

use \PDO;
use \PDOException;

abstract class Connection
{
    private static ?PDO $connection = null;
    private static string $host = "localhost";
    private static string $dbName = "universe_db";
    private static string $user = "pizza_user";
    private static string $password = "pizza123";

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbName . ";charset=utf8mb4";
                
                self::$connection = new PDO(
                    $dsn,
                    self::$user,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
            } catch (\PDOException $e) {
                throw new \PDOException("Database connection error: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
