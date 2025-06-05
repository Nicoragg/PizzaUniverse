<?php

namespace App\Dal;

use \PDO;
use \PDOException;

abstract class Connection
{
    private static ?PDO $connection = null;
    private static string $host = "localhost";
    private static string $dbName = "universe_db";
    private static string $user = "root";
    private static string $password = "";

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbName,
                    self::$user,
                    self::$password
                );
            } catch (\PDOException $e) {
                throw new \PDOException("Erro ao conectar ao banco " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
