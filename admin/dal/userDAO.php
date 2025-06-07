<?php

namespace App\Dal;

use App\Dal\Connection;
use App\Model\User;
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
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $users = [];
            foreach ($resultado as $dados) {
                $users[] = new User(
                    (int) $dados["id"],
                    $dados["username"],
                    $dados["email"],
                    $dados["password"]
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

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) return null;

            return new User(
                (int) $dados["id"],
                $dados["username"],
                $dados["email"],
                $dados["password"]
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

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) return null;

            return new User(
                (int) $dados["id"],
                $dados["username"],
                $dados["email"],
                $dados["password"]
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

            if ($stmt->rowCount() === 0) {
                throw new Exception("Nenhum registro foi alterado ou usuário não encontrado");
            }
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
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = MD5(?)");
            $stmt->execute([$email, $password]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) return null;

            return new User(
                (int) $dados["id"],
                $dados["username"],
                $dados["email"],
                $dados["password"]
            );
        } catch (\PDOException $e) {
            throw new PDOException("Erro ao autenticar usuário: " . $e->getMessage());
        }
    }
}
