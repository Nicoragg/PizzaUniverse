<?php

namespace App\Dal;

use App\Dal\Connection;
use App\Models\User;
use Exception;
use \PDO;
use \PDOException;

abstract class UserDao
{
    public static function create(User $user): int
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([
                $user->username,
                $user->email,
                $user->password
            ]);
            return (int) $pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new \PDOException("Erro ao salvar usuário no Banco de Dados: " . $e->getMessage());
        }
    }

    public static function findAll(): array
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users ORDER BY id");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($res as $data) {
                $users[] = new User(
                    (int) $data["id"],
                    $data["username"],
                    $data["email"],
                    $data["password"]
                );
            }

            return $users;
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao listar usuários: " . $e->getMessage());
        }
    }

    public static function findById(int $id): ?User
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new User(
                (int) $data["id"],
                $data["username"],
                $data["email"],
                $data["password"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar usuário: " . $e->getMessage());
        }
    }

    public static function findByEmail(string $email): ?User
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new User(
                (int) $data["id"],
                $data["username"],
                $data["email"],
                $data["password"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao buscar usuário por email: " . $e->getMessage());
        }
    }

    public static function update(User $user): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([
                $user->username,
                $user->email,
                $user->password,
                $user->id
            ]);
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao alterar usuário: " . $e->getMessage());
        }
    }

    public static function delete(int $id): void
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Nenhum registro foi excluído ou usuário não encontrado");
            }
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao excluir usuário: " . $e->getMessage());
        }
    }

    public static function authenticate(string $email, string $password): ?User
    {
        try {
            $pdo = Connection::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $stmt->execute([$email, md5($password)]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new User(
                (int) $data["id"],
                $data["username"],
                $data["email"],
                $data["password"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao autenticar usuário: " . $e->getMessage());
        }
    }
}
